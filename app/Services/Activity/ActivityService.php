<?php
namespace App\Services\Activity;

use App\Models\Activity;
use App\Models\ActivityUser;
use App\Models\User;
use App\Traits\FileUpload;
use App\Traits\Response;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Relations\Concerns\updateExistingPivot;
/**
 *
 */
class ActivityService
{

    use Response;
    use FileUpload;

    public function createGlobalActivity($request)
    {

            $date = Carbon::parse($this->formatDate($request->activity_date, 'd/m/Y', 'Y-m-d'));

            $activity = new Activity();
            $activity->created_by = auth()->user('admin')->id;
            $activity->title = $request->title;
            $activity->description = $request->description;
            $activity->activity_date =  $date;
            $activity->image_url = isset($request->image) ?  $this->uploadFileTocloudinary($request->image) : null;
            $activity->activity_type = 'global';
            $activity->save();

           if($activity){
            $this->addUsersToActivity($activity);
           }

         return $activity;
       
    }

    public function addUsersToActivity($activity)
    {
            $users = User::all();

            if(count($users) >=1){
                foreach ($users as $user) {
                 DB::table('activity_users')->insert(
                [
                    'user_id' => $user->id,
                    'activity_id' => $activity->id,
                    'title' => $activity->title,
                    'description' => $activity->description,
                    'activity_date' => $activity->activity_date,
                    'image_url' => $activity->image_url,
                    'activity_type' => $activity->activity_type,
                ]
            );

            }
    }
    }

    public function createIndividualActivity($request)
    {

            $date = Carbon::parse($this->formatDate($request->activity_date, 'd/m/Y', 'Y-m-d'));

            $activity = new Activity();
            $activity->created_by = auth()->user('admin')->id;
            $activity->title = $request->title;
            $activity->description = $request->description;
            $activity->activity_date =  $date;
            $activity->image_url = isset($request->image) ?  $this->uploadFileTocloudinary($request->image) : null;
            $activity->activity_type = 'individual';
            $activity->save();

           if($activity){
            DB::table('activity_users')->insert(
                [
                    'user_id' => $request->user_id,
                    'activity_id' => $activity->id,
                    'title' => $activity->title,
                    'description' => $activity->description,
                    'activity_date' => $activity->activity_date,
                    'image_url' => $activity->image_url,
                    'activity_type' => $activity->activity_type,
                ]
            );
           }

         return $activity;
       
    }

     public function updateGlobalActivity($request)
    {

            $date = Carbon::parse($this->formatDate($request->activity_date, 'd/m/Y', 'Y-m-d'));

            $activity = Activity::where([
                ['id', $request->activity_id],
                ['activity_type', 'global']
            ])->first();

            if($activity){
            $activity->title = $request->title;
            $activity->description = $request->description;
            $activity->activity_date =  $date;
            $activity->image_url = isset($request->image) ?  $this->uploadFileTocloudinary($request->image) : $activity->image_url;
            $activity->save();

           if($activity){
          $this->updateUserActivitiesGlobally($activity);
           }
        }

         return $activity;
       
    }

     public function updateUserActivitiesGlobally($activity)
    {
            $userActivities = DB::table('activity_users')->where([
                ['activity_id', $activity->id],
                ['activity_type', 'global']
            ])->get();


                 $attributes =
                [
                    'title' => $activity->title,
                    'description' => $activity->description,
                    'activity_date' => $activity->activity_date,
                    'image_url' => $activity->image_url,
                    'activity_type' => $activity->activity_type,
                ];
           

            if(count($userActivities) >=1){
                foreach ($userActivities as $activity) {
                    
                $user = User::find($activity->user_id);

                $user->activities()->updateExistingPivot($activity->activity_id, $attributes);
            
            }
    }
    }

        public function updateIndividualUserActivity($request)
    {

            $date = Carbon::parse($this->formatDate($request->activity_date, 'd/m/Y', 'Y-m-d'));
           

              $activity = DB::table('activity_users')->where([
                ['activity_id', $request->activity_id],
                ['user_id', $request->user_id],
            ])->first();

            if($activity){

                 $attributes =
                [
                    'title' => $request->title,
                    'description' => $request->description,
                    'activity_date' => $date,
                    'image_url' => isset($request->image) ?  $this->uploadFileTocloudinary($request->image) : $activity->image_url,
                ];

            $user = User::find($activity->user_id);
            $attributes['activity_type'] = 'individual';
            $user->activities()->updateExistingPivot($activity->activity_id, $attributes);

            $theActivity = Activity::where('id', $activity->activity_id)->first();

            if($theActivity && $theActivity->activity_type == 'individual'){

                $theActivity->update($attributes);
            }
        }

         return $activity;
       
    }

    public function myActivities($request)
    {
       // $user = User::where('id', auth()->user('user')->id)->first();
          $from = Carbon::parse($this->formatDate($request->from, 'd/m/Y', 'Y-m-d'));
          $to = Carbon::parse($this->formatDate($request->to, 'd/m/Y', 'Y-m-d'));

          $userActivities = DB::table('activity_users')->where([
                ['user_id', auth()->user('user')->id],
            ])->whereBetween('activity_date',  [$from, $to])->orderBy('id','desc')->get();

          return $userActivities;

    }

    public function validateActivityDate($date)
    {
          $getdate = Carbon::parse($this->formatDate($date, 'd/m/Y', 'Y-m-d'));

       $activities = Activity::where('activity_date', $getdate)->get();

        return $activities;
    }


    public function formatDate($date, $oldFormat, $newFormat)
    {
        return Carbon::createFromFormat($oldFormat, $date)->format($newFormat);
    }

     public function getAllActivities()
    {

       $activities = Activity::orderBy('created_at','desc')->get();
       
        return $activities;
    }

     public function getOneActivity($activityId)
    {

       $activity = Activity::where('id', $activityId)->first();
       
        return $activity;
    }

     public function deleteActivity($activityId)
    {

       $activity = Activity::where('id', $activityId)->first();

       if($activity){
        $activity->delete();
       }
       
        return $activity;
    }
}
