<?php

namespace App\Http\Controllers\API\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeEducation;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Validator;

class EmployeeEducationController extends Controller
{
    //
    private $_mEmployeeEducation;

    public function __construct()
    {
        DB::enableQueryLog();
        $this->_mEmployeeEducation = new EmployeeEducation();
    }


    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'empNo'         => 'required',
            'examPassed'    => 'nullable|string',
            'board'         => 'nullable|string',
            'passingYear'   => 'nullable|string',
            'divGrade'      => 'nullable|string',
            'marksObtained' => 'nullable|numeric',
            'totalMarks'    => 'nullable|numeric',
            'percentage'    => 'nullable|numeric',
            'uploadEduDoc'  => 'nullable|file', 
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $metaReqs = array();
            $file_name = '';
            $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
            $timestamp = now()->timestamp; 
            $empId = Employee::where('emp_no', $req->empNo)->pluck('id')->first();

            $isGroupExists = $this->_mEmployeeEducation->readEducationGroup($empId,$req);
            if (collect($isGroupExists)->isNotEmpty())
                throw new Exception("Degree Already Existing");

            if ($req->uploadEduDoc != "") {
                $uploadEduDoc = $req->uploadEduDoc;
                $edu_file_name =$req->examPassed. '-'.$timestamp . '-' . $uploadEduDoc->getClientOriginalName();
                $path = public_path('school/employees/'.$req->empNo);
                $eduFile_name = 'school/employees/' . $req->empNo.'/'.$edu_file_name;
                $uploadEduDoc->move($path, $edu_file_name);
                $metaReqs = [
                    'upload_edu_doc' => $eduFile_name,
                ];
            }
            $metaReqs = array_merge( $metaReqs,  [
                'emp_tbl_id' => $empId,
                "exam_passed_name" => $req['examPassed'],
                "board_uni_inst" => $req['board'],
                "passing_year" => $req['passingYear'],
                "div_grade_name" => $req['divGrade'],
                "marks_obtained" => $req['marksObtained'],
                "total_marks" => $req['totalMarks'],
                "percentage" => $req['percentage'],
                'academic_year' => $fy,
                'school_id' => authUser()->school_id,
                'created_by' => authUser()->id,
                'ip_address' => getClientIpAddress(),
                'version_no' => 0                        
            ]);
            
            // return $metaReqs; die; 
            $this->_mEmployeeEducation->store($metaReqs);

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

            $getData = $this->_mEmployeeEducation->retrieve($req);
          
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View All Records", $getData, "API_1.0", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    public function edit(Request $req)
    {
        $educationRules = [
            'id'            => 'required|numeric',
            'examPassed'    => 'nullable|string',
            'board'         => 'nullable|string',
            'passingYear'   => 'nullable|string',
            'divGrade'      => 'nullable|string',
            'marksObtained' => 'nullable|numeric',
            'totalMarks'    => 'nullable|numeric',
            'percentage'    => 'nullable|numeric',
            'uploadEduDoc'  => 'nullable|file', 
        ];

        $validator = Validator::make($req->all(), $educationRules);
        if ($validator->fails()) {
            return responseMsgs(false, $validator->errors()->first(), [], "", "API_1.0", responseTime(), "POST", $req->deviceId?? "");
        }
        try{
            $timestamp = now()->timestamp; 
            $getData =  $this->_mEmployeeEducation::where(['id' => $req->id], ['status' => 1])->first();
            // return $getData->id; die; 
            $empId = Employee::where('id', $getData->emp_tbl_id)->pluck('emp_no')->first();
           
            $metaReqs = array();
            if ($req->uploadEduDoc != "") {
                $uploadEduImage = $req->uploadEduDoc;
                // Handle file upload and update the file-related field
                $edu_file_name = 'Education-' . $timestamp . '-' . $uploadEduImage->getClientOriginalName();
                $path = public_path('school/employees/' . $empId);
                $eduFile_name = 'school/employees/' . $empId . '/' . $edu_file_name;
                $uploadEduImage->move($path, $edu_file_name);
                $updateData['upload_edu_doc'] = $eduFile_name;
                $metaReqs = [
                    'upload_edu_doc' => $eduFile_name,
                ];    
            }
            
            $metaReqs = array_merge( $metaReqs,  [
                "exam_passed_name" => $req['examPassed'],
                "board_uni_inst" => $req['board'],
                "passing_year" => $req['passingYear'],
                "div_grade_name" => $req['divGrade'],
                "marks_obtained" => $req['marksObtained'],
                "total_marks" => $req['totalMarks'],
                "percentage" => $req['percentage'],
                'version_no' => $getData->version_no + 1,                           
            ]);

            if (isset($req->status)) {                  // In Case of Deactivation or Activation 
                $status = $req->status == 'deactive' ? 0 : 1;
                $metaReqs = array_merge($metaReqs, [
                    'status' => $status
                ]);
            }
            $getData->update($metaReqs);
            $result['basicDetails'] = $metaReqs;

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
            $show = $this->_mEmployeeEducation->getGroupById($req->id);
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
            
            $delete = $this->_mEmployeeEducation::findOrFail($req->id);
            // $delete->update($metaReqs);
            $delete->delete();
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Delete Successfully", $req->status, "M_API_2.5", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_2.5", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
}
