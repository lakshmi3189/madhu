<?php

namespace App\Http\Controllers\API\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeExperience;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeExperienceController extends Controller
{
    //
    private $_mEmployeeExperiences;

    public function __construct()
    {
        DB::enableQueryLog();
        $this->_mEmployeeExperiences = new EmployeeExperience();
    }

    public function store(Request $req)
    {

        $validator = Validator::make($req->all(), [
            'empNo'                => 'required',
            'nameOfOrg'            => 'nullable|string',
            'positionHead'         => 'nullable|string',
            'periodFrom'           => 'nullable|string',
            'periodTo'             => 'nullable|string',
            'salary'               => 'nullable|numeric',
            'payGrade'             => 'nullable|string',
            'uploadExpLetterDocs'  => 'nullable|file',
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $metaReqs = array();
            $file_name = '';
            $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
            $timestamp = now()->timestamp; 
            $empId = Employee::where('emp_no', $req->empNo)->pluck('id')->first();

            $isGroupExists = $this->_mEmployeeExperiences->readExperienceGroup($empId,$req);
            if (collect($isGroupExists)->isNotEmpty())
                throw new Exception("Experience Already Existing");

            if ($req->uploadExpLetterDocs != "") {
                $uploadExpLetterDocs = $req->uploadExpLetterDocs;
                $exp_file_name =$req->positionHead. '-'.$timestamp . '-' . $uploadExpLetterDocs->getClientOriginalName();
                $path = public_path('school/employees/'.$req->empNo);
                $expFile_name = 'school/employees/' . $req->empNo.'/'.$exp_file_name;
                $uploadExpLetterDocs->move($path, $exp_file_name);
                $metaReqs = [
                    'upload_exp_letter' => $expFile_name,
                ];
            }
            // return $metaReqs; die; 
            $metaReqs = array_merge( $metaReqs,  [
                "emp_tbl_id"        =>  $empId,
                "name_of_org"       =>  $req['nameOfOrg'],
                "position_head"     =>  $req['positionHead'],
                "period_from"       =>  $req['periodFrom'],
                "period_to"         =>  $req['periodTo'],
                "salary"            =>  $req['salary'],
                "pay_grade"         =>  $req['payGrade'],
                "school_id" => authUser()->school_id,
                "created_by" => authUser()->id,
                "ip_address" => getClientIpAddress(),
                "version_no" => 0                        
            ]);
            
            // return $metaReqs; die; 
            $this->_mEmployeeExperiences->store($metaReqs);

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

            $getData = $this->_mEmployeeExperiences->retrieve($req);
          
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View All Records", $getData, "API_1.0", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    public function edit(Request $req)
    {
        $educationRules = [
            'id'                   => 'required|numeric',
            'nameOfOrg'            => 'nullable|string',
            'positionHead'         => 'nullable|string',
            'periodFrom'           => 'nullable|string',
            'periodTo'             => 'nullable|string',
            'salary'               => 'nullable|numeric',
            'payGrade'             => 'nullable|string',
            'uploadExpLetterDocs'  => 'nullable|file',
        ];

        $validator = Validator::make($req->all(), $educationRules);
        if ($validator->fails()) {
            return responseMsgs(false, $validator->errors()->first(), [], "", "API_1.0", responseTime(), "POST", $req->deviceId?? "");
        }
        try{
            $timestamp = now()->timestamp; 
            $getData =  $this->_mEmployeeExperiences::where(['id' => $req->id], ['status' => 1])->first();
            // return $getData->id; die; 
            $empId = Employee::where('id', $getData->emp_tbl_id)->pluck('emp_no')->first();
           
            $metaReqs = array();
            if ($req->uploadExpLetterDocs != "") {
                $uploadExpLetterDocs = $req->uploadExpLetterDocs;
                $exp_file_name =$req->positionHead. '-'.$timestamp . '-' . $uploadExpLetterDocs->getClientOriginalName();
                $path = public_path('school/employees/'.$empId);
                $expFile_name = 'school/employees/' . $empId.'/'.$exp_file_name;
                $uploadExpLetterDocs->move($path, $exp_file_name);
                $metaReqs = [
                    'upload_exp_letter' => $expFile_name,
                ];
            }
            
            $metaReqs = array_merge( $metaReqs,  [
                "name_of_org"       =>  $req['nameOfOrg'],
                "position_head"     =>  $req['positionHead'],
                "period_from"       =>  $req['periodFrom'],
                "period_to"         =>  $req['periodTo'],
                "salary"            =>  $req['salary'],
                "pay_grade"         =>  $req['payGrade'],     
                'version_no'        => $getData->version_no + 1,               
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
            $show = $this->_mEmployeeExperiences->getGroupById($req->id);
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
            
            $delete = $this->_mEmployeeExperiences::findOrFail($req->id);
            // $delete->update($metaReqs);
            $delete->delete();
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Delete Successfully", $req->id, "M_API_2.5", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_2.5", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
}
