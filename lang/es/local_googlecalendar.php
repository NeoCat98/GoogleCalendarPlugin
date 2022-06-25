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
$string['disabled'] = 'Desactivar plugin';
$string['issuer'] = 'Nombre del Servicio de Google OAuth 2';
$string['sendEvent'] = 'Enviar Evento con Google Calendar';
$string['checkMessage'] = 'Activar Evento Google Calendar';
$string['sdate'] = 'Fecha inicio del Evento';
$string['edate'] = 'Fecha fin del Evento';
//Validation
$string['msgDateError'] = 'La fecha final tiene que ser mayor que la fecha inicial';
//Notification
$string['msgDeleteEvent'] = 'El evento de Google Calendar fue eliminado correctamente';
$string['msgUpdateEvent'] = 'El evento de Google Calendar fue actualizado correctamente';
$string['msgCreateEvent'] = 'El evento de Google Calendar fue creado correctamente';
$string['msgError'] = 'Hubo un error. Espere unos minutos antes de volver a intentarlo';
