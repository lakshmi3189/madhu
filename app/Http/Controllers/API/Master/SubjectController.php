<?php

namespace App\Http\Controllers\API\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Subject;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SubjectController extends Controller
{
    private $_mSubjects;

    public function __construct()
    {
        $this->_mSubjects = new Subject();
    }

    // Add records
    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'subjectName' => 'required|string'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);

        try {
            $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
            $isGroupExists = $this->_mSubjects->readSubjectGroup(Str::ucfirst($req->subjectName));

            if (collect($isGroupExists)->isNotEmpty())
                throw new Exception("Subject Name Already existing");
            $metaReqs = [
                'subject_name' => Str::ucfirst($req->subjectName),
                'academic_year' => $fy,
                'school_id' => authUser()->school_id,
                'created_by' => authUser()->id,
                'ip_address' => getClientIpAddress(),
                'version_no' => 0
            ];
            $metaReqs = array_merge($metaReqs, [
                'json_logs' => trim(json_encode($metaReqs), ",")
            ]);
            $this->_mSubjects->store($metaReqs);
            return responseMsgs(true, "Successfully Saved", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
    // Edit records
    public function edit(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'subjectName' => 'required|string'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);

        try {
            $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));

            $isExists = $this->_mSubjects->readSubjectGroup(Str::ucfirst($req->subjectName));
            if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
                throw new Exception("Subject Name Already Existing");
            $getData = $this->_mSubjects::findOrFail($req->id);
            $metaReqs = [
                'subject_name' => Str::ucfirst($req->subjectName),
                'version_no' => $getData->version_no + 1,
                'updated_at' => Carbon::now()
            ];
            $metaReqs = array_merge($metaReqs, [
                'json_logs' => trim($getData->json_logs . "," . json_encode($metaReqs), ",")
            ]);
            if (isset($req->status)) {                  // In Case of Deactivation or Activation
                $status = $req->status == 'deactive' ? 0 : 1;
                $metaReqs = array_merge($metaReqs, [
                    'status' => $status
                ]);
            }
            $Subjects = $this->_mSubjects::findOrFail($req->id);
            $Subjects->update($metaReqs);
            return responseMsgs(true, "Successfully Updated", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
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
            // $Subjects = $this->_mSubjects::findOrFail($req->id);
            $show = $this->_mSubjects->getGroupById($req->id);
            if (collect($show)->isEmpty())
                throw new Exception("Data Not Found");
            return responseMsgs(true, "", $show, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //view by name
    public function search(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'search' => 'required|string'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $search = $this->_mSubjects->searchByName(Str::ucfirst($req->subjectName));
            if (collect($search)->isEmpty())
                throw new Exception("Subject Name Does Not Exists");
            return responseMsgs(true, "", $search, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //View All
    public function retrieveAll(Request $req)
    {
        try {
            // $SubjectMaster = $this->_mSubjects::orderByDesc('id')->where('status','1')->get();
            $SubjectMaster = $this->_mSubjects->retrieve();
            return responseMsgs(true, "", $SubjectMaster, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //Active All
    public function activeAll(Request $req)
    {
        try {
            $SubjectMaster = $this->_mSubjects->active();
            return responseMsgs(true, "", $SubjectMaster, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
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
            $Subjects = $this->_mSubjects::findOrFail($req->id);
            // if ($Subjects->status == 0)
            //     throw new Exception("Records Already Deleted");
            $Subjects->update($metaReqs);
            return responseMsgs(true, "Deleted Successfully", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
}
