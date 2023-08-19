<?php

namespace App\Http\Controllers\API\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\SubjectGroupMap;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\Master\SectionGroupMap;
use Illuminate\Support\Str;

use DB;

class SubjectGroupMapController extends Controller
{
    //new code  
    //global variable
    private $_mSubjectGroupMaps;
    private $_mSectionGroupMaps;


    public function __construct()
    {
        DB::enableQueryLog();
        $this->_mSubjectGroupMaps = new SubjectGroupMap();
        $this->_mSectionGroupMaps = new SectionGroupMap();
    }


    // Add records
    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'subjectGroupMap' => 'required|array',
            'subjectGroupMap.*.classId' => 'required|numeric',
            // 'subjectGroupMap.*.sectionId' => 'required|numeric',
            'subjectGroupMap.*.subjectName' => 'required|string'
        ]);

        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            // $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
            // $isGroupExists = $this->_mMarksEntries->readMarksEntryGroup($req, authUser()->school_id);
            // if (collect($isGroupExists)->isNotEmpty())
            //     throw new Exception("Marks Entry Already Existing");
            $data = array();
            if ($req['subjectGroupMap'] != "") {
                foreach ($req['subjectGroupMap'] as $ob) {
                    $isGroupExists = $this->_mSubjectGroupMaps->readSubjectGroupMap($ob);
                    if (collect($isGroupExists)->isNotEmpty())
                        throw new Exception("Subject Group Map Already Existing");
                    $subjectGroupMap = new SubjectGroupMap;
                    $subjectGroupMap->class_id = $ob['classId'];
                    // $subjectGroupMap->section_group_map_id = $ob['sectionId'];
                    $subjectGroupMap->subject_name = $ob['subjectName'];
                    $subjectGroupMap->school_id = authUser()->school_id;
                    $subjectGroupMap->created_by = authUser()->id;
                    $subjectGroupMap->ip_address = getClientIpAddress();
                    $subjectGroupMap->save();
                    // dd($subjectGroupMap);
                    $data[] = $subjectGroupMap;
                }
                // die;
            }
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Records Added Successfully", $data, "M_API_37.1", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_37.1", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    // Add records 
    public function store1(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'classId' => 'required|numeric',
            'sectionData' => 'required|numeric',
            'subject' => 'required|string'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $isGroupExists = $this->_mSubjectGroupMaps->readSubjectGroupMap($req);
            if (collect($isGroupExists)->isNotEmpty())
                throw new Exception("Subject Group Map Already Existing");

            $isGroupExists1 = $this->_mSectionGroupMaps->readSectionNotInGroupMap($req);
            if ($isGroupExists1 == 0)
                throw new Exception("Section Not Existing");
            $metaReqs = [
                'class_id' => $req->classId,
                'section_group_map_id' => $req->sectionData,
                'subject_name' => Str::title($req->subject),
                'school_id' => authUser()->school_id,
                'created_by' => authUser()->id,
                'ip_address' => getClientIpAddress(),
                'version_no' => 0
            ];

            $metaReqs = array_merge($metaReqs, [
                'json_logs' => trim(json_encode($metaReqs), ",")
            ]);
            $this->_mSectionGroupMaps->store($metaReqs);
            $data = ['Subject Name' => $req->subject];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Records Added Successfully", $data, "M_API_37.1", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_37.1", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    // Edit records
    public function edit(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'id' => 'required|numeric',
            'classId' => 'required|numeric',
            'sectionData' => 'required|numeric',
            'subject' => 'required|string'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {

            $isExists = $this->_mSubjectGroupMaps->readSubjectGroupMap($req);
            if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
                throw new Exception("Subject Group Map Already Existing");
            $getData = $this->_mSubjectGroupMaps::findOrFail($req->id);
            $metaReqs = [
                'class_id' => $req->classId,
                'section_group_map_id' => $req->sectionData,
                'subject_name' => Str::title($req->subject),
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
            $getData->update($metaReqs);
            $data = ['Subject Name' => $req->subject];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Records Updated Successfully", $data, "M_API_37.2", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_37.2", responseTime(), "POST", $req->deviceId ?? "");
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
            $show = $this->_mSubjectGroupMaps->getGroupById($req->id);
            if (collect($show)->isEmpty())
                throw new Exception("Data Not Found");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View Records", $show, "M_API_37.3", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_37.3", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //View All
    public function retrieveAll(Request $req)
    {
        try {
            $getData = $this->_mSubjectGroupMaps->retrieve();
            // if (collect($getData)->isNotEmpty())
            //     throw new Exception("Data Not Found");
            $perPage = $req->perPage ? $req->perPage : 10;
            $paginater = $getData->paginate($perPage);
            // if (!$paginater->total())
            //     throw new Exception("Data Not Found");
            $list = [
                "current_page" => $paginater->currentPage(),
                "perPage" => $perPage,
                "last_page" => $paginater->lastPage(),
                "data" => $paginater->items(),
                "total" => $paginater->total()
            ];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View All Records", $list, "M_API_37.4", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_37.4", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //deactive / active
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
            $delete = $this->_mSubjectGroupMaps::findOrFail($req->id);
            $delete->update($metaReqs);
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Changes Done Successfully", $req->status, "M_API_37.5", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_37.5", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //Active All
    public function activeAll(Request $req)
    {
        try {
            $getData = $this->_mSubjectGroupMaps->active();
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View All Active Records", $getData, "M_API_37.6", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_37.6", responseTime(), "POST", $req->deviceId ?? "");
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
            $getData = $this->_mSubjectGroupMaps->searchByName($req);
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
            return responseMsgsT(true, "View Searched Records", $list, "M_API_37.7", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_37.7", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    // get class wise section .. 
    public function showByClassId(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'classId' => 'required|numeric'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $show = $this->_mSubjectGroupMaps->getClassGroupById($req);
            if (collect($show)->isEmpty())
                throw new Exception("Data Not Found");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View Records", $show, "M_API_37.8", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_37.8", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    public function getAllSubject(Request $req)
    {
        // allSubject
        try {
            $show = $this->_mSubjectGroupMaps->allSubject();
            if (collect($show)->isEmpty())
                throw new Exception("Data Not Found");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View Records", $show, "M_API_37.8", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_37.8", responseTime(), "POST", $req->deviceId ?? "");
        }
    }







    // //global variable
    // private $_mSubjectGroupMaps;
    // private $_mSectionGroupMaps;


    // public function __construct()
    // {
    //     $this->_mSubjectGroupMaps = new SubjectGroupMap();
    //     $this->_mSectionGroupMaps = new SectionGroupMap();
    // }

    // // Add records
    // public function store1(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'classId' => 'required|numeric',
    //         'sectionGroupMapId' => 'required|numeric',
    //         'subjectName' => 'required|string'
    //     ]);
    //     if ($validator->fails())
    //         return responseMsgs(false, $validator->errors(), []);
    //     try {
    //         //$fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
    //         $isGroupExists = $this->_mSubjectGroupMaps->readSubjectGroupMap($req);
    //         if (collect($isGroupExists)->isNotEmpty())
    //             throw new Exception("Subject Group Map Already Existing");

    //         $isGroupExists1 = $this->_mSectionGroupMaps->readSectionNotInGroupMap($req);
    //         if ($isGroupExists1 == 0)
    //             throw new Exception("Section Not Existing");
    //         // die;
    //         $metaReqs = [
    //             'class_id' => $req->classId,
    //             'section_group_map_id' => $req->sectionGroupMapId,
    //             'subject_name' => $req->subjectName,
    //             'created_by' => authUser()->id,
    //             'ip_address' => getClientIpAddress()
    //         ];
    //         $this->_mSubjectGroupMaps->store($metaReqs);
    //         return responseMsgs(true, "Successfully Saved", [$metaReqs], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

    // // Edit records
    // public function edit1(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'id' => 'numeric',
    //         'classId' => 'required|numeric',
    //         'sectionGroupMapId' => 'required|numeric',
    //         'subjectName' => 'required|string'
    //     ]);
    //     if ($validator->fails())
    //         return responseMsgs(false, $validator->errors(), []);
    //     try {
    //         $isExists = $this->_mSubjectGroupMaps->readSubjectGroupMap($req);
    //         if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
    //             throw new Exception("Section Group Map Already Existing");
    //         $getData = $this->_mSubjectGroupMaps::findOrFail($req->id);
    //         $metaReqs = [
    //             'class_id' => $req->classId,
    //             'section_group_map_id' => $req->sectionGroupMapId,
    //             'subject_name' => $req->subjectName,
    //             'version_no' => $getData->version_no + 1,
    //             'updated_at' => Carbon::now()
    //         ];
    //         if (isset($req->status)) {                  // In Case of Deactivation or Activation 
    //             $status = $req->status == 'deactive' ? 0 : 1;
    //             $metaReqs = array_merge($metaReqs, [
    //                 'status' => $status
    //             ]);
    //         }
    //         $sectionGroupMap = $this->_mSubjectGroupMaps::findOrFail($req->id);
    //         $sectionGroupMap->update($metaReqs);
    //         return responseMsgs(true, "Successfully Updated", [$sectionGroupMap], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }
    // //View by id
    // public function show1(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'id' => 'required|numeric'
    //     ]);
    //     if ($validator->fails())
    //         return responseMsgs(false, $validator->errors(), []);
    //     try {
    //         $show = $this->_mSubjectGroupMaps->getGroupById($req->id);
    //         if (collect($show)->isEmpty())
    //             throw new Exception("Data Not Found");
    //         return responseMsgs(true, "", $show, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

    // // Get classwise section and subject...
    // public function showByClassId1(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'classId' => 'required|numeric'
    //     ]);
    //     if ($validator->fails())
    //         return responseMsgs(false, $validator->errors(), []);
    //     try {
    //         // $show = array();
    //         // $show1 = $this->_mSubjectGroupMaps->getClassGroupById($req);
    //         // $show2 = $this->_mSectionGroupMaps->getClassGroupById($req);
    //         // $show['section'] = $show2;
    //         // $show['subject'] = $show1;

    //         $show = $this->_mSubjectGroupMaps->getClassGroupById($req);
    //         // dd($show);
    //         if (collect($show)->isEmpty())
    //             throw new Exception("Data Not Found");
    //         return responseMsgs(true, "", $show, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

    // //view by name
    // public function search1(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'className' => 'required|string'
    //     ]);
    //     if ($validator->fails())
    //         return responseMsgs(false, $validator->errors(), []);
    //     try {
    //         $search = $this->_mSubjectGroupMaps->searchByClassId($req);
    //         if (collect($search)->isEmpty())
    //             throw new Exception("Marks Entry Not Exists");
    //         return responseMsgs(true, "", $search, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

    // //View All
    // public function retrieveAll1(Request $req)
    // {
    //     try {
    //         // $Routes = $this->_mSubjectGroupMaps::orderByDesc('id')->where('status', '1')->get();
    //         $sectionGroupMap = $this->_mSubjectGroupMaps->retrieve();
    //         return responseMsgs(true, "", $sectionGroupMap, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

    // //Active All
    // public function activeAll1(Request $req)
    // {
    //     try {
    //         $sectionGroupMap = $this->_mSubjectGroupMaps->active();
    //         return responseMsgs(true, "", $sectionGroupMap, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

    // public function delete1(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'status' => 'required|in:active,deactive'
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
    //         $sectionGroupMap = $this->_mSubjectGroupMaps::findOrFail($req->id);
    //         // if ($sectionGroupMap->status == 0)
    //         //     throw new Exception("Records Already Deleted");
    //         $sectionGroupMap->update($metaReqs);
    //         return responseMsgs(true, "Deleted Successfully", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }
}
