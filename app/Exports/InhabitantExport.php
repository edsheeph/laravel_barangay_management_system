<?php

namespace App\Exports;

use Helpers;
use Carbon\Carbon;
use App\Models\IncidentData;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

use App\Models\User;

class InhabitantExport implements FromCollection, WithHeadings
{
    protected $params;
    
    function __construct($params) {
        $this->params = $params;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $params = $this->params;
        
        $barangays = array_filter($params['barangay_id']);
        $genders = array_filter($params['gender']);
        $ages = array_filter($params['age']);
        
        $residences = User::whereHas('personalData', function ($query) {
            return $query->whereNotNull("resident_id");
        });

        if (count($barangays) > 0) {
            $residences = $residences->whereIn('barangay_id', $barangays);
        }

        if (count($genders) > 0) {
            $residences = $residences->whereHas('personalData', function ($query) use($genders) {
                return $query->whereIn("gender", $genders);
            });
        }

        if (!empty($params['pwd'])) {
            $residences = $residences->whereHas('otherData', function ($query) {
                return $query->where("disabled", 1);
            });
        }

        if (!empty($params['single_parent'])) {
            $residences = $residences->whereHas('otherData', function ($query) {
                return $query->where("is_single_parent", 1);
            });
        }

        if (!empty($params['lgbtq'])) {
            $residences = $residences->whereHas('otherData', function ($query) {
                return $query->where("community", 1);
            });
        }

        if (!empty($params['voters'])) {
            $residences = $residences->whereHas('otherData', function ($query) {
                return $query->whereIn("is_voter", [1, 3]);
            });
        }

        if (!empty($params['non_voters'])) {
            $residences = $residences->whereHas('otherData', function ($query) {
                return $query->whereIn("is_voter", [2, 0, ""]);
            });
        }

        if (!empty($params['student'])) {
            $residences = $residences->whereHas('employmentData', function ($query) {
                return $query->where("usual_occupation_id", 1);
            });
        }

        if (!empty($params['unemployed'])) {
            $residences = $residences->whereHas('employmentData', function ($query) {
                return $query->where("employment_type", 4);
            });
        }

        if (!empty($params['employed'])) {
            $residences = $residences->whereHas('employmentData', function ($query) {
                return $query->whereIn("employment_type", [1, 2, 3]);
            });
        }

        $residences = $residences->with([
            "barangayData", 
            "personalData", 
            "otherData", 
            "employmentData"
        ])
        ->orderBy("barangay_id", "ASC")
        ->orderBy("last_name", "ASC")
        ->get();

        $storeInArray = [];
        foreach ($residences as $residence) {
            $age = Helpers::getAge($residence->birth_date);

            if ((!empty($params['age_from']) || $params['age_from']==0) && !empty($params['age_to'])) {
                // custom
                if ($age <= $params['age_to'] && $age >= $params['age_from']) {
                    $storeInArray[] = $this->returnResponse($residence);
                }
            } else { 
                if (count($ages) > 0) {
                    // age group
                    $ageGroup = [
                        1 => $age <= 14 && $age >= 0,
                        2 => $age <= 24 && $age >= 15,
                        3 => $age <= 64 && $age >= 25,
                        4 => $age >= 65,
                    ];
                    foreach ($ages as $key => $value) {
                        if ($ageGroup[$value]) {
                            $storeInArray[] = $this->returnResponse($residence);
                            break;
                        }
                    }
                } else {
                    // all
                    $storeInArray[] = $this->returnResponse($residence);
                }
            }
        }
        
        return collect($storeInArray);
    }

    public function headings():array{
        return [
            'Barangay',
            'Last Name',
            'First Name',
            'Middle Name',
            'Address',
            'Email',
            'Contact No.',
            'Gender',
            'Birthday',
            'Age',
            // 'PWD',
            // 'Single Parent',
            // 'LGBTQIA+',
            // 'Student',
            // 'Employed',
            // 'Unemployed',
            // 'Voters',
        ];
    }

    private function returnResponse($residence) {
        $response = array(
            $residence->barangayData->description,
            $residence->last_name,
            $residence->first_name,
            $residence->middle_name,
            $residence->address,
            $residence->email,
            $residence->contact_no,
            ($residence->gender=="M" ? "Male" : "Female"),
            (date("F d, Y", strtotime($residence->birth_date))),
            Helpers::getAge($residence->birth_date),
            // (!empty($residence->otherData->disabled) ? "Yes" : "No"),
            // (!empty($residence->otherData->is_single_parent) ? "Yes" : "No"),
            // (!empty($residence->otherData->community) ? "Yes" : "No"),
            // ($residence->employmentData->usual_occupation_id == 1 ? "Yes" : "No"),
            // (
            //     $residence->employmentData->employment_type == 1 || 
            //     $residence->employmentData->employment_type == 2 || 
            //     $residence->employmentData->employment_type == 3 
            //     ? "Yes" : "No"
            // ),
            // ($residence->employmentData->employment_type == 4 ? "Yes" : "No"),
            // (
            //     $residence->otherData->is_voter == 1 || 
            //     $residence->otherData->is_voter == 3 
            //     ? "Yes" : "No"
            // ),
        );

        return $response;
    }
}
