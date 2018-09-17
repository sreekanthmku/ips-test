<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Mockery;



class DecisionLogic extends TestCase
{
    /**
     * Tests the edge cases of module completion.
     * @group module
     * @return void
     */

    // case 1- IPA module 1 and IPA module 2 completed. Expecting output IPA module 3
    public function testsDecisionLogicCase1(){
        $mock = Mockery::mock('App\Classes\UserCourseModules[getCompletedModulesInOrder]',array(['ipa', 'iea'], '12345@test.com'))->shouldAllowMockingProtectedMethods();

        $mock->shouldReceive('getCompletedModulesInOrder')->andReturn(['IPA Module 1', 'IPA Module 2']);
        $this->assertEquals('IPA Module 3', $mock->getNextModule()); 
    }


    // case 2- IPA module 1 and IPA module 7 completed. Expecting output IEA module 1
    public function testsDecisionLogicCase2(){
        $mock = Mockery::mock('App\Classes\UserCourseModules[getCompletedModulesInOrder]',array(['ipa', 'iea'], '12345@test.com'))->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('getCompletedModulesInOrder')->andReturn(['IPA Module 1', 'IPA Module 7']);
        $this->assertEquals('IEA Module 1', $mock->getNextModule()); 
    }


    // case 3- IPA module 7 and IEA module 7 completed. Expecting output 'completed'
    public function testsDecisionLogicCase3(){
        $mock = Mockery::mock('App\Classes\UserCourseModules[getCompletedModulesInOrder]',array(['ipa', 'iea'], '12345@test.com'))->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('getCompletedModulesInOrder')->andReturn(['IPA Module 7', 'IEA Module 7']);
        $this->assertEquals('completed', $mock->getNextModule()); 
    }


    // case 4- IPA module 1 and IPA module 4 completed. Expecting output IPA module 5
    public function testsDecisionLogicCase4(){
        $mock = Mockery::mock('App\Classes\UserCourseModules[getCompletedModulesInOrder]',array(['ipa', 'iea'], '12345@test.com'))->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('getCompletedModulesInOrder')->andReturn(['IPA Module 1', 'IPA Module 4']);
        $this->assertEquals('IPA Module 5', $mock->getNextModule()); 
    }


    // case 5- IPA module 3 and IEA module 1 completed. Expecting output IPA Module 4
    public function testsDecisionLogicCase5(){
        $mock = Mockery::mock('App\Classes\UserCourseModules[getCompletedModulesInOrder]',array(['ipa', 'iea'], '12345@test.com'))->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('getCompletedModulesInOrder')->andReturn(['IPA Module 3', 'IEA Module 1']);
        $this->assertEquals('IPA Module 4', $mock->getNextModule()); 
    }

}
