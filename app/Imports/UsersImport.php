<?php

namespace App\Imports;

use Hash;
use Throwable;
use App\Models\EmploymentData;
use App\Models\MedicalHistoryVaccine;
use App\Models\MedicalHistoryDisease;
use App\Models\MedicalHistory;
use App\Models\OtherData;
use App\Models\AddressData;
use App\Models\Barangay;
use App\Models\BarangayIDSequence;
use App\Models\PersonalData;
use App\Models\ResidenceApplication;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements ToCollection, WithHeadingRow, SkipsOnError, WithValidation, SkipsOnFailure, WithChunkReading
{
    use Importable, SkipsErrors, SkipsFailures;

    public function collection(Collection $rows) {
        foreach ($rows as $row) {
            $user = User::create([
                'first_name' => $row['first_name'],
                'middle_name' => $row['middle_name'],
                'last_name' => $row['last_name'],
                'email' => $row['email'],
                'contact_no' => $row['contact_no'],
                'gender' => strtoupper($row['gender']),
                'birth_date' => date("Y-m-d", strtotime($row['birth_date'])),
                'address' => $row['full_address'],
                'barangay_id' => $row['barangay_id'],
                'password' => Hash::make($row['last_name']),
                'user_type_id' => $row['user_type_id']
            ]);

            if ($row['user_type_id']==5 && !empty($row['barangay_id'])) {
                $residenceApplication = new ResidenceApplication;
                $residenceApplication->user_id = $user->id;
                $residenceApplication->status_id = 1;
                $residenceApplication->remarks = "";
                $residenceApplication->save();

                $barangay = Barangay::find($row['barangay_id']);
                $brgyIDSequence = BarangayIDSequence::where("barangay_id", $row['barangay_id'])->where("current_year", date('Y'))->first();
                if (empty($brgyIDSequence)) {
                    $brgyIDSequence = new BarangayIDSequence;
                    $brgyIDSequence->barangay_id = $row['barangay_id'];
                    $brgyIDSequence->current_year = date('Y');
                    $brgyIDSequence->sequence = 0;
                }
                $defSeq = "00000000";
                $sequence = $brgyIDSequence->sequence + 1;
                $newSequence = substr($defSeq, strlen($sequence)) . $sequence;
                $brgyIDSequence->sequence = $sequence;
                $brgyIDSequence->save();
                $residentID = $barangay->code . $brgyIDSequence->current_year . $newSequence;

                $personalData = new PersonalData;
                $personalData->user_id = $user->id;
                $personalData->application_id = date("Y") . $residenceApplication->id;
                $personalData->resident_id = $residentID;
                $personalData->last_name = $row['last_name'];
                $personalData->first_name = $row['first_name'];
                $personalData->middle_name = $row['middle_name'];
                $personalData->gender = strtoupper($row['gender']);
                $personalData->birth_date = date("Y-m-d", strtotime($row['birth_date']));
                $personalData->birth_place = $row['birth_place'];
                $personalData->contact_no = $row['contact_no'];
                $personalData->email = $row['email'];
                $personalData->additional_contact_no = $row['additional_contact_no'];
                $personalData->emergency_contact_no = $row['emergency_contact_no'];
                $personalData->save();

                $addressData = new AddressData;
                $addressData->user_id = $user->id;
                $addressData->blk = !empty($row['blk']) ? $row['blk'] : "";
                $addressData->street = !empty($row['street']) ? $row['street'] : "";
                $addressData->barangay_id = !empty($row['barangay_id']) ? $row['barangay_id'] : "";
                $addressData->district = !empty($row['district']) ? $row['district'] : "";
                $addressData->zip_code = !empty($row['zip_code']) ? $row['zip_code'] : "";
                $addressData->full_address = !empty($row['full_address']) ? $row['full_address'] : "";
                $addressData->address_type = 1;
                $addressData->starting_from = "2000-01-01";
                $addressData->primary_id_path = "";
                $addressData->primary_id_name = "";
                $addressData->secondary_id_path = "";
                $addressData->secondary_id_name = "";
                $addressData->save();

                $otherData = new OtherData;
                $otherData->user_id = $user->id;
                $otherData->disabled = !empty($row['disabled']) ? $row['disabled'] : 0;
                $otherData->community = !empty($row['community']) ? $row['community'] : 0;
                $otherData->is_voter = !empty($row['is_voter']) ? $row['is_voter'] : 0;
                $otherData->is_single_parent = !empty($row['is_single_parent']) ? $row['is_single_parent'] : 0;
                $otherData->save();

                $medicalHistoryData = new MedicalHistory;
                $medicalHistoryData->height = !empty($row['height']) ? $row['height'] : "";
                $medicalHistoryData->weight = !empty($row['weight']) ? $row['weight'] : "";
                $medicalHistoryData->blood_type = $row['blood_type'];
                $medicalHistoryData->smoke_no = !empty($row['smoke_no']) ? $row['smoke_no'] : "";
                $medicalHistoryData->smoke_status = !empty($row['smoke_status']) ? $row['smoke_status'] : 0;
                $medicalHistoryData->alcohol_no = !empty($row['alcohol_no']) ? $row['alcohol_no'] : "";
                $medicalHistoryData->alcohol_status = !empty($row['alcohol_status']) ? $row['alcohol_status'] : 0;
                $medicalHistoryData->comorbidity = !empty($row['comorbidity']) ? $row['comorbidity'] : 0;
                $medicalHistoryData->other_medical_history = !empty($row['other_medical_history']) ? $row['other_medical_history'] : "";
                $medicalHistoryData->allergies = !empty($row['allergies']) ? $row['allergies'] : "";
                $medicalHistoryData->save();

                if (!empty($row['disease_id'])) {
                    $row['disease_id'] = explode(",", $row['disease_id']);

                    MedicalHistoryDisease::where("medical_history_id", $medicalHistoryData->id)->each(function($row) {
                        $row->delete();
                    });
            
                    foreach ($row['disease_id'] as $key => $value) {
                        if (!empty($value)) {
                            $medHistoryDiseaseData = new MedicalHistoryDisease;
                            $medHistoryDiseaseData->medical_history_id = $medicalHistoryData->id;
                            $medHistoryDiseaseData->disease_id = $value;
                            $medHistoryDiseaseData->save();
                        }
                    }
                }

                if (!empty($row['vaccination'])) {
                    $row['vaccination'] = explode(",", $row['vaccination']);

                    MedicalHistoryVaccine::where("medical_history_id", $medicalHistoryData->id)->each(function($row) {
                        $row->delete();
                    });
            
                    foreach ($row['vaccination'] as $key => $value) {
                        if (!empty($value)) {
                            $medicalHistoryVaccine = new MedicalHistoryVaccine;
                            $medicalHistoryVaccine->medical_history_id = $medicalHistoryData->id;
                            $medicalHistoryVaccine->vaccine_id = $value;
                            $medicalHistoryVaccine->save();
                        }
                    }
                }

                $employmentData = new EmploymentData;
                $employmentData->user_id = $user->id;
                $employmentData->employment_type = $row['employment_type'];
                $employmentData->usual_occupation_id = $row['usual_occupation_id'];
                $employmentData->place_work_type = 3;
                $employmentData->place_work_type_specify = !empty($row['place_work_type_specify']) ? $row['place_work_type_specify'] : "";
                $employmentData->employment = !empty($row['employment']) ? $row['employment'] : "";
                $employmentData->employment_address = !empty($row['employment_address']) ? $row['employment_address'] : "";
                $employmentData->monthly_income = !empty($row['monthly_income']) ? $row['monthly_income'] : 0;
                $employmentData->annual_income = !empty($row['annual_income']) ? $row['annual_income'] : 0;
                $employmentData->save();
            }
        }
    }

    public function rules(): array {
        return [
            '*.email' => ['email', 'unique:users,email']
        ];
    }

    public function chunkSize(): int {
        return 1000;
    }
}
