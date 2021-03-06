<?php

namespace App\Http\Controllers;

use Hash;
use Helpers;
use Session;
use Validator;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\User;

use App\Classes\UserClass;
use App\Classes\UserActivityLogClass;
use App\Classes\DocumentDataClass;

use App\Models\SessionToken;
use App\Models\User as UserModel;
use App\Models\Barangay;
use App\Models\UserType;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'last_name' => 'required|string',
            'first_name' => 'required|string',
            'email' => 'required|string|unique:users|confirmed',
            'contact_no' => 'required|digits:10',
            'gender' => 'required',
            'birth_date' => 'string',
            'address' => 'required|string',
            'barangay_id' => 'required',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return customResponse()
                ->data(null)
                ->message($validator->errors()->all()[0])
                ->failed()
                ->generate();
        }

        $user = new User([
            'last_name' => $request->last_name,
            'first_name' => $request->first_name,
            'email' => $request->email,
            'contact_no' => $request->contact_no,
            'gender' => strtoupper($request->gender),
            'birth_date' => date("Y-m-d", strtotime($request->birth_date)),
            'address' => $request->address,
            'barangay_id' => $request->barangay_id,
            'password' => Hash::make($request->password),
            'user_type_id' => 6
        ]);

        $user->save();
        $request->user_id = $user->id;

        if (!empty($request->is_residence)) {
            $class = new DocumentDataClass;
            $class->saveDocumentData($request);
        }

        return customResponse()
            ->data(null)
            ->message('Successfully created user.')
            ->success()
            ->generate();
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        if ($validator->fails()) {
            return customResponse()
                ->data(null)
                ->message($validator->errors()->all()[0])
                ->failed()
                ->generate();
        }

        $credentials = [
            "email" => $request->email,
            "password" => $request->password
        ];

        if(!Auth::attempt($credentials)) {
            return customResponse()
                ->data(null)
                ->message('Invalid credentials.')
                ->unauthorized()
                ->generate();
        }

        $user = $request->user();
        if (empty($user->is_active)) {
            Auth::logout();
            return customResponse()
                ->data(null)
                ->message('Your account is deactivated.')
                ->unauthorized()
                ->generate();
        }

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
            
        $token->save();

        $eventType = UserActivityLogClass::EVENT_LOGIN;
        $userActivityLog = new UserActivityLogClass;
        $userActivityLog->insert(Auth::user()->id, $eventType);

        $request->session()->regenerate();

        // $params = array(
        //     'user_id' => Auth::user()->id,
        //     'session_id' => Session::getId(),
        //     'token' => $tokenResult->plainTextToken
        // );

        // SessionToken::updateOrCreate(
        //     [
        //         'user_id' => Auth::user()->id
        //     ],
        //     [
        //         'user_id' => Auth::user()->id,
        //         'session_id' => Session::getId(),
        //         'token' => ''
        //     ]
        // );

        // return customResponse()
        //     ->data([
        //         'barangay' => (!empty($user->barangayData->description) ? $user->barangayData->description : NULL),
        //         'user' => $user,
        //         'access_token' => $tokenResult->plainTextToken,
        //         'token_type' => 'Bearer',
        //         'expires_at' => Carbon::parse(
        //             Carbon::now()->addDays(1)
        //         )->toDateTimeString()
        //     ])
        //     ->message('You have successfully logged in.')
        //     ->success()
        //     ->generate();

        return customResponse()
            ->message('You have successfully logged in.')
            ->data([
                'barangay' => (!empty($user->barangayData->description) ? $user->barangayData->description : NULL),
                'user' => $user,
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ])
            ->success()
            ->generate();
    }

    public function logout(Request $request) {
        $request->user()->token()->revoke();
        // $request->user()->tokens()->delete();
        // $request->session()->invalidate();
        // $request->user()->sessionToken->delete();

        return customResponse()
            ->data(null)
            ->message('Successfully logged out.')
            ->success()
            ->generate();
    }

    public function resetPassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'password' => 'required|string|confirmed'
        ]);

        if ($validator->fails()) {
            return customResponse()
                ->data(null)
                ->message($validator->errors()->all()[0])
                ->failed()
                ->generate();
        }

        $user = UserModel::find($request->user_id);
        $user->password = Hash::make($request->password);
        $user->save();

        return customResponse()
            ->data(null)
            ->message('Password has been reset.')
            ->success()
            ->generate();
    }

    public function changePassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'old_password' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);

        if ($validator->fails()) {
            return customResponse()
                ->data(null)
                ->message($validator->errors()->all()[0])
                ->failed()
                ->generate();
        }

        $user = UserModel::find($request->user_id);
        if (!(Hash::check($request->old_password, $user->password))) {
            return customResponse()
                ->data(null)
                ->message('Current password did not match.')
                ->failed()
                ->generate();
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return customResponse()
            ->data(null)
            ->message('Password has been changed.')
            ->success()
            ->generate();
    }

    public function passwordValidation(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return customResponse()
                ->data(null)
                ->message($validator->errors()->all()[0])
                ->failed()
                ->generate();
        }

        $user = UserModel::find($request->user_id);
        if (empty($user)) {
            return customResponse()
                ->data(null)
                ->message("User not found.")
                ->failed()
                ->generate();
        }

        if (!(Hash::check($request->password, $user->password))) {
            return customResponse()
                ->data(null)
                ->message('Incorrect password.')
                ->failed()
                ->generate();
        }

        return customResponse()
            ->data(null)
            ->message('Validated.')
            ->success()
            ->generate();
    }

    public function list(Request $request){
        $class = new UserClass;
        $userList = $class->getUserList($request);

        return customResponse()
            ->message("User account list.")
            ->data($userList)
            ->success()
            ->generate();
    }

    public function show(Request $request, $id){
        $userData = UserModel::find($id);

        return customResponse()
            ->message("User account data.")
            ->data($userData)
            ->success()
            ->generate();
    }

    public function user(Request $request){
        return customResponse()
            ->message("User data.")
            ->data($request->user())
            ->success()
            ->generate();
    }

    public function getBarangayList(Request $request) {
        $barangayList = Barangay::select(
            "id",
            "description"
        )->fromBarangaySystem()->get();

        return customResponse()
            ->message("List of barangay.")
            ->data($barangayList)
            ->success()
            ->generate();
    }

    public function getUserTypeList(Request $request) {
        $userTypeList = UserType::select(
            "id",
            "name",
            "level"
        )->get();

        return customResponse()
            ->message("List of user type.")
            ->data($userTypeList)
            ->success()
            ->generate();
    }
}
