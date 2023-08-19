<?php

namespace App\Http\Controllers\API\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance\StudentAttendance;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class StudentAttendanceController extends Controller
{
    //global variable
    private $_mStudentAttendances;

    public function __construct()
    {
        $this->_mStudentAttendances = new StudentAttendance();
    }
    // Add records
    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'classId' => 'required|numeric',
            // 'sectionId' => 'required|numeric',
            'admissionNo' => 'string|numeric',
            // 'sectionId' => 'required|numeric',
            // 'studentId' => 'required|numeric',
            // 'attendanceStatus' => 'required|numeric',
            'attendanceDate' => 'required|date',
            'description' => 'string|nullable',
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $result = array();
            $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
            $isGroupExists = $this->_mStudentAttendances->readStudentAttendanceGroup($req);
            if (collect($isGroupExists)->isNotEmpty())
                throw new Exception("Attendance Already Existing");
            if ($req['attendance'] != "") {
                foreach ($req['attendance'] as $ob) {
                    $stdAttendance = new StudentAttendance;
                    $stdAttendance->class_id = $ob['classId'];
                    // $stdAttendance->section_id = $ob['sectionId'];
                    $stdAttendance->student_id = $ob['studentId'];
                    $stdAttendance->attendance_status = $ob['attendanceStatus'];
                    $stdAttendance->description = $ob['description'];
                    $stdAttendance->attendance_date = $ob['attendanceDate'];
                    $stdAttendance->academic_year = $fy;
                    $stdAttendance->school_id = authUser()->school_id;
                    $stdAttendance->created_by = authUser()->id;
                    $stdAttendance->ip_address = getClientIpAddress();
                    $stdAttendance->save();
                }
            }

            // $metaReqs = [
            //     'class_id' => $req->classId,
            //     'section_id' => $req->sectionId,
            //     'student_id' => $req->studentId,
            //     'attendance_status' => $req->attendanceStatus,
            //     'attendance_date' => $req->attendanceDate,
            //     'description' => $req->description,
            //     'academic_year' => $fy,
            //     'school_id' => authUser()->school_id,
            //     'created_by' => authUser()->id,
            //     'ip_address' => getClientIpAddress()
            // ];
            // $this->_mStudentAttendances->store($metaReqs);
            $result['attendance'] = $stdAttendance;
            return responseMsgs(true, "Successfully Saved", [$result], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    // // Edit records
    // public function edit(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'id' => 'numeric',
    //         'classId' => 'required|numeric',
    //         'sectionId' => 'required|numeric',
    //         'studentId' => 'required|numeric',
    //         'attendanceStatus' => 'required|numeric',
    //         'attendanceDate' => 'required|date',
    //         'description' => 'string|nullable',          
    //     ]);
    //     if ($validator->fails())
    //         return responseMsgs(false, $validator->errors(), []);
    //     $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
    //     try {
    //         $isExists = $this->_mStudentAttendances->readStudentAttendanceGroup($req);
    //         if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
    //             throw new Exception("Attendance Already existing");    
    //         $getData = $this->_mStudentAttendances::findOrFail($req->id);            
    //         $metaReqs = [ 
    //             'class_id' => $req->classId,
    //             'section_id' => $req->sectionId,
    //             'student_id' => $req->studentId,
    //             'attendance_status' => $req->attendanceStatus,
    //             'attendance_date' => $req->attendanceDate,
    //             'description' => $req->description,
    //             'version_no' => $getData->version_no + 1,
    //             'updated_at' => Carbon::now()
    //         ];
    //         if (isset($req->status)) {                  // In Case of Deactivation or Activation 
    //             $status = $req->status == 'deactive' ? 0 : 1;
    //             $metaReqs = array_merge($metaReqs, [
    //                 'status' => $status
    //             ]);
    //         }
    //         $timeTable = $this->_mStudentAttendances::findOrFail($req->id);
    //         $timeTable->update($metaReqs);
    //         return responseMsgs(true, "Successfully Updated", [$metaReqs], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }
    //View by id
    // public function show(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'id' => 'required|numeric'
    //     ]);
    //     if ($validator->fails())
    //         return responseMsgs(false, $validator->errors(), []);
    //     try {
    //         // $Routes = $this->_mStudentAttendances::findOrFail($req->id);
    //         $Routes = $this->_mStudentAttendances->getGroupById($req->id);
    //         return responseMsgs(true, "", $Routes, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

    //View All
    public function retrieveAll(Request $req)
    {
        try {
            // $Routes = $this->_mStudentAttendances::orderByDesc('id')->where('status', '1')->get();
            $getAll = $this->_mStudentAttendances->retrieveAll();
            return responseMsgs(true, "", $getAll, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //Active All
    public function activeAll(Request $req)
    {
        try {
            $getActive = $this->_mStudentAttendances->active();
            return responseMsgs(true, "", $getActive, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    // public function delete(Request $req)
    // {
    //     try {
    //         if (isset($req->status)) {                  // In Case of Deactivation or Activation
    //             $status = $req->status == 'deactive' ? 0 : 1;
    //             $metaReqs =  [
    //                 'status' => $status
    //             ];
    //         }
    //         $timeTable = $this->_mStudentAttendances::findOrFail($req->id);
    //         if ($timeTable->status == 0)
    //             throw new Exception("Records Already Deleted");
    //         $timeTable->update($metaReqs);
    //         return responseMsgs(true, "Deleted Successfully", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

    // Search By ClassId & SectionId
    // public function search(Request $req) 
    // {
    //     $validator = Validator::make($req->all(), [
    //         'classId' => 'required|numeric',
    //         'sectionId' => 'required|numeric'
    //     ]);
    //         if ($validator->fails())
    //         return responseMsgs(false, $validator->errors(), []);
    //     try {
    //         $Banks = $this->_mStudentAttendances->searchById($req);
    //         // if (collect($Banks)->isEmpty())
    //         //     throw new Exception("Attendance Not Exists");
    //         return responseMsgs(true, "", $Banks, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

}
