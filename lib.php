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

defined('MOODLE_INTERNAL') || die();

/**
 * @param moodleform $formwrapper The moodle quickforms wrapper object.
 * @param MoodleQuickForm $mform The actual form object (required to modify the form).
 * https://docs.moodle.org/dev/Callbacks
 * This function name depends on which plugin is implementing it. So if you were
 * implementing mod_wordsquare
 * This function would be called wordsquare_coursemodule_standard_elements
 * (the mod is assumed for course activities)
 */
function local_googlecalendar_coursemodule_standard_elements($formwrapper, $mform) {
    GLOBAL $DB;
    $moduleid = $formwrapper->get_current()->coursemodule;
    $courseid = $formwrapper->get_current()->course;
    $modulename = $formwrapper->get_current()->modulename;
    
    $user = $DB->get_record_sql('SELECT checkbox FROM {googlecalendar} WHERE course = ? AND assign = ?;',[$courseid,$moduleid]);
    
    if ($modulename == 'assign') {

        $elementname1 = 'checkboxGoogleCalendar';

        $mform->addElement('header', 'exampleheader', get_string('message1', 'local_googlecalendar'));
        
        $mform->addElement('advcheckbox', $elementname1, get_string('message1', 'local_googlecalendar'));
        $mform->setType($elementname1, PARAM_BOOL);

        if(empty($user)){
            $mform->setdefault($elementname1, ['checkbydefault']);
        }
        else{
            $mform->setdefault($elementname1, $user->checkbox);
        }

    }
    
}

/**
 * Process data from submitted form
 *
 * @param stdClass $data
 * @param stdClass $course
 * @return $data
 */
function local_googlecalendar_coursemodule_edit_post_actions($data, $course) {
    GLOBAL $DB,$SESSION;
    $modulename = $data->modulename;
    if($modulename == 'assign'){
        $context = context_course::instance($data->course);
        //Find if the assign is already created
        $event = $DB->get_record_sql('SELECT * FROM {googlecalendar} WHERE course = ? AND assign = ?;',[$data->course,$data->coursemodule]);
        //Define Objects
        $newobj = new stdClass();
        $dateend = new stdClass();
        $datestart = new stdClass();
        //Obtain the course id
        $newobj->course = $data->course;
        //Obtain the assign id
        $newobj->assign = $data->coursemodule;
        //Obtaining value of the Google Calendar Form
        $newobj->checkbox = $data->checkboxGoogleCalendar;
        //Obtain the date when the activity start
        $datestart->dateTime = gmdate("Y-m-d",$data->allowsubmissionsfromdate).'T'.gmdate("H:m:s.000",$data->allowsubmissionsfromdate).'Z';
        //Obtain the name of the assign
        $summary = $data->name;
        //Obtain the date when the activity end
        $dateend->dateTime = gmdate("Y-m-d",$data->duedate) .'T'. gmdate("H:m:s.000",$data->duedate).'Z';
        //Add variables of dateTime to add them in the database
        $newobj->end = $dateend->dateTime;
        $newobj->start = $datestart->dateTime;
        //If the assign is already created just update, in other case insert the new assign
        if(!empty($event->id)){  
            $newobj->id = $event->id;
            $newobj->google_event_id = $event->google_event_id;
            $event_id = $newobj->google_event_id;
            $DB->update_record('googlecalendar', $newobj);
        }
        //Check if the app need to send reminders and It's enable start and end date
        if($newobj->checkbox == 1 and $newobj->end != '1970-01-01T01:01:00.000Z' and $newobj->start != '1970-01-01T01:01:00.000Z'){
            //Get all users in the course
            $submissioncandidates = get_enrolled_users($context, $withcapability = '', $groupid = 0, $userfields = 'u.*', $orderby = '', $limitfrom = 0, $limitnum = 0);
            //Save each users email in array attendees 
            $attendees = [];
            foreach ($submissioncandidates as $d){
                $attendee = new stdClass();
                $attendee->email = $d->email;
                array_push($attendees,$attendee);
            } 
            // Call API
            // Get an issuer from the id
            $issuer = \core\oauth2\api::get_issuer(1);
            // Put in the returnurl the course id and sesskey
            $sesskey = sesskey();   
            $params = array('id' => $data->course, 'sesskey' => $sesskey);
            // Get an OAuth client from the issuer
            $returnurl  = new moodle_url('/course/view.php',$params);
            // Add all scopes for the API
            $scopes = 'https://www.googleapis.com/auth/calendar';
            $client = \core\oauth2\api::get_user_oauth_client($issuer, $returnurl , $scopes);
            // Check the google session
            if (!$client->is_logged_in()) {
                redirect($client->get_login_url());
            }
            else{   
                $service = new \local_googlecalendar\rest($client);
                $params = [
                    'end' => $dateend,
                    'summary' => $summary,
                    'start' => $datestart,
                    'attendees' => $attendees
                ]; 
                $SESSION->myvar = $params;
                if(empty($event_id)){
                    $response = $service->call('insert',[],json_encode($SESSION->myvar));
                    $post = json_decode($response);
                    $event_id = $post->id;
                    $newobj->google_event_id = $event_id;
                    $DB->insert_record('googlecalendar',$newobj);
                }else{
                    $functionargs = ['eventId' => $event_id];
                    $service->call('update',$functionargs,json_encode($SESSION->myvar));
                }
                
            }
        }  
    } 
    return $data;
}


/**
 * Validate the data in the new field when the form is submitted
 *
 * @param moodleform_mod $fromform
 * @param array $fields
 * @return void
 */
function local_googlecalendar_coursemodule_validation($fromform, $fields) {
    if (get_class($fromform) == 'mod_assign_mod_form') {
       // \core\notification::add($fields[1], \core\notification::INFO);
    }
}