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
    
    $user = $DB->get_record_sql('SELECT * FROM {googlecalendar} WHERE course = ? AND assign = ?;',[$courseid,$moduleid]);
     
    // Event form for normal modules
    if ($modulename == 'assign' or $modulename == 'quiz' or $modulename == 'feedback' or $modulename == 'data' or 
    $modulename == 'forum' or $modulename =='scorm' or $modulename =='workshop'
    ) {
        // Define the event checbox
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
            $mform->setdefault($startDate, strtotime($user->start));  
            $mform->setdefault($endDate, strtotime($user->end));    
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
    
    //GLOBAL VARIABLES
    GLOBAL $DB;
    $modulename = $data->modulename;
    $context = context_course::instance($data->course);
    $sesskey = sesskey();
    
    $datestart = new stdClass();
    $dateend = new stdClass();
    $newEvent = new stdClass();
    
    $params = array('id' => $data->course, 'sesskey' => $sesskey);
    $returnurl = new moodle_url('/course/view.php',$params);

    $event_service = new \local_googlecalendar\event_service($returnurl); //EVENT SERVICE
    $module_helper = new \local_googlecalendar\helper_service(); //HELPER SERVICE

    //Find if the assignment already exists
    $event = $event_service->getExistingEvent($data,$DB);

    $client = $event_service->getClient();
    
    if (!$client->is_logged_in()){
        redirect($client->get_login_url());
    }else{

        //Obtain the name of the assignment
        $summary = $data->name;

        //Creates new Event depending on module type
        if($module_helper->isRegularModule($modulename)){
            
            //Get start and end times of the assignment
            $start_end_dates = $module_helper->getStartAndEndDates($modulename,false,$data,$datestart,$dateend);
            $datestart = $start_end_dates['datestart'];
            $dateend = $start_end_dates['dateend'];

            //Create new event to insert into DB
            $newEvent = $event_service->createEvent($newEvent,$data);
            $newEvent->start = $datestart->dateTime;
            $newEvent->end = $dateend->dateTime;


        }else if($module_helper->isSpecialModule($modulename)){

            //Get start and end times of the assignment
            $start_end_dates = $module_helper->getStartAndEndDates($modulename,true,$data,$datestart,$dateend);
            $datestart = $start_end_dates['datestart'];
            $dateend = $start_end_dates['dateend'];
            
            //Create new event to insert into DB
            $newEvent = $event_service->createEvent($newEvent,$data);
            $newEvent->start = $datestart->dateTime;
            $newEvent->end = $dateend->dateTime;

        }
        //Validates whether checkbox is activated
        if($module_helper->isCheckedAndDatesValid($newEvent)){

            //Get all users in the course
            $submissioncandidates = get_enrolled_users($context, $withcapability = '', $groupid = 0, $userfields = 'u.*', $orderby = '', $limitfrom = 0, $limitnum = 0);
            $attendees = [];
            foreach ($submissioncandidates as $d){
                $attendee = new stdClass();
                $attendee->email = $d->email;
                array_push($attendees,$attendee);
            }

            //CALL GOOGLE API
            $service = new \local_googlecalendar\rest($client);
            $params = ['end' => $dateend,'summary' => $summary,'start' => $datestart,'attendees' => $attendees];
            $event_id = $event->google_event_id;
            
            //If the google calendat event is new create one otherwise update it
            if(empty($event_id)){
                $response = $service->call('insert',[],json_encode($params));
                $JSON_response = json_decode($response);
                
                $event_id = $JSON_response->id;
                $newEvent->google_event_id = $event_id;
                
                if($event){
                    $DB->update_record('googlecalendar', $newEvent);
                }else{
                    $DB->insert_record('googlecalendar',$newEvent);
                }
            }else{
                //Updates the database
                $newEvent->id = $event->id;
                $newEvent->google_event_id = $event->google_event_id;
                $DB->update_record('googlecalendar', $newEvent);

                //Updates API
                $functionargs = ['eventId' => $event_id];
                $service->call('update',$functionargs,json_encode($params));
            }



        }else if($module_helper->isUncheckedAndEventExists($newEvent,$event)){

            $service = new \local_googlecalendar\rest($client);
            $functionargs = ['eventId' => $event->google_event_id];
            
            //DELETES EVENT FROM GOOGLE
            $service->call('delete',$functionargs,[]);
            $newEvent->google_event_id = null;

            //UPDATES DATABASE
            $DB->update_record('googlecalendar', $newEvent);

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