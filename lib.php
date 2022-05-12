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
     // Event form for normal modules
    if ($modulename == 'assign' or $modulename == 'quiz' or $modulename == 'feedback' or $modulename == 'data' or 
    $modulename == 'forum' or $modulename =='scorm' or $modulename =='workshop'
    ) {

        $eventCheckbox = 'checkboxGoogleCalendar';

        $mform->addElement('header', 'exampleheader', get_string('sendEvent', 'local_googlecalendar'));
        
        $mform->addElement('advcheckbox', $eventCheckbox, get_string('checkMessage', 'local_googlecalendar'));
        $mform->setType($eventCheckbox, PARAM_BOOL);

        if(empty($user)){
            $mform->setdefault($eventCheckbox, ['checkbydefault']);
        }
        else{
            $mform->setdefault($eventCheckbox, $user->checkbox);
        }


    }
    // Event form for specials modules
    if ($modulename == 'book' or $modulename == 'chat' or $modulename == 'choice' or $modulename == 'lti' or $modulename == 'resource'
    or $modulename == 'folder' or $modulename == 'glossary' or $modulename == 'h5pactivity' or $modulename == 'imscp' or $modulename == 'label'
    or $modulename == 'lesson' or $modulename == 'page' or $modulename == 'survey' or $modulename == 'url' or $modulename == 'wiki') {
        
        // Define the event checbox
        $eventCheckbox = 'checkboxGoogleCalendar';
        // Define the start date of the event
        $startDate = 'startDate';
        // Define the end date of the event
        $endDate = 'endDate';
        // Adding the form elements
        $mform->addElement('header', 'exampleheader', get_string('sendEvent', 'local_googlecalendar'));
        $mform->addElement('advcheckbox', $eventCheckbox, get_string('checkMessage', 'local_googlecalendar'));
        $mform->addElement('date_time_selector', $startDate, get_string('sdate', 'local_googlecalendar'));
        $mform->addElement('date_time_selector', $endDate, get_string('edate', 'local_googlecalendar'));
         // setting dataType
        $mform->setType($eventCheckbox, PARAM_BOOL);

         // Condition 
        $mform->disabledIf($startDate, $eventCheckbox,'notchecked' );
        $mform->disabledIf($endDate, $eventCheckbox,'notchecked' );

        // Setting a default checkBox value 
        if(empty($user)){
            $mform->setdefault($eventCheckbox, ['checkbydefault']);
        }
        else{
            // Get the checkbox value if it exists
            $mform->setdefault($eventCheckbox, $user->checkbox);  
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
    //post form assign/quiz/feedback/data/forum/scrom/workshop module
    if($modulename == 'assign' or $modulename == 'quiz' or $modulename == 'feedback' or $modulename == 'data' or 
    $modulename == 'forum' or $modulename =='scorm' or $modulename =='workshop'){
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

        //Obtain date by module type
        if($modulename == 'assign'){
            //Obtain the date when the activity start
            $datestart->dateTime = gmdate("Y-m-d",$data->allowsubmissionsfromdate).'T'.gmdate("H:i:s.000",$data->allowsubmissionsfromdate).'Z';
            //Obtain the date when the activity end
            $dateend->dateTime = gmdate("Y-m-d",$data->duedate) .'T'. gmdate("H:i:s.000",$data->duedate).'Z';
        }
        if($modulename == 'quiz' or $modulename == 'feedback' or $modulename == 'scorm'){
            //Obtain the date when the activity start
            $datestart->dateTime = gmdate("Y-m-d",$data->timeopen).'T'.gmdate("H:i:s.000",$data->timeopen).'Z';
            //Obtain the date when the activity end
            $dateend->dateTime = gmdate("Y-m-d",$data->timeclose) .'T'. gmdate("H:i:s.000",$data->timeclose).'Z';
       }
       if($modulename == 'data'){
            //Obtain the date when the activity start
            $datestart->dateTime = gmdate("Y-m-d",$data->timeavailablefrom).'T'.gmdate("H:i:s.000",$data->timeavailablefrom).'Z';
            //Obtain the date when the activity end
            $dateend->dateTime = gmdate("Y-m-d",$data->timeavailableto) .'T'. gmdate("H:i:s.000",$data->timeavailableto).'Z';
        }
        if($modulename == 'forum'){
            //Obtain the date when the activity start
            $datestart->dateTime = gmdate("Y-m-d",$data->duedate).'T'.gmdate("H:i:s.000",$data->duedate).'Z';
            //Obtain the date when the activity end
            $dateend->dateTime = gmdate("Y-m-d",$data->cutoffdate) .'T'. gmdate("H:i:s.000",$data->cutoffdate).'Z';
        }
        if($modulename == 'workshop'){
            //Obtain the date when the activity start
            $datestart->dateTime = gmdate("Y-m-d",$data->submissionstart).'T'.gmdate("H:i:s.000",$data->submissionstart).'Z';
            //Obtain the date when the activity end
            $dateend->dateTime = gmdate("Y-m-d",$data->submissionend) .'T'. gmdate("H:i:s.000",$data->submissionend).'Z';
        }
        //Add variables of dateTime to add them in the database
        $newobj->end = $dateend->dateTime;
        $newobj->start = $datestart->dateTime;

        //Obtain the name of the assign
        $summary = $data->name;

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
                //If the google calendat event is new create one otherwise update it
                if(empty($event_id)){
                    $response = $service->call('insert',[],json_encode($SESSION->myvar));
                    $post = json_decode($response);
                    $event_id = $post->id;
                    $newobj->google_event_id = $event_id;
                    $DB->insert_record('googlecalendar',$newobj);
                }else{
                    if($event){
                        $response = $service->call('insert',[],json_encode($SESSION->myvar));
                        $post = json_decode($response);
                        $event_id = $post->id;
                        $newobj->google_event_id = $event_id;
                        $DB->update_record('googlecalendar', $newobj);

                    }else{
                        $functionargs = ['eventId' => $event_id];
                        $service->call('update',$functionargs,json_encode($SESSION->myvar));
                    }
                }
                
            }
        }
        //Cancel google calendar event if it was create
        if($newobj->checkbox == 0 and !empty($event->google_event_id)){
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
                $functionargs = ['eventId' => $event_id];
                $service->call('delete',$functionargs,[]);
                $newobj->google_event_id = null;
                $DB->update_record('googlecalendar', $newobj);
            }
        }  
    }


    //post form book/chat/choise/lti/resource/folder/glossary/h5pactivity/imscp/label/lesson/page/survey/url/wiki module
    if($modulename == 'book' or $modulename == 'chat' or $modulename == 'choice' or $modulename == 'lti' or $modulename == 'resource'
    or $modulename == 'folder' or $modulename == 'glossary' or $modulename == 'h5pactivity' or $modulename == 'imscp' or $modulename == 'label'
    or $modulename == 'lesson' or $modulename == 'page' or $modulename == 'survey' or $modulename == 'url' or $modulename == 'wiki' ){
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
        $datestart->dateTime = gmdate("Y-m-d",$data->startDate).'T'.gmdate("H:i:s.000",$data->startDate).'Z';
        //Obtain the date when the activity end
        $dateend->dateTime = gmdate("Y-m-d",$data->endDate) .'T'. gmdate("H:i:s.000",$data->endDate).'Z';

        //Add variables of dateTime to add them in the database
        $newobj->end = $dateend->dateTime;
        $newobj->start = $datestart->dateTime;

        //Obtain the name of the assign
        $summary = $data->name;

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
                //If the google calendat event is new create one otherwise update it
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
        //Cancel google calendar event if it was create
        if($newobj->checkbox == 0 and !empty($event->google_event_id)){
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
                $functionargs = ['eventId' => $event_id];
                $service->call('delete',$functionargs,[]);
                $newobj->google_event_id = null;
                $DB->update_record('googlecalendar', $newobj);
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