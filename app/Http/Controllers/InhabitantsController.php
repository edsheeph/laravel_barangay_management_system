<?php

namespace App\Http\Controllers;

use Throwable;
use Helpers;
use Session;
use Validator;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\PersonalData;
use App\Models\User;

class InhabitantsController extends Controller
{
    public function getInhabitantsList(Request $request) {
        try {
            $peronalDataList = PersonalData::select(
                'personal_data.id',
                'personal_data.user_id',
                'personal_data.application_id',
                'personal_data.first_name',
                'personal_data.middle_name',
                'personal_data.last_name',
                'users.barangay_id',
                'barangays.description as barangay_desc',
                'personal_data.birth_date',
                'users.user_type_id',
                'residence_application.status_id',
                'residence_status.description as status_desc',
                'personal_data.created_at as date_requested'
            )
            ->join("users", "users.id", "personal_data.user_id")
            ->join("barangays", "barangays.id", "users.barangay_id")
            ->join("residence_application", "residence_application.user_id", "users.id")
            ->join("residence_status", "residence_status.id", "residence_application.status_id");
    
            if ($request->search) {
                $peronalDataList = $peronalDataList->where(function($q) use($request){
                    $q->orWhereRaw("personal_data.application_id LIKE ?","%".$request->search."%");
                    $q->orWhereRaw("CONCAT_WS(' ',CONCAT(personal_data.last_name,','),personal_data.first_name,personal_data.first_name) LIKE ?","%".$request->search."%");
                });
            }
    
            if ($request->barangay_id) {
                $peronalDataList = $peronalDataList->where("users.barangay_id", $request->barangay_id);
            }
    
            if (!empty($request->sort) && !empty($request->order)) {
                $peronalDataList = $peronalDataList->orderBy("personal_data.".$request->sort, strtoupper($request->order));
            } else {
                $peronalDataList = $peronalDataList->orderBy("residence_application.status_id", "DESC");
                $peronalDataList = $peronalDataList->orderBy("personal_data.id", "DESC");
            }
    
            $peronalDataList = $peronalDataList->paginate(
                (int) $request->get('per_page', 10),
                ['*'],
                'page',
                (int) $request->get('page', 1)
            );
    
            return customResponse()
                ->message("List of applicants.")
                ->data($peronalDataList)
                ->success()
                ->generate();
        } catch (\Throwable $th) {
            return customResponse()
                ->message("Sorry, Something went wrong.")
                ->data(null)
                ->success()
                ->generate();
        }
    }

    public function getResidenceList(Request $request) {
        try {
            $resident = 5;
            $peronalDataList = PersonalData::select(
                'personal_data.id',
                'personal_data.user_id',
                'personal_data.application_id',
                'personal_data.resident_id',
                'personal_data.first_name',
                'personal_data.middle_name',
                'personal_data.last_name',
                'personal_data.emergency_contact_no',
                'users.barangay_id',
                'barangays.description as barangay_desc',
                'personal_data.birth_date',
                'users.user_type_id',
                'residence_application.status_id',
                'residence_status.description as status_desc',
                'personal_data.created_at as date_requested',
                'barangay_id_generated.date_printed',
                'barangay_id_generated.status'
            )
            ->join("users", "users.id", "personal_data.user_id")
            ->join("barangays", "barangays.id", "users.barangay_id")
            ->join("residence_application", "residence_application.user_id", "users.id")
            ->join("residence_status", "residence_status.id", "residence_application.status_id")
            ->leftJoin("barangay_id_generated", "barangay_id_generated.user_id", "users.id")
            ->where("users.user_type_id", $resident);

            if ($request->search) {
                $peronalDataList = $peronalDataList->where(function($q) use($request){
                    $q->orWhereRaw("personal_data.application_id LIKE ?","%".$request->search."%");
                    $q->orWhereRaw("CONCAT_WS(' ',CONCAT(personal_data.last_name,','),personal_data.first_name,personal_data.first_name) LIKE ?","%".$request->search."%");
                });
            }

            if ($request->barangay_id) {
                $peronalDataList = $peronalDataList->where("users.barangay_id", $request->barangay_id);
            }

            // if (!empty($request->sort) && !empty($request->order)) {
            //     $peronalDataList = $peronalDataList->orderBy("personal_data.".strtolower($request->sort), strtoupper($request->order));
            // } else {
            //     $peronalDataList = $peronalDataList->orderBy("residence_application.status_id", "DESC");
            //     $peronalDataList = $peronalDataList->orderBy("personal_data.id", "DESC");
            // }

            if (!empty($request->sort) && !empty($request->order)) {
                if ($request->sort=='last_name' || $request->sort=='first_name') {
                    $peronalDataList = $peronalDataList->orderBy("personal_data.".$request->sort, strtoupper($request->order));
                }
                if ($request->sort=='resident_id') {
                    $peronalDataList = $peronalDataList->orderBy("personal_data.".$request->sort, strtoupper($request->order));
                }
                if ($request->sort=='barangay_desc') {
                    $peronalDataList = $peronalDataList->orderBy("barangays.description", strtoupper($request->order));
                }
            }

            $peronalDataList = $peronalDataList->paginate(
                (int) $request->get('per_page', 10),
                ['*'],
                'page',
                (int) $request->get('page', 1)
            );

            return customResponse()
                ->message("List of residence.")
                ->data($peronalDataList)
                ->success()
                ->generate();
        } catch (\Throwable $th) {
            return customResponse()
                ->message("Sorry, Something went wrong.")
                ->data(null)
                ->success()
                ->generate();
        }
    }

    public function show(Request $request, $id) {
        $userData = User::find($id);
        $personalData = !empty($userData->personalData) ? $userData->personalData : "";
        if (!empty($personalData)) {
            $profilePicture = $userData->profilePicture;
            $personalData = $userData->personalData;
            $otherData = $userData->otherData;
            $otherDataLanguage = !empty($otherData->language) ? $otherData->language : "";
            $addressData = $userData->addressData;
            $employmentData = $userData->employmentData;
            $educationalData = $userData->educationalData;
            $educationalOtherData = $userData->educationalOtherData;
            $familyData = $userData->familyData;
            $documentData = $userData->documentData;
            $groupsAndAffiliationData = $userData->groupsAndAffiliationData;
            $residenceApplicationStatus = $userData->residenceApplicationStatus;
            $medicalHistoryData = $userData->medicalHistory;
            $medicalHistoryDiseaseData = !empty($medicalHistoryData->medicalHistoryDisease) ? $medicalHistoryData->medicalHistoryDisease : "";
            $medicalActiveConditionData = !empty($medicalHistoryData->medicalActiveCondition) ? $medicalHistoryData->medicalActiveCondition : "";
            $houseHoldData = $userData->houseHold;
            $waterSourceList = !empty($houseHoldData->waterSource) ? $houseHoldData->waterSource : "";
            $landOwnershipList = !empty($houseHoldData->landOwnership) ? $houseHoldData->landOwnership : "";
            $presenceHouseHoldList = !empty($houseHoldData->presenceHouseHold) ? $houseHoldData->presenceHouseHold : "";
            $internetAccessList = !empty($houseHoldData->internetAccess) ? $houseHoldData->internetAccess : "";
            $houseKeeperList = $userData->houseKeeper;
        }

        return customResponse()
            ->message("Residence user data.")
            ->data($userData)
            ->success()
            ->generate();
    }
}
