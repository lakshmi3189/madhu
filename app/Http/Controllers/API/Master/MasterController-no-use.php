<?php

namespace App\Http\Controllers\API\Master;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//Models
use App\Models\Master\ClassTable;
use App\Models\Master\Board;
use App\Models\Master\Caste;
use App\Models\Master\Certificate;
use App\Models\Master\Section;
use App\Models\Master\Designation;
use App\Models\Master\Subject;
use App\Models\Master\FinancialYear;
use App\Models\Master\Installment;
use App\Models\Master\Leave;
use App\Models\Master\Role;
use App\Models\Master\Sport;
use App\Models\Master\TimeTable;
use App\Models\Master\Attendance;
use App\Models\Master\SchoolId;
use App\Models\Master\Module;
use App\Models\Master\SubModule;
use App\Models\Master\Semester;
use App\Models\Master\Department;
use App\Models\Master\Course;
use App\Models\Master\Institution;
use App\Models\Master\Permission;
use App\Models\Master\MiscellaneousCategory;
use App\Models\Master\MiscellaneousSubCategory;
use App\Models\Master\Salutation;
use App\Models\Master\EmploymentType;
use App\Models\Master\TeachingTitle;
use App\Models\Master\Country;
use App\Models\Master\State;
use App\Models\Master\District;
use App\Models\Master\Bank;

use Validator;
// use DB;


/*=================================================== Master API =========================================================
Created By : Lakshmi kumari 
Created On : 11-Apr-2023 
Code Status : Open 
*/

class MasterController extends Controller
{
    //Class API Start    

    /**
     * @OA\Post(
     *   path="/add_class",
     *   tags={"Class"},
     *   summary="Add class",
     *   operationId="addClassTable",
     *   @OA\Parameter(name="className",in="query",required=true,@OA\Schema(type="integer",example="1")),       
     *   @OA\Parameter(name="classNameDisplay",in="query",required=true,@OA\Schema(type="string",example="I")),              
     *   @OA\Response(response=201,description="Success",@OA\MediaType(mediaType="application/json",)),
     *   @OA\Response(response=401,description="Unauthenticated"),
     *   @OA\Response(response=400,description="Bad Request"),
     *   @OA\Response(response=404,description="not found"),   
     *)
     **/
    public function addClassTable(Request $req)
    {
        //Description: store master records

        try {
            $data = array();
            $validator = Validator::make($req->all(), [
                'classNameDisplay' => 'required|string|regex:/^[iIvVxX]+$/|max:4',
                'className' => 'required|numeric|digits_between:1,2',
                // 'class_name' => 'required|string|unique:class_tables|max:30'
                // 'className' => 'required|string|regex:/^[iIvVxX]+$/|max:4',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json([
                    'error' => $errors
                ], 400);
            }
            if ($validator->passes()) {
                $mObject = new ClassTable();
                $data = $mObject->insertData($req);
                $mDeviceId = $req->deviceId ?? "";
                return responseMsgs(true, "Records added successfully", $data, "API_ID_10", "", "168ms", "post", $mDeviceId);
            }
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_10", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Get(
     *  path="/view_class",
     *  operationId="viewClassTable",
     *  tags={"Class"},
     *  summary="Get list of class",
     *  description="Get list of class",       
     *  @OA\Response(response=200, description="Success",
     *  @OA\JsonContent(@OA\Property(property="status", type="string", example="200"),
     *  @OA\Property(property="data",type="object"))))
     **/
    public function viewClassTable()
    {
        //Description: Get all records
        try {
            $data = ClassTable::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_11", "", "186ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_11", "", "", "get", $mDeviceId);
        }
    }

    public function viewClassTableById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new ClassTable();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_12", "", "146ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_12", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Post(
     * path="/edit_class",
     * tags={"Class"},
     * summary="Edit Class",
     * operationId="editClassTable",
     * @OA\Parameter(name="id",in="query",required=true,@OA\Schema(type="integer",example="1")),
     * @OA\Parameter(name="className",in="query",required=true,@OA\Schema(type="integer",example="1")),
     * @OA\Parameter(name="classNameDisplay",in="query",required=true,@OA\Schema(type="string",example="I")),
     * @OA\Response(response=200, description="Success",@OA\JsonContent(
     * @OA\Property(property="status", type="integer", example=""),
     * @OA\Property(property="data",type="object")
     *  )))
     **/
    public function editClassTable(Request $req)
    {
        //Description: edit records of a particular id 
        // $data = array();
        // $validator = Validator::make($req->all(), [
        //     'id' => 'required|numeric',
        //     'className' => 'required|string|regex:/^[iIvVxX]+$/|max:30',
        //     'classNameDisplay'=> 'required|numeric|digits_between:1,2'
        // ]);
        try {
            $data = array();
            $validator = Validator::make($req->all(), [
                'classNameDisplay' => 'required|string|regex:/^[iIvVxX]+$/|max:4',
                'className' => 'required|numeric|digits_between:1,2'
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json([
                    'error' => $errors
                ], 400);
            }
            if ($validator->passes()) {
                $mObject = new ClassTable();
                $data = $mObject->updateData($req);
                $mDeviceId = $req->deviceId ?? "";
                return responseMsgs(true, "Records updated successfully", $data, "API_ID_13", "", "213ms", "post", $mDeviceId);
            }
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_13", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Post(
     * path="/delete_class",
     * operationId="deleteClassTableById",
     * tags={"Class"},
     * summary="Delete Class",
     * description="Delete Class",
     * @OA\RequestBody(required=true,@OA\JsonContent(required={"id"},
     * @OA\Property(property="id", type="string", format="string", example="1"),),),
     * @OA\Response(response=200, description="Success",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="integer", example=""),
     *    @OA\Property(property="data",type="object")
     * )))
     **/
    public function deleteClassTableById(Request $req)
    {
        //Description: delete record of a particular id
        $data = array();
        $mDeviceId = $req->deviceId ?? "";
        try {
            $mObject = new ClassTable();
            $data = $mObject->deleteData($req);
            // $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_14", "", "173ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_14", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllClassTable()
    {
        //Description: delete all records 
        try {
            $mObject = new ClassTable();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_15", "", "175ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_15", "", "", "delete", $mDeviceId);
        }
    }
    //Class API End

    //Subject API Start 
    public function addSubject(Request $req)
    {
        //Description: store master records
        $data = array();
        $validator = Validator::make($req->all(), [
            'subject_name' => 'required|string|unique:subjects|max:30'
        ]);
        try {
            $mObject = new Subject();
            $data = $mObject->insertData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records added successfully", $data, "API_ID_16", "", "210ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_16", "", "", "post", $mDeviceId);
        }
    }

    public function viewSubject()
    {
        //Description: Get all records
        try {
            $data = Subject::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_17", "", "173ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_17", "", "", "get", $mDeviceId);
        }
    }

    public function viewSubjectById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new Subject();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_18", "", "185ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_18", "", "", "post", $mDeviceId);
        }
    }

    public function editSubject(Request $req)
    {
        //Description: edit records of a particular id
        try {
            $mObject = new Subject();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_19", "", "143ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_19", "", "", "post", $mDeviceId);
        }
    }

    public function deleteSubjectById(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new Subject();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_20", "", "144ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_20", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllSubject()
    {
        //Description: delete all records 
        try {
            $mObject = new Subject();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_21", "", "166ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_21", "", "", "delete", $mDeviceId);
        }
    }
    //Subject API End

    //Section API Start
    /**
     * @OA\Post(
     *   path="/add_section",
     *   tags={"Section"},
     *   summary="Add Section",
     *   operationId="addSection",
     *   @OA\Parameter(name="sectionName",in="query",required=true,@OA\Schema(type="string",example="")),
     *   @OA\Response(response=201,description="Success",@OA\MediaType(mediaType="application/json",)),
     *   @OA\Response(response=401,description="Unauthenticated"),
     *   @OA\Response(response=400,description="Bad Request"),
     *   @OA\Response(response=404,description="not found"),   
     *)
     **/
    public function addSection(Request $req)
    {
        //Description: store master records         
        try {
            $data = array();
            $validator = Validator::make($req->all(), [
                'sectionName' => 'required|string|regex:/^[a-zA-z]+$/|max:1'
                // 'sectionName' => 'required|string|regex:/^[ABCDEFabcdef]+$/|max:1'
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json([
                    'error' => $errors
                ], 400);
            }
            if ($validator->passes()) {
                $mObject = new Section();
                $data = $mObject->insertData($req);
                $mDeviceId = $req->deviceId ?? "";
                return responseMsgs(true, "Records added successfully", $data, "API_ID_22", "", "180ms", "post", $mDeviceId);
            }
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_22", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Get(
     *  path="/view_section",
     *  operationId="viewSection",
     *  tags={"Section"},
     *  summary="Get list of sections",
     *  description="Get list of sections",       
     *  @OA\Response(response=200, description="Success",
     *  @OA\JsonContent(@OA\Property(property="status", type="string", example="200"),
     *  @OA\Property(property="data",type="object"))))
     **/
    public function viewSection()
    {
        //Description: Get all records
        try {
            $data = Section::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_23", "", "133ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_23", "", "", "get", $mDeviceId);
        }
    }

    public function viewSectionById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new Section();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_24", "", "160ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_24", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Post(
     * path="/edit_section",
     * tags={"Section"},
     * summary="Edit Section",
     * operationId="editSection",
     * @OA\Parameter(name="id",in="query",required=true,@OA\Schema(type="integer",example="")),
     * @OA\Parameter(name="sectionName",in="query",required=true,@OA\Schema(type="string",example="A")),
     * @OA\Response(response=200, description="Success",@OA\JsonContent(
     * @OA\Property(property="status", type="integer", example=""),
     * @OA\Property(property="data",type="object")
     *  )))
     **/
    public function editSection(Request $req)
    {
        //Description: edit records of a particular id
        try {
            $mObject = new Section();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_25", "", "161ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_25", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Post(
     * path="/delete_section",
     * operationId="deleteSectionById",
     * tags={"Section"},
     * summary="Delete Section",
     * description="Delete Section",
     * @OA\RequestBody(required=true,@OA\JsonContent(required={"id"},
     * @OA\Property(property="id", type="string", format="string", example="1"),),),
     * @OA\Response(response=200, description="Success",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="integer", example=""),
     * @OA\Property(property="data",type="object")
     * )))
     **/
    public function deleteSectionById(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new Section();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_26", "", "180ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_26", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllSection()
    {
        //Description: delete all records 
        try {
            $mObject = new Section();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_27", "", "167ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_27", "", "", "delete", $mDeviceId);
        }
    }
    //Section API End

    //Designation API Start
    public function addDesignation(Request $req)
    {
        //Description: store master records
        $data = array();
        $validator = Validator::make($req->all(), [
            'designation_name' => 'required|string|unique:designations|max:30'
        ]);
        try {
            $mObject = new Designation();
            $data = $mObject->insertData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records added successfully", $data, "API_ID_28", "", "159ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_28", "", "", "post", $mDeviceId);
        }
    }

    public function viewDesignation()
    {
        //Description: Get all records
        try {
            $data = Designation::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_29", "", "161ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_29", "", "", "get", $mDeviceId);
        }
    }

    public function viewDesignationById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new Designation();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_30", "", "171ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_30", "", "", "post", $mDeviceId);
        }
    }

    public function editDesignation(Request $req)
    {
        //Description: edit records of a particular id
        try {
            $mObject = new Designation();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_31", "", "142ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_31", "", "", "post", $mDeviceId);
        }
    }

    public function deleteDesignationById(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new Designation();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_32", "", "168ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_32", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllDesignation()
    {
        //Description: delete all records 
        try {
            $mObject = new Designation();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_33", "", "154ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_33", "", "", "delete", $mDeviceId);
        }
    }
    //Designation API End    

    //Board API Start
    public function addBoard(Request $req)
    {
        //Description: store master records
        $data = array();
        $validator = Validator::make($req->all(), [
            'board_name' => 'required|string|unique:boards|max:30'
        ]);
        try {
            $mObject = new Board();
            $data = $mObject->insertData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records added successfully", $data, "API_ID_34", "", "187ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_34", "", "", "post", $mDeviceId);
        }
    }

    public function viewBoard()
    {
        //Description: Get all records
        try {
            $data = Board::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_35", "", "147ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_35", "", "", "get", $mDeviceId);
        }
    }

    public function viewBoardById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new Board();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_36", "", "154ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_36", "", "", "post", $mDeviceId);
        }
    }

    public function editBoard(Request $req)
    {
        //Description: edit records of a particular id
        try {
            $mObject = new Board();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_37", "", "162ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_37", "", "", "post", $mDeviceId);
        }
    }

    public function deleteBoardById(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new Board();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_38", "", "176ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_38", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllBoard()
    {
        //Description: delete all records 
        try {
            $mObject = new Board();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_39", "", "185ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_39", "", "", "delete", $mDeviceId);
        }
    }
    //Board API End

    //Caste API Start
    public function addCaste(Request $req)
    {
        //Description: store master records
        $data = array();
        $validator = Validator::make($req->all(), [
            'caste_name' => 'required|string|unique:castes|max:30'
        ]);
        try {
            $mObject = new Caste();
            $data = $mObject->insertData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records added successfully", $data, "API_ID_40", "", "168ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_40", "", "", "post", $mDeviceId);
        }
    }

    public function viewCaste()
    {
        //Description: Get all records
        try {
            $data = Caste::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_41", "", "140ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_41", "", "", "get", $mDeviceId);
        }
    }

    public function viewCasteById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new Caste();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_42", "", "167ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_42", "", "", "post", $mDeviceId);
        }
    }

    public function editCaste(Request $req)
    {
        //Description: edit records of a particular id
        try {
            $mObject = new Caste();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_43", "", "161ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_43", "", "", "post", $mDeviceId);
        }
    }

    public function deleteCasteById(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new Caste();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_44", "", "153ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_44", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllCaste()
    {
        //Description: delete all records 
        try {
            $mObject = new Caste();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_45", "", "143ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_45", "", "", "delete", $mDeviceId);
        }
    }
    //Caste API End

    //Attendance API Start
    public function addAttendance(Request $req)
    {
        //Description: store master records
        $data = array();
        $validator = Validator::make($req->all(), [
            'attendance_name' => 'required|string|unique:attendances|max:30'
        ]);
        try {
            $mObject = new Attendance();
            $data = $mObject->insertData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records added successfully", $data, "API_ID_46", "", "177ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_46", "", "", "post", $mDeviceId);
        }
    }

    public function viewAttendance()
    {
        //Description: Get all records
        try {
            $data = Attendance::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_47", "", "166ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_47", "", "", "get", $mDeviceId);
        }
    }

    public function viewAttendanceById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new Attendance();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_48", "", "138ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_48", "", "", "post", $mDeviceId);
        }
    }

    public function editAttendance(Request $req)
    {
        //Description: edit records of a particular id
        try {
            $mObject = new Attendance();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_49", "", "143ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_49", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAttendanceById(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new Attendance();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_50", "", "164ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_50", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllAttendance()
    {
        //Description: delete all records 
        try {
            $mObject = new Attendance();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_51", "", "162ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_51", "", "", "delete", $mDeviceId);
        }
    }
    //Attendance API End

    //Certificate API Start
    public function addCertificate(Request $req)
    {
        //Description: store master records
        $data = array();
        $validator = Validator::make($req->all(), [
            'certificate_name' => 'required|string|unique:certificates|max:30'
        ]);
        try {
            $mObject = new Certificate();
            $data = $mObject->insertData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records added successfully", $data, "API_ID_52", "", "160ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_52", "", "", "post", $mDeviceId);
        }
    }

    public function viewCertificate()
    {
        //Description: Get all records
        try {
            $data = Certificate::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_53", "", "167ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_53", "", "", "get", $mDeviceId);
        }
    }

    public function viewCertificateById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new Certificate();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_54", "", "175ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_54", "", "", "post", $mDeviceId);
        }
    }

    public function editCertificate(Request $req)
    {
        //Description: edit records of a particular id
        try {
            $mObject = new Certificate();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_55", "", "134ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_55", "", "", "post", $mDeviceId);
        }
    }

    public function deleteCertificateById(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new Certificate();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_56", "", "130ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_56", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllCertificate()
    {
        //Description: delete all records 
        try {
            $mObject = new Certificate();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_57", "", "160ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_57", "", "", "delete", $mDeviceId);
        }
    }
    //Certificate API End

    //Financialyear API Start
    public function store(Request $req)
    {
        //Description: store master records  
        // print_r($req);die;      
        $data = array();
        $validator = Validator::make($req->all(), [
            'fyName' => 'required|string'
        ]);
        try {
            // echo $req->fyName; die;
            $mObject = new FinancialYear();
            $data = $mObject->insertData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records added successfully", $data, "API_ID_58", "", "169ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_58", "", "", "post", $mDeviceId);
        }
    }

    public function retrieveAll()
    {
        //Description: Get all records
        try {
            $data = FinancialYear::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_59", "", "155ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_59", "", "", "get", $mDeviceId);
        }
    }

    public function show(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new FinancialYear();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_60", "", "139ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_60", "", "", "post", $mDeviceId);
        }
    }

    public function edit(Request $req)
    {
        //Description: edit records of a particular id
        try {
            $mObject = new FinancialYear();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_61", "", "143ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_61", "", "", "post", $mDeviceId);
        }
    }

    public function delete(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new FinancialYear();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_62", "", "144ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_62", "", "", "post", $mDeviceId);
        }
    }

    // public function deleteAllFinancialYear(){
    //     //Description: delete all records 
    //     try {
    //         $mObject = new FinancialYear();
    //         $data = $mObject->truncateData();
    //         $mDeviceId = $req->deviceId ?? "";
    //         return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_63","", "180ms", "delete", $mDeviceId);
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), $data, "API_ID_63","", "", "delete", $mDeviceId);
    //     }    
    // }
    //Financialyear API End

    //Installment API Start
    public function addInstallment(Request $req)
    {
        //Description: store master records
        $data = array();
        $validator = Validator::make($req->all(), [
            'fy_name' => 'required|string|unique:financial_years|max:30'
        ]);
        try {
            $mObject = new Installment();
            $data = $mObject->insertData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records added successfully", $data, "API_ID_64", "", "178ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_64", "", "", "post", $mDeviceId);
        }
    }

    public function viewInstallment()
    {
        //Description: Get all records
        try {
            $data = Installment::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_65", "", "140ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_65", "", "", "get", $mDeviceId);
        }
    }

    public function viewInstallmentById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new Installment();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_66", "", "129ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_66", "", "", "post", $mDeviceId);
        }
    }

    public function editInstallment(Request $req)
    {
        //Description: edit records of a particular id
        try {
            $mObject = new Installment();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_67", "", "175ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_67", "", "", "post", $mDeviceId);
        }
    }

    public function deleteInstallmentById(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new Installment();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_68", "", "168ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_68", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllInstallment()
    {
        //Description: delete all records 
        try {
            $mObject = new Installment();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_69", "", "177ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_69", "", "177ms", "delete", $mDeviceId);
        }
    }
    //Installment API End

    //Leave API Start
    public function addLeave(Request $req)
    {
        //Description: store master records
        $data = array();
        $validator = Validator::make($req->all(), [
            'leave_name' => 'required|string|unique:leaves|max:30'
        ]);
        try {
            $mObject = new Leave();
            $data = $mObject->insertData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records added successfully", $data, "API_ID_70", "", "172ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_70", "", "", "post", $mDeviceId);
        }
    }

    public function viewLeave()
    {
        //Description: Get all records
        try {
            $data = Leave::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_71", "", "167ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_71", "", "", "get", $mDeviceId);
        }
    }

    public function viewLeaveById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new Leave();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_72", "", "152ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_72", "", "", "post", $mDeviceId);
        }
    }

    public function editLeave(Request $req)
    {
        //Description: edit records of a particular id
        try {
            $mObject = new Leave();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_73", "", "135ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_73", "", "", "post", $mDeviceId);
        }
    }

    public function deleteLeaveById(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new Leave();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_74", "", "155ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_74", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllLeave()
    {
        //Description: delete all records 
        try {
            $mObject = new Leave();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_75", "", "169ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_75", "", "", "delete", $mDeviceId);
        }
    }
    //Leave API End

    //Role API Start
    public function addRole(Request $req)
    {
        //Description: store master records
        $data = array();
        $validator = Validator::make($req->all(), [
            'role_name' => 'required|string|unique:roles|max:30'
        ]);
        try {
            $mObject = new Role();
            $data = $mObject->insertData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records added successfully", $data, "API_ID_76", "", "177ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_76", "", "", "post", $mDeviceId);
        }
    }

    public function viewRole()
    {
        //Description: Get all records
        try {
            $data = Role::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_77", "", "141ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_77", "", "", "get", $mDeviceId);
        }
    }

    public function viewRoleById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new Role();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_78", "", "184ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_78", "", "", "post", $mDeviceId);
        }
    }

    public function editRole(Request $req)
    {
        //Description: edit records of a particular id
        try {
            $mObject = new Role();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_79", "", "159ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_79", "", "", "post", $mDeviceId);
        }
    }

    public function deleteRoleById(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new Role();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_80", "", "159ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_80", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllRole()
    {
        //Description: delete all records 
        try {
            $mObject = new Role();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_81", "", "169ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_81", "", "", "delete", $mDeviceId);
        }
    }
    //Role API End

    //Sport API Start
    public function addSport(Request $req)
    {
        //Description: store master records
        $data = array();
        $validator = Validator::make($req->all(), [
            'sport_name' => 'required|string|unique:sports|max:30'
        ]);
        try {
            $mObject = new Sport();
            $data = $mObject->insertData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records added successfully", $data, "API_ID_82", "", "186ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_82", "", "", "post", $mDeviceId);
        }
    }

    public function viewSport()
    {
        //Description: Get all records
        try {
            $data = Sport::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_83", "", "144ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_83", "", "", "get", $mDeviceId);
        }
    }

    public function viewSportById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new Sport();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_84", "", "171ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_84", "", "", "post", $mDeviceId);
        }
    }

    public function editSport(Request $req)
    {
        //Description: edit records of a particular id
        try {
            $mObject = new Sport();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_85", "", "177ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_85", "", "", "post", $mDeviceId);
        }
    }

    public function deleteSportById(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new Sport();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_86", "", "176ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_86", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllSport()
    {
        //Description: delete all records 
        try {
            $mObject = new Sport();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_87", "", "155ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_87", "", "", "delete", $mDeviceId);
        }
    }
    //Sport API End

    //Timetable API Start
    public function addTimeTable(Request $req)
    {
        //Description: store master records
        $data = array();
        $validator = Validator::make($req->all(), [
            'time_tbl_name' => 'required|string|unique:time_tables|max:30'
        ]);
        try {
            $mObject = new TimeTable();
            $data = $mObject->insertData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records added successfully", $data, "API_ID_88", "", "175ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_88", "", "", "post", $mDeviceId);
        }
    }

    public function viewTimeTable()
    {
        //Description: Get all records
        try {
            $data = TimeTable::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_89", "", "174ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_89", "", "", "get", $mDeviceId);
        }
    }

    public function viewTimeTableById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new TimeTable();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_90", "", "131ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_90", "", "", "post", $mDeviceId);
        }
    }

    public function editTimeTable(Request $req)
    {
        //Description: edit records of a particular id
        try {
            $mObject = new TimeTable();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_91", "", "177ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_91", "", "", "post", $mDeviceId);
        }
    }

    public function deleteTimeTableById(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new TimeTable();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_92", "", "165ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_92", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllTimeTable()
    {
        //Description: delete all records 
        try {
            $mObject = new TimeTable();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_93", "", "165ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_93", "", "", "delete", $mDeviceId);
        }
    }
    //Timetable API End

    //SchoolId API Start
    public function addSchoolId(Request $req)
    {
        //Description: store master records
        $data = array();
        $validator = Validator::make($req->all(), [
            'school_id'         => 'required|string|unique:school_ids|max:50',
            'school_name'       => 'required|string|max:200',
            'mobile_no'         => 'required|string|max:10',
            'email'             => 'required|string|email|max:100',
            'password'          => 'required|min:3',
            'country'           => 'required|string|max:50',
            'state'             => 'required|string|max:100',
            'city'              => 'required|string|max:100',
            'address'           => 'required|string|max:255',
            'pincode'           => 'required|string|max:8'
        ]);
        try {
            $mObject = new SchoolId();
            $data = $mObject->insertData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records added successfully", $data, "API_ID_94", "", "150ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_94", "", "", "post", $mDeviceId);
        }
    }

    public function viewSchoolId()
    {
        //Description: Get all records
        try {
            $data = SchoolId::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_95", "", "153ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_95", "", "", "get", $mDeviceId);
        }
    }

    public function viewSchoolIdById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new SchoolId();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_96", "", "174ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_96", "", "", "post", $mDeviceId);
        }
    }

    public function editSchoolId(Request $req)
    {
        //Description: edit records of a particular id
        try {
            $mObject = new SchoolId();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_97", "", "172ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_97", "", "", "post", $mDeviceId);
        }
    }

    public function deleteSchoolIdById(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new SchoolId();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_98", "", "149ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_98", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllSchoolId()
    {
        //Description: delete all records 
        try {
            $mObject = new SchoolId();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_99", "", "186ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_99", "", "", "delete", $mDeviceId);
        }
    }
    //SchoolId API End

    //Module API Start
    public function addModule(Request $req)
    {
        //Description: store master records
        $data = array();
        $validator = Validator::make($req->all(), [
            'module_name'         => 'required|string|unique:modules|max:50'
        ]);
        try {
            $mObject = new Module();
            $data = $mObject->insertData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records added successfully", $data, "API_ID_100", "", "176ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_100", "", "", "post", $mDeviceId);
        }
    }

    public function viewModule()
    {
        //Description: Get all records
        try {
            $data = Module::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_101", "", "138ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_101", "", "", "get", $mDeviceId);
        }
    }

    public function viewModuleById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new Module();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_102", "", "155ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_102", "", "", "post", $mDeviceId);
        }
    }

    public function editModule(Request $req)
    {
        //Description: edit records of a particular id
        try {
            $mObject = new Module();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_103", "", "159ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_103", "", "", "post", $mDeviceId);
        }
    }

    public function deleteModuleById(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new Module();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_104", "", "168ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_104", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllModule()
    {
        //Description: delete all records 
        try {
            $mObject = new Module();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_105", "", "180ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_105", "", "", "delete", $mDeviceId);
        }
    }
    //Module API End

    //Sub Module API Start
    public function addSubModule(Request $req)
    {
        //Description: store master records
        $data = array();
        $validator = Validator::make($req->all(), [
            'module_id'             => 'required',
            'sub_module_name'       => 'required|string|unique:sub_modules|max:50'
        ]);
        try {
            $mObject = new SubModule();
            $data = $mObject->insertData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records added successfully", $data, "API_ID_106", "", "153ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_106", "", "", "post", $mDeviceId);
        }
    }

    public function viewSubModule()
    {
        //Description: Get all records
        try {
            $data = SubModule::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_107", "", "139ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_107", "", "", "get", $mDeviceId);
        }
    }

    public function viewSubModuleById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new SubModule();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_108", "", "159ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_108", "", "", "post", $mDeviceId);
        }
    }

    public function editSubModule(Request $req)
    {
        //Description: edit records of a particular id
        try {
            $mObject = new SubModule();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_109", "", "161ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_109", "", "", "post", $mDeviceId);
        }
    }

    public function deleteSubModuleById(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new SubModule();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_110", "", "172ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_110", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllSubModule()
    {
        //Description: delete all records 
        try {
            $mObject = new SubModule();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_111", "", "160ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_111", "", "", "delete", $mDeviceId);
        }
    }
    //Sub Module API End

    //Department API Start 
    /**
     * @OA\Post(
     *   path="/add_department",
     *   tags={"Department"},
     *   summary="Add Department",
     *   operationId="addDepartment",
     *   @OA\Parameter(name="departmentName",in="query",required=true,@OA\Schema(type="string",example="")),       
     *   @OA\Parameter(name="abbreviationName",in="query",required=true,@OA\Schema(type="string",example="")),              
     *   @OA\Response(response=201,description="Success",@OA\MediaType(mediaType="application/json",)),
     *   @OA\Response(response=401,description="Unauthenticated"),
     *   @OA\Response(response=400,description="Bad Request"),
     *   @OA\Response(response=404,description="not found"),   
     *)
     **/
    public function addDepartment(Request $req)
    {
        //Description: store master records
        $data = array();
        $validator = Validator::make($req->all(), [
            'departmentName'       => 'required|string|regex:/^[a-zA-z]+$/|max:50',
            'abbreviationName'     => 'string'
        ]);
        try {

            $mObject = new Department();
            $data = $mObject->insertData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records added successfully", $data, "API_ID_112", "", "156ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_112", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Get(
     *  path="/view_department",
     *  operationId="viewDepartment",
     *  tags={"Department"},
     *  summary="Get list of Department",
     *  description="Get list of Department",       
     *  @OA\Response(response=200, description="Success",
     *  @OA\JsonContent(@OA\Property(property="status", type="string", example="200"),
     *  @OA\Property(property="data",type="object"))))
     **/
    public function viewDepartment()
    {
        //Description: Get all records
        try {
            $data = Department::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_113", "", "157ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_113", "", "", "get", $mDeviceId);
        }
    }

    public function viewDepartmentById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new Department();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_114", "", "156ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_114", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Post(
     * path="/edit_department",
     * tags={"Department"},
     * summary="Edit Department",
     * operationId="editDepartment",
     * @OA\Parameter(name="id",in="query",required=true,@OA\Schema(type="integer",example="")),
     * @OA\Parameter(name="departmentName",in="query",required=true,@OA\Schema(type="string",example="")),
     * @OA\Parameter(name="abbreviationName",in="query",required=true,@OA\Schema(type="string",example="")),
     * @OA\Response(response=200, description="Success",@OA\JsonContent(
     * @OA\Property(property="status", type="integer", example=""),
     * @OA\Property(property="data",type="object")
     *  )))
     **/
    public function editDepartment(Request $req)
    {
        //Description: edit records of a particular id
        $validator = Validator::make($req->all(), [
            'departmentName'       => 'required|string|regex:/^[a-zA-z]+$/|max:50',
            'abbreviationName'     => 'string'
        ]);
        try {
            $mObject = new Department();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_115", "", "148ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_115", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Post(
     * path="/delete_department",
     * operationId="deleteDepartmentById",
     * tags={"Department"},
     * summary="Delete Department",
     * description="Delete Department",
     * @OA\RequestBody(required=true,@OA\JsonContent(required={"id"},
     * @OA\Property(property="id", type="string", format="string", example="1"),),),
     * @OA\Response(response=200, description="Success",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="integer", example=""),
     *    @OA\Property(property="data",type="object")
     * )))
     **/
    public function deleteDepartmentById(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new Department();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_116", "", "137ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_116", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllDepartment()
    {
        //Description: delete all records 
        try {
            $mObject = new Department();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_117", "", "158ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_117", "", "", "delete", $mDeviceId);
        }
    }
    //Department API End

    //Course API Start
    public function addCourse(Request $req)
    {
        //Description: store master records
        $data = array();
        $validator = Validator::make($req->all(), [
            'course_name'       => 'required|string|unique:courses|max:50'
        ]);
        try {
            $mObject = new Course();
            $data = $mObject->insertData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records added successfully", $data, "API_ID_118", "", "182ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_118", "", "", "post", $mDeviceId);
        }
    }

    public function viewCourse()
    {
        //Description: Get all records
        try {
            $data = Course::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_119", "", "191ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_119", "", "", "get", $mDeviceId);
        }
    }

    public function viewCourseById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new Course();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_120", "", "160ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_120", "", "", "post", $mDeviceId);
        }
    }

    public function editCourse(Request $req)
    {
        //Description: edit records of a particular id
        try {
            $mObject = new Course();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_121", "", "161ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_121", "", "", "post", $mDeviceId);
        }
    }

    public function deleteCourseById(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new Course();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_122", "", "168ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_122", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllCourse()
    {
        //Description: delete all records 
        try {
            $mObject = new Course();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_123", "", "164ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_123", "", "", "delete", $mDeviceId);
        }
    }
    //Course API End

    //Semester API Start
    public function addSemester(Request $req)
    {
        //Description: store master records
        $data = array();
        $validator = Validator::make($req->all(), [
            'semester_name'       => 'required|string|unique:semesters|max:50'
        ]);
        try {
            $mObject = new Semester();
            $data = $mObject->insertData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records added successfully", $data, "API_ID_124", "", "176ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_124", "", "", "post", $mDeviceId);
        }
    }

    public function viewSemester()
    {
        //Description: Get all records
        try {
            $data = Semester::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_125", "", "149ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_125", "", "", "get", $mDeviceId);
        }
    }

    public function viewSemesterById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new Semester();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_126", "", "153ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_126", "", "", "post", $mDeviceId);
        }
    }

    public function editSemester(Request $req)
    {
        //Description: edit records of a particular id
        try {
            $mObject = new Semester();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_127", "", "133ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_127", "", "", "post", $mDeviceId);
        }
    }

    public function deleteSemesterById(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new Semester();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_128", "", "137ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_128", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllSemester()
    {
        //Description: delete all records 
        try {
            $mObject = new Semester();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_129", "", "170ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_129", "", "", "delete", $mDeviceId);
        }
    }
    //Semester API End 

    //Institution code start
    public function addInstitutionCode(Request $req)
    {
        //Description: store master records
        $data = array();
        $validator = Validator::make($req->all(), [
            'institution_code' => 'required|string|unique:institutions|max:30'
        ]);
        try {
            $mObject = new Institution();
            $data = $mObject->insertData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records added successfully", $data, "API_ID_136", "", "175ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_136", "", "", "post", $mDeviceId);
        }
    }

    public function viewInstitutionCode()
    {
        //Description: Get all records
        try {
            $data = Institution::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_137", "", "174ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_137", "", "", "get", $mDeviceId);
        }
    }

    public function viewInstitutionCodeById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new Institution();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_138", "", "131ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_138", "", "", "post", $mDeviceId);
        }
    }

    public function editInstitutionCode(Request $req)
    {
        //Description: edit records of a particular id
        try {
            $mObject = new Institution();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_139", "", "177ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_139", "", "", "post", $mDeviceId);
        }
    }

    public function deleteInstitutionCodeById(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new Institution();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_140", "", "165ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_140", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllInstitutionCode()
    {
        //Description: delete all records 
        try {
            $mObject = new Institution();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_141", "", "165ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_141", "", "", "delete", $mDeviceId);
        }
    }
    //Institution code end

    //Permission code start
    public function addPermission(Request $req)
    {
        //Description: store master records
        $data = array();
        $validator = Validator::make($req->all(), [
            'user_id' => 'required|integer',
            'module_id' => 'required|integer',
            'sub_module_id' => 'required|integer'
        ]);
        try {
            $mObject = new Permission();
            $data = $mObject->insertData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records added successfully", $data, "API_ID_142", "", "623ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_142", "", "", "post", $mDeviceId);
        }
    }

    public function viewPermission()
    {
        //Description: Get all records
        try {
            $data = Permission::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_143", "", "210ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_143", "", "", "get", $mDeviceId);
        }
    }

    public function viewPermissionById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new Permission();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_144", "", "192ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_144", "", "", "post", $mDeviceId);
        }
    }

    public function editPermission(Request $req)
    {
        //Description: edit records of a particular id
        try {
            $mObject = new Permission();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_145", "", "188ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_145", "", "", "post", $mDeviceId);
        }
    }

    public function deletePermissionById(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new Permission();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_146", "", "187ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_146", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllPermission()
    {
        //Description: delete all records 
        try {
            $mObject = new Permission();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_147", "", "194ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_147", "", "", "delete", $mDeviceId);
        }
    }
    //Permission code end

    //Miscellaneous category start
    /**
     * @OA\Post(
     *      path="/add_misc_category",
     *      operationId="add_misc_category",
     *      tags={"Miscellaneous Category"},
     *      summary="Add Miscellaneous Category",
     *      description="Add Miscellaneous Category",
     *      @OA\RequestBody(required=true,@OA\JsonContent(required={"misc_category_name"},
     *            @OA\Property(property="misc_category_name", type="string", format="string", example="Gender"),
     *         ),
     *      ),
     *     @OA\Response(response=200, description="Success",@OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=""),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
    public function addMiscCategory(Request $req)
    {
        //Description: store master records          
        try {
            $data = array();
            $validator = Validator::make($req->all(), [
                'misc_category_name' => 'required|string|unique:miscellaneous_categories|max:50',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json([
                    'error' => $errors
                ], 400);
            }
            if ($validator->passes()) {
                $mObject = new MiscellaneousCategory();
                $data = $mObject->insertData($req);
                $mDeviceId = $req->deviceId ?? "";
                $getResponseTime = responseTime();
                return responseMsgs(true, "Records added successfully", $data, "API_ID_148", "", $getResponseTime, "post", $mDeviceId);
            }
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_148", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Get(
     *    path="/view_misc_category",
     *    operationId="view_misc_category",
     *    tags={"Miscellaneous Category"},
     *    summary="View Miscellaneous Category",
     *    description="View Miscellaneous Category",           
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="200"),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
    public function viewMiscCategory()
    {
        //Description: Get all records
        try {
            $data = MiscellaneousCategory::list();
            $mDeviceId = $req->deviceId ?? "";
            $getResponseTime = responseTime();
            return responseMsgs(true, "View all records", $data, "API_ID_149", "", $getResponseTime, "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_149", "", "", "get", $mDeviceId);
        }
    }

    /**
     * @OA\Post(
     *      path="/view_misc_category_by_id",
     *      operationId="view_misc_category_by_id",
     *      tags={"Miscellaneous Category"},
     *      summary="View Miscellaneous Category",
     *      description="View Miscellaneous Category",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"id"},
     *            @OA\Property(property="id", type="string", format="string", example="1"),
     *         ),
     *      ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=""),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
    public function viewMiscCategoryById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new MiscellaneousCategory();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            $getResponseTime = responseTime();
            return responseMsgs(true, "View all records", $data, "API_ID_150", "", $getResponseTime, "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_150", "", "", "post", $mDeviceId);
        }
    }


    /**
     * @OA\Post(
     ** path="/edit_misc_category",
     *  tags={"Miscellaneous Category"},
     *  summary="Edit Miscellaneous Category",
     *  operationId="Edit Miscellaneous Category",
     *  @OA\Parameter(name="id",in="query",required=true,@OA\Schema(type="integer")),
     *  @OA\Parameter(name="misc_category_name",in="query",required=true,@OA\Schema(type="string")),
     *  @OA\Response(response=200, description="Success",@OA\JsonContent(
     *     @OA\Property(property="status", type="integer", example=""),
     *     @OA\Property(property="data",type="object")
     *    )
     *  )
     * )
     **/
    public function editMiscCategory(Request $req)
    {
        //Description: edit records of a particular id
        try {
            $mObject = new MiscellaneousCategory();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            $getResponseTime = responseTime();
            //$auth = authUser();
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_151", "", $getResponseTime, "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_151", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Post(
     *      path="/delete_misc_category",
     *      operationId="delete_misc_category",
     *      tags={"Miscellaneous Category"},
     *      summary="Delete Miscellaneous Category",
     *      description="Delete Miscellaneous Category",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"id"},
     *            @OA\Property(property="id", type="string", format="string", example="1"),
     *         ),
     *      ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=""),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
    public function deleteMiscCategoryById(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new MiscellaneousCategory();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_152", "", "187ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_152", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllMiscCategory()
    {
        //Description: delete all records 
        try {
            $mObject = new MiscellaneousCategory();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_153", "", "194ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_153", "", "", "delete", $mDeviceId);
        }
    }
    //Miscellaneous category code end

    /**
     * @OA\Post(
     ** path="/add_misc_sub_category",
     *   tags={"Miscellaneous Sub Category"},
     *   summary="Add Miscellaneous Sub Category",
     *   operationId="Add Miscellaneous Sub Category",
     *
     *  @OA\Parameter(
     *      name="misc_category_id",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="misc_sub_category_name",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),          
     *   @OA\Response(
     *      response=201,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     
     *)
     **/
    public function addMiscSubCategory(Request $req)
    {
        //Description: store master records
        $data = array();
        $validator = Validator::make($req->all(), [
            'misc_sub_category_name' => 'required|string|unique:miscellaneous_sub_categories|max:50',
        ]);
        try {
            $mObject = new MiscellaneousSubCategory();
            $data = $mObject->insertData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records added successfully", $data, "API_ID_154", "", "623ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_154", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\post(
     *    path="/view_misc_sub_category",
     *    operationId="view_misc_sub_category",
     *    tags={"Miscellaneous Sub Category"},
     *    summary="View Miscellaneous Sub Category",
     *    description="View Miscellaneous Sub Category",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"id"},
     *            @OA\Property(property="id", type="string", format="string", example="1"),
     *         ),
     *      ),           
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="200"),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
    public function viewMiscSubCategory(Request $req)
    {
        //Description: Get all records
        try {
            $data = array();
            $getData = new MiscellaneousSubCategory();
            // $mDeviceId = $req->deviceId ?? "";
            $data  = $getData->list($req);
            return responseMsgs(true, "View all records", $data, "API_ID_155", "", "210ms", "get", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_155", "", "", "get", $req->deviceId ?? "");
        }
    }
    // public function viewMiscSubCategory(){
    //     //Description: Get all records
    //     try {
    //         $data = MiscellaneousSubCategory::list(); 
    //         $mDeviceId = $req->deviceId ?? "";
    //         return responseMsgs(true, "View all records", $data, "API_ID_155","", "210ms", "get", $mDeviceId);
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), $data, "API_ID_155","", "", "get", $mDeviceId);
    //     }
    // }

    /**
     * @OA\Post(
     *      path="/view_misc_sub_category_by_id",
     *      operationId="viewMiscSubCategoryById",
     *      tags={"Miscellaneous Sub Category"},
     *      summary="View Miscellaneous Category",
     *      description="View Miscellaneous Category",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"id"},
     *            @OA\Property(property="id", type="string", format="string", example="1"),
     *         ),
     *      ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=""),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
    public function viewMiscSubCategoryById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new MiscellaneousSubCategory();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_156", "", "192ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_156", "", "", "post", $mDeviceId);
        }
    }

    // /**
    //  * @OA\Post(
    //  *      path="/view_misc_sub_category_by_name",
    //  *      operationId="view_misc_sub_category_by_name",
    //  *      tags={"View Miscellaneous Sub Category By Name"},
    //  *      summary="View Miscellaneous Sub Category By Name",
    //  *      description="View Miscellaneous Sub Category By Name",
    //  *      @OA\RequestBody(
    //  *         required=true,
    //  *         @OA\JsonContent(
    //  *            required={"misc_category_name"},
    //  *            @OA\Property(property="misc_category_name", type="string", format="string", example="gender"),
    //  *         ),
    //  *      ),
    //  *     @OA\Response(
    //  *          response=200, description="Success",
    //  *          @OA\JsonContent(
    //  *             @OA\Property(property="status", type="integer", example=""),
    //  *             @OA\Property(property="data",type="object")
    //  *          )
    //  *       )
    //  *  )
    //  */

    /**
     * @OA\Get(
     *    path="/view_misc_sub_category_by_name",
     *    operationId="view_misc_sub_category_by_name",
     *    tags={"Miscellaneous Sub Category By Name"},
     *    summary="View Miscellaneous Sub Category By Name",
     *    description="View Miscellaneous Sub Category By Name",           
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="200"),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
    public function viewMiscSubCategoryByName(Request $req)
    {
        //Description: Get records by name
        try {
            $listbyName = new MiscellaneousSubCategory();
            $data  = $listbyName->listByName($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_157", "", "192ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_157", "", "", "post", $mDeviceId);
        }
    }


    /**
     * @OA\Post(
     ** path="/edit_misc_sub_category",
     *   tags={"Miscellaneous Sub Category"},
     *   summary="Edit Miscellaneous Sub Category",
     *   operationId="Edit Miscellaneous Sub Category",
     *
     *  @OA\Parameter(
     *      name="id",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="integer"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="misc_sub_category_name",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *   @OA\Response(
     *        response=200, description="Success",
     *        @OA\JsonContent(
     *        @OA\Property(property="status", type="integer", example=""),
     *        @OA\Property(property="data",type="object")
     *      )
     *    )
     
     *)
     **/
    public function editMiscSubCategory(Request $req)
    {
        //Description: edit records of a particular id
        try {
            $mObject = new MiscellaneousSubCategory();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_158", "", "188ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_158", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Post(
     *      path="/delete_misc_sub_category",
     *      operationId="delete_misc_sub_category",
     *      tags={"Miscellaneous Sub Category"},
     *      summary="Delete Miscellaneous Sub Category",
     *      description="Delete Miscellaneous Sub Category",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"id"},
     *            @OA\Property(property="id", type="string", format="string", example="1"),
     *         ),
     *      ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=""),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
    public function deleteMiscSubCategoryById(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new MiscellaneousSubCategory();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_159", "", "187ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_159", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllMiscSubCategory()
    {
        //Description: delete all records 
        try {
            $mObject = new MiscellaneousSubCategory();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_160", "", "194ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_160", "", "", "delete", $mDeviceId);
        }
    }
    //Miscellaneous sub category code end

    //Employment Type API Start    
    /**
     * @OA\Post(
     ** path="/add_emp_type",
     *   tags={"Employment Type"},
     *   summary="Add Employment Type",
     *   operationId="addEmpType",
     *
     *  @OA\Parameter(
     *      name="emp_type_name",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string",
     *           example="Permanent"
     *      )
     *   ),       
     *   @OA\Response(
     *      response=201,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     
     *)
     **/
    public function addEmpType(Request $req)
    {
        //Description: store master records
        $data = array();
        $validator = Validator::make($req->all(), [
            'emp_type_name'       => 'required|string|unique:employment_types|max:50'
        ]);
        try {
            $mObject = new EmploymentType();
            $data = $mObject->insertData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records added successfully", $data, "API_ID_112", "", "156ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_112", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Get(
     *    path="/view_emp_type",
     *    operationId="viewEmpType",
     *    tags={"Employment Type"},
     *    summary="View Employment Type",
     *    description="View Employment Type",           
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="200"),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
    public function viewEmpType()
    {
        //Description: Get all records
        try {
            $data = EmploymentType::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_113", "", "157ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_113", "", "", "get", $mDeviceId);
        }
    }

    public function viewEmpTypeById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new EmploymentType();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_114", "", "156ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_114", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Post(
     ** path="/edit_emp_type",
     *   tags={"Employment Type"},
     *   summary="Edit Employment Type",
     *   operationId="editEmpType",
     *
     *  @OA\Parameter(
     *      name="id",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="integer",
     *           example="1"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="emp_type_name",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string",
     *           example="contractual"
     *      )
     *   ),
     *   @OA\Response(
     *        response=200, description="Success",
     *        @OA\JsonContent(
     *        @OA\Property(property="status", type="integer", example=""),
     *        @OA\Property(property="data",type="object")
     *      )
     *    )
     
     *)
     **/
    public function editEmpType(Request $req)
    {
        //Description: edit records of a particular id
        $validator = Validator::make($req->all(), [
            'emp_type_name'       => 'required|string|unique:employment_types|max:50'
        ]);
        try {
            $mObject = new EmploymentType();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_115", "", "148ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_115", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Post(
     *      path="/delete_emp_type",
     *      operationId="deleteEmpTypeById",
     *      tags={"Employment Type"},
     *      summary="Delete Employment Type",
     *      description="Delete Employment Type",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"id"},
     *            @OA\Property(property="id", type="string", format="string", example="1"),
     *         ),
     *      ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=""),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     **/
    public function deleteEmpTypeById(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new EmploymentType();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_116", "", "137ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_116", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllEmpType()
    {
        //Description: delete all records 
        try {
            $mObject = new EmploymentType();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_117", "", "158ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_117", "", "", "delete", $mDeviceId);
        }
    }
    //Employement Type API End

    //Teaching Title API Start    
    /**
     * @OA\Post(
     ** path="/add_teaching_type",
     *   tags={"Teaching Title"},
     *   summary="Add Teaching Title",
     *   operationId="addTeachingTitle",
     *
     *  @OA\Parameter(
     *      name="teaching_title_name",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string",
     *           example="prt"
     *      )
     *   ),       
     *   @OA\Response(
     *      response=201,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     
     *)
     **/
    public function addTeachingTitle(Request $req)
    {
        //Description: store master records
        $data = array();
        $validator = Validator::make($req->all(), [
            'emp_type_name'       => 'required|string|unique:teaching_titles|max:50'
        ]);
        try {
            $mObject = new TeachingTitle();
            $data = $mObject->insertData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records added successfully", $data, "API_ID_112", "", "156ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_112", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Get(
     *    path="/view_teaching_type",
     *    operationId="viewTeachingTitle",
     *    tags={"Teaching Title"},
     *    summary="View Teaching Title",
     *    description="View Teaching Title",           
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="200"),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */
    public function viewTeachingTitle()
    {
        //Description: Get all records
        try {
            $data = TeachingTitle::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_113", "", "157ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_113", "", "", "get", $mDeviceId);
        }
    }

    public function viewTeachingTitleById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new TeachingTitle();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_114", "", "156ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_114", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Post(
     ** path="/edit_teaching_type",
     *   tags={"Teaching Title"},
     *   summary="Edit Teaching Title",
     *   operationId="editTeachingTitle",
     *
     *  @OA\Parameter(
     *      name="id",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="integer",
     *           example="1"
     *      )
     *   ),
     *  @OA\Parameter(
     *      name="teaching_title_name",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string",
     *           example="tgt"
     *      )
     *   ),
     *   @OA\Response(
     *        response=200, description="Success",
     *        @OA\JsonContent(
     *        @OA\Property(property="status", type="integer", example=""),
     *        @OA\Property(property="data",type="object")
     *      )
     *    )
     
     *)
     **/
    public function editTeachingTitle(Request $req)
    {
        //Description: edit records of a particular id
        $validator = Validator::make($req->all(), [
            'emp_type_name'       => 'required|string|unique:teaching_titles|max:50'
        ]);
        try {
            $mObject = new TeachingTitle();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_115", "", "148ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_115", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Post(
     *      path="/delete_teaching_type",
     *      operationId="deleteTeachingTitleById",
     *      tags={"Teaching Title"},
     *      summary="Delete Teaching Title",
     *      description="Delete Teaching Title",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"id"},
     *            @OA\Property(property="id", type="string", format="string", example="1"),
     *         ),
     *      ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=""),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     **/
    public function deleteTeachingTitleById(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new TeachingTitle();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_116", "", "137ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_116", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllTeachingTitle()
    {
        //Description: delete all records 
        try {
            $mObject = new TeachingTitle();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_117", "", "158ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_117", "", "", "delete", $mDeviceId);
        }
    }
    //Teaching Title API End

    //Country API Start
    /**
     *  @OA\Post(
     *  path="/add_country",
     *  tags={"Country"},
     *  summary="Add Country",
     *  operationId="addCountry",     
     *  @OA\Parameter(name="country_name",in="query",required=true,@OA\Schema(type="string",example="India")),       
     *  @OA\Response(response=201,description="Success",@OA\MediaType(mediaType="application/json",)),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *  @OA\Response(response=400,description="Bad Request"),
     *  @OA\Response(response=404,description="not found"),     
     *)
     **/
    public function addCountry(Request $req)
    {
        //Description: store country records
        $data = array();
        $validator = Validator::make($req->all(), [
            'country_name' => 'required|string|unique:countries|max:50'
        ]);

        try {
            $mObject = new Country();
            $data = $mObject->insertData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records added successfully", $data, "API_ID_28", "", "180 ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_28", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Get(
     * path="/view_country",
     * operationId="viewCountry",
     * tags={"Country"},
     * summary="View Country",
     * description="View Country",           
     * @OA\Response(response=200, description="Success",
     * @OA\JsonContent(@OA\Property(property="status", type="string", example="200"),
     *  @OA\Property(property="data",type="object"))))
     */
    public function viewCountry(Request $req)
    {
        //Description: Get all records
        try {
            $data = Country::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_29", "", "168 ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_29", "", "", "get", $mDeviceId);
        }
    }

    public function viewCountryById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new Country();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_30", "", "146ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_30", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Post(
     * path="/edit_country",
     * tags={"Country"},
     * summary="Edit Country",
     * operationId="editCountry",
     * @OA\Parameter(name="id",in="query",required=true,@OA\Schema(type="integer",example="1")),
     * @OA\Parameter(name="country_name",in="query",required=true,@OA\Schema(type="string",example=" ")),
     * @OA\Response(response=200, description="Success",@OA\JsonContent(
     *    @OA\Property(property="status", type="integer", example=""),
     *    @OA\Property(property="data",type="object")
     *  )))
     **/
    public function editCountry(Request $req)
    {
        //Description: edit records of a particular id 
        try {
            $mObject = new Country();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_31", "", "213ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_31", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Post(
     * path="/delete_country",
     * operationId="deleteCountryById",
     * tags={"Country"},
     * summary="Delete Country",
     * description="Delete Country",
     * @OA\RequestBody(required=true,@OA\JsonContent(required={"id"},
     * @OA\Property(property="id", type="string", format="string", example="1"),),),
     * @OA\Response(response=200, description="Success",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="integer", example=""),
     *    @OA\Property(property="data",type="object")
     * )))
     **/
    public function deleteCountryById(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new Country();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_32", "", "173ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_32", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllCountry(Request $req)
    {
        //Description: delete all records 
        try {
            $mObject = new Country();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_33", "", "175ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_33", "", "", "delete", $mDeviceId);
        }
    }
    //Country API End


    //State API Start
    /**
     *  @OA\Post(
     *  path="/add_state",
     *  tags={"State"},
     *  summary="Add State",
     *  operationId="addState", 
     *  @OA\Parameter(name="country_id",in="query",required=true,@OA\Schema(type="string",example="1")),    
     *  @OA\Parameter(name="state_name",in="query",required=true,@OA\Schema(type="string",example="Jharkhand")),       
     *  @OA\Response(response=201,description="Success",@OA\MediaType(mediaType="application/json",)),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *  @OA\Response(response=400,description="Bad Request"),
     *  @OA\Response(response=404,description="not found"),     
     *)
     **/
    public function addState(Request $req)
    {
        //Description: store master records
        $data = array();
        $validator = Validator::make($req->all(), [
            'country_id' => 'required|numeric',
            'state_name' => 'required|string|unique:states|max:50'
        ]);

        try {
            $mObject = new State();
            $data = $mObject->insertData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records added successfully", $data, "API_ID_34", "", "180ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_34", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Get(
     * path="/view_state",
     * operationId="viewState",
     * tags={"State"},
     * summary="View State",
     * description="View State",           
     * @OA\Response(response=200, description="Success",
     * @OA\JsonContent(@OA\Property(property="status", type="string", example="200"),
     *  @OA\Property(property="data",type="object"))))
     */
    public function viewState(Request $req)
    {
        //Description: Get all records
        try {
            $data = State::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_35", "", "181ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_35", "", "", "get", $mDeviceId);
        }
    }

    public function viewStateById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new State();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_36", "", "162ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_36", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Post(
     * path="/edit_state",
     * tags={"State"},
     * summary="Edit State",
     * operationId="editState",
     * @OA\Parameter(name="id",in="query",required=true,@OA\Schema(type="integer",example="1")),
     * @OA\Parameter(name="state_name",in="query",required=true,@OA\Schema(type="string",example=" ")),
     * @OA\Response(response=200, description="Success",@OA\JsonContent(
     *    @OA\Property(property="status", type="integer", example=""),
     *    @OA\Property(property="data",type="object")
     *  )))
     **/
    public function editState(Request $req)
    {
        //Description: edit records of a particular id 
        try {
            $mObject = new State();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_37", "", "162ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_37", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Post(
     * path="/delete_state",
     * operationId="deleteStateById",
     * tags={"State"},
     * summary="Delete State",
     * description="Delete State",
     * @OA\RequestBody(required=true,@OA\JsonContent(required={"id"},
     * @OA\Property(property="id", type="string", format="string", example="1"),),),
     * @OA\Response(response=200, description="Success",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="integer", example=""),
     *    @OA\Property(property="data",type="object")
     * )))
     **/
    public function deleteStateById(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new State();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_38", "", "164ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_38", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllState(Request $req)
    {
        //Description: delete all records 
        try {
            $mObject = new State();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_39", "", "161ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_39", "", "", "delete", $mDeviceId);
        }
    }
    //State API End

    //District API Start
    /**
     *  @OA\Post(
     *  path="/add_district",
     *  tags={"District"},
     *  summary="Add District",
     *  operationId="addDistrict",     
     *  @OA\Parameter(name="country_id",in="query",required=true,@OA\Schema(type="string",example="1")), 
     *  @OA\Parameter(name="state_id",in="query",required=true,@OA\Schema(type="string",example="1")),
     *  @OA\Parameter(name="district_name",in="query",required=true,@OA\Schema(type="string",example="Ranchi")),      
     *  @OA\Response(response=201,description="Success",@OA\MediaType(mediaType="application/json",)),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *  @OA\Response(response=400,description="Bad Request"),
     *  @OA\Response(response=404,description="not found"),     
     *)
     **/
    public function addDistrict(Request $req)
    {
        //Description: store master records
        $data = array();
        $validator = Validator::make($req->all(), [
            'country_id' => 'required|numeric',
            'state_id' => 'required|numeric',
            'district_name' => 'required|string|unique:districts|max:50'
        ]);

        try {
            $mObject = new District();
            $data = $mObject->insertData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records added successfully", $data, "API_ID_40", "", "180ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_40", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Get(
     * path="/view_district",
     * operationId="viewDistrict",
     * tags={"District"},
     * summary="View District",
     * description="View District",           
     * @OA\Response(response=200, description="Success",
     * @OA\JsonContent(@OA\Property(property="status", type="string", example="200"),
     *  @OA\Property(property="data",type="object"))))
     */
    public function viewDistrict(Request $req)
    {
        //Description: Get all records
        try {
            $data = District::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_41", "", "181ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_41", "", "", "get", $mDeviceId);
        }
    }

    public function viewDistrictById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new District();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_42", "", "187ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_42", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Post(
     * path="/edit_district",
     * tags={"District"},
     * summary="Edit District",
     * operationId="editDistrict",
     * @OA\Parameter(name="id",in="query",required=true,@OA\Schema(type="integer",example="1")),
     * @OA\Parameter(name="district_name",in="query",required=true,@OA\Schema(type="string",example=" ")),
     * @OA\Response(response=200, description="Success",@OA\JsonContent(
     *    @OA\Property(property="status", type="integer", example=""),
     *    @OA\Property(property="data",type="object")
     *  )))
     **/
    public function editDistrict(Request $req)
    {
        //Description: edit records of a particular id 
        try {
            $mObject = new District();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_43", "", "172ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_43", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Post(
     * path="/delete_district",
     * operationId="deleteDistrictById",
     * tags={"District"},
     * summary="Delete District",
     * description="Delete District",
     * @OA\RequestBody(required=true,@OA\JsonContent(required={"id"},
     * @OA\Property(property="id", type="string", format="string", example="1"),),),
     * @OA\Response(response=200, description="Success",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="integer", example=""),
     *    @OA\Property(property="data",type="object")
     * )))
     **/
    public function deleteDistrictById(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new District();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_44", "", "164ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_44", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllDistrict(Request $req)
    {
        //Description: delete all records 
        try {
            $mObject = new District();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_45", "", "161ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_45", "", "", "delete", $mDeviceId);
        }
    }
    //District API End

    //Bank API Start
    /**
     *  @OA\Post(
     *  path="/add_bank",
     *  tags={"Bank"},
     *  summary="Add Bank",
     *  operationId="addBank",     
     *  @OA\Parameter(name="bank_name",in="query",required=true,@OA\Schema(type="string",example="SBI")),       
     *  @OA\Response(response=201,description="Success",@OA\MediaType(mediaType="application/json",)),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *  @OA\Response(response=400,description="Bad Request"),
     *  @OA\Response(response=404,description="not found"),     
     *)
     **/
    public function addBank(Request $req)
    {
        //Description: store country records
        $data = array();
        $validator = Validator::make($req->all(), [
            'bank_name' => 'required|string|unique:banks|max:50'
        ]);

        try {
            $mObject = new Bank();
            $data = $mObject->insertData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records added successfully", $data, "API_ID_191", "", "193ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_191", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Get(
     * path="/view_bank",
     * operationId="viewBank",
     * tags={"Bank"},
     * summary="View Bank",
     * description="View Bank",           
     * @OA\Response(response=200, description="Success",
     * @OA\JsonContent(@OA\Property(property="status", type="string", example="200"),
     *  @OA\Property(property="data",type="object"))))
     */
    public function viewBank(Request $req)
    {
        //Description: Get all records
        try {
            $data = Bank::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_192", "", "378ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_192", "", "", "get", $mDeviceId);
        }
    }

    public function viewBankById(Request $req)
    {
        //Description: Get records by id
        try {
            $listbyId = new Bank();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_193", "", "174ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_193", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Post(
     * path="/edit_bank",
     * tags={"Bank"},
     * summary="Edit Bank",
     * operationId="editBank",
     * @OA\Parameter(name="id",in="query",required=true,@OA\Schema(type="integer",example="1")),
     * @OA\Parameter(name="bank_name",in="query",required=true,@OA\Schema(type="string",example=" ")),
     * @OA\Response(response=200, description="Success",@OA\JsonContent(
     *    @OA\Property(property="status", type="integer", example=""),
     *    @OA\Property(property="data",type="object")
     *  )))
     **/
    public function editBank(Request $req)
    {
        //Description: edit records of a particular id 
        try {
            $mObject = new Bank();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_194", "", "347ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_194", "", "", "post", $mDeviceId);
        }
    }

    /**
     * @OA\Post(
     * path="/delete_bank",
     * operationId="deleteBankById",
     * tags={"Bank"},
     * summary="Delete Bank",
     * description="Delete Bank",
     * @OA\RequestBody(required=true,@OA\JsonContent(required={"id"},
     * @OA\Property(property="id", type="string", format="string", example="1"),),),
     * @OA\Response(response=200, description="Success",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="integer", example=""),
     *    @OA\Property(property="data",type="object")
     * )))
     **/
    public function deleteBankById(Request $req)
    {
        //Description: delete record of a particular id
        try {
            $mObject = new Bank();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_195", "", "173ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_195", "", "", "post", $mDeviceId);
        }
    }

    public function deleteAllBank(Request $req)
    {
        //Description: delete all records 
        try {
            $mObject = new Bank();
            $data = $mObject->truncateData();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_196", "", "193ms", "delete", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_196", "", "", "delete", $mDeviceId);
        }
    }
    //Bank API End


}
//=================================================== End Master API ======================================================
