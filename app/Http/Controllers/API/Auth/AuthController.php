<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Auth\Auth;
use Illuminate\Support\Carbon;
use Validator;
use Exception;
use DB;

/*
Created By : Lakshmi kumari 
Created On : 03-July-2023 
Code Status : Open 
*/

class AuthController extends Controller
{
    private $_mAuth;

    public function __construct()
    {
        DB::enableQueryLog();
        $this->_mAuth = new Auth();
    }

    /**
     * | Login Super Admin 
     * | Description: This user will be Super Admin and can create Admin or others users and grant them menu permission.
     */
    public function login(Request $req)
    {
        //validation
        $validator = Validator::make($req->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            //check email existing or not
            $mAuth = Auth::where('email', $req->email)->first();
            $rolId = $mAuth->role_id;


            $mAuthRole = DB::table('roles')
                ->select(DB::raw("id,role_name"))
                ->where('id', $rolId)
                ->where('status', 1)
                ->first();
            $roleName =    $mAuthRole->role_name;
            if (!$mAuth) {
                $msg = "Oops! Given email does not exist";
                return responseMsg(false, $msg, "");
            }
            // check if user deleted or not
            if ($mAuth->status == 0) {
                $msg = "Cant logged in!! You Have Been Suspended or Deleted !";
                return responseMsg(false, $msg, "");
            }
            //check if user and password is existing  
            if ($mAuth && Hash::check($req->password, $mAuth->password)) {
                $token = $mAuth->createToken('auth_token')->plainTextToken;
                $mAuth->remember_token = $token;
                $mAuth->save();
                $data1 = ['name' => $mAuth->name, 'email' => $req->email, 'token' => $token, 'token_type' => 'Bearer', 'roleName' => $roleName, 'roleId' => $mAuth->role_id];
            } else
                throw new Exception("Password is incorrect");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            if (!$data1)
                throw new Exception("Record Not Found!");
            return responseMsgsT(true, "Login Successfully", $data1, "API_14.1", $queryTime, responseTime(), "POST", $req->deviceId ?? "", $token);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), "", "API_14.1", "", "", "", $req->deviceId ?? "");
        }
    }

    /**
     * | Change Password 
     * | Description: Change password of authenticate user's using sanctum token
     */
    public function changePassword(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $data = $this->_mAuth->updatePassword($req);
            if (!$data)
                throw new Exception("Record Not Found!");
            $data = ['name' => $data->name, 'email' => $data->email, 'token' => $data->remember_token, 'token_type' => 'Bearer'];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Password Changed Successfully", $data, "API_14.2", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), "", "API_14.2", "", "", "", $req->deviceId ?? "");
        }
    }

    /**
     * | Logout  
     * | Description: logout using token and email address
     */
    public function logout(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'email' => 'required|email',
            'token' => 'required'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $data = $this->_mAuth->readLogout($req);
            if (!$data)
                throw new Exception("Record Not Found!");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Logout Successfully", $req->email, "API_14.3", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), "", "API_14.3", "", "", "", $req->deviceId ?? "");
        }
    }

    /**
     * | Get Discont Group By Id
     */
    public function show(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'id' => 'required|numeric'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $data = $this->_mAuth->getGroupById($req->id);
            if (collect($data)->isEmpty())
                throw new Exception("Data Not Found");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View Details", $data, "API_14.4", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), "", "API_14.4", "", "", "", $req->deviceId ?? "");
        }
    }
}
