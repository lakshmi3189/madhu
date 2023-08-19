<?php

namespace App\Http\Controllers\API\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeEducation;
use App\Models\Employee\EmployeeExperience;
use App\Models\Employee\EmployeeFamily;
use DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\Admin\User;
use App\Models\Master\Bank;
use App\Models\Master\City;
use App\Models\Master\Country;
use App\Models\Master\Department;
use App\Models\Master\EmploymentType;
use App\Models\Master\State;
use App\Models\Master\TeachingTitle;
use Illuminate\Support\Facades\Hash;
use Exception;
use Validator;

/*
Created By : Lakshmi kumari 
Created On : 20-Apr-2023 
Code Status : Open 
*/

class EmployeeController extends Controller
{
    private $_mEmployees;
    private $_mEmployeeEducation;
    private $_mEmployeeExperience;
    private $_mEmployeeFamily;

    public function __construct()
    {
        DB::enableQueryLog();
        $this->_mEmployees = new Employee();
        $this->_mEmployeeEducation = new EmployeeEducation();
        $this->_mEmployeeExperience = new EmployeeExperience();
        $this->_mEmployeeFamily = new EmployeeFamily();
    }

    /**
     * | Search emp by emp no
     */

    public function search(Request $req)
    {
        //validation 
        $validator = Validator::make($req->all(), [
            'empNo' => 'required|string'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);

        try {
            // $data1 = array();
            $msg = '';
            $data = Employee::select('id')
                ->where([['emp_no', '=', $req->empNo], ['status', '=', '1']])->first();
            if ($data != "") {
                $msg = "Employee ID Already Existing";
                $data1 = $data;
            } else {
                $msg = "Employee Not Found";
                $data1 = ['emp_no' => $req->empNo, 'message' => 'Employee No. not found', 'value' => 'false'];
            }
            return responseMsgs(true, $msg, $data1, "API_ID_132", "", "146ms", "post",  $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_132", "", "", "post", $req->deviceId ?? "");
        }
    }

    public function search1(Request $req)
    {
        //validation
        $validator = Validator::make($req->all(), [
            'empNo' => 'required|string'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);

        try {
            $searchEmpId = new Employee();
            $data1  = $searchEmpId->searchEmpId($req);
            $msg = $data1['message'];
            $data = $data1;
            return responseMsgs(true, $msg, $data, "API_ID_132", "", "146ms", "post",  $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_132", "", "", "post", $req->deviceId ?? "");
        }
    }
    /**
     * |add bulk data using csv
     */
    public function storeCSV(Request $req)
    {
        //validation
        $validator = Validator::make($req->all(), [
            'uploadCSV' => 'required|mimes:csv,xlsx|max:2048'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $file = $req->file('uploadCSV');
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();
            // Valid File Extensions
            $valid_extension = array("csv");
            $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
            // 2MB in Bytes
            $maxFileSize = 2097152;

            // Check file extension
            if (in_array(strtolower($extension), $valid_extension)) {
                // Check file size
                if ($fileSize <= $maxFileSize) {
                    // File upload location
                    $location = 'uploads';
                    // Upload file
                    $file->move($location, $filename);
                    // Import CSV to Database
                    $filepath = public_path($location . "/" . $filename);

                    // Reading file
                    $file = fopen($filepath, "r");
                    $importData_arr = array();
                    $i = 0;
                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata);
                        // Skip first row (Remove below comment if you want to skip the first row)
                        if ($i == 0) {
                            $i++;
                            continue;
                        }
                        for ($c = 0; $c < $num; $c++) {
                            $importData_arr[$i][] = $filedata[$c];
                        }
                        $i++;
                    }

                    fclose($file);
                    foreach ($importData_arr as $importData) {

                        if ($importData[10] == null) {
                            $importData[10] = null;
                        }

                        if ($importData[12] == null) {
                            $importData[12] = null;
                        }

                        if ($importData[13] == null) {
                            $date = '';
                        } else {
                            $date = $importData[13];
                        }

                        $basicData = array(
                            "emp_no" => $importData[0],
                            "highest_qualification" => $importData[1],
                            "total_work_experience" => $importData[2],
                            "first_name" => $importData[3],
                            "last_name" => $importData[4],
                            "fathers_name" => $importData[5],
                            "mothers_name" => $importData[6],
                            "mobile" => $importData[7],
                            "emergency_contact_no" => $importData[8],
                            "email" => $importData[9],
                            // "dob" => $importData[10],
                            "marital_status_name" => $importData[11],
                            // "doj" => $importData[12],
                            // "dol" => $date,
                            "c_address1" => $importData[14],
                            "p_address1" => $importData[15],
                            "designation" => $importData[16],
                            "gender_name" => $importData[17],
                            "account_title" => $importData[18],
                            "account_no" => $importData[19],
                            "bank_name" => $importData[20],
                            "ifsc_code" => $importData[21],
                            "branch_name" => $importData[22],
                            "payscale" => $importData[23],
                            "basic_salary" => $importData[24],
                            "epf_no" => $importData[25],
                            "employee_type_name" => $importData[26],
                            "shift" => $importData[27],
                            "location" => $importData[28],
                            "facebook" => $importData[29],
                            "twitter" => $importData[30],
                            "linkedin" => $importData[31],
                            "instagram" => $importData[32],
                            "resume" => $importData[33],
                            "joining_letter" => $importData[34],
                            "resignation_letter" => $importData[35],
                            "academic_year" => $fy,
                            "school_id" => authUser()->school_id,
                            "created_by" => authUser()->id,
                            "ip_address" => getClientIpAddress()
                        );



                        // return $importData[24]; die;
                        // $basicData = array(
                        //     "emp_no" => $importData[0],
                        //     // "salutation_name" => $importData[1],
                        //     "first_name" => $importData[3],
                        //     // "middle_name" => $importData[3],
                        //     "last_name" => $importData[4],
                        //     "fathers_name" => $importData[5],
                        //     "mothers_name" => $importData[6],
                        //     "mobile" => $importData[7],
                        //     // "mobile" => 6201033951,
                        //     "emergency_contact_no" => $importData[8],
                        //     // "emergency_contact_no" => 6201033951,
                        //     "email" => $importData[9],

                        //     "dob" => $importData[10],
                        //     "marital_status_name" => $importData[11],
                        //     "doj" => $importData[12],
                        //     // "dol" => $importData[13],
                        //     "c_address1" => $importData[14],
                        //     "p_address1" => $importData[15],
                        //     "teaching_title_name" => $importData[16],
                        //     "gender_name" => $importData[17],
                        //     "account_type" => $importData[18],
                        //     "account_no" => $importData[19],
                        //     "bank_name" => $importData[20],
                        //     "ifsc_code" => $importData[21],
                        //     "branch_name" => $importData[22],

                        //     "payscale" => $importData[23],

                        //     "basic_salary" => $importData[24],
                        //     "epf_no" => $importData[25],
                        //     "contract_type" => $importData[26],
                        //     "shift" => $importData[27],
                        //     "location" => $importData[28],
                        //     "facebook" => $importData[29],
                        //     "twitter" => $importData[30],
                        //     "linkedin" => $importData[31],
                        //     "instagram" => $importData[32],
                        //     "resume" => $importData[33],
                        //     "joining_letter" => $importData[34],
                        //     "resignation_letter" => $importData[35],




                        //     // "upload_image" => $importData[18],
                        //     "academic_year" => 2023-2024,
                        //     "school_id" => authUser()->school_id,
                        //     "created_by" => authUser()->id,
                        //     "ip_address" => getClientIpAddress(),
                        //     "department_id" => 0,
                        //     "employee_type_id" => 0,
                        //     "teaching_title_id" => 0
                        // );
                        $data = array();
                        // return $basicData; die;
                        $duplicateData = Employee::where('emp_no', $importData[0])->first();

                        if (!$duplicateData) {

                            $data = Employee::create($basicData);
                            // $this->_mStudents->csv($insertData);

                            // $empTableId = Employee::where('emp_no', $importData[0])->first();
                            // $qualificationData = array(
                            //     "emp_tbl_id" => $empTableId->id,
                            //     "exam_passed_name" => $importData[1],
                            // );

                            // $educationDetails = EmployeeEducation::create($qualificationData);

                            // $experienceData = array(
                            //     "emp_tbl_id" => $empTableId->id,
                            //     "in_year" => $importData[2],
                            // );

                            // $experienceDetails = EmployeeExperience::create($experienceData);
                        } else {
                            throw new Exception("Employee Already Existing");
                        }
                        // return "OK2";die; 

                        // return $data; die;

                        // $insertData = array_merge($insertData, [
                        //     'json_logs' => trim(json_encode($insertData), ",")
                        // ]);
                        // Check for duplicate data before inserting
                    }

                    // $data = array(
                    //     "basic_details" => $basicData,
                    //     "education_details" => $educationDetails,
                    //     "experience_details" => $experienceDetails
                    // );
                }
            }
            return responseMsgs(true, "Successfully Uploaded", $data, "", "3.1", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "3.1", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    /**
     * |add bulk data using csv
     */
    public function storeCSV2(Request $req)
    {
        //validation
        $validator = Validator::make($req->all(), [
            'uploadCSV' => 'required|mimes:csv,xlsx'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $file = $req->file('uploadCSV');
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalName();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();
            // Valid File Extensions
            $valid_extension = array("csv");

            // 2MB in Bytes
            $maxFileSize = 2097152;

            // Check file extension
            if (in_array(strtolower($extension), $valid_extension)) {
                // Check file size
                if ($fileSize <= $maxFileSize) {
                    // File upload location
                    $location = 'uploads';
                    // Upload file
                    $file->move($location, $filename);
                    // Import CSV to Database
                    $filepath = public_path($location . "/" . $filename);

                    // Reading file
                    $file = fopen($filepath, "r");
                    $importData_arr = array();
                    $i = 0;
                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata);
                        // Skip first row (Remove below comment if you want to skip the first row)
                        if ($i == 0) {
                            $i++;
                            continue;
                        }
                        for ($c = 0; $c < $num; $c++) {
                            $importData_arr[$i][] = $filedata[$c];
                        }
                        $i++;
                    }
                    fclose($file);
                    // Insert to MySQL database
                    foreach ($importData_arr as $importData) {

                        $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
                        $insertData = array(
                            "emp_no" => $importData[0],
                            "salutation_name" => $importData[1],
                            "first_name" => $importData[2],
                            "middle_name" => $importData[3],
                            "last_name" => $importData[4],
                            "email" => $importData[5],
                            "mobile" => $importData[6],
                            "dob" => $importData[7],
                            "doj" => $importData[8],
                            "aadhar_no" => $importData[9],
                            "disability" => $importData[10],
                            "gender_name" => $importData[11],
                            "category_name" => $importData[12],
                            "blood_group_name" => $importData[13],
                            "department_name" => $importData[14],
                            "employee_type_name" => $importData[15],
                            "teaching_title_name" => $importData[16],
                            "marital_status_name" => $importData[17],
                            "upload_image" => $importData[18],
                            "academic_year" => $fy,
                            "school_id" => authUser()->school_id,
                            "created_by" => authUser()->id,
                            "ip_address" => getClientIpAddress(),
                            "department_id" => 0,
                            "employee_type_id" => 0,
                            "teaching_title_id" => 0
                        );
                        // dd($insertData);
                        Employee::csv($insertData);
                    }
                }
            }
            return responseMsgs(true, "Successfully Uploaded", $insertData, "", "1.1", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.1", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    /**
     * | Add data
     */
    public function store(Request $req)
    {

        //validation
        $basicRules = [

            'empNo'                   => 'required',
            'salutation'              => 'required|string',
            'firstName'               => 'required|string',
            'middleName'              => 'nullable|string',
            'lastName'                => 'required|string',
            'email'                   => 'required|email',
            'mobile'                  => 'required|string',
            'dob'                     => 'required|date',
            'doj'                     => 'required|date',
            'aadharNo'                => 'required|numeric|digits:12',
            'disability'              => 'nullable|string',
            'gender'                  => 'required|string',
            'category'                => 'required|string',
            'bloodGroup'              => 'required|string',
            'department'              => 'required|numeric',
            'employeeType'            => 'nullable|numeric',
            'teachingTitle'           => 'nullable|numeric',
            'maritalStatus'           => 'nullable|string',
            'uploadImage'             => 'nullable|file|mimes:jpg,png,jpeg',
            'pAddress1'               => 'nullable|string',
            'pAddress2'               => 'nullable|string',
            'pLocality'               => 'nullable|string',
            'pLandmark'               => 'nullable|string',
            'pCountry'                => 'nullable',
            'pState'                  => 'nullable',
            'pDistrict'               => 'nullable',
            'pPincode'                => 'nullable|string',
            'cAddress1'               => 'nullable|string',
            'cAddress2'               => 'nullable|string',
            'cLocality'               => 'nullable|string',
            'cLandmark'               => 'nullable|string',
            'cCountry'                => 'nullable',
            'cState'                  => 'nullable',
            'cDistrict'               => 'nullable',
            'cPincode'                => 'nullable|string',
            'fathersName'             => 'nullable|string',
            'fathersQualification'    => 'nullable|string',
            'fathersOccupation'       => 'nullable|string',
            'mothersName'             => 'nullable|string',
            'mothersQualification'    => 'nullable|string',
            'mothersOccupation'       => 'nullable|string',
            'bank'                    => 'nullable',
            'accountNo'               => 'nullable|string',
            'accountType'             => 'nullable|string',
            'ifscCode'                => 'nullable|string',
            'branchName'              => 'nullable|string',
            'nomineeName'             => 'nullable|string',
            'nomineeRelation'         => 'nullable|string',
            'panNo'                   => 'nullable|string',
            'epfNo'                   => 'nullable|string',
            'uanNo'                   => 'nullable|string',
            'esiNo'                   => 'nullable|string',
            'npsNo'                   => 'nullable|string',

        ];

        $educationRules = [
            // 'educationDetails' => 'array',
            'educationDetails.*.examPassed'    => 'nullable|string',
            'educationDetails.*.board'         => 'nullable|string',
            'educationDetails.*.passingYear'   => 'nullable|string',
            'educationDetails.*.divGrade'      => 'nullable|string',
            'educationDetails.*.marksObtained' => 'nullable|numeric',
            'educationDetails.*.totalMarks'    => 'nullable|numeric',
            'educationDetails.*.percentage'    => 'nullable|numeric',
            'educationDetails.*.uploadEduDoc'  => 'nullable|file',
        ];

        $expRules = [
            'experienceDetails.*.nameOfOrg'            => 'nullable|string',
            'experienceDetails.*.positionHead'         => 'nullable|string',
            'experienceDetails.*.periodFrom'           => 'nullable|string',
            'experienceDetails.*.periodTo'             => 'nullable|string',
            'experienceDetails.*.salary'               => 'nullable|numeric',
            'experienceDetails.*.payGrade'             => 'nullable|string',
            'experienceDetails.*.uploadExpLetterDocs'  => 'nullable|file',
        ];
        $familyDetailsRules = [
            'familyDetails.*.fMemberName'             => 'nullable|string',
            'familyDetails.*.fMemberRelation'         => 'nullable|string',
            'familyDetails.*.fMemberDob'              => 'nullable|string',
            'familyDetails.*.uploadFMemberImage'      => 'nullable|file',

        ];

        $allRules = array_merge($basicRules, $educationRules, $expRules, $familyDetailsRules);

        $validator = Validator::make($req->all(), $allRules);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);

        try {
            // DB::beginTransaction();
            //variable
            $result = array();
            $data = array();
            $file_name = '';
            //check if employee no existing or not 
            $isExists = $this->_mEmployees->readEmployeeGroup($req);
            if (collect($isExists)->isNotEmpty())
                throw new Exception("Employee ID Already Existing");

            $isExists = $this->_mEmployees->readEmployeeAadharGroup($req);
            if (collect($isExists)->isNotEmpty())
                throw new Exception("Aadhar No Already Existing");

            //get financial year
            $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
            $timestamp = now()->timestamp;
            if ($req->uploadImage != "") {
                $uploadImage = $req->uploadImage;
                $get_file_name = 'image-' . $timestamp . '-' . $uploadImage->getClientOriginalName();
                $path = public_path('school/employees/' . $req->empNo);
                $file_name = 'school/employees/' . $req->empNo . '/' . $get_file_name;
                $uploadImage->move($path, $get_file_name);
                // $req->file('uploadImage')->move($path, $get_file_name);
            }

            $this->_mEmployees->emp_no = $req->empNo;
            $this->_mEmployees->salutation_name = $req->salutation;
            $this->_mEmployees->first_name = $req->firstName;
            $this->_mEmployees->middle_name = $req->middleName;
            $this->_mEmployees->last_name = $req->lastName;
            $this->_mEmployees->email = $req->email;
            $this->_mEmployees->mobile = $req->mobile;
            $this->_mEmployees->dob = $req->dob;
            $this->_mEmployees->doj = $req->doj;
            $this->_mEmployees->aadhar_no = $req->aadharNo;
            $this->_mEmployees->disability = $req->disability;

            $this->_mEmployees->gender_name = $req->gender;
            $genderId = null;
            $genderName = $req->gender;
            if ($genderName == 'Male') {
                $genderId = 1;
            } elseif ($genderName == 'Female') {
                $genderId = 2;
            } else {
                $genderId = 3;
            }
            $this->_mEmployees->gender_id = $genderId;

            $this->_mEmployees->category_name = $req->category;
            $this->_mEmployees->blood_group_name = $req->bloodGroup;

            $this->_mEmployees->department_id = $req->department;
            $departmentName = Department::where('id', '=', $req->department)->pluck('department_name')->first();
            $this->_mEmployees->department_name = $departmentName;

            $this->_mEmployees->employee_type_id = $req->employeeType;
            $employeTypeName = EmploymentType::where('id', '=', $req->employeeType)->pluck('emp_type_name')->first();
            $this->_mEmployees->employee_type_name = $employeTypeName;
            // return $this->_mEmployees; die; 

            $this->_mEmployees->teaching_title_id = $req->teachingTitle;
            $teachingTitleName = TeachingTitle::where('id', '=', $req->teachingTitle)->pluck('teaching_title_name')->first();
            $this->_mEmployees->teaching_title_name = $teachingTitleName;


            $this->_mEmployees->marital_status_name = $req->maritalStatus;
            $this->_mEmployees->upload_image = $file_name;
            $this->_mEmployees->p_address1 = $req->pAddress1;
            $this->_mEmployees->p_address2 = $req->pAddress2;
            $this->_mEmployees->p_locality = $req->pLocality;
            $this->_mEmployees->p_landmark = $req->pLandmark;

            $this->_mEmployees->p_country_id = $req->pCountry;
            $countryName = Country::where('id', '=', $req->pCountry)->pluck('country_name')->first();
            $this->_mEmployees->p_country_name = $countryName;

            $this->_mEmployees->p_state_id = $req->pState;
            $stateName = State::where('id', '=', $req->pState)->pluck('state_name')->first();
            $this->_mEmployees->p_state_name = $stateName;

            $this->_mEmployees->p_district_id = $req->pDistrict;
            $stateName = City::where('id', '=', $req->pDistrict)->pluck('city_name')->first();
            $this->_mEmployees->p_district_name = $stateName;

            $this->_mEmployees->p_pincode = $req->pPincode;
            $this->_mEmployees->c_address1 = $req->cAddress1;
            $this->_mEmployees->c_address2 = $req->cAddress2;
            $this->_mEmployees->c_locality = $req->cLocality;
            $this->_mEmployees->c_landmark = $req->cLandmark;

            $this->_mEmployees->c_country_id = $req->cCountry;
            $ccName = Country::where('id', '=', $req->cCountry)->pluck('country_name')->first();
            $this->_mEmployees->c_country_name = $ccName;

            $this->_mEmployees->c_state_id = $req->cState;
            $csName = State::where('id', '=', $req->cState)->pluck('state_name')->first();
            $this->_mEmployees->c_state_name = $csName;

            $this->_mEmployees->c_district_id = $req->cDistrict;
            $cdName = City::where('id', '=', $req->cDistrict)->pluck('city_name')->first();
            $this->_mEmployees->c_district_name = $cdName;

            $this->_mEmployees->c_pincode = $req->cPincode;
            $this->_mEmployees->fathers_name = $req->fathersName;
            $this->_mEmployees->fathers_qualification_name = $req->fathersQualification;
            $this->_mEmployees->fathers_occupation_name = $req->fathersOccupation;
            $this->_mEmployees->mothers_name = $req->mothersName;
            $this->_mEmployees->mothers_qualification_name = $req->mothersQualification;
            $this->_mEmployees->mothers_occupation_name = $req->mothersOccupation;

            $this->_mEmployees->bank_id = $req->bank;
            $bank = Bank::where('id', '=', $req->bank)->pluck('bank_name')->first();
            $this->_mEmployees->bank_name = $bank;

            $this->_mEmployees->account_no = $req->accountNo;
            $this->_mEmployees->account_type = $req->accountType;
            $this->_mEmployees->ifsc_code = $req->ifscCode;
            $this->_mEmployees->branch_name = $req->branchName;
            $this->_mEmployees->nominee_name = $req->nomineeName;
            $this->_mEmployees->nominee_relation_name = $req->nomineeRelation;
            $this->_mEmployees->pan_no = $req->panNo;
            $this->_mEmployees->epf_no = $req->epfNo;
            $this->_mEmployees->uan_no = $req->uanNo;
            $this->_mEmployees->esi_no = $req->esiNo;
            $this->_mEmployees->nps_no = $req->npsNo;
            $this->_mEmployees->academic_year = $fy;
            $this->_mEmployees->school_id = authUser()->school_id;
            $this->_mEmployees->created_by = authUser()->id;
            $this->_mEmployees->ip_address = getClientIpAddress();
            // print_var($this->_mEmployees);

            $this->_mEmployees->save();

            // print_var($req['educationDetails']);
            // die;
            //insert single data or multi data for employee education
            $empEdudata = '';
            if ($req['educationDetails'] != "") {
                foreach ($req['educationDetails'] as $key => $ob) {
                    $edu_file_name = "";
                    $eduFile_name = '';
                    // if ($ob->uploadEduDoc != "") {
                    //     //$uploadEduDoc = $req->uploadEduDoc;
                    //     $edu_file_name = $this->_mEmployees->empNo . '-' . $ob->uploadEduDoc->getClientOriginalName();
                    //     $path = public_path('school/employees/');
                    //     $file_name = 'school/employees/' . $this->_mEmployees->empNo . '-' . $ob->uploadEduDoc->getClientOriginalName();
                    //     $req->file('uploadEduDoc')->move($path, $edu_file_name);
                    // }
                    $mEmployeeEducation = array();
                    if ($ob['uploadEduDoc'] != "") {
                        $uploadEduDoc = $ob['uploadEduDoc'];
                        $edu_file_name = $ob['examPassed'] . '-' . $timestamp . '-' . $uploadEduDoc->getClientOriginalName();
                        $path = public_path('school/employees/' . $req->empNo);
                        $eduFile_name = 'school/employees/' . $req->empNo . '/' . $edu_file_name;
                        $uploadEduDoc->move($path, $edu_file_name);
                        $mEmployeeEducation['upload_edu_doc'] = $eduFile_name;
                    }

                    // $mEmpEduObject = new EmployeeEducation();
                    // $insert = [
                    //     $mEmpEduObject->emp_tbl_id = $this->_mEmployees->id,
                    //     $mEmpEduObject->exam_passed_name = $ob['examPassed'],
                    //     $mEmpEduObject->board_uni_inst = $ob['board'],
                    //     $mEmpEduObject->passing_year = $ob['passingYear'],
                    //     $mEmpEduObject->div_grade_name = $ob['divGrade'],
                    //     $mEmpEduObject->marks_obtained = $ob['marksObtained'],
                    //     $mEmpEduObject->total_marks = $ob['totalMarks'],
                    //     $mEmpEduObject->percentage = $ob['percentage'],
                    //     $mEmpEduObject->upload_edu_doc = $eduFile_name,
                    //     $mEmpEduObject->academic_year = $fy,
                    //     $mEmpEduObject->school_id = authUser()->school_id,
                    //     $mEmpEduObject->created_by = authUser()->id,
                    //     $mEmpEduObject->ip_address = getClientIpAddress()
                    // ];
                    // $mEmpEduObject->save($insert);

                    $mEmployeeEducation["emp_tbl_id"] =  $this->_mEmployees->id;
                    $mEmployeeEducation["exam_passed_name"] =  $ob['examPassed'];
                    $mEmployeeEducation["board_uni_inst"] =  $ob['board'];
                    $mEmployeeEducation["passing_year"] =  $ob['passingYear'];
                    $mEmployeeEducation["div_grade_name"] =  $ob['divGrade'];
                    $mEmployeeEducation["marks_obtained"] =  $ob['marksObtained'];
                    $mEmployeeEducation["total_marks"] =  $ob['totalMarks'];
                    $mEmployeeEducation["percentage"] =  $ob['percentage'];
                    // $mEmployeeEducation["upload_edu_doc"] =  $edu_file_name;
                    $mEmployeeEducation["academic_year"] =  $fy;
                    $mEmployeeEducation["school_id"] =  authUser()->school_id;
                    $mEmployeeEducation["created_by"] =  authUser()->id;
                    $mEmployeeEducation["ip_address"] =  getClientIpAddress();
                    // return $mEmployeeEducation; die;
                    $empEdudata = $this->_mEmployeeEducation->insertData($mEmployeeEducation);
                    // print_var($empEdudata->id);
                }
            }
            // DB::rollback();
            // DB::commit();
            // dd();
            //insert single data or multi data for experience 
            $empExpdata = '';
            if ($req['experienceDetails'] != "") {
                foreach ($req['experienceDetails'] as $ob) {
                    $exp_file_name = "";
                    $expFile_name = '';
                    if ($ob['uploadExpLetterDocs'] != "") {
                        $uploadExpLetterDocs = $ob['uploadExpLetterDocs'];
                        $exp_file_name = 'Experience-' . $ob['positionHead'] . '-' . $timestamp . '-' . $uploadExpLetterDocs->getClientOriginalName();
                        $path = public_path('school/employees/' . $req->empNo);
                        $expFile_name = 'school/employees/' . $req->empNo . '/' . $exp_file_name;
                        $uploadExpLetterDocs->move($path, $exp_file_name);
                        $mEmployeeExperience['upload_exp_letter'] = $expFile_name;
                    }
                    $mEmployeeExperience["emp_tbl_id"]        =  $this->_mEmployees->id;
                    $mEmployeeExperience["name_of_org"]       =  $ob['nameOfOrg'];
                    $mEmployeeExperience["position_head"]     =  $ob['positionHead'];
                    $mEmployeeExperience["period_from"]       =  $ob['periodFrom'];
                    $mEmployeeExperience["period_to"]         =  $ob['periodTo'];
                    $mEmployeeExperience["salary"]            =  $ob['salary'];
                    $mEmployeeExperience["pay_grade"]         =  $ob['payGrade'];
                    $mEmployeeExperience["upload_exp_letter"] =  $expFile_name;
                    $mEmployeeExperience["academic_year"]     =  $fy;
                    $mEmployeeExperience["school_id"]         =  authUser()->school_id;
                    $mEmployeeExperience["created_by"]        =  authUser()->id;
                    $mEmployeeExperience["ip_address"]        =  getClientIpAddress();
                    $empExpdata = $this->_mEmployeeExperience->insertData($mEmployeeExperience);

                    // $this->_mEmployeeExperience->emp_tbl_id = $this->_mEmployees->id;
                    // $this->_mEmployeeExperience->name_of_org = $ob['nameOfOrg'];
                    // $this->_mEmployeeExperience->position_head = $ob['positionHead'];
                    // $this->_mEmployeeExperience->period_from = $ob['periodFrom'];
                    // $this->_mEmployeeExperience->period_to = $ob['periodTo'];
                    // $this->_mEmployeeExperience->salary = $ob['salary'];
                    // $this->_mEmployeeExperience->pay_grade = $ob['payGrade'];
                    // $this->_mEmployeeExperience->upload_exp_letter = $exp_file_name;
                    // $this->_mEmployeeExperience->academic_year = $fy;
                    // $this->_mEmployeeExperience->school_id = authUser()->school_id;
                    // $this->_mEmployeeExperience->created_by = authUser()->id;
                    // $this->_mEmployeeExperience->ip_address = getClientIpAddress();
                    // $this->_mEmployeeExperience->save();
                }
            }

            //insert single data or multi data for experience 
            $empFamilydata = "";
            if ($req['familyDetails'] != "") {
                foreach ($req['familyDetails'] as $ob) {
                    $fam_file_name = "";
                    $famFile_name = "";
                    if ($ob['uploadFMemberImage'] != "") {
                        $uploadFMemberImage = $ob['uploadFMemberImage'];
                        $fam_file_name = $ob['fMemberRelation'] . '-' . $timestamp . '-' . $uploadFMemberImage->getClientOriginalName();
                        $path = public_path('school/employees/' . $req->empNo);
                        $famFile_name = 'school/employees/' . $req->empNo . '/' . $fam_file_name;
                        $uploadFMemberImage->move($path, $fam_file_name);
                        $mEmployeeFamily['upload_f_member_image'] = $famFile_name;
                    }

                    $mEmployeeFamily["emp_tbl_id"] =  $this->_mEmployees->id;
                    $mEmployeeFamily["f_member_name"] =  $ob['fMemberName'];
                    $mEmployeeFamily["f_member_relation_name"] =  $ob['fMemberRelation'];
                    $mEmployeeFamily["f_member_dob"] =  $ob['fMemberDob'];
                    // $mEmployeeFamily["upload_f_member_image"] =  $famFile_name;
                    $mEmployeeFamily["academic_year"] =  $fy;
                    $mEmployeeFamily["school_id"] =  authUser()->school_id;
                    $mEmployeeFamily["created_by"] =  authUser()->id;
                    $mEmployeeFamily["ip_address"] =  getClientIpAddress();
                    $empFamilydata = $this->_mEmployeeFamily->insertData($mEmployeeFamily);

                    // $this->_mEmployeeFamily->emp_tbl_id = $this->_mEmployees->id;
                    // $this->_mEmployeeFamily->f_member_name = $ob['fMemberName'];
                    // $this->_mEmployeeFamily->f_member_relation_name = $ob['fMemberRelation'];
                    // $this->_mEmployeeFamily->f_member_dob = $ob['fMemberDob'];
                    // $this->_mEmployeeFamily->upload_f_member_image = $fam_file_name;
                    // $this->_mEmployeeFamily->academic_year = $fy;
                    // $this->_mEmployeeFamily->school_id = authUser()->school_id;
                    // $this->_mEmployeeFamily->created_by = authUser()->id;
                    // $this->_mEmployeeFamily->ip_address = getClientIpAddress();
                    // $this->_mEmployeeFamily->save();
                }
            }
            // DB::rollback();
            // DB::commit();
            // dd();
            //return response
            $result['basic_details'] = $this->_mEmployees;
            // $result['educationDetails'] = $mEmpEduObject;
            $result['educationDetails'] = $empEdudata;
            $result['experienceDetails'] = $empExpdata;
            $result['familyDetails'] = $empFamilydata;

            return responseMsgs(true, "Successfully Saved", $result, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    public function addUser($req)
    {
        $pass = Str::random(10);
        $school_id = '123';
        $userType = 'Employee';
        $mObjectU = new User();
        $insert = [
            $mObjectU->name        = $req['first_name'],
            $mObjectU->email       = $req['email'],
            $mObjectU->password    = Hash::make($pass),
            $mObjectU->c_password  = $pass,
            $mObjectU->school_id   = $school_id,
            $mObjectU->user_id     = $req['emp_no'],
            $mObjectU->user_type   = $userType,
            $mObjectU->ip_address  = getClientIpAddress()
        ];
        $mObjectU->save($insert);
    }

    /**
     * | Add data
     */
    public function edit2(Request $req)
    {
        //validation
        $validator = Validator::make($req->all(), [
            'id' => 'required|numeric'
            // 'empNo' => 'required|string',
            // 'salutation' => 'required|string',
            // 'firstName' => 'required|string',
            // 'lastName' => 'required|string',
            // 'gender' => 'required|string',
            // 'category' => 'required|string',
            // 'dob' => 'required|string',
            // 'doj' => 'required|string',
            // 'mobile' => 'required|numeric',
            // 'bloodGroup' => 'required|string',
            // 'department' => 'required|numeric',
            // 'employeeType' => 'required|numeric',
            // 'maritalStatus'  => 'required|string',
            // 'teachingTitle' => 'required|numeric',
            // 'disability' => 'required|string'
            // 'aadharNo' => 'required|numeric|digits:12',
            // 'email' => 'string|email',
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);

        try {
            //variable
            $result = array();
            $file_name = '';
            $getData = $this->_mEmployees::findOrFail($req->id);
            if (!$getData)
                throw new Exception("Employee Not Found!");
            //check if employee no existing or not 
            $isExists = $this->_mEmployees->readEmployeeGroup($req);
            if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
                throw new Exception("Employee ID Already Existing");

            $isExists = $this->_mEmployees->readEmployeeAadharGroup($req);
            if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
                throw new Exception("Aadhar No Already Existing");

            //get financial year
            $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
            $metaReqs = array();
            $timestamp = now()->timestamp;
            if ($req->uploadImage != "") {
                $uploadImage = $req->uploadImage;
                $get_file_name = 'image-' . $timestamp . '-' . $uploadImage->getClientOriginalName();
                $path = public_path('school/employees/' . $getData->empNo);
                $file_name = 'school/employees/' . $get_file_name;
                $uploadImage->move($path, $get_file_name);
                // $req->file('uploadImage')->move($path, $get_file_name);
                $metaReqs = [
                    'upload_image' => $file_name,
                ];
            }

            // return $file_name; die; 
            // $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));

            //check if employee no existing or not 
            // $isExists = $this->_mEmployees->readEmployeeGroup($req->id);
            // if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
            //     throw new Exception("Employee ID  or Aadhar No Already Existing");

            // $isExists = $this->_mEmployees->readEmployeeGroup($req);
            // if (collect($isExists)->isNotEmpty())
            //     throw new Exception("Employee ID or Aadhar No Already Existing");

            // if ($req->uploadImage != "") {
            //     $uploadImage = $req->uploadImage;
            //     $file_name = $req->empNo . '-' . $uploadImage->getClientOriginalName();
            //     $path = public_path('school/employees/');
            //     $req->file('uploadImage')->move($path, $req->empNo . '-' . $file_name);
            // }

            $metaReqs = array_merge($metaReqs, [
                // 'emp_no' => $req->empNo,
                'salutation_name' => $req->salutation,
                'first_name' => $req->firstName,
                'middle_name' => $req->middleName,
                'last_name' => $req->lastName,
                'email' => $req->email,
                'mobile' => $req->mobile,
                'dob' => $req->dob,
                'doj' => $req->doj,
                'aadhar_no' => $req->aadharNo,
                'disability' => $req->disability,
                'gender_name' => $req->gender,
                'category_name' => $req->category,
                'blood_group_name' => $req->bloodGroup,
                'department_id' => $req->department,
                'employee_type_id' => $req->employeeType,
                'teaching_title_id' => $req->teachingTitle,
                'marital_status_name' => $req->maritalStatus,
                // 'upload_image' => $file_name,
                'p_address1' => $req->pAddress1,
                'p_address2' => $req->pAddress2,
                'p_locality' => $req->pLocality,
                'p_landmark' => $req->pLandmark,
                'p_country_id' => $req->pCountry,
                'p_state_id' => $req->pState,
                'p_district_id' => $req->pDistrict,
                'p_pincode' => $req->pPincode,
                'c_address1' => $req->cAddress1,
                'c_address2' => $req->cAddress2,
                'c_locality' => $req->cLocality,
                'c_landmark' => $req->cLandmark,
                'c_country_id' => $req->cCountry,
                'c_state_id' => $req->cState,
                'c_district_id' => $req->cDistrict,
                'c_pincode' => $req->cPincode,
                'fathers_name' => $req->fathersName,
                'fathers_qualification_name' => $req->fathersQualification,
                'fathers_occupation_name' => $req->fathersOccupation,
                'mothers_name' => $req->mothersName,
                'mothers_qualification_name' => $req->mothersQualification,
                'mothers_occupation_name' => $req->mothersOccupation,
                'bank_id' => $req->bank,
                'account_no' => $req->accountNo,
                'account_type' => $req->accountType,
                'ifsc_code' => $req->ifscCode,
                'branch_name' => $req->branchName,
                'nominee_name' => $req->nomineeName,
                'nominee_relation_name' => $req->nomineeRelation,
                'pan_no' => $req->panNo,
                'epf_no' => $req->epfNo,
                'uan_no' => $req->uanNo,
                'esi_no' => $req->esiNo,
                'nps_no' => $req->npsNo,
                'version_no' => $getData->version_no + 1,
            ]);

            if (isset($req->status)) {                  // In Case of Deactivation or Activation 
                $status = $req->status == 'deactive' ? 0 : 1;
                $metaReqs = array_merge($metaReqs, [
                    'status' => $status
                ]);
            }
            // return $metaReqs; die; 
            //$updateData = $this->_mEmployees::findOrFail($req->id);
            $getData->update($metaReqs);
            // return $getData; die;
            $result['basicDetails'] = $metaReqs;

            //insert single data or multi data for employee education 
            if ($req['educationDetails'] != "") {
                $mObjectEdu =  $this->_mEmployeeEducation::where(['emp_tbl_id' => $req->id], ['status' => 1])->get();
                // return $mObjectEdu; die; 
                // $mObjectEdu->delete();
                $arrEdu = array();

                foreach ($req['educationDetails'] as $ob) {
                    $edu_file_name = "";
                    $empEdudata = array();


                    $educationRecords = EmployeeEducation::where(['emp_tbl_id' => $req->id])->get();

                    foreach ($educationRecords as $index => $educationRecord) {
                        $updateData = [
                            "exam_passed_name" => $req['educationDetails'][$index]['examPassed'],
                            "board_uni_inst" => $req['educationDetails'][$index]['board'],
                            "passing_year" => $req['educationDetails'][$index]['passingYear'],
                            "div_grade_name" => $req['educationDetails'][$index]['examPassed'],
                            "marks_obtained" => $req['educationDetails'][$index]['board'],
                            "total_marks" => $req['educationDetails'][$index]['passingYear'],
                            "percentage" => $req['educationDetails'][$index]['examPassed'],
                            "academic_year" =>  $fy,
                            "school_id"  =>  authUser()->school_id,
                            "created_by" =>  authUser()->id,
                            "ip_address" =>  getClientIpAddress(),
                        ];

                        $uploadEduDoc = $req['educationDetails'][$index]['uploadEduDoc'];
                        return $ob['uploadEduDoc'];
                        die;
                        if ($uploadEduDoc != "") {
                            // Handle file upload and update the file-related field
                            $edu_file_name = 'Education-' . $timestamp . '-' . $uploadEduDoc->getClientOriginalName();
                            $path = public_path('school/employees/' . $getData->emp_no);
                            $eduFile_name = 'school/employees/' . $getData->emp_no . '/' . $edu_file_name;
                            $uploadEduDoc->move($path, $edu_file_name);
                            $updateData['upload_edu_doc'] = $eduFile_name;
                        }

                        // $educationRecord->update($updateData);
                    }
                    return "OK";
                    die;


                    if ($ob['uploadEduDoc'] != "") {
                        $uploadEduDoc = $ob['uploadEduDoc'];
                        $edu_file_name = 'Education-' . $timestamp . '-' . $uploadEduDoc->getClientOriginalName();
                        $path = public_path('school/employees/' . $getData->emp_no);
                        $eduFile_name = 'school/employees/' . $getData->emp_no . '/' . $edu_file_name;
                        $uploadEduDoc->move($path, $edu_file_name);
                        $mEmployeeEducation['upload_edu_doc'] = $eduFile_name;
                    }
                    // $mEmployeeEducation["emp_tbl_id"] =  $req->id;
                    // $mEmployeeEducation["exam_passed_name"] =  $ob['examPassed'];
                    // $mEmployeeEducation["board_uni_inst"] =  $ob['board'];
                    // $mEmployeeEducation["passing_year"] =  $ob['passingYear'];
                    // $mEmployeeEducation["div_grade_name"] =  $ob['divGrade'];
                    // $mEmployeeEducation["marks_obtained"] =  $ob['marksObtained'];
                    // $mEmployeeEducation["total_marks"] =  $ob['totalMarks'];
                    // $mEmployeeEducation["percentage"] =  $ob['percentage'];
                    // $mEmployeeEducation["academic_year"] =  $fy;
                    // $mEmployeeEducation["school_id"] =  authUser()->school_id;
                    // $mEmployeeEducation["created_by"] =  authUser()->id;
                    // $mEmployeeEducation["ip_address"] =  getClientIpAddress();

                    // return $mEmployeeEducation; 
                    return $arrEdu;
                    die;
                    // $empEdudata = $this->_mEmployeeEducation->insertData($mEmployeeEducation);
                    // return $empEdudata; 
                    // $this->_mEmployeeEducation->emp_tbl_id = $req->id;
                    // $this->_mEmployeeEducation->exam_passed_name = $ob['examPassed'];
                    // $this->_mEmployeeEducation->board_uni_inst = $ob['board'];
                    // $this->_mEmployeeEducation->passing_year = $ob['passingYear'];
                    // $this->_mEmployeeEducation->div_grade_name = $ob['divGrade'];
                    // $this->_mEmployeeEducation->marks_obtained = $ob['marksObtained'];
                    // $this->_mEmployeeEducation->total_marks = $ob['totalMarks'];
                    // $this->_mEmployeeEducation->percentage = $ob['percentage'];
                    // $this->_mEmployeeEducation->upload_edu_doc = $edu_file_name;
                    // $this->_mEmployeeEducation->academic_year = $fy;
                    // $this->_mEmployeeEducation->school_id = authUser()->school_id;
                    // $this->_mEmployeeEducation->created_by = authUser()->id;
                    // $this->_mEmployeeEducation->ip_address = getClientIpAddress();
                    // $this->_mEmployeeEducation->update();
                    // $arrEdu[] = $this->_mEmployeeEducation;


                    // $this->_mEmployeeEducation::create($mEmployeeEducation);

                }
            }
            return "OK";
            die;

            //insert single data or multi data for experience 
            if ($req['experienceDetails'] != "") {
                $mObjectExp =  $this->_mEmployeeExperience::where(['emp_tbl_id' => $req->id], ['status' => 1]);
                $mObjectExp->delete();

                // $arrExp = array();

                foreach ($req['experienceDetails'] as $ob) {
                    $exp_file_name = "";

                    if ($ob['uploadExpLetterDocs'] != "") {
                        $uploadExpLetterDocs = $ob['uploadExpLetterDocs'];
                        $exp_file_name = 'Experience-' . $ob['positionHead'] . '-' . $timestamp . '-' . $uploadExpLetterDocs->getClientOriginalName();
                        $path = public_path('school/employees/' . $getData->emp_no);
                        $expFile_name = 'school/employees/' . $getData->emp_no . '/' . $exp_file_name;
                        $uploadExpLetterDocs->move($path, $exp_file_name);
                        $mEmployeeExperience['upload_exp_letter'] = $expFile_name;
                    }

                    $mEmployeeExperience["emp_tbl_id"]        =  $req->id;
                    $mEmployeeExperience["name_of_org"]       =  $ob['nameOfOrg'];
                    $mEmployeeExperience["position_head"]     =  $ob['positionHead'];
                    $mEmployeeExperience["period_from"]       =  $ob['periodFrom'];
                    $mEmployeeExperience["period_to"]         =  $ob['periodTo'];
                    $mEmployeeExperience["salary"]            =  $ob['salary'];
                    $mEmployeeExperience["pay_grade"]         =  $ob['payGrade'];
                    $mEmployeeExperience["upload_exp_letter"] =  $expFile_name;
                    $mEmployeeExperience["academic_year"]     =  $fy;
                    $mEmployeeExperience["school_id"]         =  authUser()->school_id;
                    $mEmployeeExperience["created_by"]        =  authUser()->id;
                    $mEmployeeExperience["ip_address"]        =  getClientIpAddress();
                    // $mObjectExp->update($mEmployeeExperience);

                    // $this->_mEmployeeExperience->emp_tbl_id = $req->id;
                    // $this->_mEmployeeExperience->name_of_org = $ob['nameOfOrg'];
                    // $this->_mEmployeeExperience->position_head = $ob['positionHead'];
                    // $this->_mEmployeeExperience->period_from = $ob['periodFrom'];
                    // $this->_mEmployeeExperience->period_to = $ob['periodTo'];
                    // $this->_mEmployeeExperience->salary = $ob['salary'];
                    // $this->_mEmployeeExperience->pay_grade = $ob['payGrade'];
                    // $this->_mEmployeeExperience->upload_edu_doc = $edu_file_name;
                    // $this->_mEmployeeExperience->academic_year = $fy;
                    // $this->_mEmployeeExperience->school_id = authUser()->school_id;
                    // $this->_mEmployeeExperience->created_by = authUser()->id;
                    // $this->_mEmployeeExperience->ip_address = getClientIpAddress();
                    // $this->_mEmployeeExperience->save();

                    // return $mEmployeeExperience; die; 
                    $this->_mEmployeeExperience::create($mEmployeeExperience);
                    // return "OK"; die; 
                }
            }

            //insert single data or multi data for experience 
            if ($req['familyDetails'] != "") {
                $mObjectFam =  $this->_mEmployeeFamily::where(['emp_tbl_id' => $req->id], ['status' => 1]);
                $mObjectFam->delete();

                // $arrFam = array();
                $mEmployeeFamily = array();
                foreach ($req['familyDetails'] as $ob) {
                    $fam_file_name = "";
                    $famFile_name = "";

                    if ($ob['uploadFMemberImage'] != "") {
                        $uploadFMemberImage = $ob['uploadFMemberImage'];
                        $fam_file_name = $ob['fMemberRelation'] . '-' . $timestamp . '-' . $uploadFMemberImage->getClientOriginalName();
                        $path = public_path('school/employees/' . $getData->emp_no);
                        $famFile_name = 'school/employees/' . $getData->emp_no . '/' . $fam_file_name;
                        $uploadFMemberImage->move($path, $fam_file_name);
                        $mEmployeeFamily['upload_f_member_image'] = $famFile_name;
                    }
                    $mEmployeeFamily["emp_tbl_id"] =  $req->id;
                    $mEmployeeFamily["f_member_name"] =  $ob['fMemberName'];
                    $mEmployeeFamily["f_member_relation_name"] =  $ob['fMemberRelation'];
                    $mEmployeeFamily["f_member_dob"] =  $ob['fMemberDob'];
                    // $mEmployeeFamily["upload_f_member_image"] =  $famFile_name;
                    $mEmployeeFamily["academic_year"] =  $fy;
                    $mEmployeeFamily["school_id"] =  authUser()->school_id;
                    $mEmployeeFamily["created_by"] =  authUser()->id;
                    $mEmployeeFamily["ip_address"] =  getClientIpAddress();

                    $this->_mEmployeeFamily::create($mEmployeeFamily);
                }
            }

            //return response

            // $result['educationDetails'] = $arrEdu;
            // $result['experienceDetails'] = $arrExp;
            // $result['familyDetails'] = $arrFam;
            return responseMsgs(true, "Successfully Updated", $result, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    // editBasic

    public function edit(Request $req)
    {
        //validation
        $validator = Validator::make($req->all(), [
            'id' => 'required|numeric'
            // 'empNo' => 'required|string',
            // 'salutation' => 'required|string',
            // 'firstName' => 'required|string',
            // 'lastName' => 'required|string',
            // 'gender' => 'required|string',
            // 'category' => 'required|string',
            // 'dob' => 'required|string',
            // 'doj' => 'required|string',
            // 'mobile' => 'required|numeric',
            // 'bloodGroup' => 'required|string',
            // 'department' => 'required|numeric',
            // 'employeeType' => 'required|numeric',
            // 'maritalStatus'  => 'required|string',
            // 'teachingTitle' => 'required|numeric',
            // 'disability' => 'required|string'
            // 'aadharNo' => 'required|numeric|digits:12',
            // 'email' => 'string|email',
        ]);

        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);

        try {
            //variable
            $result = array();
            $file_name = '';
            $getData = $this->_mEmployees::findOrFail($req->id);
            if (!$getData)
                throw new Exception("Employee Not Found!");
            //check if employee no existing or not 
            $isExists = $this->_mEmployees->readEmployeeGroup($req);
            if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
                throw new Exception("Employee ID Already Existing");

            $isExists = $this->_mEmployees->readEmployeeAadharGroup($req);
            if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
                throw new Exception("Aadhar No Already Existing");

            //get financial year
            $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
            $metaReqs = array();
            $timestamp = now()->timestamp;
            if ($req->uploadImage != "") {
                $uploadImage = $req->uploadImage;
                $get_file_name = 'image-' . $timestamp . '-' . $uploadImage->getClientOriginalName();
                $path = public_path('school/employees/' . $getData->empNo);
                $file_name = 'school/employees/' . $get_file_name;
                $uploadImage->move($path, $get_file_name);
                // $req->file('uploadImage')->move($path, $get_file_name);
                $metaReqs = [
                    'upload_image' => $file_name,
                ];
            }

            $metaReqs = array_merge($metaReqs, [
                // 'emp_no' => $req->empNo,
                'salutation_name' => $req->salutation,
                'first_name' => $req->firstName,
                'middle_name' => $req->middleName,
                'last_name' => $req->lastName,
                'email' => $req->email,
                'mobile' => $req->mobile,
                'dob' => $req->dob,
                'doj' => $req->doj,
                'aadhar_no' => $req->aadharNo,
                'disability' => $req->disability,
                'gender_name' => $req->gender,
                'category_name' => $req->category,
                'blood_group_name' => $req->bloodGroup,
                'department_id' => $req->department,
                'employee_type_id' => $req->employeeType,
                'teaching_title_id' => $req->teachingTitle,
                'marital_status_name' => $req->maritalStatus,
                // 'upload_image' => $file_name,
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


    // Edit Address and Parent Details 

    public function editAddress(Request $req)
    {
        //validation
        $validator = Validator::make($req->all(), [
            'id' => 'required|numeric'
        ]);

        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);

        try {
            //variable
            $result = array();
            $file_name = '';
            $getData = $this->_mEmployees::findOrFail($req->id);
            if (!$getData)
                throw new Exception("Employee Not Found!");
            //check if employee no existing or not 
            $isExists = $this->_mEmployees->readEmployeeGroup($req);
            if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
                throw new Exception("Employee ID Already Existing");

            $isExists = $this->_mEmployees->readEmployeeAadharGroup($req);
            if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
                throw new Exception("Aadhar No Already Existing");

            //get financial year
            $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
            $metaReqs = array();
            $timestamp = now()->timestamp;
            if ($req->uploadImage != "") {
                $uploadImage = $req->uploadImage;
                $get_file_name = 'image-' . $timestamp . '-' . $uploadImage->getClientOriginalName();
                $path = public_path('school/employees/' . $getData->empNo);
                $file_name = 'school/employees/' . $get_file_name;
                $uploadImage->move($path, $get_file_name);
                // $req->file('uploadImage')->move($path, $get_file_name);
                $metaReqs = [
                    'upload_image' => $file_name,
                ];
            }

            $metaReqs = array_merge($metaReqs, [
                // 'emp_no' => $req->empNo,
                'p_address1' => $req->pAddress1,
                'p_address2' => $req->pAddress2,
                'p_locality' => $req->pLocality,
                'p_landmark' => $req->pLandmark,
                'p_country_id' => $req->pCountry,
                'p_state_id' => $req->pState,
                'p_district_id' => $req->pDistrict,
                'p_pincode' => $req->pPincode,
                'c_address1' => $req->cAddress1,
                'c_address2' => $req->cAddress2,
                'c_locality' => $req->cLocality,
                'c_landmark' => $req->cLandmark,
                'c_country_id' => $req->cCountry,
                'c_state_id' => $req->cState,
                'c_district_id' => $req->cDistrict,
                'c_pincode' => $req->cPincode,
                'fathers_name' => $req->fathersName,
                'fathers_qualification_name' => $req->fathersQualification,
                'fathers_occupation_name' => $req->fathersOccupation,
                'mothers_name' => $req->mothersName,
                'mothers_qualification_name' => $req->mothersQualification,
                'mothers_occupation_name' => $req->mothersOccupation,
                // 'upload_image' => $file_name,
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

    // Edit Bank Details
    public function editBank(Request $req)
    {
        //validation
        $validator = Validator::make($req->all(), [
            'id' => 'required|numeric'
        ]);

        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);

        try {
            //variable
            $result = array();
            $file_name = '';
            $getData = $this->_mEmployees::findOrFail($req->id);
            if (!$getData)
                throw new Exception("Employee Not Found!");
            //check if employee no existing or not 
            $isExists = $this->_mEmployees->readEmployeeGroup($req);
            if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
                throw new Exception("Employee ID Already Existing");

            $isExists = $this->_mEmployees->readEmployeeAadharGroup($req);
            if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
                throw new Exception("Aadhar No Already Existing");

            //get financial year
            $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
            $metaReqs = array();
            $timestamp = now()->timestamp;
            if ($req->uploadImage != "") {
                $uploadImage = $req->uploadImage;
                $get_file_name = 'image-' . $timestamp . '-' . $uploadImage->getClientOriginalName();
                $path = public_path('school/employees/' . $getData->empNo);
                $file_name = 'school/employees/' . $get_file_name;
                $uploadImage->move($path, $get_file_name);
                // $req->file('uploadImage')->move($path, $get_file_name);
                $metaReqs = [
                    'upload_image' => $file_name,
                ];
            }

            $metaReqs = array_merge($metaReqs, [
                'bank_id' => $req->bank,
                'account_no' => $req->accountNo,
                'account_type' => $req->accountType,
                'ifsc_code' => $req->ifscCode,
                'branch_name' => $req->branchName,
                'nominee_name' => $req->nomineeName,
                'nominee_relation_name' => $req->nomineeRelation,
                'pan_no' => $req->panNo,
                'epf_no' => $req->epfNo,
                'uan_no' => $req->uanNo,
                'esi_no' => $req->esiNo,
                'nps_no' => $req->npsNo,
                'version_no' => $getData->version_no + 1,
            ]);

            if (isset($req->status)) {                  // In Case of Deactivation or Activation 
                $status = $req->status == 'deactive' ? 0 : 1;
                $metaReqs = array_merge($metaReqs, [
                    'status' => $status
                ]);
            }
            $getData->update($metaReqs);
            $result['bankDetails'] = $metaReqs;

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
            $show = $this->_mEmployees->getGroupById($req->id);
            if (collect($show)->isEmpty())
                throw new Exception("Record Not Found!");
            return responseMsgs(true, "", $show, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //check duplicate aadhar existing
    public function getDuplicate1(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'aadharNo' => 'required|numeric'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $checkData = $this->_mEmployees->getDuplicateAadhar($req);
            if (collect($checkData)->isEmpty())
                throw new Exception("Record Not Found!");
            return responseMsgs(true, "", $checkData, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    public function getDuplicateAadhar(Request $req)
    {
        //validation 
        $validator = Validator::make($req->all(), [
            'aadharNo' => 'required|numeric'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);

        try {
            // $data1 = array();
            $msg = '';
            $data = Employee::select('id')
                ->where([['aadhar_no', '=', $req->aadharNo], ['status', '=', '1']])->first();
            if ($data != "") {
                $msg = "Employee Aadhar Already Existing";
                $data1 = $data;
            } else {
                //$msg = "Employee Not Found";
                $data1 = ['aadhar_no' => $req->aadharNo, 'message' => '', 'value' => 'false'];
            }
            return responseMsgs(true, $msg, "", "API_ID_132", "", "146ms", "post",  $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), "", "API_ID_132", "", "", "post", $req->deviceId ?? "");
        }
    }

    /**
     * | View data
     */
    public function retrieveAll(Request $req)
    {
        try {
            $getData = $this->_mEmployees->retrieve();
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
            return responseMsgsT(true, "View All Records", $list, "API_1.0", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    /**
     * | Delete data
     */
    // public function delete(Request $req)
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
    //         $emp = $this->_mEmployees::findOrFail($req->id);
    //         // if ($emp->status == 0)
    //         //     throw new Exception("Records Already Deleted");
    //         $emp->update($metaReqs);
    //         return responseMsgs(true, "Deleted Successfully", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

    //Activate / Deactivate
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
            $delete = $this->_mEmployees::findOrFail($req->id);
            $delete->update($metaReqs);
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Changes Done Successfully", $req->status, "M_API_2.5", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_2.5", responseTime(), "POST", $req->deviceId ?? "");
        }
    }


    // Edit records
    public function updateRole(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'id' => 'required|numeric',
            'roleId' => 'required|numeric'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            // $isExists = $this->_mEmployees->readRoleExist($req);
            // if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
            //     throw new Exception("Role Already Existing");
            $getData = $this->_mEmployees::findOrFail($req->id);
            $metaReqs = [
                'role_id' => $req->roleId,
                'version_no' => $getData->version_no + 1
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
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Records Updated Successfully", [], "API_3.9", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_3.9", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    public function countActiveStudent(Request $req)
    {
        try {
            $rowCount = $this->_mEmployees->countActive();
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Total Count of All Active Students", $rowCount, "API_3.10", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_3.10", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
}
