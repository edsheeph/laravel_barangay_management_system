<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Classes\Report\InhabitantsReportClass;
use App\Models\PersonalData;
use App\Models\User;
use App\Models\Barangay;

class InhabitantsReportController extends Controller
{

    // public function getInhabitantsReport(Request $request){
    //     $inhabitantReport = new InhabitantsReportClass;
    //     #SELECT * FROM residence_application WHERE status_id='1';
    //     $barangayId = !empty($request->barangay_id) ? $request->barangay_id : "";
    //     $approvedResidenceList = $inhabitantReport->getApprovedResidenceData();
    //    # $populationCount = count($approvedResidenceList);
    //     $populationCount = $inhabitantReport->getPopulationCount($barangayId,$approvedResidenceList);

    //     $ageGroupList = $inhabitantReport->getAgeGroupList($barangayId,$approvedResidenceList);

    //     $genderPopulationList = $inhabitantReport->getPopulationByGender($barangayId,$approvedResidenceList);

    //     $otherDataList = $inhabitantReport->getOtherPopulationData($barangayId,$approvedResidenceList);

    //     $populationByBarangayList = $inhabitantReport->getPopulationByBarangay($approvedResidenceList);



    //     $return = array(
    //         'population_count' => $populationCount,
    //         'age_group_list' => $ageGroupList,
    //         'gender_list' => array(
    //             'male' => $genderPopulationList['M'],
    //             'female' => $genderPopulationList['F']
    //         ),
    //         'other_data' => array(
    //             'pwd' => $otherDataList['pwd'],
    //             'single_parent' => $otherDataList['singleParent'],
    //             'lgbtq' => $otherDataList['lgbtq'],
    //         ),
    //         'voter_count' => $otherDataList['voter'],
    //         'population_by_barangay' => $populationByBarangayList



    //     );


    //     return $return;
    // }

    public function getInhabitantsReport(Request $request) {
        $population = $this->getResidencePopulation($request->barangay_id);
        $males = $this->getResidencePopulationByMale($request->barangay_id);
        $females = $this->getResidencePopulationByFemale($request->barangay_id);
        $ages = $this->getResidencePopulationByAge($request->barangay_id);
        $disables = $this->getResidencePopulationByPWD($request->barangay_id);
        $singeParents = $this->getResidencePopulationBySingleParent($request->barangay_id);
        $community = $this->getResidencePopulationByLGBTQ($request->barangay_id);
        $voters = $this->getResidencePopulationByVoter($request->barangay_id);
        $barangays = $this->getResidencePopulationByBarangay($request->barangay_id);
        $employments = $this->getResidencePopulationByEmployment($request->barangay_id);

        $reports = [
            'population' => $population,
            'males' => $males,
            'females' => $females,
            'ages' => $ages,
            'disables' => $disables,
            'singeParents' => $singeParents,
            'community' => $community,
            'voters' => $voters,
            'barangays' => $barangays,
            'employments' => $employments,
        ];

        $response = $this->returnResponse($reports);

        return customResponse()
            ->message("Inhabitans Report.")
            ->data($response)
            ->success()
            ->generate();
    }

    private function getResidencePopulation($barangay) {
        $residences = PersonalData::whereNotNull("resident_id");
        if (!empty($barangay)) {
            $residences = $residences->whereRelation('userData', 'barangay_id', $barangay);
        }
        return $residences->count();
    }

    private function getResidencePopulationByMale($barangay) {
        $residences = PersonalData::whereNotNull("resident_id")
            ->where("gender", "M");
        if (!empty($barangay)) {
            $residences = $residences->whereRelation('userData', 'barangay_id', $barangay);
        }
        return $residences->count();
    }

    private function getResidencePopulationByFemale($barangay) {
        $residences = PersonalData::whereNotNull("resident_id")
            ->where("gender", "F");
        if (!empty($barangay)) {
            $residences = $residences->whereRelation('userData', 'barangay_id', $barangay);
        }
        return $residences->count();
    }

    private function getResidencePopulationByAge($barangay) {
        $residences = PersonalData::whereNotNull("resident_id");
        if (!empty($barangay)) {
            $residences = $residences->whereRelation('userData', 'barangay_id', $barangay);
        }
        $residences = $residences->get();

        $groups = [
            'children' => 0,
            'youth' => 0,
            'adults' => 0,
            'seniors' => 0,
        ];

        foreach ($residences as $residence) {
            $age = Helpers::getAge($residence->birth_date);
            switch (true) {
                case ($age <= 14 && $age >= 0):
                    $groups['children']++;
                break;
                case ($age <= 24 && $age >= 15):
                    $groups['youth']++;
                break;
                case ($age <= 64 && $age >= 25):
                    $groups['adults']++;
                break;
                case ($age >= 65):
                    $groups['seniors']++;
                break;
            }
        }
        
        return $groups;
    }

    private function getResidencePopulationByPWD($barangay) {
        $residences = User::whereHas('personalData', function ($query) {
            return $query->whereNotNull("resident_id");
        });
        if (!empty($barangay)) {
            $residences = $residences->where('barangay_id', $barangay);
        }
        $residences = $residences->join("other_data", "other_data.user_id", "users.id")
        ->where("disabled", 1);
        
        return $residences->count();
    }

    private function getResidencePopulationBySingleParent($barangay) {
        $residences = User::whereHas('personalData', function ($query) {
            return $query->whereNotNull("resident_id");
        });
        if (!empty($barangay)) {
            $residences = $residences->where('barangay_id', $barangay);
        }
        $residences = $residences->join("other_data", "other_data.user_id", "users.id")
        ->where("is_single_parent", 1);
        
        return $residences->count();
    }

    private function getResidencePopulationByLGBTQ($barangay) {
        $residences = User::whereHas('personalData', function ($query) {
            return $query->whereNotNull("resident_id");
        });
        if (!empty($barangay)) {
            $residences = $residences->where('barangay_id', $barangay);
        }
        $residences = $residences->join("other_data", "other_data.user_id", "users.id")
        ->where("community", 1);
        
        return $residences->count();
    }

    private function getResidencePopulationByVoter($barangay) {
        $residences = User::whereHas('personalData', function ($query) {
            return $query->whereNotNull("resident_id");
        });
        if (!empty($barangay)) {
            $residences = $residences->where('barangay_id', $barangay);
        }
        $residences = $residences->join("other_data", "other_data.user_id", "users.id")
        ->where("is_voter", 1);
        
        return $residences->count();
    }

    private function getResidencePopulationByBarangay($barangay) {
        $barangays = new Barangay;
        if (!empty($barangay)) {
            $barangays = $barangays->where('id', $barangay);
        }
        $barangays = $barangays->get();

        $barangayList = [];
        foreach ($barangays as $row) {
            $males = $this->getResidencePopulationByMale($row->id);
            $females = $this->getResidencePopulationByFemale($row->id);
            $ages = $this->getResidencePopulationByAge($row->id);
            $voters = $this->getResidencePopulationByVoter($row->id);
            $barangayList[] = array(
                'barangay' => $row->description,
                'males' => $males,
                'females' => $females,
                'voters' => $voters,
                'seniors' => $ages['seniors'],
            );
        }

        return $barangayList;
    }

    private function getResidencePopulationByEmployment($barangay) {
        $residences = User::whereHas('personalData', function ($query) {
            return $query->whereNotNull("resident_id");
        });
        if (!empty($barangay)) {
            $residences = $residences->where('barangay_id', $barangay);
        }
        $residences = $residences->join("employment_data", "employment_data.user_id", "users.id")
        ->get();

        $employments = [
            'students' => 0,
            'unemployed' => 0,
            'employed' => 0,
        ];

        foreach ($residences as $residence) {
            if ($residence->usual_occupation_id == 1) {
                $employments['students']++;
            } 

            if ($residence->employment_type == 3) {
                $employments['unemployed']++;
            } else {
                $employments['employed']++;
            }
        }

        return $employments;
    }

    private function returnResponse($params) {
        return [
            'population' => array(
                'description' => 'Total Population',
                'count' => $params['population']
            ),
            'males' => array(
                'description' => 'by Male',
                'count' => $params['males']
            ),
            'females' => array(
                'description' => 'by Female',
                'count' => $params['females']
            ),
            'population_by_age_group' => array(
                'description' => 'Population by age group',
                'group' => [
                    array(
                        'description' => 'Children',
                        'count' => $params['ages']['children']
                    ),
                    array(
                        'description' => 'Youth',
                        'count' => $params['ages']['youth']
                    ),
                    array(
                        'description' => 'Adults',
                        'count' => $params['ages']['adults']
                    ),
                    array(
                        'description' => 'Seniors',
                        'count' => $params['ages']['seniors']
                    ),
                ]
            ),
            'pwd' => array(
                'description' => 'PWD',
                'count' => $params['disables']
            ),
            'single_parents' => array(
                'description' => 'Single Parent',
                'count' => $params['singeParents']
            ),
            'lgbtq' => array(
                'description' => 'LGBTQ',
                'count' => $params['community']
            ),
            'voters' => array(
                'description' => 'Voters',
                'count' => $params['voters']
            ),
            'population_by_employment' => array(
                'description' => 'Population by employment',
                'group' => [
                    array(
                        'description' => 'Students',
                        'count' => $params['employments']['students']
                    ),
                    array(
                        'description' => 'Unemployed',
                        'count' => $params['employments']['unemployed']
                    ),
                    array(
                        'description' => 'Employed',
                        'count' => $params['employments']['employed']
                    ),
                ]
            ),
            'population_by_barangay' => array(
                'description' => 'Population by barangay',
                'group' => $params['barangays']
            )
        ];
        // return [
        //     array(
        //         'description' => 'Total Population',
        //         'count' => $params['population']
        //     ),
        //     array(
        //         'description' => 'by Male',
        //         'count' => $params['males']
        //     ),
        //     array(
        //         'description' => 'by Female',
        //         'count' => $params['females']
        //     ),
        //     array(
        //         'description' => 'Population by age group',
        //         'age_group' => [
        //             array(
        //                 'description' => 'Children',
        //                 'count' => $params['ages']['children']
        //             ),
        //             array(
        //                 'description' => 'Youth',
        //                 'count' => $params['ages']['youth']
        //             ),
        //             array(
        //                 'description' => 'Adults',
        //                 'count' => $params['ages']['adults']
        //             ),
        //             array(
        //                 'description' => 'Seniors',
        //                 'count' => $params['ages']['seniors']
        //             ),
        //         ]
        //     ),
        //     array(
        //         'description' => 'PWD',
        //         'count' => $params['disables']
        //     ),
        //     array(
        //         'description' => 'Single Parent',
        //         'count' => $params['singeParents']
        //     ),
        //     array(
        //         'description' => 'LGBTQ',
        //         'count' => $params['community']
        //     ),
        //     array(
        //         'description' => 'Voters',
        //         'count' => $params['voters']
        //     ),
        //     array(
        //         'description' => 'Population by employment',
        //         'employment_group' => [
        //             array(
        //                 'description' => 'Students',
        //                 'count' => $params['employments']['students']
        //             ),
        //             array(
        //                 'description' => 'Unemployed',
        //                 'count' => $params['employments']['unemployed']
        //             ),
        //             array(
        //                 'description' => 'Employed',
        //                 'count' => $params['employments']['employed']
        //             ),
        //         ]
        //     ),
        //     array(
        //         'description' => 'Population by barangay',
        //         'barangay_group' => $params['barangays']
        //     ),
        // ];
    }
}
