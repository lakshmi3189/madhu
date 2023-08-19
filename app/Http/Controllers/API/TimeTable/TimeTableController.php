<?php

namespace App\Http\Controllers\API\TimeTable;

use App\Http\Controllers\Controller;
use App\Models\TimeTable\TimeTable;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TimeTableController extends Controller
{
    //global variable
    private $_mTimeTables;

    public function __construct()
    {
        $this->_mTimeTables = new TimeTable();
    }
    private function getWeekdays()
    {
        return ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    }
    // Add records
    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'classId' => 'numeric',
            'subjectId' => 'string',
            // 'subjectId' => 'numeric',
            'sectionId' => 'numeric',
            'empId' => 'numeric',
            'description' => 'string|nullable',
            'day' => Rule::in($this->getWeekdays()),
            // 'day' => 'string',  
            // 'startTime' => 'nullable|date_format:h:i A',
            // 'endTime' => 'nullable|date_format:h:i A|after:startTime',
            'ttDate' => 'nullable|string', //time table date
            'disabled' => 'string|nullable',
            'color' => 'string|nullable'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {

            $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
            // $isGroupExists = $this->_mTimeTables->readTimeTableGroup($req);
            // if (collect($isGroupExists)->isNotEmpty())
            //     throw new Exception("Time Table Already Existing");
            $metaReqs = [
                'class_id' => $req->classId,
                'subject_name' => $req->subjectId,
                // 'subject_id' => $req->subjectId,
                'section_id' => $req->sectionId,
                'emp_id' => $req->empId,
                'description' => $req->description,
                'day' => $req->day,
                'start_time' => $req->startTime,
                'end_time' => $req->endTime,
                'tt_date' => $req->ttDate,
                'disabled' => $req->disabled,
                'color' => $req->color,
                'academic_year' => $fy,
                'school_id' => authUser()->school_id,
                'created_by' => authUser()->id,
                'ip_address' => getClientIpAddress()
            ];
            $this->_mTimeTables->store($metaReqs);
            return responseMsgs(true, "Successfully Saved", [$metaReqs], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    // Edit records
    public function edit(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'id' => 'numeric',
            'classId' => 'numeric',
            'subjectId' => 'string',
            // 'subjectId' => 'numeric',
            'sectionId' => 'numeric',
            'empId' => 'numeric',
            'description' => 'string|nullable',
            'day' => Rule::in($this->getWeekdays()),
            // 'day' => 'string',  
            // 'startTime' => 'nullable|date_format:h:i A',
            // 'endTime' => 'nullable|date_format:h:i A|after:startTime',
            'ttDate' => 'nullable|string',
            'disabled' => 'string|nullable',
            'color' => 'string|nullable'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
        try {
            $isExists = $this->_mTimeTables->readTimeTableGroup($req);
            if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
                throw new Exception("Time Table Already existing");
            $getData = $this->_mTimeTables::findOrFail($req->id);
            $metaReqs = [
                'class_id' => $req->classId,
                'subject_name' => $req->subjectId,
                // 'subject_id' => $req->subjectId,
                'section_id' => $req->sectionId,
                'emp_id' => $req->empId,
                'description' => $req->description,
                'day' => $req->day,
                'start_time' => $req->startTime,
                'end_time' => $req->endTime,
                'tt_date' => $req->ttDate,
                'disabled' => $req->disabled,
                'color' => $req->color,
                'academic_year' => $fy,
                'school_id' => authUser()->school_id,
                'created_by' => authUser()->id,
                'ip_address' => getClientIpAddress(),
                'version_no' => $getData->version_no + 1,
                'updated_at' => Carbon::now()
            ];
            if (isset($req->status)) {                  // In Case of Deactivation or Activation 
                $status = $req->status == 'deactive' ? 0 : 1;
                $metaReqs = array_merge($metaReqs, [
                    'status' => $status
                ]);
            }
            $timeTable = $this->_mTimeTables::findOrFail($req->id);
            $timeTable->update($metaReqs);
            return responseMsgs(true, "Successfully Updated", [$metaReqs], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
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
            // $Routes = $this->_mTimeTables::findOrFail($req->id);
            $show = $this->_mTimeTables->getGroupById($req->id);
            if (collect($show)->isEmpty())
                throw new Exception("Data Not Found");
            return responseMsgs(true, "", $show, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //view by name
    // public function search(Request $req) 
    // {
    //     $validator = Validator::make($req->all(), [
    //         'day' => 'required|string'
    //     ]);
    //         if ($validator->fails())
    //         return responseMsgs(false, $validator->errors(), []);
    //     try {
    //         $Banks = $this->_mTimeTables->searchByName($req->day);
    //         if (collect($Banks)->isEmpty())
    //             throw new Exception("Time Table Not Exists");
    //         return responseMsgs(true, "", $Banks, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

    //View All
    public function retrieveAll(Request $req)
    {
        try {
            // $Routes = $this->_mTimeTables::orderByDesc('id')->where('status', '1')->get();
            $Routes = $this->_mTimeTables->retrieve();
            return responseMsgs(true, "", $Routes, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //Active All
    public function activeAll(Request $req)
    {
        try {
            $Banks = $this->_mTimeTables->active();
            return responseMsgs(true, "", $Banks, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

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
            $timeTable = $this->_mTimeTables::findOrFail($req->id);
            // if ($timeTable->status == 0)
            //     throw new Exception("Records Already Deleted");
            $timeTable->update($metaReqs);
            return responseMsgs(true, "Deleted Successfully", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    // Search By ClassId & SectionId 
    public function search(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'classId' => 'required|numeric',
            'sectionId' => 'required|numeric'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $Banks = $this->_mTimeTables->searchById($req);
            // if (collect($Banks)->isEmpty())
            //     throw new Exception("Time Table Not Exists");
            return responseMsgs(true, "", $Banks, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
}
