<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Admin\User;
use App\Models\Admin\SchoolMaster;
use Validator;
use Auth;
use Exception;

/*
Created By : Lakshmi kumari 
Created On : 03-Apr-2023 
Code Status : Open 
*/

class UserController extends Controller
{
    /**
     * | Registration for users 
     * | Description: This user will Admin or others and can create or view application activity.
     */
    public function register(Request $req)
    {
        //validation
        $validator = Validator::make($req->all(), [
            'name' => 'required|string|max:30',
            'email' => 'required|email|unique:users|max:100',
            'password' => 'required|string|max:30'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $mObject = new User();
            $data = $mObject->insertData($req);
            // $data1 = ["name" => $req->name, "email" =>$req->email, "password" =>$req->password, "UserId"=>$data->$genUserID];
            return responseMsgs(true, "User Registration Done Successfully", [], "", "API-1.1", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API-1.1", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    /**
     * | Login for users 
     * | Description: Login of a user with sanctum token.
     */
    public function login(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            //check email existing or not
            $mUser = User::where('email', $req->email)->first();
            $mDeviceId = $req->deviceId ?? "";
            if (!$mUser) {
                $msg = "Oops! Given email does not exist";
                return responseMsg(false, $msg, "");
            }

            // check if user deleted
            if ($mUser->is_deleted == 1) {
                $msg = "Cant logged in!! You Have Been Suspended or Deleted !";
                return responseMsg(false, $msg, "");
            }

            //check if user and password is existing   
            if ($mUser && Hash::check($req->password, $mUser->password)) {
                $token = $mUser->createToken('auth_token')->plainTextToken;
                $mUser->remember_token = $token;
                $mUser->save();
                $data1 = ['name' => $mUser->name, 'userType' => $mUser->user_type, 'email' => $req->email, 'token' => $token, 'token_type' => 'Bearer'];
                return responseMsgsT(true, "Login successfully", $data1, "API-1.2", "", "186ms", "post", $mDeviceId, $token);
            } else
                throw new Exception("Password is incorrect");
        } catch (Exception $e) {
            return responseMsgsT(false, $e->getMessage(), "", "API-1.2", "", "", "post", "", "");
        }
    }

    /**
     * | View 
     */
    public function profile(Request $req)
    {
        try {
            $userProfile = new User();
            $data  = $userProfile->viewProfile($req);
            return responseMsgs(true, "View profile", $data, "", "API-1.3", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API-1.3", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    /**
     * | Edit 
     */
    public function editProfile(Request $req)
    {
        try {
            $mObject = new User();
            $data = $mObject->updateProfile($req);
            return responseMsgs(true, "Records updated successfully", $data, "", "API-1.4", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API-1.4", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    /**
     * | Delete 
     */
    public function deleteProfile(Request $req)
    {
        //Description: delete of authenticate user's profile using sanctum token
        try {
            $mObject = new User();
            $data = $mObject->deleteData($req);
            return responseMsgs(true, "Records deleted successfully", $data, "", "API-1.5", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API-1.5", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    /**
     * | Change Password 
     */
    public function changePassword(Request $req)
    {
        //Description: Change password of authenticate user's using sanctum token
        try {
            $mObject = new User();
            $data = $mObject->updatePassword($req);
            return responseMsgs(true, "Password changed successfully", $data, "", "API-1.6", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API-1.6", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    /**
     * | Logout
     */
    public function logout()
    {
        try {
            $id = auth()->user()->id;
            //user master logout
            $user = User::where('id', $id)->first();
            $user->remember_token = null;
            $user->save();
            $user->tokens()->delete();

            //school master logout
            $school = SchoolMaster::where('id', $id)->first();
            $school->remember_token = null;
            $school->save();
            $school->tokens()->delete();
            return responseMsgs(true, "Logged out successfully", "", "API-1.7", "", responseTime(), "POST", "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API-1.7", responseTime(), "POST", "");
        }
    }
}

//================================================= End User API ===========================================================
