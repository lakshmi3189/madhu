<?php

namespace App\Http\Controllers\API\Examination;

use App\Http\Controllers\Controller;
use App\Models\Examination\MarksEntry;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Str;

/*=================================================== Marks Entry =========================================================
Created By : Umesh Kumar
Created On : 17-June-2023 
Code Status : Open 
*/

class MarksEntryController extends Controller
{
    //global variable
    private $_mMarksEntries;

    public function __construct()
    {
        DB::enableQueryLog();
        $this->_mMarksEntries = new MarksEntry();
    }

    // Add records
    public function store(Request $req)
    {
        // $validator = Validator::make($req->all(), [
        //     'classId' => 'required|numeric',
        //     'subjectId' => 'required|numeric',
        //     'sectionId' => 'required|numeric',
        //     'isMainSubject' => 'boolean',
        //     'isOptionalSubject' => 'boolean',
        //     'fullMarks' => 'required|numeric',
        //     'passMarks' => 'required|numeric'
        // ]);

        $validator = Validator::make($req->all(), [
            'marksEntries' => 'required|array',
            'marksEntries.*.classId' => 'required|numeric',
            'marksEntries.*.subjectId' => 'required|numeric',
            // 'marksEntries.*.sectionId' => 'required|numeric',
            'marksEntries.*.isMainSubject' => 'boolean',
            'marksEntries.*.isOptionalSubject' => 'boolean',
            'marksEntries.*.fullMarks' => 'required|numeric',
            'marksEntries.*.passMarks' => 'required|numeric'
        ]);

        // $validator = Validator::make($req->all(), [
        //     'tabulation' => 'required|array',
        //     'admissionNo' => 'required|string',
        //     'classId' => 'required|integer',
        //     'sectionId' => 'required|integer',
        //     'termId' => 'required|integer',
        //     'tabulation.*.marksEntryId' => 'required|integer',
        //     'tabulation.*.obtainedMarks' => 'required|numeric',
        // ]);

        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
            // $isGroupExists = $this->_mMarksEntries->readMarksEntryGroup($req, authUser()->school_id);
            // if (collect($isGroupExists)->isNotEmpty())
            //     throw new Exception("Marks Entry Already Existing");
            $data = array();
            if ($req['marksEntries'] != "") {
                foreach ($req['marksEntries'] as $ob) {
                    $isGroupExists = $this->_mMarksEntries->readMarksEntryGroup($ob);
                    if (collect($isGroupExists)->isNotEmpty())
                        throw new Exception("Marks Entry Already Existing");

                    $marksEntry = new MarksEntry;
                    $marksEntry->fy_name = $fy;
                    $marksEntry->class_id = $ob['classId'];
                    // $marksEntry->section_id = $ob['sectionId'];
                    $marksEntry->subject_id = $ob['subjectId'];
                    $marksEntry->is_main_subject = $ob['isMainSubject'];
                    $marksEntry->is_optional_subject = $ob['isOptionalSubject'];
                    $marksEntry->full_marks = $ob['fullMarks'];
                    $marksEntry->pass_marks = $ob['passMarks'];
                    $marksEntry->academic_year = $fy;
                    $marksEntry->school_id = authUser()->school_id;
                    $marksEntry->created_by = authUser()->id;
                    $marksEntry->ip_address = getClientIpAddress();
                    $marksEntry->save();
                    // dd($marksTabulation);
                    $data[] = $marksEntry;
                }
                // die;
            }

            // $metaReqs = [
            //     'fy_name' => $fy,
            //     'class_id' => $req->classId,
            //     'subject_id' => $req->subjectId,
            //     'section_id' => $req->sectionId,
            //     'full_marks' => $req->fullMarks,
            //     'pass_marks' => $req->passMarks,
            //     'academic_year' => $fy,
            //     'school_id' => authUser()->school_id,
            //     'created_by' => authUser()->id,
            //     'ip_address' => getClientIpAddress()
            // ];
            // if ($req->isOptionalSubject != "" && $req->isMainSubject == "") {
            //     $metaReqs = array_merge($metaReqs, [
            //         'is_optional_subject' => $req->isOptionalSubject,
            //         'is_main_subject' => false,
            //     ]);
            // }
            // if ($req->isOptionalSubject == "" && $req->isMainSubject != "") {
            //     $metaReqs = array_merge($metaReqs, [
            //         'is_main_subject' => $req->isMainSubject,
            //         'is_optional_subject' => false,
            //     ]);
            // }
            // $this->_mMarksEntries->store($metaReqs);
            return responseMsgs(true, "Successfully Saved", [$data], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    public function store1(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'classId' => 'required|numeric',
            'subjectId' => 'required|numeric',
            'sectionId' => 'required|numeric',
            'isMainSubject' => 'boolean',
            'isOptionalSubject' => 'boolean',
            'fullMarks' => 'required|numeric',
            'passMarks' => 'required|numeric'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
            $isGroupExists = $this->_mMarksEntries->readMarksEntryGroup($req, authUser()->school_id);
            if (collect($isGroupExists)->isNotEmpty())
                throw new Exception("Marks Entry Already Existing");
            $metaReqs = [
                'fy_name' => $fy,
                'class_id' => $req->classId,
                'subject_id' => $req->subjectId,
                'section_id' => $req->sectionId,
                'full_marks' => $req->fullMarks,
                'pass_marks' => $req->passMarks,
                'academic_year' => $fy,
                'school_id' => authUser()->school_id,
                'created_by' => authUser()->id,
                'ip_address' => getClientIpAddress()
            ];
            if ($req->isOptionalSubject != "" && $req->isMainSubject == "") {
                $metaReqs = array_merge($metaReqs, [
                    'is_optional_subject' => $req->isOptionalSubject,
                    'is_main_subject' => false,
                ]);
            }
            if ($req->isOptionalSubject == "" && $req->isMainSubject != "") {
                $metaReqs = array_merge($metaReqs, [
                    'is_main_subject' => $req->isMainSubject,
                    'is_optional_subject' => false,
                ]);
            }
            $this->_mMarksEntries->store($metaReqs);
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
            'classId' => 'required|numeric',
            'subjectId' => 'required|numeric',
            'sectionId' => 'required|numeric',
            'isMainSubject' => 'boolean',
            'isOptionalSubject' => 'boolean',
            'fullMarks' => 'required|numeric',
            'passMarks' => 'required|numeric'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
        try {
            $isExists = $this->_mMarksEntries->readMarksEntryGroup($req, authUser()->school_id);
            if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
                throw new Exception("Marks Entry Already Existing");
            $getData = $this->_mMarksEntries::findOrFail($req->id);
            $metaReqs = [
                'fy_name' => $fy,
                'class_id' => $req->classId,
                'subject_id' => $req->subjectId,
                'section_id' => $req->sectionId,
                'full_marks' => $req->fullMarks,
                'pass_marks' => $req->passMarks,
                'version_no' => $getData->version_no + 1,
                'updated_at' => Carbon::now()
            ];
            if ($req->isOptionalSubject != "" && $req->isMainSubject == "") {
                $metaReqs = array_merge($metaReqs, [
                    'is_optional_subject' => $req->isOptionalSubject,
                    'is_main_subject' => false,
                ]);
            }
            if ($req->isOptionalSubject == "" && $req->isMainSubject != "") {
                $metaReqs = array_merge($metaReqs, [
                    'is_main_subject' => $req->isMainSubject,
                    'is_optional_subject' => false,
                ]);
            }
            if (isset($req->status)) {                  // In Case of Deactivation or Activation 
                $status = $req->status == 'deactive' ? 0 : 1;
                $metaReqs = array_merge($metaReqs, [
                    'status' => $status
                ]);
            }
            $marksEntry = $this->_mMarksEntries::findOrFail($req->id);
            $marksEntry->update($metaReqs);
            return responseMsgs(true, "Successfully Updated", [$metaReqs], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
    //View by id
    public function show(Request $req)
    {
        // $validator = Validator::make($req->all(), [
        //     'id' => 'required|numeric'
        // ]);
        $validator = Validator::make($req->all(), [
            'classId' => 'required|numeric',
            'sectionId' => 'required|numeric'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            // $Routes = $this->_mMarksEntries::findOrFail($req->id);
            $show = $this->_mMarksEntries->getGroupById($req);
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
            'className' => 'required|string',
            'subjectName' => 'required|string'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $search = $this->_mMarksEntries->searchByName($req);
            if (collect($search)->isEmpty())
                throw new Exception("Marks Entry Not Exists");
            return responseMsgs(true, "", $search, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //View All
    public function retrieveAll(Request $req)
    {
        try {
            $getData = $this->_mMarksEntries->retrieve();
            $perPage = $req->perPage ? $req->perPage : 10;
            $paginater = $getData->paginate($perPage);
            // if ($paginater == "")
            //     throw new Exception("Data Not Found");
            $list = [
                "current_page" => $paginater->currentPage(),
                "perPage" => $perPage,
                "last_page" => $paginater->lastPage(),
                "data" => $paginater->items(),
                "total" => $paginater->total()
            ];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View All Records", $list, "M_API_19.4", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_19.4", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
    //Records All
    public function sectionWiseMarks(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'classId' => 'required|numeric',
            // 'sectionId' => 'required|numeric'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            // $isGroupExists = $this->_mMarksEntries->readMarksEntryGroup($req, authUser()->school_id);
            // if (collect($isGroupExists)->isNotEmpty())
            //     throw new Exception("Marks Entry Already Existing");

            $getActive = $this->_mMarksEntries->sectionGroups($req);
            if (collect($getActive)->isEmpty())
                throw new Exception("Records Not Found");
            return responseMsgs(true, "", $getActive, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
    //Active All
    // public function activeAll(Request $req)
    // {
    //     try {
    //         $getActive = $this->_mMarksEntries->active();
    //         return responseMsgs(true, "", $getActive, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

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
            $marksEntry = $this->_mMarksEntries::findOrFail($req->id);
            // if ($marksEntry->status == 0)
            //     throw new Exception("Records Already Deleted");
            $marksEntry->update($metaReqs);
            return responseMsgs(true, "Deleted Successfully", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
}
