<?php

namespace App\Classes;

use Carbon\Carbon;

use App\Models\EducationLevel;
use App\Models\EducationalData;
use App\Models\EducationalOtherData;
use App\Models\User;

class EducationalDataClass
{
    public function saveEducationalData($request) {
        $userData = $request->user();
        if (!empty($request->user_id)) {
            $userData = User::find($request->user_id);
        }

        $educationLevelData = EducationLevel::find($request->level_id);

        $educationalData = EducationalData::where("user_id", $userData->id)
            ->where("level_id", $request->level_id)
            ->first();
        if (empty($educationalData)) {
            $educationalData = new EducationalData;
        }

        $educationalData->user_id = $userData->id;
        $educationalData->level_id = $request->level_id;
        $educationalData->level_code = $educationLevelData->code;
        $educationalData->course_id = $request->course_id;
        $educationalData->school_name = $request->school_name;
        $educationalData->year_graduated = $request->year_graduated;
        $educationalData->highest_year_reached = $request->highest_year_reached;
        $educationalData->save();

        // $educationalList = $userData->educationalData;

        // EducationalData::where("user_id", $userData->id)->each(function($row){
        //     $row->delete();
        // });

        // foreach ($request->level_id as $key => $value) {
        //     $educationalData = new EducationalData;
        //     $educationalData->user_id = $userData->id;
        //     $educationalData->level_id = $request->level_id[$key];
        //     $educationalData->level_code = !empty($request->level_code[$key]) ? $request->level_code[$key] : "";
        //     $educationalData->course_id = $request->tertiary_course;
        //     $educationalData->school_name = !empty($request->school_name[$key]) ? $request->school_name[$key] : "";
        //     $educationalData->school_address = !empty($request->school_address[$key]) ? $request->school_address[$key] : "";
        //     $educationalData->year_graduated = !empty($request->year_graduated[$key]) ? $request->year_graduated[$key] : "";
        //     $educationalData->highest_year_reached = !empty($request->highest_year_reached[$key]) ? $request->highest_year_reached[$key] : "";
        //     $educationalData->save();
        // }

        // $this->saveEducationalOtherData($request);
    }

    protected function saveEducationalOtherData($request) {
        $userData = $request->user();
        if (!empty($request->user_id)) {
            $userData = User::find($request->user_id);
        }

        $educationalOtherData = $userData->educationalOtherData;
        if (empty($educationalOtherData)) {
            $educationalOtherData = new EducationalOtherData;
            $educationalOtherData->user_id = $userData->id;
        }

        $educationalOtherData->level_id = $request->highest_degree_id;
        $educationalOtherData->course_id = $request->course_id;
        $educationalOtherData->save();
    }
}
