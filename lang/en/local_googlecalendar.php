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
 * @copyright   2022 Javier Mejia, Luis Anstirman, Ricardo Villeda, David Guardado
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Google Calendar Events';
$string['disabled'] = 'Disabled plugin';
$string['issuer'] = 'Google OAuth 2 Service Name';
$string['sendEvent'] = 'Send Events via Google Calendar';
$string['checkMessage'] = 'Activate Google Calendar Events';
$string['sdate'] = 'Start event Date';
$string['edate'] = 'End event Date';
//Validation
$string['msgDateError'] = 'The final date has to be greater than the initial date';
//Notification
$string['msgDeleteEvent'] = 'Google Calendar Event deleted successfully';
$string['msgUpdateEvent'] = 'Google Calendar Event updated successfully';
$string['msgCreateEvent'] = 'Google Calendar Event created successfully';
$string['msgError'] = 'There was an error. Please wait a few minutes before you try again';
