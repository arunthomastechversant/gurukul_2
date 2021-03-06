<?php
// This file is part of Moodle - http://moodle.org/
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
 * Local plugin "staticpage" - Version file
 *
 * @package    local_staticpage
 * @copyright  2013 Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
$plugin->version  = 2019023100;   // The (date) version of this module + 2 extra digital for daily versions
$plugin->requires = 2019012500;  // Requires this Moodle version - at least 2.0
$plugin->component = 'local_staticpage';
$plugin->cron     = 0;
$plugin->release = '1.0';
$plugin->maturity = MATURITY_STABLE;

// $plugin->component = 'local_staticpage';
// $plugin->version = 2021010900;
// $plugin->release = 'v3.10-r1';
// $plugin->requires = 2020110900;
// $plugin->supported = [310, 310];
// $plugin->maturity = MATURITY_STABLE;
