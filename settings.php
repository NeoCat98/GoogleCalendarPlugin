<?php
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     local_googlecalendar
 * @copyright   2022 Javier Mejia
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) { // Needs this condition or there is error on login page.
    $settings = new admin_settingpage(
        'local_googlecalendar',
        get_string('pluginname','local_googlecalendar')
    );

    $ADMIN->add('localplugins',$settings);

    $settings->add(
        new admin_setting_configcheckbox(
            'local_googlecalendar_remove',
            get_string('disabled','local_googlecalendar'),
            '',
            0
        )
    );
    $settings->add(
        new admin_setting_configtext(
            'local_googlecalendar_issuer',
            get_string('issuer','local_googlecalendar'),
            '',
            'Google'
        )
    );
}