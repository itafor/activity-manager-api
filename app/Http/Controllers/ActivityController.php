<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use App\Models\User;
use App\Services\Activity\ActivityService;
use App\Traits\Response;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
         use Response;

    public $activityService;

	public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

     public function createGlobalActivity(CreateActivityRequest $request)
    {
        try {

             if(isset($request->image)){
                $request->validate([
                "image"=> "required|file|mimes:jpg,jpeg,png|max:5240",
            ]);
            }

           $activitiesPerDate = $this->activityService->validateActivityDate($request->activity_date);

           if(count($activitiesPerDate) == 4)
           {
            return $this->fail(true, "You can only add 4 activities for each day!", "");
            
           }

            
        	$Activity = $this->activityService->createGlobalActivity($request);

            return $this->success(false, "Activity successfully created!", $Activity);

        } catch (Exception $e) {
        	$error = $e->getMessage();
            return $this->fail(true, "Couldn't create activity! $error", "");
        }
    }

     public function createIndividualActivity(CreateActivityRequest $request)
    {
        try {

        	 if(!isset($request->user_id)){
               return $this->fail(true, "The user id is required!", "");
            }

            $user = User::where('id', $request->user_id)->first();

            if(!$user){
            return $this->fail(true, "User not found!", "");
            }

             $activitiesPerDate = $this->activityService->validateActivityDate($request->activity_date);

           if(count($activitiesPerDate) == 4)
           {
            return $this->fail(true, "You can only add a maximum of 4 activities for each day!", "");
            
           }

             if(isset($request->image)){
                $request->validate([
                "image"=> "required|file|mimes:jpg,jpeg,png|max:5240",
            ]);
            }

            
            
        	$Activity = $this->activityService->createIndividualActivity($request);

            return $this->success(false, "Activity successfully created!", $Activity);

        } catch (Exception $e) {
        	$error = $e->getMessage();
            return $this->fail(true, "Couldn't create activity! $error", "");
        }
    }

      public function updateGlobalActivity(UpdateActivityRequest $request)
    {
        try {

             if(isset($request->image)){
                $request->validate([
                "image"=> "required|file|mimes:jpg,jpeg,png|max:5240",
            ]);
            }
            
        	$Activity = $this->activityService->updateGlobalActivity($request);

            return $this->success(false, "Activity successfully updated!", $Activity);

        } catch (Exception $e) {
        	$error = $e->getMessage();
            return $this->fail(true, "Couldn't update activity! $error", "");
        }
    }

     public function updateIndividualUserActivity(UpdateActivityRequest $request)
    {
        try {

             if(isset($request->image)){
                $request->validate([
                "image"=> "required|file|mimes:jpg,jpeg,png|max:5240",
            ]);
            }
            
             if(!isset($request->user_id)){
               return $this->fail(true, "The user id is required!", "");
            }

            $user = User::where('id', $request->user_id)->first();

            if(!$user){
            return $this->fail(true, "User not found!", "");
            }
            
            $activity = $this->activityService->updateIndividualUserActivity($request);
            if($activity){
            return $this->success(false, "Activity successfully updated!", $activity);
            }
            return $this->fail(true, "Activity not found!", "");

        } catch (Exception $e) {
            $error = $e->getMessage();
            return $this->fail(true, "Couldn't update activity! $error", "");
        }
    }

        public function myActivities(Request $request)
    {
        try {

            $validated = $request->validate([
                "from"=> "required|string",
                "to"=> "required|string",
            ]);

            
        	$activities = $this->activityService->myActivities($request);

            return $this->success(false, "My activities!", $activities);

        } catch (Exception $e) {
        	$error = $e->getMessage();
            return $this->fail(true, "Couldn't fetch activities! $error", "");
        }
    }

       public function getAllActivities()
    {
        try {
            
        	$activities = $this->activityService->getAllActivities();

            return $this->success(false, "All activities!", $activities);

        } catch (Exception $e) {
        	$error = $e->getMessage();
            return $this->fail(true, "Couldn't fetch activities! $error", "");
        }
    }


    public function getOneActivity($activityId)
    {
        try {
            
        	$activity = $this->activityService->getOneActivity($activityId);

            return $this->success(false, "Activity!", $activity);

        } catch (Exception $e) {
        	$error = $e->getMessage();
            return $this->fail(true, "Couldn't fetch activity! $error", "");
        }
    }


    public function deleteActivity($activityId)
    {
        try {
            
        	 $activity = Activity::where('id', $activityId)->first();


       if($activity){
        $activity->delete();
            return $this->success(false, "Activity deleted successfully!", "");
       }

            return $this->success(false, "Activity not found!", "");

        } catch (Exception $e) {
        	$error = $e->getMessage();
            return $this->fail(true, "Couldn't delete activity! $error", "");
        }
    }

}
