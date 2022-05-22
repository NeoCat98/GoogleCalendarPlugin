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
include('config.php');

defined('MOODLE_INTERNAL') || die();

class event_service
{
    private $GOOGLE_ISSUER_NAME;
    private $scopes = 'https://www.googleapis.com/auth/calendar';
    
    private $issuer;
    private $client;


    function __construct($returnurl,$GOOGLE_ISSUER_NAME){
        $this->setIssuerName($GOOGLE_ISSUER_NAME);
        $this->issuer = \core\oauth2\api::get_issuer($this->getIssuerID());
        $this->client = \core\oauth2\api::get_user_oauth_client($this->issuer, $returnurl , $this->scopes);
    }

    function setIssuerName($GOOGLE_ISSUER_NAME){
        if(empty($GOOGLE_ISSUER_NAME)){
            $this->GOOGLE_ISSUER_NAME = 'Google';
        }else{
            $this->GOOGLE_ISSUER_NAME = $GOOGLE_ISSUER_NAME;
        }
    }

    function getIssuerID(){
        global $DB;
        $GOOGLE_ISSUER_ID = $DB->get_record_sql('SELECT id FROM {oauth2_issuer} WHERE name = :issuername;',['issuername' =>$this->GOOGLE_ISSUER_NAME]);
        return $GOOGLE_ISSUER_ID->id;
    }

    /**
     * returns the current logged in client
     *
     * @return object
     */
    function getClient(){
        return $this->client;
    }
    /**
     * Gets an existing evetn from the database
     *
     * @param object $data
     * @param object $DB
     * @return object
     */
    function getExistingEvent($data,$DB){
        return $DB->get_record_sql('SELECT * FROM {googlecalendar} WHERE course = ? AND assign = ?;',[$data->course,$data->coursemodule]);
    }
    /**
     * Creates new event object to insert into database
     *
     * @param object $newEvent
     * @param object $data
     * @return object
     */
    function createEvent($newEvent,$data){
        //Define Objects
        $newEvent->course = $data->course; //obtain course id
        $newEvent->assign = $data->coursemodule; // assign id 
        $newEvent->checkbox = $data->checkboxGoogleCalendar; // google plugin checkbox value

        return $newEvent;
    }

}