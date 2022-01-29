<?php

namespace App\Http\Controllers;

use Helpers;
use Session;
use Validator;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\IncidentData;
use App\Models\IncidentType;
use App\Models\User as UserModel;

class IncidentController extends Controller
{
    public function countIncident(Request $request) {
        $incidentCount = IncidentData::where("mark_as_read", "!=", 1)->count();
        $display = [
            'notification_count' => $incidentCount
        ];

        return customResponse()
            ->message("Incident Count.")
            ->data($display)
            ->success()
            ->generate();
    }

    public function incidentList(Request $request) {
        $incidentList = IncidentData::select(
            'incident_data.id',
            'incident_data.user_id',
            'users.first_name',
            'users.last_name',
            'incident_data.incident_type_id',
            'incident_type.description as incident_type_desc',
            'incident_data.incident_message',
            'incident_data.incident_address',
            'incident_data.incident_latitude',
            'incident_data.incident_longitude',
            'incident_data.created_at as incident_date',
            'incident_data.mark_as_read'
        )
        ->join('users', 'users.id', 'incident_data.user_id')
        ->join('incident_type', 'incident_type.id', 'incident_data.incident_type_id')
        ->orderBy("incident_data.created_at", "desc");

        if ($request->search) {
            $incidentList = $incidentList->where(function($q) use($request){
                $q->orWhereRaw("CONCAT_WS(' ',CONCAT(last_name,','),first_name,first_name) LIKE ?","%".$request->search."%");
            });
        }

        if ($request->incident_type_id) {
            $incidentList = $incidentList->where("incident_data.incident_type_id", $request->incident_type_id);
        }

        if ($request->barangay_id) {
            $incidentList = $incidentList->where("users.barangay_id", $request->barangay_id);
        }

        $incidentList = $incidentList->paginate(
            (int) $request->get('per_page', 10),
            ['*'],
            'page',
            (int) $request->get('page', 1)
        );

        return customResponse()
            ->message("Incident list.")
            ->data($incidentList)
            ->success()
            ->generate();
    }

    public function list(Request $request, $id) {
        $incidentList = IncidentData::select(
            'incident_data.id',
            'incident_data.user_id',
            'users.first_name',
            'users.last_name',
            'incident_data.incident_type_id',
            'incident_type.description as incident_type_desc',
            'incident_data.incident_message',
            'incident_data.incident_address',
            'incident_data.incident_latitude',
            'incident_data.incident_longitude',
            'incident_data.created_at as incident_date',
            'incident_data.mark_as_read'
        )
        ->join('users', 'users.id', 'incident_data.user_id')
        ->join('incident_type', 'incident_type.id', 'incident_data.incident_type_id')
        ->where("incident_data.user_id", $id)
        ->orderBy("incident_data.created_at", "desc");

        if ($request->incident_type_id) {
            $userList = $userList->where("incident_data.incident_type_id", $request->incident_type_id);
        }

        $incidentList = $incidentList->paginate(
            (int) $request->get('per_page', 10),
            ['*'],
            'page',
            (int) $request->get('page', 1)
        );

        return customResponse()
            ->message("Incident list.")
            ->data($incidentList)
            ->success()
            ->generate();
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(),[
            'incident_type_id' => 'required',
            'incident_message' => 'required'
        ]);

        if($validator->fails()){
            return customResponse()
                ->data(null)
                ->message($validator->errors()->all()[0])
                ->failed()
                ->generate();
        }

        $userData = $request->user();
        if (!empty($request->user_id)) {
            $userData = UserModel::find($request->user_id);
        }

        $incidentData = IncidentData::find($request->incident_id);
        if (empty($incidentData)) {
            $incidentData = new IncidentData;
        }

        $incidentData->user_id = $userData->id;
        $incidentData->incident_type_id = $request->incident_type_id;
        $incidentData->incident_message = $request->incident_message;
        $incidentData->incident_address = $request->incident_address;
        $incidentData->incident_latitude = $request->incident_latitude;
        $incidentData->incident_longitude = $request->incident_longitude;
        $incidentData->save();

        return customResponse()
            ->data(null)
            ->message('Record has been saved.')
            ->success()
            ->generate(); 
    }

    public function markAsRead(Request $request, $id) {
        $incidentData = IncidentData::find($id);
        if (empty($incidentData)) {
            return customResponse()
                ->message("No data.")
                ->data(null)
                ->success()
                ->generate();
        }

        $incidentData->mark_as_read = 1;
        $incidentData->save();

        return customResponse()
            ->message("Record has been updated.")
            ->data(null)
            ->success()
            ->generate();
    }

    public function show(Request $request, $id) {
        $incidentData = IncidentData::select(
            'incident_data.id',
            'incident_data.user_id',
            'users.first_name',
            'users.last_name',
            'incident_data.incident_type_id',
            'incident_type.description as incident_type_desc',
            'incident_data.incident_message',
            'incident_data.incident_address',
            'incident_data.incident_latitude',
            'incident_data.incident_longitude',
            'incident_data.created_at as incident_date',
            'incident_data.mark_as_read'
        )
        ->join('users', 'users.id', 'incident_data.user_id')
        ->join('incident_type', 'incident_type.id', 'incident_data.incident_type_id')
        ->find($id);

        if (empty($incidentData)) {
            return customResponse()
                ->message("No data.")
                ->data(null)
                ->success()
                ->generate();
        }

        return customResponse()
            ->message("Incident data.")
            ->data($incidentData)
            ->success()
            ->generate();
    }

    public function destroy(Request $request, $id) {
        $incidentData = IncidentData::find($id);
        if (empty($incidentData)) {
            return customResponse()
                ->message("No data.")
                ->data(null)
                ->success()
                ->generate();
        }

        $incidentData->delete();

        return customResponse()
            ->message("Record has been deleted.")
            ->data(null)
            ->success()
            ->generate();
    }

    public function getIncidentTypeList(Request $request) {
        $list = IncidentType::select(
            'id',
            'description'
        )
        ->get();

        return customResponse()
            ->message("List of incident report type.")
            ->data($list)
            ->success()
            ->generate();
    }

}
