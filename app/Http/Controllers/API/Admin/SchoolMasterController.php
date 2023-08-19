<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Admin\SchoolMaster;
use Illuminate\Support\Carbon;
use Validator;
use Exception;
use DB;
use Illuminate\Support\Str;


/*
Created By : Lakshmi kumari 
Created On : 06-June-2023 
Code Status : Open 
*/

class SchoolMasterController extends Controller
{
    private $_mSchoolRegistration;

    public function __construct()
    {
        DB::enableQueryLog();
        $this->_mSchoolRegistration = new SchoolMaster();
    }

    /**
     * | Registration for schools 
     * | Description: This user will be Super Admin and can create Admin or others users and grant them menu permission.
     */
    public function registration(Request $req)
    {
        $file_name = '';
        $validator = Validator::make($req->all(), [
            'schoolName' => 'required|string',
            'contactPersonName' => 'required|string',
            'contactPersonMobile' => 'required|string',
            'contactPersonEmail' => 'required|email',
            'userName' => 'required|string',
            'address' => 'required|string',
            'pincode' => 'integer',
            'fax' => 'integer',
            'password' => 'required|string',
            'confirmPassword' => 'required|string'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            //validation on school name and email is existing
            $isExists = $this->_mSchoolRegistration->readSchoolRegGroup($req);
            if (collect($isExists)->isNotEmpty())
                throw new Exception("School Name & Email Already Existing");
            $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
            if ($req->password != $req->confirmPassword)
                throw new Exception("Password Not Matched");
            if ($req->schoolLogo != "") {
                $get_file_name = $req->userName . '-' . $req->schoolLogo->getClientOriginalName();
                $path = public_path('school/school-master/');
                $file_name = 'school/school-master/' . $req->userName . '-' . $req->schoolLogo->getClientOriginalName();
                $req->file('schoolLogo')->move($path, $get_file_name);
            }
            $metaReqs = [
                'school_name' => Str::title($req->schoolName),
                'user_name' => $req->userName,
                'school_code' => $req->schoolCode,
                'logo' => $file_name,
                'address' => $req->address,
                'pincode' => $req->pincode,
                'fax_no' => $req->fax,
                'contact_person_name' => $req->contactPersonName,
                'contact_person_mobile' => $req->contactPersonMobile,
                'contact_person_email' => $req->contactPersonEmail,
                'password' => Hash::make($req->password),
                'academic_year' => $fy,
                'ip_address' => getClientIpAddress(),
                'version_no' => 0
            ];
            $metaReqs = array_merge($metaReqs, [
                'json_logs' => trim(json_encode($metaReqs), ",")
            ]);
            $this->_mSchoolRegistration->store($metaReqs);
            $data = ["schoolName" => $req->schoolName, "userName" => $req->userName, "name" => $req->contactPersonName, "mobile" => $req->contactPersonMobile, "email" => $req->contactPersonEmail];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "School Registration Done Successfully", $data, "API_5.1", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_5.1", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    /**
     * | Login Super Admin 
     * | Description: This user will be Super Admin and can create Admin or others users and grant them menu permission.
     */
    public function login(Request $req)
    {
        //validation
        $validator = Validator::make($req->all(), [
            //'email' => 'required|email',
            'userName' => 'required',
            'password' => 'required'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            //check email existing or not
            $mSchoolMaster = SchoolMaster::where('user_name', $req->userName)->first();
            $rolId = $mSchoolMaster->role_id;

            if ($rolId == '' || $rolId == 0) {
                $msg = "Oops! You Have Not Permission To Access This ERP";
                return responseMsg(false, $msg, "");
            }
            $mSchoolMasterRole = DB::table('roles')
                ->select(DB::raw("id,role_name"))
                ->where('id', $rolId)
                ->where('status', 1)
                ->first();
            $roleName =    $mSchoolMasterRole->role_name;



            if (!$mSchoolMaster) {
                $msg = "Oops! Given username does not exist";
                return responseMsg(false, $msg, "");
            }
            // check if user deleted or not
            if ($mSchoolMaster->status == 0) {
                $msg = "Cant logged in!! You Have Been Suspended or Deleted !";
                return responseMsg(false, $msg, "");
            }
            //check if user and password is existing  
            if ($mSchoolMaster && Hash::check($req->password, $mSchoolMaster->password)) {
                $token = $mSchoolMaster->createToken('auth_token')->plainTextToken;
                $mSchoolMaster->remember_token = $token;
                $mSchoolMaster->save();
                $data1 = ['name' => $mSchoolMaster->school_name, 'contact person name' => $mSchoolMaster->contact_person_name, 'userName' => $mSchoolMaster->user_name, 'email' => $mSchoolMaster->contact_person_email, 'token' => $token, 'token_type' => 'Bearer', 'roleName' => $roleName, 'roleId' => $mSchoolMaster->role_id];
                //return responseMsgsT(true, "Login successfully", $data1, "API-5.2", "", responseTime(), "POST", $req->deviceId ?? "", $token);
            } else
                throw new Exception("Password is incorrect");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            if (!$data1)
                throw new Exception("Record Not Found!");
            return responseMsgsT(true, "Login Successfully", $data1, "API_5.2", $queryTime, responseTime(), "POST", $req->deviceId ?? "", $token);
        } catch (Exception $e) {
            return responseMsgsT(false, $e->getMessage(), "", "API_5.2", "", "", "post", "", "");
        }
    }

    /**
     * | Search User Name 
     * | Description: Search user name at the time of registration 
     */
    public function searchUserName(Request $req)
    {
        $msg = 'User Name is Available';
        $validator = Validator::make($req->all(), [
            'userName' => 'string'
        ]);
        if ($validator->fails()) {
            return responseMsgs(false, $validator->errors(), []);
        }
        try {
            $isExists = SchoolMaster::where('user_name', $req->userName)->first();
            if (collect($isExists)->isNotEmpty())
                $msg = "User Name Already Existing!";
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View Search Records", $msg, "API_5.3", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_5.3", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //View by id
    public function show(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'id' => 'required|numeric'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $data = $this->_mSchoolRegistration->getGroupById($req->id);
            if (collect($data)->isEmpty())
                throw new Exception("Data Not Found");
            $data = ['userName' => $data->user_name, 'schoolName' => $data->school_name, 'contactPersonName' => $data->contact_person_name, 'mobile' => $data->contact_person_mobile];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View Details", $data, "API_5.4", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_5.4", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    /**
     * | Change Password 
     * | Description: Change password of authenticate user's using sanctum token
     */
    public function changePassword(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'userName' => 'required',
            'password' => 'required'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $data = $this->_mSchoolRegistration->updatePassword($req);
            if (!$data)
                throw new Exception("Record Not Found!");
            $data = ['userName' => $data->user_name, 'schoolName' => $data->school_name, 'contactPersonName' => $data->contact_person_name, 'mobile' => $data->contact_person_mobile];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Password Changed Successfully", $data, "API_5.6", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), "", "API_5.6", "", "", "", $req->deviceId ?? "");
        }
    }

    /**
     * | Update Profile
     */
    public function edit(Request $req)
    {
        $file_name = '';
        $validator = Validator::make($req->all(), [
            'id' => 'required|numeric',
            // 'schoolName' => 'required|string',
            'schoolCode' => 'string',
            'schoolLogo' => 'mimes:jpg,png,jpeg',
            'contactPersonName' => 'string',
            'contactPersonMobile' => 'string',
            'contactPersonEmail' => 'email'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $getData = $this->_mSchoolRegistration::findOrFail($req->id);
            if ($req->schoolLogo != "") {
                $get_file_name = $getData->user_name . '-' . $req->schoolLogo->getClientOriginalName();
                $path = public_path('school/school-master/');
                $file_name = 'school/school-master/' . $getData->user_name . '-' . $req->schoolLogo->getClientOriginalName();
                $req->file('schoolLogo')->move($path, $get_file_name);
            }
            $metaReqs = [
                'school_code' => $req->schoolCode,
                'contact_person_name' => $req->contactPersonName,
                'contact_person_mobile' => $req->contactPersonMobile,
                'contact_person_email' => $req->contactPersonEmail,
                'logo' => $file_name,
                'version_no' => $getData->version_no + 1,
                'updated_at' => Carbon::now()
            ];
            $metaReqs = array_merge($metaReqs, [
                'json_logs' => trim($getData->json_logs . "," . json_encode($metaReqs), ",")
            ]);
            if (isset($req->status)) {                          // In Case of Deactivation or Activation 
                $status = $req->status == 'deactive' ? 0 : 1;
                $metaReqs = array_merge($metaReqs, [
                    'status' => $status
                ]);
            }
            $data = $this->_mSchoolRegistration::findOrFail($req->id);
            $data->update($metaReqs);
            if (!$data)
                throw new Exception("Record Not Found!");
            $data = ['schoolCode' => $req->schoolCode, 'contactPersonName' => $req->contactPersonName, 'contactPersonMobile' => $req->contactPersonMobile, 'contactPersonEmail' => $req->contactPersonEmail];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Successfully Updated", $data, "API_5.7", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_5.7", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    /**
     * | Logout  
     * | Description: logout using token and email address
     */
    public function logout(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'userName' => 'required|email',
            'token' => 'required'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $data = $this->_mSchoolRegistration->readLogout($req);
            if (!$data)
                throw new Exception("Record Not Found!");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Logout Successfully", $req->userName, "API_5.8", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), "", "API_5.8", "", "", "", $req->deviceId ?? "");
        }
    }

    //View All
    public function retrieveAll(Request $req)
    {
        try {
            $getData = $this->_mSchoolRegistration->retrieve();
            $perPage = $req->perPage ? $req->perPage : 10;
            DB::enableQueryLog();
            $paginater = $getData->paginate($perPage);
            $list = [
                "current_page" => $paginater->currentPage(),
                "perPage" => $perPage,
                "last_page" => $paginater->lastPage(),
                "data" => $paginater->items(),
                "total" => $paginater->total()
            ];
            if (!$getData)
                throw new Exception("Record Not Found!");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View All List", $list, "API_5.9", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
            // return responseMsgs(true, "", $list, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_5.9", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //Active All
    public function activeAll(Request $req)
    {
        try {
            $getData = $this->_mSchoolRegistration->active();
            if (!$getData)
                throw new Exception("Record Not Found!");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View All List",  $getData, "API_5.10", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_5.10", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //delete
    public function delete(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'status' => 'required|in:active,deactive'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            if (isset($req->status)) {                  // In Case of Deactivation or Activation
                $status = $req->status == 'deactive' ? 0 : 1;
                $metaReqs =  [
                    'status' => $status
                ];
            }
            $delete = $this->_mSchoolRegistration::findOrFail($req->id);
            $delete->update($metaReqs);
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Changes Done Successfully",  [], "API_5.11", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_5.11", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    public function updateRole(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'id' => 'required|numeric',
            'roleId' => 'required|numeric'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $getData = $this->_mSchoolRegistration::findOrFail($req->id);
            $metaReqs = [
                'role_id' => $req->roleId,
                'version_no' => $getData->version_no + 1,
                'updated_at' => Carbon::now()
            ];
            $metaReqs = array_merge($metaReqs, [
                'json_logs' => trim($getData->json_logs . "," . json_encode($metaReqs), ",")
            ]);
            $getData->update($metaReqs);
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Successfully Updated", [], "API_5.12", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_5.12", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
}
