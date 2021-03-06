<?php

namespace App\Classes;

use Carbon\Carbon;

use App\Models\FamilyData;
use App\Models\User;

class FamilyDataClass
{
    public function saveFamilyData($request) {
        $userData = $request->user();
        if (!empty($request->user_id)) {
            $userData = User::find($request->user_id);
        }

        $familyData = FamilyData::where("user_id", $userData->id)
            ->where("family_user_id", $request->family_user_id)
            ->first();
        if (empty($familyData)) {
            $familyData = new FamilyData;
        }

        $familyData->user_id = $userData->id;
        $familyData->family_user_id = $request->family_user_id;
        $familyData->relationship_type_id = $request->relationship_type_id;
        $familyData->save();

        // FamilyData::where("user_id", $userData->id)->each(function($row){
        //     $row->delete();
        // });

        // foreach ($request->relationship_type_id as $key => $value) {
        //     $personalDataID = !empty($request->family_user_id[$key]) ? $request->family_user_id[$key] : 0;
            
        //     $familyData = FamilyData::where("user_id", $userData->id)->where("family_user_id", $personalDataID)->first();
        //     if (empty($familyData)) {
        //         $familyData = new FamilyData;
        //     }

        //     $familyData->user_id = $userData->id;
        //     $familyData->family_user_id = $personalDataID;
        //     $familyData->relationship_type_id = $request->relationship_type_id[$key];
        //     $familyData->first_name = $request->first_name[$key];
        //     $familyData->middle_name = !empty($request->middle_name[$key]) ? $request->middle_name[$key] : "";
        //     $familyData->last_name = $request->last_name[$key];
        //     $familyData->birth_date = date("Y-m-d", strtotime($request->birth_date[$key]));
        //     $familyData->contact_no = $request->contact_no[$key];
        //     $familyData->same_address = !empty($request->same_address[$key]) ? $request->same_address[$key] : 0;
        //     $familyData->address = $request->address[$key];
        //     $familyData->save();
        // }
    }
}
