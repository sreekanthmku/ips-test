<?php
namespace App\Classes;

use App\User;
use App\Tag;
use Exception;
use Log;
use Illuminate\Support\Facades\DB;


class UserCourseModules
{
/**
* @var array
*/
    protected $courses = [];
    protected $email;
    protected $order;


/**
* Construct the usercoursemodule with the given email, courses, order.
*
* @param array $items
*/
    public function __construct($courses = [], $email)
    {
        $this->courses = $courses;
        $this->email= $email;
        $this->order = $this->getModulesOrder();
    }


/**
* Get the modules in order as per the order C1M7,C1M6...C1M1,C2M7,C2M6..
*
* @return array
*/
    protected function getModulesOrder(){
        
        $order = [];
        foreach ($this->courses as $course) {
            
            for ($i = 7; $i >= 1; $i--){
                
                $order[] = strtoupper($course)." Module ".$i;

            }
        }
        return $order;
    }


/**
* Get modules completed by the user  in order. (C1M7,C1M6...C1M1,C2M7,C2M6..)
*
* @return array
*/
    protected function getCompletedModulesInOrder(){
        
        $user = User::whereEmail($this->email)->first();
        
        if(!empty($user)){

            $order_string = implode ("\",\"", $this->order);
            // get completed courses from database in order
            $completed_modules_in_order = $user->completed_modules_by_order($order_string)
                ->pluck('name')->toArray();

            return $completed_modules_in_order;      
        }
           
    }



/**
* Returns array in with all the modules in the order C1M7,C1M6...C1M1,C2M7,C2M6.. with completed modules values as "completed"
*
* @return array
*/
    protected function markCompletedModules(){
        
        $order = $this->order;
        $completed_modules_in_order = $this->getCompletedModulesInOrder();
        
        // if module completed, change it's value to "completed"
        foreach ($order as &$module) {
            
            if (in_array($module, $completed_modules_in_order)){
              
              $module = 'completed';
            
            }         
        }
        return $order;
    }



/**
* Return marked array as a multidimentional array with arrays of each courses modules
*
* @return array
*/
    protected function getModulesAsArray($marked_array){
        
        $no_of_courses = $this->getNumberOfCourses();

        // Create multidimentional array. can use array_chunk since modules are grouped by courses
        $modules_as_array = array_chunk($marked_array, ceil(count($marked_array) / $no_of_courses));
        
        return $modules_as_array;
    }



/**
* Return number of courses of the user
*
* @return string
*/
    protected function getNumberOfCourses(){
        return count($this->courses);
    }



/**
* Checks whether last modules of each courses is complete. 
*
* @return bool
*/
    protected function isCompleted($mod_array){

        $final_modules = array();
        $i = 0;

        // get array of module 7 of each course
        foreach($mod_array as $module) {
            
            if ($i % 7 === 0) {
                $final_modules[] = $module;
            }
            
            $i++;
        }

        // check if all course module 7 is completed
        if (count(array_unique($final_modules)) === 1 && end($final_modules) === 'completed') {
            return true;
        }
        else{
            return false;
        }

    }



/**
* Get the next module for which reminder is to be sent.returns "completed" if all modules are completed
*
* @return string
*/ 
    public function getNextModule(){
        
        $marked_array = $this->markCompletedModules();
        
        // check whether completed 
        $modules_as_array = $this->getModulesAsArray($marked_array);
        if ($this->isCompleted($marked_array) == true) {
            $next_module = 'completed';
        }
        // Not complete. findout module 
        else{

            foreach ($modules_as_array as $course) {

                $next_module = array_values(array_slice($course, -1))[0];
                $incomplete_count = 0;

                for ($i=0; $i < 7; $i++) { 

                    if ($course[$i] == 'completed') {
                        // check whether module 7 is completed. Modules are arranged such that 7 th module is at 0 th position. 
                        if ($i == 0) {
                            break ;
                        }
                        else{
                            $next_module = $course[$i-1];
                            break 2;
                        }
                    }
                    else{
                        // See whether incomplete modules are present in an array so that foreach loop has to be terminated
                        $incomplete_count++;
                    }
                }
                if ($incomplete_count > 0) {
                    break;
                }
            }
        }
        
        return $next_module;       
    }



/**
* Returns the tag id to be sent to  Infusionsoft server.
*
* @return string
*/ 
    public function getTagId(){
        
        $next_module = $this->getNextModule();
        $tag = DB::table('tags')
                ->where('name', 'like', '%' . $next_module . '%')
                ->first();
        
        return $tag->id;
    }

}