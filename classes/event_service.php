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
 * Manage course custom fields
 *
 * @package local_googlecalendar
 * @copyright 2022 Fernando Anstirman
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_googlecalendar;

require_once($CFG->libdir . '/filelib.php');
defined('MOODLE_INTERNAL') || die();

class event_service
{
    private $GOOGLE_ISSUER_ID = 1;
    private $scopes = 'https://www.googleapis.com/auth/calendar';
    
    private $issuer;
    private $params;
    private $returnurl;
    private $client;


    function __construct($data,$sesskey){
        $this->issuer = \core\oauth2\api::get_issuer($GOOGLE_ISSUER_ID);
        $this->params = array('id' => $data->course, 'sesskey' => $sesskey);
        $this->returnurl = new moodle_url('/course/view.php',$params);

        $this->client = \core\oauth2\api::get_user_oauth_client($issuer, $returnurl , $scopes);
    }
    function getClient(){
        return $this->client;
    }
    function getExistingEvent($data){
        return $DB->get_record_sql('SELECT * FROM {googlecalendar} WHERE course = ? AND assign = ?;',[$data->course,$data->coursemodule]);
    }
    function createEvent($modulename,$data){
        //Define Objects
        $newEvent = new stdClass();
        $newEvent->course = $data->course; //obtain course id
        $newEvent->assign = $data->coursemodule; // assign id 
        $newEvent->checkbox = $data->checkboxGoogleCalendar; // google plugin checkbox value

        return $newEvent;
    }

}