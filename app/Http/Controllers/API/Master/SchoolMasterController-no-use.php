<?php

namespace App\Http\Controllers\API\master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Masters\SchoolMasterReq;
use App\Models\Master\SchoolMaster;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SchoolMasterController extends Controller
{
    private $_mSchoolMasters;
    /**
     * | Created On-24-05-2023 
     * | Author-Anshu Kumar
     * | Created for school crud operations
     */
    public function __construct()
    {
        $this->_mSchoolMasters = new SchoolMaster();
    }

    /**
     * | Add Record
     */
    public function store(SchoolMasterReq $req)
    {
        try {
            $mUser = new User();
            $emailExistance = $mUser->getUserByEmail($req->email);
            if (collect($emailExistance)->isNotEmpty())
                throw new Exception("Email Already Existing");
            $fYear = getFinancialYear(Carbon::now()->format('Y-m-d'));
            $ipAddress = getClientIpAddress();
            $password = Hash::make($req->password);
            $userType = "Super Admin";
            $schoolReqs = [
                "first_name" => $req->firstName,
                "middle_name" => $req->middleName,
                "last_name" => $req->lastName,
                "mobile" => $req->mobile,
                "email" => $req->email,
                "password" => $password,
                "address" => $req->address,
                "school_code" => $req->schoolCode,
                "academic_year" => $fYear,
                "designation" => $userType,
                "role" => $userType,
                "user_type" => $userType,
                "ip_address" => $ipAddress
            ];
            DB::beginTransaction();
            $createdSchool = $this->_mSchoolMasters->store($schoolReqs);
            $useReqs = [
                "name" => $req->firstName . ' ' . $req->middleName . ' ' . $req->lastName,
                "user_type" => $userType,
                "email" => $req->email,
                "password" => $password,
                "c_password" => $password,
                "school_id" => $createdSchool['id'],
                "ip_address" => $ipAddress
            ];
            $mUser->store($useReqs);
            DB::commit();
            return responseMsgs(true, "Successfully Saved", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            DB::rollBack();
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    /**
     * | Update Record
     */
    public function edit(SchoolMasterReq $req)
    {
        $validator = Validator::make($req->all(), [                     // Validation Merged
            'id' => 'required|integer'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);

        try {
            $mUser = new User();
            $emailExistance = $mUser->getUserByEmail($req->email);
            if (collect($emailExistance)->isNotEmpty() && collect($emailExistance)->where('school_id', '!=', $req->id)->isNotEmpty())
                throw new Exception("Email Already Existing");
            $fYear = getFinancialYear(Carbon::now()->format('Y-m-d'));
            $ipAddress = getClientIpAddress();
            $password = Hash::make($req->password);
            $userType = "Super Admin";
            $school = $this->_mSchoolMasters::findOrFail($req->id);
            $schoolReqs = [
                "first_name" => $req->firstName,
                "middle_name" => $req->middleName,
                "last_name" => $req->lastName,
                "mobile" => $req->mobile,
                "email" => $req->email,
                "password" => $password,
                "address" => $req->address,
                "school_code" => $req->schoolCode,
                "academic_year" => $fYear,
                "designation" => $userType,
                "role" => $userType,
                "user_type" => $userType,
                "ip_address" => $ipAddress,
                "version_no" => $school->version_no + 1
            ];
            DB::beginTransaction();
            $school->update($schoolReqs);
            $user = $mUser->getUserBySchoolId($req->id);
            $useReqs = [
                "name" => $req->firstName . ' ' . $req->middleName . ' ' . $req->lastName,
                "user_type" => $userType,
                "email" => $req->email,
                "password" => $password,
                "c_password" => $password,
                "school_id" => $req->id,
                "ip_address" => $ipAddress
            ];
            $user->update($useReqs);
            DB::commit();
            return responseMsgs(true, "Successfully Updated", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            DB::rollBack();
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    /**
     * | Show Record By Id
     */
    public function show(Request $req)
    {
        $validator = Validator::make($req->all(), [                     // Validation Merged
            'id' => 'required|integer',

        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);

        try {
            $school = $this->_mSchoolMasters->getSchoolById($req->id);
            return responseMsgs(true, "School Details", remove_null($school), "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            DB::rollBack();
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    /**
     * | Retrieve all Schools
     */
    public function retrieveAll(Request $req)
    {
        try {
            $school = $this->_mSchoolMasters::all();
            $school = $school->sortByDesc('id')->values();
            return responseMsgs(true, "School Details", remove_null($school), "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            DB::rollBack();
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
}
