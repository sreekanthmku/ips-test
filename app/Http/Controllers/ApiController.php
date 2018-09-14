<?php

namespace App\Http\Controllers;

use App\Http\Helpers\InfusionsoftHelper;
use Illuminate\Http\Request;
use Response;
use App\Classes\UserCourseModules;
use App\User;

class ApiController extends Controller
{
    // Todo: Module reminder assigner

    private function exampleCustomer(){

        $infusionsoft = new InfusionsoftHelper();

        $uniqid = uniqid();

        $infusionsoft->createContact([
            'Email' => $uniqid.'@test.com',
            "_Products" => 'ipa,iea'
        ]);

        $user = User::create([
            'name' => 'Test ' . $uniqid,
            'email' => $uniqid.'@test.com',
            'password' => bcrypt($uniqid)
        ]);

        // attach IPA M1-3 & M5
        $user->completed_modules()->attach(Module::where('course_key', 'ipa')->limit(3)->get());
        $user->completed_modules()->attach(Module::where('name', 'IPA Module 5')->first());


        return $user;
    }


    // dependency injection is used so that InfusionsoftHelper mock class can be used while testing 
    public function moduleReminderAssigner(Request $request,InfusionsoftHelper $infusionsoft){

        if ($request->isMethod('post')) {
            $email = $request->input('contact_email');

            // check email
            if (isset($email) && !empty($email)) {
                $user = $infusionsoft->getContact($email);
                $courses = explode(',', $user["_Products"]);
                
                if (!empty($courses)) {

                    $usercoursemodules = new UserCourseModules($courses,$email);
                    $tag =  $usercoursemodules->getTagId();
                    $save_tag = $infusionsoft->addTag($user['Id'],$tag);
                    
                    return response()->json([[
                        'success' => true,
                        'message' => 'Tag added successfully'
                    ]]);

                }
                // no courses found
                else{
                    return response()->json([[
                        'success' => false,
                        'message' => 'No courses found'
                    ]]);
                }
            }
            // email empty or not set
            else{
                return response()->json([[
                    'success' => false,
                    'message' => 'Email not set'
                ]]);
            }
        }
        
    }
}
