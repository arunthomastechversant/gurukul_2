// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Provides utility method to communicate with a VPL (this is an API wrapper to use VPL_Util)
 * @package    qtype_vplquestion
 * @copyright  Astor Bizard, 2019
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// VPL_Util has to be loaded to use this module.
/* globals VPL_Util */
define(['core/url'], function(url) {

    /**
     * Build ajax url to call with VPL_Util.
     * @param {String|Number} vplId VPL ID.
     * @param {String|Number} userId User ID.
     * @param {String} file (optional) Ajax file to use. Defaults to edit.
     * @return {String} The ajax url built.
     */
    function getAjaxUrl(vplId, userId, file) {
        if (file === undefined) {
            file = 'edit';
        }
        return url.relativeUrl('/mod/vpl/forms') + '/' + file + '.json.php?id=' + vplId + '&userId=' + userId + '&action=';
    }

    var VPLService = {};

    // Cache for info.
    var cache = {
        reqfile: [],
        execfiles: []
    };

    // Retrieve specified files from the VPL (either 'reqfile' or 'execfile').
    // Note : these files are stored in cache. To clear it, the user has to reload the page.
    VPLService.info = function(filesType, vplId) {
        if (cache[filesType][vplId] != undefined) {
            return Promise.resolve(cache[filesType][vplId]);
        } else {
            var deferred = filesType == 'reqfile' ?
                VPL_Util.requestAction('resetfiles', '', {}, getAjaxUrl(vplId, '')) :
                VPL_Util.requestAction('load', '', {}, getAjaxUrl(vplId, '', 'executionfiles'));
            return deferred.promise()
            .then(function(response) {
                var files = filesType == 'reqfile' ?
                    response.files[0] :
                    response.files;
                cache[filesType][vplId] = files;
                return files;
            });
        }
    };

    // Save student answer to VPL, by replacing {{ANSWER}} in the template by the student answer.
    VPLService.save = function(vplId, userId, template, answer, extraFiles) {
        return VPLService.info('reqfile', vplId)
        .then(function(reqfile) {
            // Replace the {{ANSWER}} tag, propagating indentation.
            reqfile.contents = template.replace(/([ \t]*)(.*)\{\{ANSWER\}\}/i, '$1$2' + answer.split('\n').join('\n$1'));
            var files = [reqfile];
            if (extraFiles) {
                extraFiles.forEach(function(file) {
                    files.push(file);
                });
            }
            return VPL_Util.requestAction('save', '', {files: files}, getAjaxUrl(vplId, userId)).promise();
        });
    };

    // Execute the specified action (should be 'run' or 'evaluate').
    // Note that this function does not call save, it has to be called beforehand if needed.
    // Note also that callback may be called several times
    // (especially one time with (false) execution error and one time right after with execution result).
    VPLService.exec = function(action, vplId, userId, terminal, callback) {
        // Build the options object for VPL_Util.
        var options = {
            ajaxurl: getAjaxUrl(vplId, userId),
            resultSet: false,
            setResult: function(result) {
                this.resultSet = true;
                callback(result);
            },
            close: function() {
                // If connection is closed without a result set, display an error.
                // /!\ It can happen that result will be set about 0.3s after closing.
                // -> Set a timeout to avoid half-second display of error.
                // Note : if delay between close and result is greater than timeout, it is fine
                // (there will just be a 0.1s error display before displaying the result).
                var _this = this;
                setTimeout(function() {
                    if (!_this.resultSet) {
                        callback({execerror: M.util.get_string('execerrordetails', 'qtype_vplquestion')});
                    }
                }, 600);
            },

            // The following will only be used for the 'run' action.
            getConsole: function() {
                return terminal;
            },
            run: function(type, conInfo, ws) {
                terminal.connect(conInfo.executionURL, function() {
                    ws.close();
                });
            }
        };

        return VPL_Util.requestAction(action, '', {}, options.ajaxurl)
        .done(function(response) {
            VPL_Util.webSocketMonitor(response, '', '', options);
        });
    };

    return {
        call: function(service, ...args) {
            // Deactivate VPL_Util progress bar, as we have our own progress indicator.
            VPL_Util.progressBar = function() {
                this.setLabel = function() {
                    return;
                };
                this.close = function() {
                    return;
                };
                this.isClosed = function() {
                    return true;
                };
            };
            // Call service.
            return VPLService[service](...args);
        },

        langOfFile: function(fileName) {
            return VPL_Util.langType(fileName.split('.').pop());
        }
    };
});
