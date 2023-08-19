<?php

namespace App\Http\Controllers\API\Examination;

use App\Http\Controllers\Controller;
use App\Models\Examination\MarksTabulation;
use App\Models\Student\student;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class MarksTabulationController extends Controller
{
    //global variable 
    private $_mMarksTabulations;

    public function __construct()
    {
        $this->_mMarksTabulations = new MarksTabulation();
    }

    // Add records
    public function store(Request $req)
    {
        // $validator = Validator::make($req->all(), [
        //     'tabulation' => 'required|array',
        //     'admissionNo' => 'required|string',
        //     'classId' => 'required|integer',
        //     'sectionId' => 'required|integer',
        //     'termId' => 'required|integer',
        //     'tabulation.*.marksEntryId' => 'required|integer',
        //     'tabulation.*.obtainedMarks' => 'required|numeric',
        // ]);
        $validator = Validator::make($req->all(), [
            'admissionNo' => 'required',
            // 'classId' => 'required|numeric',
            // 'sectionId' => 'required|numeric',
            // 'fy' => 'required',
            // 'termId' => 'required|numeric',
            'tabulation' => 'required|array',
            'tabulation.*.admissionNo' => 'required',
            'tabulation.*.classId' => 'required|numeric',
            // 'tabulation.*.sectionId' => 'required|numeric',
            'tabulation.*.termId' => 'required|numeric',
            'tabulation.*.marksEntryId' => 'required|numeric',
            'tabulation.*.obtainedMarks' => 'required|numeric',
            // 'tabulation.*.admissionNo' => 'required',
            // 'tabulation.*.classId' => 'required|numeric',
            // 'tabulation.*.sectionId' => 'required|numeric',            
            'tabulation.*.fy' => 'required'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            // echo $req->admissionNo;
            // die;
            $mStudents = Student::where('admission_no', $req->admissionNo)
                // ->where('class_id', $req->classId)
                // ->where('section_id', $req->sectionId)
                // ->where('academic_year', $req->fy)
                ->where('status', 1)
                ->first();
            // print_var($mStudents);
            // die;
            if (collect($mStudents)->isEmpty())
                throw new Exception('Admission no is not existing');
            // dd($mStudents);
            $studentId  = $mStudents->id;
            // $classId  = $mStudents->class_id;
            // $sectionId  = $mStudents->section_id;
            // $termId = $req->termId;
            // $finYear = $req->fy;
            $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
            $data = array();
            if ($req['tabulation'] != "") {
                foreach ($req['tabulation'] as $ob) {
                    $isGroupExists = $this->_mMarksTabulations->readMarksTabulationGroup($ob, $studentId);
                    // print_var($isGroupExists);
                    // die;
                    if (collect($isGroupExists)->isNotEmpty())
                        throw new Exception("Marks Tabulation Already Existing");

                    $marksTabulation = new MarksTabulation;
                    $marksTabulation->fy_name = $ob['fy'];
                    $marksTabulation->class_id =  $ob['classId'];
                    // $marksTabulation->section_id = $ob['sectionId'];
                    $marksTabulation->student_id = $studentId;
                    $marksTabulation->term_id = $ob['termId'];
                    $marksTabulation->marks_entry_id = $ob['marksEntryId'];
                    $marksTabulation->obtained_marks = $ob['obtainedMarks'];
                    $marksTabulation->academic_year = $fy;
                    $marksTabulation->school_id = authUser()->school_id;
                    $marksTabulation->created_by = authUser()->id;
                    $marksTabulation->ip_address = getClientIpAddress();
                    $marksTabulation->save();
                    // dd($marksTabulation);
                    $data[] = $marksTabulation;
                }
                // die;
            }






            // $data = array();
            // $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
            // // $tabulationData = $req['tabulation'];

            // $mStudents = Student::where('admission_no', $req->admissionNo)
            //     ->where('class_id', $req->classId)
            //     ->where('section_id', $req->sectionId)
            //     ->where('status', 1)
            //     ->first();

            // if (collect($mStudents)->isEmpty())
            //     throw new Exception('Admission no is not existing');
            // // dd($mStudents);
            // $studentId  = $mStudents->id;
            // $classId  = $mStudents->class_id;
            // $sectionId  = $mStudents->section_id;

            // if ($req['tabulation'] != "") {
            //     foreach ($req['tabulation'] as $ob) {
            //         $marksEntryId = $ob['marksEntryId'];
            //         $isGroupExists = $this->_mMarksTabulations->readMarksTabulationGroup($req, $studentId, $classId, $sectionId, $marksEntryId);
            //         if (collect($isGroupExists)->isNotEmpty())
            //             throw new Exception("Marks Tabulation Already Existing");

            //         $marksTabulation = new MarksTabulation;
            //         $marksTabulation->fy_name = $fy;
            //         $marksTabulation->class_id = $classId;
            //         $marksTabulation->section_id = $sectionId;
            //         $marksTabulation->student_id = $studentId;
            //         $marksTabulation->term_id = $req['termId'];
            //         $marksTabulation->marks_entry_id = $ob['marksEntryId'];
            //         $marksTabulation->obtained_marks = $ob['obtainedMarks'];
            //         $marksTabulation->academic_year = $fy;
            //         $marksTabulation->school_id = authUser()->school_id;
            //         $marksTabulation->created_by = authUser()->id;
            //         $marksTabulation->ip_address = getClientIpAddress();
            //         $marksTabulation->save();
            //         // dd($marksTabulation);
            //         $data[] = $marksTabulation;
            //     }
            //     // die;
            // }
            return responseMsgs(true, "Successfully Saved", [$data], "", "13.1", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "13.1", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //View All 
    public function retrieveAll(Request $req)
    {
        try {
            $getData = $this->_mMarksTabulations->retrieve();
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
            return responseMsgsT(true, "View All Records", $list, "API_13.2", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_13.2", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    // Edit records
    // public function edit(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'tabulation' => 'required|array',
    //         'studentId' => 'required|integer',  
    //         'classId' => 'required|integer',
    //         'sectionId' => 'required|integer',
    //         'termId' => 'required|integer',
    //         'tabulation.*.marksEntryId' => 'required|integer',
    //         'tabulation.*.obtainedMarks' => 'required|numeric',
    //     ]);
    //     if ($validator->fails())
    //         return responseMsgs(false, $validator->errors(), []);
    //     $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
    //     try {
    //         $isExists = $this->_mMarksTabulations->readMarksTabulationGroup($req,  $req);
    //         if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
    //             throw new Exception("Marks Entry Already Existing");
    //         $getData = $this->_mMarksTabulations::findOrFail($req->id);
    //         $metaReqs = [
    //             'fy_name' => $fy,
    //             'class_id' => $req->classId,
    //             'subject_id' => $req->subjectId,
    //             'section_id' => $req->sectionId,
    //             'full_marks' => $req->fullMarks,
    //             'pass_marks' => $req->passMarks,
    //             'version_no' => $getData->version_no + 1,
    //             'updated_at' => Carbon::now()
    //         ];
    //         if ($req->isOptionalSubject != "" && $req->isMainSubject == "") {
    //             $metaReqs = array_merge($metaReqs, [
    //                 'is_optional_subject' => $req->isOptionalSubject,
    //                 'is_main_subject' => false,
    //             ]);
    //         }
    //         if ($req->isOptionalSubject == "" && $req->isMainSubject != "") {
    //             $metaReqs = array_merge($metaReqs, [
    //                 'is_main_subject' => $req->isMainSubject,
    //                 'is_optional_subject' => false,
    //             ]);
    //         }
    //         if (isset($req->status)) {                  // In Case of Deactivation or Activation 
    //             $status = $req->status == 'deactive' ? 0 : 1;
    //             $metaReqs = array_merge($metaReqs, [
    //                 'status' => $status
    //             ]);
    //         }
    //         $marksEntry = $this->_mMarksTabulations::findOrFail($req->id);
    //         $marksEntry->update($metaReqs);
    //         return responseMsgs(true, "Successfully Updated", [$metaReqs], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }
    // //View by id
    // public function show(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'id' => 'required|numeric'
    //     ]);
    //     if ($validator->fails())
    //         return responseMsgs(false, $validator->errors(), []);
    //     try {
    //         // $Routes = $this->_mMarksTabulations::findOrFail($req->id);
    //         $show = $this->_mMarksTabulations->getGroupById($req->id);
    //         if (collect($show)->isEmpty())
    //         throw new Exception("Data Not Found");
    //         return responseMsgs(true, "", $show, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

    // //view by name
    // public function search(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'className' => 'required|string',
    //         'subjectName' => 'required|string'
    //     ]);
    //     if ($validator->fails())
    //         return responseMsgs(false, $validator->errors(), []);
    //     try {
    //         $search = $this->_mMarksTabulations->searchByName($req);
    //         if (collect($search)->isEmpty())
    //             throw new Exception("Marks Entry Not Exists");
    //         return responseMsgs(true, "", $search, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

    // //Active All
    // public function activeAll(Request $req)
    // {
    //     try {
    //         $marksEntry = $this->_mMarksTabulations->active();
    //         return responseMsgs(true, "", $marksEntry, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

    // public function delete(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         // 'status' => 'required|in:active,deactive'
    //     ]);
    //     if ($validator->fails())
    //         return responseMsgs(false, $validator->errors(), []);
    //     try {
    //         if (isset($req->status)) {                  // In Case of Deactivation or Activation
    //             $status = $req->status == 'deactive' ? 0 : 1;
    //             $metaReqs =  [
    //                 'status' => $status
    //             ];
    //         }
    //         $marksEntry = $this->_mMarksTabulations::findOrFail($req->id);
    //         // if ($marksEntry->status == 0)
    //         //     throw new Exception("Records Already Deleted");
    //         $marksEntry->update($metaReqs);
    //         return responseMsgs(true, "Deleted Successfully", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }
}
