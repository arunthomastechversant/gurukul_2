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
 * Provides utility methods to load VPL scripts.
 * @package    qtype_vplquestion
 * @copyright  Astor Bizard, 2019
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/url', 'core/log'], function($, url, log) {

    /**
     * Constructor for a scripts loader.
     * @param {String[]} scripts Array of scripts urls, to be loaded sequentially.
     * @param {Boolean} alreadyDefined Whether the scripts are already loaded.
     */
    function Loader(scripts, alreadyDefined) {
        this.loading = false;
        this.loaded = alreadyDefined;
        this.callbacks = [];
        this.scripts = scripts;
    }

    // Loader for VPLUtil script.
    // It beforehand loads VPL JQuery scripts.
    var utilLoader = new Loader([
        '/mod/vpl/editor/jquery/jquery-1.9.1.js',
        '/mod/vpl/editor/jquery/jquery-ui-1.10.4.js',
        '/mod/vpl/editor/VPL_jquery_no_conflict.js',
        '/mod/vpl/editor/VPLUtil.js'],
        typeof VPL_Util != 'undefined');

    // Loader for VPLTerminal script.
    // It beforehand loads XTerm script.
    // Note: it needs VPLUtil to be loaded.
    var terminalLoader = new Loader([
        '/mod/vpl/editor/xterm/term.js',
        '/mod/vpl/editor/VPLTerminal.js'],
        typeof VPL_Terminal != 'undefined');

    /**
     * Load scripts according to given loader.
     * It can be called multiple times but will only load them once.
     * @param {Loader} loader The Loader from which to load scripts.
     * @return {function} A function to be called with a callback as parameter.
     */
    function load(loader) {
        return function(callback) {
            if (loader.loaded) {
                callback();
                return;
            }
            loader.callbacks.push(callback);
            if (loader.loading) {
                return;
            }
            loader.loading = true;
            /**
             * Recursive function to load scripts sequentially.
             * @param {Number} iscript Index of the script to load in the Loader array.
             */
            function loadNextScript(iscript) {
                if (iscript < loader.scripts.length) {
                    $.ajax({
                        url: url.relativeUrl(loader.scripts[iscript]),
                        dataType: 'script',
                        cache: true
                    })
                    .then(function() {
                        loadNextScript(iscript + 1);
                        return;
                    })
                    .catch(log.error);
                } else {
                    loader.loaded = true;
                    loader.callbacks.forEach(function(cb) {
                        cb();
                    });
                }
            }
            loadNextScript(0);
        };
    }

    return {
        loadVPLUtil: load(utilLoader),
        loadVPLTerminal: function(callback) {
            load(utilLoader)(function() {
                load(terminalLoader)(callback);
            });
        }
    };
});