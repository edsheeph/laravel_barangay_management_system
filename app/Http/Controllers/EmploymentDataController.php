<?php

namespace App\Http\Controllers;

use Helpers;
use Session;
use Validator;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Classes\EmploymentDataClass;

use App\Models\ClassWorker;
use App\Models\UsualOccupation;
use App\Models\WorkAffiliation;
use App\Models\User as UserModel;

class EmploymentDataController extends Controller
{
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'employment_type' => 'required'
        ]);

        // if ($validator->fails()) {
        //     return customResponse()
        //         ->data(null)
        //         ->message($validator->errors()->all()[0])
        //         ->failed()
        //         ->generate();
        // }

        $class = new EmploymentDataClass;
        $class->saveEmploymentData($request);

        return customResponse()
            ->data(null)
            ->message('Record has been saved.')
            ->success()
            ->generate();      
    }

    public function getEmploymentData(Request $request, $id) {
        $userData = UserModel::find($id);
        if (empty($userData)) {
            return customResponse()
                ->message("No data")
                ->data(null)
                ->failed()
                ->generate();
        }
        
        $employmentData = $userData->employmentData;

        return customResponse()
            ->message("Employment data.")
            ->data($userData)
            ->success()
            ->generate();
    }

    public function getClassWorkerList(Request $request) {
        $classWorkerList = ClassWorker::select(
            'id',
            'description'
        )
        ->get();

        return customResponse()
            ->message("List of class worker.")
            ->data($classWorkerList)
            ->success()
            ->generate();
    }

    public function getUsualOccupationList(Request $request) {
        $usualOccupationList = UsualOccupation::select(
            'id',
            'description'
        )
        ->get();
        
        return customResponse()
            ->message("List of usual occupation.")
            ->data($usualOccupationList)
            ->success()
            ->generate();
    }

    public function getWorkAffiliationList(Request $request) {
        $workAffiliationList = WorkAffiliation::select(
            'id',
            'description'
        )
        ->get();
        
        return customResponse()
            ->message("List of work affiliation.")
            ->data($workAffiliationList)
            ->success()
            ->generate();
    }

    public function getPlaceWorkType(Request $request) {
        return customResponse()
            ->message("Place work type.")
            ->data(Helpers::getPlaceWorkType())
            ->success()
            ->generate();
    }

    public function getRadioEmployeeType(Request $request) {
        return customResponse()
            ->message("List of radio employee type.")
            ->data(Helpers::getRadioEmployeeType())
            ->success()
            ->generate();
    }

    public function getMonthlyIncomeList(Request $request) {
        return customResponse()
            ->message("List of monthly income.")
            ->data([
                array(
                    'id' => 1,
                    'description' => 'Less than ₱10,000'
                ),
                array(
                    'id' => 2,
                    'description' => '₱10,000 to ₱20,000'
                ),
                array(
                    'id' => 3,
                    'description' => '₱20,001 to ₱40,000'
                ),
                array(
                    'id' => 4,
                    'description' => '₱40,001 to ₱70,000'
                ),
                array(
                    'id' => 5,
                    'description' => '₱70,001 to ₱130,000'
                ),
                array(
                    'id' => 6,
                    'description' => '₱130,001 to ₱200,000'
                ),
                array(
                    'id' => 7,
                    'description' => 'At least ₱200,000 and up'
                ),
            ])
            ->success()
            ->generate();
    }
}
