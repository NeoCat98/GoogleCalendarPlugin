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
 * @copyright 2022 Javier Mejia
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_googlecalendar; 

defined('MOODLE_INTERNAL') || die();

class rest extends \core\oauth2\rest {                                                                                              
                                                                                                                                    
    /**                                                                                                                             
     * Define the functions of the rest API.                                                                                        
     *                                                                                                                              
     * @return array Example:                                                                                                       
     *  [ 'listFiles' => [ 'method' => 'get', 'endpoint' => 'http://...', 'args' => [ 'folder' => PARAM_STRING ] ] ]                
     */                                                                                                                             
    function get_api_functions() {                                                                                           
        return [                                                                                                                    
            'insert' => [                                                                                                           
                'endpoint' => 'https://www.googleapis.com/calendar/v3/calendars/primary/events',                                                          
                'method' => 'post',                                                                                                 
                'args' => [                                                                                              
                    'end' => PARAM_RAW,
                    'start'  => PARAM_RAW,  
                    'attendees' =>  PARAM_RAW,
                    'summary' =>  PARAM_RAW                                                                     
                ],                                                                                                                  
                'response' => 'xml'                                                                                                
            ],
            'update' => [                                                                                                           
                'endpoint' => 'https://www.googleapis.com/calendar/v3/calendars/primary/events/{eventId}',                                                          
                'method' => 'put',                                                                                                 
                'args' => [       
                    'eventId' =>  PARAM_RAW,                                                                                       
                    'end' => PARAM_RAW,
                    'start'  => PARAM_RAW, 
                    'attendees' =>  PARAM_RAW,
                    'summary' =>  PARAM_RAW                                                                 
                ],                                                                                                                  
                'response' => 'xml'                                                                                                
            ],
            'delete' => [                                                                                                           
                'endpoint' => 'https://www.googleapis.com/calendar/v3/calendars/primary/events/{eventId}',                                                          
                'method' => 'delete',                                                                                                 
                'args' => [       
                    'eventId' =>  PARAM_RAW                                                                    
                ],                                                                                                                  
                'response' => 'xml'                                                                                                
            ],
            'create' => [                                                                                                           
                'endpoint' => 'https://www.googleapis.com/calendar/v3/calendars',                                                          
                'method' => 'post',                                                                                                 
                'args' => [                                                                                              
                    'summary' => PARAM_RAW,                                                            
                ],                                                                                                                  
                'response' => 'xml'                                                                                                
            ] ,
            'get' => [                                                                                                           
                'endpoint' => 'https://www.googleapis.com/calendar/v3/calendars/primary/events/{eventId}',                                                          
                'method' => 'get',                                                                                                 
                'args' => [                                                                                              
                    'eventId' => PARAM_RAW,                                                                          
                ],                                                                                                                  
                'response' => 'xml'                                                                                                
            ]                                                                                                                   
        ];                                                                                                                          
    }
    
}