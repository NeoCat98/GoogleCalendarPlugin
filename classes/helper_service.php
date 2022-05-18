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

class helper_service
{

    /**
     * Gets the start and end date of an activity
     * 
     * @param string modulename
     * @return $Start_end_dates
     */
    function getStartAndEndDates($modulename,$specialModule = false,$data,$datestart,$dateend){
    

        if($specialModule){
            $datestart->dateTime = gmdate("Y-m-d",$data->startDate).'T'.gmdate("H:i:s.000",$data->startDate).'Z';
            $dateend->dateTime = gmdate("Y-m-d",$data->endDate) .'T'. gmdate("H:i:s.000",$data->endDate).'Z';
        }
        else{
            //Obtain date by module type
            if($modulename == 'assign'){
                 //Obtain the date when the activity start
                $datestart->dateTime = gmdate("Y-m-d",$data->allowsubmissionsfromdate).'T'.gmdate("H:i:s.000",$data->allowsubmissionsfromdate).'Z';
                //Obtain the date when the activity end
                    $dateend->dateTime = gmdate("Y-m-d",$data->duedate) .'T'. gmdate("H:i:s.000",$data->duedate).'Z';
            }
            else if($modulename == 'quiz' or $modulename == 'feedback' or $modulename == 'scorm'){
                //Obtain the date when the activity start
                $datestart->dateTime = gmdate("Y-m-d",$data->timeopen).'T'.gmdate("H:i:s.000",$data->timeopen).'Z';
                //Obtain the date when the activity end
                $dateend->dateTime = gmdate("Y-m-d",$data->timeclose) .'T'. gmdate("H:i:s.000",$data->timeclose).'Z';
            }
            else if($modulename == 'data'){
                //Obtain the date when the activity start
                $datestart->dateTime = gmdate("Y-m-d",$data->timeavailablefrom).'T'.gmdate("H:i:s.000",$data->timeavailablefrom).'Z';
                //Obtain the date when the activity end
                $dateend->dateTime = gmdate("Y-m-d",$data->timeavailableto) .'T'. gmdate("H:i:s.000",$data->timeavailableto).'Z';
            }
            else if($modulename == 'forum'){
                 //Obtain the date when the activity start
                $datestart->dateTime = gmdate("Y-m-d",$data->duedate).'T'.gmdate("H:i:s.000",$data->duedate).'Z';
                //Obtain the date when the activity end
                $dateend->dateTime = gmdate("Y-m-d",$data->cutoffdate) .'T'. gmdate("H:i:s.000",$data->cutoffdate).'Z';
            }
            else if($modulename == 'workshop'){
                //Obtain the date when the activity start
                $datestart->dateTime = gmdate("Y-m-d",$data->submissionstart).'T'.gmdate("H:i:s.000",$data->submissionstart).'Z';
                //Obtain the date when the activity end
                $dateend->dateTime = gmdate("Y-m-d",$data->submissionend) .'T'. gmdate("H:i:s.000",$data->submissionend).'Z';
            }
        }
        
        return ['datestart' => $datestart, 'dateend' => $dateend];
    }

    /**
     * Return whether a module is a regular module
     *
     * @param string $modulename
     * @return boolean
     */
    function isRegularModule($modulename){
        return ($modulename == 'assign' or $modulename == 'quiz' or $modulename == 'feedback' or $modulename == 'data' or 
        $modulename == 'forum' or $modulename =='scorm' or $modulename =='workshop');
    }

    /**
     * Return whether a module is a regular module
     *
     * @param string $modulename
     * @return boolean
     */
    function isSpecialModule($modulename){
        return ($modulename == 'book' or $modulename == 'chat' or $modulename == 'choice' or $modulename == 'lti' or $modulename == 'resource'
        or $modulename == 'folder' or $modulename == 'glossary' or $modulename == 'h5pactivity' or $modulename == 'imscp' or $modulename == 'label'
        or $modulename == 'lesson' or $modulename == 'page' or $modulename == 'survey' or $modulename == 'url' or $modulename == 'wiki' );
    }

    /**
     * checks whether the event reminder checkbox was checked, and the dates are valid
     *
     * @param object $newEvent 
     * @return boolean
     */
    function isCheckedAndDatesValid($newEvent){
        return ($newEvent->checkbox == 1 and $newEvent->end != '1970-01-01T01:01:00.000Z' and $newEvent->start != '1970-01-01T01:01:00.000Z');
    }
    /**
     * checks whether the event reminder checkbox was unchecked, and if the event prevoisuly existed
     *
     * @param object $newEvent 
     * @return boolean
     */
    function isUncheckedAndEventExists($newEvent,$event){
        return ($newEvent->checkbox == 0 and !empty($event->google_event_id));
    }

    /**
     * Returns the email address of an array of enrolled students
     *
     * @param object $context
     * @param object $attendee  
     * @return array
     */
    function getStudentEmails($context,$attendee,$gggfdsfdsfds){
        $submissioncandidates = get_enrolled_users($context, $withcapability = '', $groupid = 0, $userfields = 'u.*', $orderby = '', $limitfrom = 0, $limitnum = 0);
        
        $attendees = [];
        foreach ($submissioncandidates as $d){
            $attendee->email = $d->email;
            array_push($attendees,$attendee);
        }
        return $attendees; 
    }
}