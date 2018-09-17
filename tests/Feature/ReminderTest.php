<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Http\Helpers\InfusionsoftHelper;



class ReminderTest extends TestCase
{
    /**
     * Tests the reminder api endpoint - api/module_reminder_assigner.
     *
     * @return void
     */

    public function testSendReminderTest()
    {
        // replace the InfusionsoftHelper class in the container with a mock with dependency injection
        $this->app->bind(InfusionsoftHelper::class, function ($app) {
            $id = '12345@test.com';
            
            $mock = $this->createMock(InfusionsoftHelper::class);
            
            $mock->method('getContact')->with($this->identicalTo($id))->willReturn([
                            "Email" => "123457@test.com",
                            "_Products" => "ipa,iea",
                            "Id" => 3973
                        ]);
            $mock->method('addTag')->with($this->greaterThan(0),$this->greaterThan(0))->willReturn([[
                                "success"=> true, "message" => "User created successfully"
                            ]]);
            $mock->method('getAllTags')->willReturn([1,2,3,4.3]);
            
            return $mock;      
        });
           
        \App::Make('App\Http\Helpers\InfusionsoftHelper');
        
        $response = $this->json('POST', 'api/module_reminder_assigner', ['contact_email' => '12345@test.com']);
        // varify the response and status code of the request
        $response
        ->assertStatus(200)
        ->assertJson([[
            'success' => true,
            'message' => 'Tag added successfully'
        ]]);
    }

}
