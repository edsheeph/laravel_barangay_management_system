<?php

namespace App\Classes;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use App\Models\PersonalData;
use App\Models\ProfilePicture;
use App\Models\User;
use App\Models\ResidenceApplication;

use App\Classes\ResidenceApplicationClass;

class PersonalDataClass
{
    public function saveProfile($request) {
        $user = $request->user();
        if (!empty($request->user_id)) {
            $user = User::find($request->user_id);
        }
        
        $profile = $user->profilePicture;
        if (empty($profile)) {
            $profile = new ProfilePicture;
            $profile->user_id = $user->id;
            $user->save();
        }

        if ($request->hasFile('profile')) {
            $path = 'images/profile';

            $image = $request->file('profile');
            $imageName = $image->getClientOriginalName();

            $request->file('profile')->storeAs("public/".$path,$imageName);
            
            $profile->profile_path = $path.'/'.$imageName;
            $profile->profile_name = $imageName;
        }

        $profile->save();

        return $profile;
    }
    
    public function savePersonalData($request) {
        $user = $request->user();
        if (!empty($request->user_id)) {
            $user = User::find($request->user_id);
        }
        
        // $resident = 5;
        $personalData = $user->personalData;

        if (empty($personalData)) {
            $personalData = new PersonalData;
            $personalData->user_id = $user->id;
            $personalData->application_id = 0;
            // $user->user_type_id = $resident;
            $user->email = $request->email;
            $user->save();
        }

        $personalData->last_name = $request->last_name;
        $personalData->first_name = $request->first_name;
        $personalData->middle_name = $request->middle_name;
        $personalData->suffix = $request->suffix;
        $personalData->gender = strtoupper($request->gender);
        $personalData->marital_status_id = $request->marital_status_id;
        $personalData->religious_id = $request->religious_id;
        $personalData->citizenship = $request->citizenship;
        $personalData->citizenship_id = $request->citizenship_id;
        $personalData->birth_date = date("Y-m-d", strtotime($request->birth_date));
        $personalData->birth_place = $request->birth_place;
        $personalData->contact_no = $request->contact_no;
        $personalData->land_line = $request->land_line;
        $personalData->email = $request->email;
        $personalData->additional_contact_no = $request->additional_contact_no;
        $personalData->emergency_contact_no = $request->emergency_contact_no;
        $personalData->save();

        if (empty($personalData->application_id)) {
            $residenceClass = new ResidenceApplicationClass;
            $residenceClass->updateResidenceApplication($request);

            $this->saveApplicationID($personalData);
        }
    }

    protected function saveApplicationID($personalData) {
        $residenceData = ResidenceApplication::where("user_id", $personalData->user_id)->first();
        $applicationID = date("Y") . $residenceData->id;
        $personalData->application_id = $applicationID;
        $personalData->save();
    }
}
