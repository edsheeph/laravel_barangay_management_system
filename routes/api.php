<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\UsersImportController;

use App\Http\Controllers\InhabitantsController;
use App\Http\Controllers\PersonalDataController;
use App\Http\Controllers\OtherDataController;
use App\Http\Controllers\AddressDataController;
use App\Http\Controllers\EmploymentDataController;
use App\Http\Controllers\EducationalDataController;
use App\Http\Controllers\FamilyDataController;
use App\Http\Controllers\ResidenceApplicationController;
use App\Http\Controllers\DocumentDataController;
use App\Http\Controllers\GroupsAndAffiliationController;
use App\Http\Controllers\MedicalHistoryController;
use App\Http\Controllers\HouseHoldController;
use App\Http\Controllers\HouseKeeperController;

use App\Http\Controllers\PermitTypeController;
use App\Http\Controllers\PermitTemplateController;
use App\Http\Controllers\PermitFeesController;
use App\Http\Controllers\BarangayOfficialController;
use App\Http\Controllers\BarangayPositionController;
use App\Http\Controllers\PermitCategoryController;
use App\Http\Controllers\PermitRequestController;
use App\Http\Controllers\PermitPaymentMethodController;
use App\Http\Controllers\PermitLayoutController;

use App\Http\Controllers\ClearanceTypeController;
use App\Http\Controllers\ClearancePaymentMethodController;
use App\Http\Controllers\ClearanceCategoryController;
use App\Http\Controllers\ClearanceRequestController;
use App\Http\Controllers\ClearancePurposeController;
use App\Http\Controllers\ClearanceTemplateController;


use App\Http\Controllers\InhabitantsReportController;
use App\Http\Controllers\MedicineInventoryController;






use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\BarangayController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\IncidentTypeController;
use App\Http\Controllers\BlotterAndComplainController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('password-validation', [AuthController::class, 'passwordValidation']);
Route::get('announcement/city-hall/display', [AnnouncementController::class, 'cityHall']);

Route::post('users/import', [UsersImportController::class, 'store']);

Route::group(['middleware' => ['auth:api']], function() {
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);
    Route::get('user/list', [AuthController::class, 'list']);
    Route::get('user/{id}', [AuthController::class, 'show']);
    Route::post('password', [AuthController::class, 'changePassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);

    ##User Management
    Route::get('user-management/{id}', [UserManagementController::class, 'show']);
    Route::post('user-management/store', [UserManagementController::class, 'store']);
    Route::post('user-management/deactivate/{id}', [UserManagementController::class, 'deactivate']);
    Route::post('user-management/activate/{id}', [UserManagementController::class, 'activate']);
    Route::get('user-management/status-history/{id}', [UserManagementController::class, 'statusHistory']);

    ##Inhabitants
    Route::get('inhabitants/list', [InhabitantsController::class, 'getInhabitantsList']);
    Route::get('inhabitants/residence/list', [InhabitantsController::class, 'getResidenceList']);
    Route::get('inhabitants/{id}', [InhabitantsController::class, 'show']);

    Route::get('inhabitants/personal/{id}', [PersonalDataController::class, 'getPersonalData']);
    Route::get('inhabitants/personal/picture/{id}', [PersonalDataController::class, 'getProfile']);
    Route::post('inhabitants/personal/store', [PersonalDataController::class, 'store']);
    Route::post('inhabitants/personal/picture/store', [PersonalDataController::class, 'profile']);

    Route::get('inhabitants/other/{id}', [OtherDataController::class, 'getOtherData']);
    Route::post('inhabitants/other/store', [OtherDataController::class, 'store']);

    Route::get('inhabitants/address/{id}', [AddressDataController::class, 'getAddressData']);
    Route::post('inhabitants/address/store', [AddressDataController::class, 'store']);

    Route::get('inhabitants/employment/{id}', [EmploymentDataController::class, 'getEmploymentData']);
    Route::post('inhabitants/employment/store', [EmploymentDataController::class, 'store']);

    Route::get('inhabitants/educational/{id}', [EducationalDataController::class, 'getEducationalData']);
    Route::get('inhabitants/educational/list/{id}', [EducationalDataController::class, 'list']);
    Route::post('inhabitants/educational/store', [EducationalDataController::class, 'store']);
    Route::post('inhabitants/educational/destroy/{id}', [EducationalDataController::class, 'destroy']);

    Route::get('inhabitants/family/{id}', [FamilyDataController::class, 'getFamilyData']);
    Route::get('inhabitants/family/list/{id}', [FamilyDataController::class, 'list']);
    Route::get('inhabitants/family/user/list/{id}', [FamilyDataController::class, 'userList']);
    Route::post('inhabitants/family/store', [FamilyDataController::class, 'store']);
    Route::post('inhabitants/family/destroy/{id}', [FamilyDataController::class, 'destroy']);

    Route::post('inhabitants/application/update', [ResidenceApplicationController::class, 'update']);

    Route::get('inhabitants/document/{id}', [DocumentDataController::class, 'getDocumentData']);
    Route::get('inhabitants/document/list/{id}', [DocumentDataController::class, 'list']);
    Route::post('inhabitants/document/store', [DocumentDataController::class, 'store']);
    Route::post('inhabitants/document/destroy/{id}', [DocumentDataController::class, 'destroy']);

    Route::get('inhabitants/groups/{id}', [GroupsAndAffiliationController::class, 'getGroupsAndAffiliationData']);
    Route::post('inhabitants/groups/store', [GroupsAndAffiliationController::class, 'store']);

    Route::get('inhabitants/medical-history/{id}', [MedicalHistoryController::class, 'getMedicalHistory']);
    Route::post('inhabitants/medical-history/store', [MedicalHistoryController::class, 'store']);

    Route::get('inhabitants/active-medical-condition/{id}', [MedicalHistoryController::class, 'getActiveMedicalConditionData']);
    Route::get('inhabitants/active-medical-condition/list/{id}', [MedicalHistoryController::class, 'getActiveMedicalConditionList']);
    Route::post('inhabitants/active-medical-condition/store', [MedicalHistoryController::class, 'saveMedicalCondtion']);
    Route::post('inhabitants/active-medical-condition/destroy/{id}', [MedicalHistoryController::class, 'destroyMedicalCondtion']);

    Route::get('inhabitants/house-hold/{id}', [HouseHoldController::class, 'index']);
    Route::post('inhabitants/house-hold/store', [HouseHoldController::class, 'store']);
    Route::get('inhabitants/house-hold-water-source/list', [HouseHoldController::class, 'houseHoldWaterSourceList']);
    Route::post('inhabitants/house-hold-water-source/store', [HouseHoldController::class, 'saveHouseHoldWaterSource']);

    Route::get('inhabitants/house-keeper/{id}', [HouseKeeperController::class, 'getHouseKeeperData']);
    Route::get('inhabitants/house-keeper/list/{id}', [HouseKeeperController::class, 'list']);
    Route::get('inhabitants/house-keeper/user/list/{id}', [HouseKeeperController::class, 'userList']);
    Route::post('inhabitants/house-keeper/store', [HouseKeeperController::class, 'store']);
    Route::post('inhabitants/house-keeper/destroy/{id}', [HouseKeeperController::class, 'destroy']);





    Route::post('permit/type', [PermitTypeController::class, 'store']);
    Route::get('permit/type/{id}/edit', [PermitTypeController::class, 'edit']);
    Route::post('permit/type/update', [PermitTypeController::class,'update']);
    Route::post('permit/type/delete', [PermitTypeController::class,'delete']);
    Route::get('permit/type/{id}', [PermitTypeController::class, 'getPermitType']);

    Route::get('permit/type/{id}', [PermitTypeController::class, 'show']);
    Route::get('permit/types', [PermitTypeController::class, 'list']);

    Route::post('permit/category', [PermitCategoryController::class, 'store']);
    Route::get('permit/category/{id}', [PermitCategoryController::class, 'show']);
    Route::get('permit/category/{id}/edit', [PermitCategoryController::class, 'edit']);
    Route::post('permit/category/update', [PermitCategoryController::class, 'update']);
    Route::post('permit/category/delete', [PermitCategoryController::class, 'delete']);
    Route::get('permit/categories', [PermitCategoryController::class, 'list']);


   /*  Route::post('permit/fee', [PermitFeesController::class, 'store']);
    Route::get('permit/fees/{id}', [PermitFeesController::class, 'show']);
    Route::get('permit/fees/{id}/edit', [PermitFeesController::class, 'edit']);
    Route::post('permit/fees/update', [PermitFeesController::class,'update']);
    Route::post('permit/fees/delete', [PermitFeesController::class,'delete']);
    Route::get('permit/fees', [PermitFeesController::class,'list']);
 */

     ##Barangay Officials
    Route::post('barangay/official', [BarangayOfficialController::class, 'store']);
    Route::get('barangay/official/{id}', [BarangayOfficialController::class, 'show']);
    Route::get('barangay/official/{id}/edit', [BarangayOfficialController::class, 'edit']);
    Route::post('barangay/official/update', [BarangayOfficialController::class, 'update']);
    Route::post('barangay/official/delete', [BarangayOfficialController::class, 'delete']);
    Route::get('barangay/officials', [BarangayOfficialController::class, 'list']);

    ##Barangay Position
    Route::get('barangay/positions', [BarangayPositionController::class, 'list']);





    Route::get('permit/paymentmethod/list', [PermitPaymentMethodController::class, 'list']);
    Route::get('permit/list', [PermitRequestController::class, 'list']);



    Route::post('permit/payment', [PermitRequestController::class, 'permitPayment']);


    Route::post('permit/admin/request', [PermitRequestController::class, 'generatePermit']);
    Route::post('permit/request/deny', [PermitRequestController::class, 'denyRequest']);


    Route::get('permit/payment/{id}', [PermitRequestController::class, 'getPermitPaymentData']);
    Route::post('permit/request', [PermitRequestController::class, 'generatePermit']);

    Route::post('permit/request/approve', [PermitRequestController::class, 'approveRequest']);
    Route::post('permit/request/layout/update', [PermitLayoutController::class, 'updateRequestLayout']);
    Route::get('permit/request/{id}', [PermitRequestController::class, 'show']);
    Route::get('permit/request/layout/{id}/edit', [PermitLayoutController::class, 'editRequestLayout']);


    Route::post('permit/request/print', [PermitRequestController::class, 'printPermit']);

    #Clearance Category
    Route::post('clearance/category', [ClearanceCategoryController::class, 'store']);
    Route::get('clearance/category/{id}', [ClearanceCategoryController::class, 'show']);
    Route::get('clearance/category/{id}/edit', [ClearanceCategoryController::class, 'edit']);
    Route::post('clearance/category/update', [ClearanceCategoryController::class, 'update']);
    Route::post('clearance/category/delete', [ClearanceCategoryController::class, 'delete']);
    Route::get('clearance/categories', [ClearanceCategoryController::class, 'list']);



    #Clearance Type
    Route::post('clearance/type', [ClearanceTypeController::class, 'store']);
    Route::get('clearance/type/{id}/edit', [ClearanceTypeController::class, 'edit']);
    Route::post('clearance/type/update', [ClearanceTypeController::class,'update']);
    Route::post('clearance/type/delete', [ClearanceTypeController::class,'delete']);
    Route::get('clearance/type/{id}', [ClearanceTypeController::class, 'show']);
    Route::get('clearance/types', [ClearanceTypeController::class, 'list']);


    #Clearance Payment Method
    Route::get('clearance/paymentmethod/list', [ClearancePaymentMethodController::class, 'list']);

    #Clearance Request
    Route::post('clearance/request', [ClearanceRequestController::class, 'requestPermit']);
    Route::post('clearance/admin/request', [ClearanceRequestController::class, 'requestPermit']);
    Route::get('clearance/request/list', [ClearanceRequestController::class, 'list']);
    Route::post('clearance/payment', [ClearanceRequestController::class, 'clearancePayment']);
    Route::get('clearance/request/{id}', [ClearanceRequestController::class, 'show']);
    Route::post('clearance/request/deny', [ClearanceRequestController::class, 'denyRequest']);
    Route::get('clearance/payment/{id}', [ClearanceRequestController::class, 'getClearancePaymentData']);
    Route::post('clearance/request/approve', [ClearanceRequestController::class, 'approveRequest']);
    Route::post('clearance/print', [ClearanceRequestController::class, 'printClearance']);
    Route::post('clearance/printPDF', [ClearanceRequestController::class, 'printClearancePDF']);





    #Report
    Route::get('report/dashboard/printPDF', [ClearanceRequestController::class, 'printClearancePDF']);

    #Medicine Inventory
    Route::post('health/medicine', [MedicineInventoryController::class, 'store']);
    Route::get('health/medicine/{id}', [MedicineInventoryController::class, 'show']);
    Route::get('health/medicine/{id}/edit', [MedicineInventoryController::class, 'edit']);
    Route::post('health/medicine/update', [MedicineInventoryController::class, 'update']);
    Route::post('health/medicine/delete', [MedicineInventoryController::class, 'delete']);
    Route::get('health/medicines', [MedicineInventoryController::class, 'list']);

    Route::get('health/status/list', [MedicineInventoryController::class, 'statusList']);


    ##Announcement
    Route::get('announcement/list', [AnnouncementController::class, 'index']);
    Route::get('announcement/display', [AnnouncementController::class, 'display']);
    Route::get('announcement/{id}', [AnnouncementController::class, 'show']);
    Route::post('announcement/store', [AnnouncementController::class, 'store']);
    Route::post('announcement/destroy/{id}', [AnnouncementController::class, 'destroy']);
    Route::post('announcement/destroy-img/{id}', [AnnouncementController::class, 'destroyImg']);

    ##Barangay
    Route::post('barangay/print/id', [BarangayController::class, 'printBarangayID']);

    Route::get('incident/admin/list', [IncidentController::class, 'incidentList']);
    Route::get('incident/count', [IncidentController::class, 'countIncident']);
    Route::get('incident/list/{id}', [IncidentController::class, 'list']);
    Route::post('incident/store', [IncidentController::class, 'store']);
    Route::post('incident/take-action/{id}', [IncidentController::class, 'takeAction']);
    Route::post('incident/resolution/{id}', [IncidentController::class, 'resolution']);
    Route::post('incident/mark-as-read/{id}', [IncidentController::class, 'markAsRead']);
    Route::get('incident/show/{id}', [IncidentController::class, 'show']);
    Route::post('incident/destroy/{id}', [IncidentController::class, 'destroy']);
    Route::get('incident/export-excel', [IncidentController::class, 'exportIntoExcel']);
    Route::get('incident/export-csv', [IncidentController::class, 'exportIntoCSV']);

    Route::post('incident/type/store', [IncidentTypeController::class, 'store']);
    Route::post('incident/type/update/{id}', [IncidentTypeController::class, 'update']);
    Route::post('incident/type/destroy/{id}', [IncidentTypeController::class, 'destroy']);

    Route::get('blotter/admin/list', [BlotterAndComplainController::class, 'blotterList']);
    Route::get('blotter/user/list', [BlotterAndComplainController::class, 'userList']);
    Route::get('blotter/list/{id}', [BlotterAndComplainController::class, 'list']);
    Route::get('blotter/show/{id}', [BlotterAndComplainController::class, 'show']);
    Route::post('blotter/store', [BlotterAndComplainController::class, 'store']);
    Route::post('blotter/resolution', [BlotterAndComplainController::class, 'resolution']);
    Route::post('blotter/destroy/{id}', [BlotterAndComplainController::class, 'destroy']);
    Route::get('blotter/export-excel', [BlotterAndComplainController::class, 'exportIntoExcel']);
    Route::get('blotter/export-csv', [BlotterAndComplainController::class, 'exportIntoCSV']);


    ####Report
    Route::get('report/inhabitants', [InhabitantsReportController::class, 'getInhabitantsReport']);
    Route::get('report/incidents', [IncidentController::class, 'getIncidentReport']);
    Route::get('report/blotters', [BlotterAndComplainController::class, 'getBlotterReport']);

    Route::post('business_permit/request', [PermitRequestController::class, 'requestPermitFromBusinessPermitSystem']);

    ##Clearance Purpose
    Route::post('clearance/purpose', [ClearancePurposeController::class, 'store']);
    Route::get('clearance/purpose/{id}/edit', [ClearancePurposeController::class, 'edit']);
    Route::post('clearance/purpose/update', [ClearancePurposeController::class, 'update']);
    Route::post('clearance/purpose/delete', [ClearancePurposeController::class, 'delete']);
    Route::get('clearance/purpose/{id}', [ClearancePurposeController::class, 'show']);
    Route::get('clearance/purposes', [ClearancePurposeController::class, 'purposeList']);

    #Clearance Template
    Route::get('clearance/templates', [ClearanceTemplateController::class, 'templateList']);
    Route::get('clearance/template/images', [ClearanceTemplateController::class, 'templateImageList']);
    Route::post('clearance/template', [ClearanceTemplateController::class, 'store']);
    Route::get('clearance/template/{id}/edit', [ClearanceTemplateController::class, 'edit']);
    Route::post('clearance/template/update', [ClearanceTemplateController::class, 'update']);
    Route::post('clearance/template/delete', [ClearanceTemplateController::class, 'delete']);
    Route::get('clearance/template/{id}', [ClearanceTemplateController::class, 'show']);
    




});









##Others
Route::get('barangay/list', [AuthController::class, 'getBarangayList']);
Route::get('user-type/list', [AuthController::class, 'getUserTypeList']);

Route::get('radio/citizenship/list', [PersonalDataController::class, 'getRadioCitizen']);
Route::get('gender/list', [PersonalDataController::class, 'getRadioGender']);
Route::get('marital-status/list', [PersonalDataController::class, 'getMaritalStatusList']);
Route::get('religious/list', [PersonalDataController::class, 'getReligiousList']);
Route::get('citizenship/list', [PersonalDataController::class, 'getCitizenshipList']);
Route::get('residence-status/list', [PersonalDataController::class, 'getResidenceStatusList']);
Route::get('country/list', [PersonalDataController::class, 'getCountryList']);
Route::get('province/list', [PersonalDataController::class, 'getProvinceList']);
Route::get('municipality/list', [PersonalDataController::class, 'getMunicipalityList']);

Route::get('ethnicity/list', [OtherDataController::class, 'getEthnicityList']);
Route::get('language/list', [OtherDataController::class, 'getLanguageList']);
Route::get('disability/list', [OtherDataController::class, 'getDisabilityList']);
Route::get('community/list', [OtherDataController::class, 'getCommunityList']);
Route::get('city/list', [OtherDataController::class, 'getCityList']);
Route::get('radio/voter-choices/list', [OtherDataController::class, 'getRadioVoterChoices']);

Route::get('radio/address-type/list', [AddressDataController::class, 'getRadioAddressType']);
Route::get('radio/temporary-type/list', [AddressDataController::class, 'getRadioTemporaryType']);

Route::get('employee-type/list', [EmploymentDataController::class, 'getRadioEmployeeType']);
Route::get('usual-occupation/list', [EmploymentDataController::class, 'getUsualOccupationList']);
Route::get('class-worker/list', [EmploymentDataController::class, 'getClassWorkerList']);
Route::get('work-affiliation/list', [EmploymentDataController::class, 'getWorkAffiliationList']);
Route::get('radio/place-work-type/list', [EmploymentDataController::class, 'getPlaceWorkType']);
Route::get('/monthly-income/list', [EmploymentDataController::class, 'getMonthlyIncomeList']);

Route::get('education-level/list', [EducationalDataController::class, 'getEducationLevel']);
Route::get('course/list', [EducationalDataController::class, 'getCourseList']);
Route::get('year-level/list', [EducationalDataController::class, 'getYearLevelList']);

Route::get('relationship/list', [FamilyDataController::class, 'getRelationshipTypeList']);

Route::get('document/list', [DocumentDataController::class, 'getDocumentFileList']);

Route::get('groups-and-affiliation/list', [GroupsAndAffiliationController::class, 'getGroupsAndAffiliationList']);

Route::get('alcohol-status/list', [MedicalHistoryController::class, 'getAlcoholStatus']);
Route::get('vaccine/list', [MedicalHistoryController::class, 'getVaccineList']);
Route::get('blood-type/list', [MedicalHistoryController::class, 'getBloodTypeList']);
Route::get('disease/list', [MedicalHistoryController::class, 'getDiseaseList']);
Route::get('height-type/list', [MedicalHistoryController::class, 'getHeightTypeList']);
Route::get('weight-type/list', [MedicalHistoryController::class, 'getWeightTypeList']);

Route::get('water-source/list', [HouseHoldController::class, 'getWaterSourceList']);
Route::get('land-ownership/list', [HouseHoldController::class, 'getLandOwnershipList']);
Route::get('conveniences-devices/list', [HouseHoldController::class, 'getPresenceList']);
Route::get('radio/residence-type/list', [HouseHoldController::class, 'getRadioResidenceType']);
Route::get('checkbox/internet-access/list', [HouseHoldController::class, 'getInternetAccess']);
Route::get('building-house-type/list', [HouseHoldController::class, 'getBuildingHouseType']);
Route::get('roof-materials/list', [HouseHoldController::class, 'getRoofList']);
Route::get('wall-materials/list', [HouseHoldController::class, 'getWallList']);
Route::get('building-house-repair/list', [HouseHoldController::class, 'getBuildingHouseRepair']);
Route::get('year-built/list', [HouseHoldController::class, 'getYearBuiltList']);
Route::get('floor-area/list', [HouseHoldController::class, 'getFloorArea']);
Route::get('lighting/list', [HouseHoldController::class, 'getLightingList']);
Route::get('cooking/list', [HouseHoldController::class, 'getCookingList']);
Route::get('house-status/list', [HouseHoldController::class, 'getHouseStatusList']);
Route::get('house-acquisition/list', [HouseHoldController::class, 'getHouseAcquisitionList']);
Route::get('house-financing-source/list', [HouseHoldController::class, 'getHouseFinancingSource']);
Route::get('monthly-rental/list', [HouseHoldController::class, 'getMonthlyRental']);
Route::get('lot-status/list', [HouseHoldController::class, 'getLotStatusList']);
Route::get('garbage-disposal/list', [HouseHoldController::class, 'getGarbageDisposal']);
Route::get('toilet-facility/list', [HouseHoldController::class, 'getToiletFacility']);
Route::get('radio/garage-and-parking-status/list', [HouseHoldController::class, 'getGarageAndParkingList']);
Route::get('radio/septic-tank-status/list', [HouseHoldController::class, 'getSepticTankStatusList']);

Route::get('house-keeper-type/list', [HouseKeeperController::class, 'getHouseKeeperType']);


Route::post('permit/template', [PermitTemplateController::class, 'store']);
Route::get('permit/template/{id}', [PermitTemplateController::class, 'show']);
Route::post('permit/template/delete', [PermitTemplateController::class, 'delete']);

Route::get('incident/type/list', [IncidentController::class, 'getIncidentTypeList']);
Route::get('incident/status/list', [IncidentController::class, 'getIncidentStatusList']);

Route::get('blotter/type/list', [BlotterAndComplainController::class, 'getBlotterTypeList']);
Route::get('blotter/status/list', [BlotterAndComplainController::class, 'getBlotterStatusList']);

