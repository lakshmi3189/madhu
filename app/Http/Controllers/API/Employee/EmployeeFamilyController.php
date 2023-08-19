<?php

namespace App\Http\Controllers\API\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeFamily;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeFamilyController extends Controller
{
    //
    //
    private $_mEmployeeFamily;

    public function __construct()
    {
        DB::enableQueryLog();
        $this->_mEmployeeFamily = new EmployeeFamily();
    }

    public function store(Request $req)
    {

        $validator = Validator::make($req->all(), [
            'empNo'                   => 'required',
            'fMemberName'             => 'nullable|string',
            'fMemberRelation'         => 'nullable|string',
            'fMemberDob'              => 'nullable|string',
            'uploadFMemberImage'      => 'nullable|file', 
        ]);
        
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $metaReqs = array();
            $file_name = '';
            $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
            $timestamp = now()->timestamp; 
            $empId = Employee::where('emp_no', $req->empNo)->pluck('id')->first();

            $isGroupExists = $this->_mEmployeeFamily->readFamilyGroup($empId,$req);
            if (collect($isGroupExists)->isNotEmpty())
                throw new Exception("Family Member Already Existing");

            if ($req->uploadFMemberImage != "") {
                $uploadFMemberImage = $req->uploadFMemberImage;
                $fam_file_name =$req->positionHead. '-'.$timestamp . '-' . $uploadFMemberImage->getClientOriginalName();
                $path = public_path('school/employees/'.$req->empNo);
                $famFile_name = 'school/employees/' . $req->empNo.'/'.$fam_file_name;
                $uploadFMemberImage->move($path, $fam_file_name);
                $metaReqs = [
                    'upload_f_member_image' => $famFile_name,
                ];
            }
            // return $metaReqs; die; 
            $metaReqs = array_merge( $metaReqs,  [
                "emp_tbl_id"        =>  $empId,
                "f_member_name"       =>  $req['fMemberName'],
                "f_member_relation_name"     =>  $req['fMemberRelation'],
                "f_member_dob"       =>  $req['fMemberDob'],
                'academic_year' => $fy,
                "school_id" => authUser()->school_id,
                "created_by" => authUser()->id,
                "ip_address" => getClientIpAddress(),
                "version_no" => 0                        
            ]);
            
            // return $metaReqs; die; 
            $this->_mEmployeeFamily->store($metaReqs);

            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Records Added Successfully", $metaReqs, "API_8.1", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_8.1", responseTime(), "POST", $req->deviceId ?? "");
        }
    }


    /**
     * | View data
     */
    public function retrieveAll(Request $req)
    {
        try {
            // $empId = Employee::where('emp_no', $req->empNo)->pluck('id')->first(); 

            $getData = $this->_mEmployeeFamily->retrieve($req);
          
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View All Records", $getData, "API_1.0", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    public function edit(Request $req)
    {
        $educationRules = [
            'id'                      => 'required|numeric',
            'empNo'                   => 'required',
            'fMemberName'             => 'nullable|string',
            'fMemberRelation'         => 'nullable|string',
            'fMemberDob'              => 'nullable|string',
            'uploadFMemberImage'      => 'nullable|file', 
        ];

        $validator = Validator::make($req->all(), $educationRules);
        if ($validator->fails()) {
            return responseMsgs(false, $validator->errors()->first(), [], "", "API_1.0", responseTime(), "POST", $req->deviceId?? "");
        }
        try{
            $timestamp = now()->timestamp; 
            $getData =  $this->_mEmployeeFamily::where(['id' => $req->id], ['status' => 1])->first();
            // return $getData->id; die; 
            $empId = Employee::where('id', $getData->emp_tbl_id)->pluck('emp_no')->first();
           
            $metaReqs = array();
            if ($req->uploadFMemberImage != "") {
                $uploadFMemberImage = $req->uploadFMemberImage;
                $fam_file_name =$req->positionHead. '-'.$timestamp . '-' . $uploadFMemberImage->getClientOriginalName();
                $path = public_path('school/employees/'.$empId);
                $famFile_name = 'school/employees/' . $empId.'/'.$fam_file_name;
                $uploadFMemberImage->move($path, $fam_file_name);
                $metaReqs = [
                    'upload_f_member_image' => $famFile_name,
                ];
            }
            // return $metaReqs; die; 
            $metaReqs = array_merge( $metaReqs,  [
                // "emp_tbl_id"        =>  $empId,
                "f_member_name"       =>  $req['fMemberName'],
                "f_member_relation_name"     =>  $req['fMemberRelation'],
                "f_member_dob"       =>  $req['fMemberDob'],  
                'version_no' => $getData->version_no + 1,                    
            ]);
            // return $metaReqs; die; 

            if (isset($req->status)) {                  // In Case of Deactivation or Activation 
                $status = $req->status == 'deactive' ? 0 : 1;
                $metaReqs = array_merge($metaReqs, [
                    'status' => $status
                ]);
            }
            $getData->update($metaReqs);
            $result['experienceDetails'] = $metaReqs;

            return responseMsgs(true, "Successfully Updated", $result, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
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
            // $Banks = $this->_mBanks::findOrFail($req->id);
            $show = $this->_mEmployeeFamily->getGroupById($req->id);
            if (collect($show)->isEmpty())
                throw new Exception("Record Not Found!");
            return responseMsgs(true, "", $show, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //Activate / Deactivate
    public function delete(Request $req)
    {
        $validator = Validator::make($req->all(), [
            // 'status' => 'in:active,deactive'
            'id' => 'required|numeric',
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            
            $delete = $this->_mEmployeeFamily::findOrFail($req->id);
            // $delete->update($metaReqs);
            $delete->delete();
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Delete Successfully", $req->id, "M_API_2.5", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_2.5", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
}
