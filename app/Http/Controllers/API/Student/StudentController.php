<?php

namespace App\Http\Controllers\API\Student;

use App\Http\Controllers\Controller;
use App\Http\Traits\CustomTraits;
use App\Models\Master\ClassMaster;
use App\Models\Master\FinancialYear;
use App\Models\Master\Section;
use App\Models\Master\SectionGroupMap;
use Illuminate\Http\Request;
use App\Models\Student\Student;
use App\Models\Student\StudentTransport;
use App\Models\Student\StudentSibling;
use Exception;
use Illuminate\Support\Carbon;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Validator;
use DB;
use Illuminate\Support\Facades\Hash;


/*
Created By : Lakshmi kumari  
Created On : 19-May-2023 
Code Status : Open 
Description : Student registration is working in two way 1: Offline Mode -- 2: Online Mode
Here custom trait file helpful to store user id and school id for online registartion mode 

Updated By  : Umesh Kumar
Updated On  : 17-July-2023
Code Status : Open

*/

class StudentController extends Controller
{
    private $_mStudents;
    private $_tCustome;
    private $_mStudentTransport;
    private $_mStudentSibling;

    public function __construct()
    {
        DB::enableQueryLog();
        $this->_mStudents = new Student();
        $this->_tCustome = new CustomTraits();
        $this->_mStudentTransport = new StudentTransport();
        $this->_mStudentSibling = new StudentSibling();
    }

    /**---------------------------------------------------------------------------------------------
     * |For BLL 
     */
    public function searchStudentByAdmission(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'admissionNo' => 'required|string'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            //$data = array();           
            $searchAdmNo = new Student();
            $data  = $searchAdmNo->readStudentGroup($req->admissionNo);
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View Records", $data, "API_3.3", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_3.3", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //         return responseMsgs(true, "Records", $data, "API_ID_132", "", "146ms", "post", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), $data, "API_ID_132", "", "", "post", $req->deviceId ?? "");
    //     }
    // }
    /*-------------------------------------------------------------------------------------------*/

    public function store(Request $req)
    {
        //Description: add students basic details 
        $validator = Validator::make($req->all(), [
            'admissionNo'                  => 'required|string',
            'rollNo'                       => 'required',
            'classId'                      => 'required|integer',
            // 'className'                    => 'required|string|max:20',
            'sectionId'                    => 'required|integer',
            // 'sectionName'                  => 'required|string|max:20',
            'firstName'                    => 'required|string|max:20',
            'lastName'                     => 'required|string|max:20',
            'Email'                        => 'string|email|max:255',
            'admissionDate'                => 'required|date',
            'Mobile'                       => 'required|numeric',
            'Dob'                          => 'required|date',
            'aadharNo'                     => 'required|numeric|digits:12',
            'disability'                   => 'required|string|max:10',
            // 'genderId'                     => 'required|integer',
            'genderName'                   => 'required|string|max:20',
            // 'categoryId'                   => 'required|integer',
            'categoryName'                 => 'required|string|max:100',
            // 'casteId'                      => 'required|integer',
            'casteName'                    => 'required|string|max:100',
            // 'bloodGroupId'                 => 'required|integer',
            'bloodGroupName'               => 'required|string|max:10',
            // 'religionId'                   => 'required|integer',
            'religionName'                 => 'required|string|max:50',
            // 'houseWardId'                  => 'required|integer',
            'houseWardName'                => 'required|string|max:50'
            // 'uploadImage'                  => 'file|mimes:jpg,png,jpeg|max:255',
            // 'pAddress1'                    => 'string|max:255',
            // 'pAddress2'                    => 'string|max:255',
            // 'pLocality'                    => 'string|max:255',
            // 'pLandmark'                    => 'string|max:255',
            // 'pCountry'                     => 'string|max:50',
            // 'pState'                       => 'string|max:50',
            // 'pDistrict'                    => 'string|max:50',
            // 'pPincode'                     => 'numeric|digits:6',
            // 'cAddress1'                    => 'string|max:255',
            // 'cAddress2'                    => 'string|max:255',
            // 'cLocality'                    => 'string|max:255',
            // 'cLandmark'                    => 'string|max:255',
            // 'cCountry'                     => 'string|max:50',
            // 'cState'                       => 'string|max:50',
            // 'cDistrict'                    => 'string|max:50',
            // 'cPincode'                     => 'numeric|digits:6',
            // 'fathersName'                  => 'string|max:50',
            // 'fathersQualificationName'     => 'string|max:50',
            // 'fathersOccupationName'        => 'string|max:50',
            // // 'fathersAnnualIncome'         => 'string|max:10',
            // 'mothersName'                  => 'string|max:50',
            // 'mothersQualificationName'     => 'string|max:50',
            // 'mothersOccupationName'        => 'string|max:50',
            // // 'mothersAnnualIncome'         => 'string|max:10',
            // 'bankName'                     => 'string|max:50',
            // 'accountNo'                    => 'numeric',
            // 'accountType'                  => 'string|max:20',
            // 'ifscCode'                     => 'string|max:20',
            // 'branchName'                   => 'string|max:50'

        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            DB::beginTransaction();

            $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
            $isExists = $this->_mStudents->readStudentGroup($req);
            if (collect($isExists)->isNotEmpty())
                throw new Exception("Student already existing");

            $result = array();
            $stdImg = '';
            $fatherImg = '';
            $motherImg = '';
            $guardianImg = '';

            if ($req->uploadImage != "") {
                $uploadImage = $req->uploadImage;
                $get_file_name = $req->admissionNo . '-' . $req->uploadImage->getClientOriginalName();
                $path = public_path('school/students/');
                $stdImg = 'school/students/' . $req->admissionNo . '-' . $uploadImage->getClientOriginalName();
                $req->file('uploadImage')->move($path, $get_file_name);
            }

            if ($req->guardianImage != "") {
                $uploadImage = $req->guardianImage;
                $get_file_name = $req->admissionNo . '-' . $req->uploadImage->getClientOriginalName();
                $path = public_path('school/students/');
                $guardianImg = 'school/students/' . $req->admissionNo . '-' . $uploadImage->getClientOriginalName();
                $req->file('guardianImage')->move($path, $get_file_name);
            }

            if ($req->mothersImage != "") {
                $uploadImage = $req->mothersImage;
                $get_file_name = $req->admissionNo . '-' . $req->uploadImage->getClientOriginalName();
                $path = public_path('school/students/');
                $motherImg = 'school/students/' . $req->admissionNo . '-' . $uploadImage->getClientOriginalName();
                $req->file('mothersImage')->move($path, $get_file_name);
            }

            if ($req->fathersImage != "") {
                $uploadImage = $req->fathersImage;
                $get_file_name = $req->admissionNo . '-' . $req->uploadImage->getClientOriginalName();
                $path = public_path('school/students/');
                $fatherImg = 'school/students/' . $req->admissionNo . '-' . $uploadImage->getClientOriginalName();
                $req->file('fathersImage')->move($path, $get_file_name);
            }

            $this->_mStudents->admission_no = $req['admissionNo'];
            $this->_mStudents->roll_no = $req['rollNo'];
            $this->_mStudents->first_name = $req['firstName'];
            $this->_mStudents->middle_name = $req['middleName'];
            $this->_mStudents->last_name = $req['lastName'];
            $this->_mStudents->class_id = $req['classId'];
            $this->_mStudents->class_name = $req['className'];
            $this->_mStudents->section_id = $req['sectionId'];
            $this->_mStudents->section_name = $req['sectionName'];
            $this->_mStudents->dob = $req['Dob'];
            $this->_mStudents->admission_date = $req['admissionDate'];
            $this->_mStudents->gender_id = $req['genderId'];
            $this->_mStudents->gender_name = $req['genderName'];
            $this->_mStudents->blood_group_id = $req['bloodGroupId'];
            $this->_mStudents->blood_group_name = $req['bloodGroupName'];
            $this->_mStudents->email = $req['Email'];
            $this->_mStudents->mobile = $req['Mobile'];
            $this->_mStudents->aadhar_no = $req['aadharNo'];
            $this->_mStudents->disability = $req['disability'];
            $this->_mStudents->category_id = $req['categoryId'];
            $this->_mStudents->category_name = $req['categoryName'];
            $this->_mStudents->caste_id = $req['casteId'];
            $this->_mStudents->caste_name = $req['casteName'];
            $this->_mStudents->religion_id = $req['religionId'];
            $this->_mStudents->religion_name = $req['religionName'];
            $this->_mStudents->house_ward_id = $req['houseWardId'];
            $this->_mStudents->house_ward_name = $req['houseWardName'];
            $this->_mStudents->upload_image = $stdImg;
            $this->_mStudents->last_school_name = $req['lastSchoolName'];
            $this->_mStudents->last_school_address = $req['lastSchoolAddress'];
            $this->_mStudents->admission_mid_session = $req['admissionMidSession'];
            $this->_mStudents->admission_month = $req['admissionMonth'];
            $this->_mStudents->fathers_name = $req['fathersName'];
            $this->_mStudents->fathers_mob_no = $req['fathersMobNo'];
            $this->_mStudents->fathers_qualification_id = $req['fathersQualificationId'];
            $this->_mStudents->fathers_qualification_name = $req['fathersQualificationName'];
            $this->_mStudents->fathers_occupation_id = $req['fathersOccupationId'];
            $this->_mStudents->fathers_occupation_name = $req['fathersOccupationName'];
            $this->_mStudents->fathers_email = $req['fathersEmail'];
            $this->_mStudents->fathers_aadhar = $req['fathersAadhar'];
            $this->_mStudents->fathers_image = $fatherImg;
            $this->_mStudents->fathers_annual_income = $req['fathersAnnualIncome'];
            $this->_mStudents->mothers_name = $req['mothersName'];
            $this->_mStudents->mothers_mob_no = $req['mothersMobNo'];
            $this->_mStudents->mothers_qualification_id = $req['mothersQualificationId'];
            $this->_mStudents->mothers_qualification_name = $req['mothersQualificationName'];
            $this->_mStudents->mothers_occupation_id = $req['mothersOccupationId'];
            $this->_mStudents->mothers_occupation_name = $req['mothersOccupationName'];
            $this->_mStudents->mothers_email = $req['mothersEmail'];
            $this->_mStudents->mothers_aadhar = $req['mothersAadhar'];
            $this->_mStudents->mothers_image = $motherImg;
            $this->_mStudents->mothers_annual_income = $req['mothersAnnualIncome'];
            $this->_mStudents->guardian_name = $req['guardianName'];
            $this->_mStudents->guardian_mob_no = $req['guardianMobNo'];
            $this->_mStudents->guardian_qualification_id = $req['guardianQualificationId'];
            $this->_mStudents->guardian_qualification_name = $req['guardianQualificationName'];
            $this->_mStudents->guardian_occupation_id = $req['guardianOccupationId'];
            $this->_mStudents->guardian_occupation_name = $req['guardianOccupationName'];
            $this->_mStudents->guardian_email = $req['guardianEmail'];
            $this->_mStudents->guardian_aadhar = $req['guardianAadhar'];
            $this->_mStudents->guardian_image = $guardianImg;
            $this->_mStudents->guardian_annual_income = $req['guardianAnnualIncome'];
            // $this->_mStudents->guardian_relation = $req['guardian_relation'];
            $this->_mStudents->p_address1 = $req['pAddress1'];
            $this->_mStudents->p_address2 = $req['pAddress2'];
            $this->_mStudents->p_locality = $req['pLocality'];
            $this->_mStudents->p_landmark = $req['pLandmark'];
            $this->_mStudents->p_country_id = $req['pCountryId'];
            $this->_mStudents->p_country_name = $req['pCountryName'];
            $this->_mStudents->p_state_id = $req['pStateId'];
            $this->_mStudents->p_state_name = $req['pStateName'];
            $this->_mStudents->p_district_id = $req['pDistrictId'];
            $this->_mStudents->p_district_name = $req['pDistrictName'];
            $this->_mStudents->p_pincode = $req['pPincode'];
            $this->_mStudents->c_address1 = $req['cAddress1'];
            $this->_mStudents->c_address2 = $req['cAddress2'];
            $this->_mStudents->c_locality = $req['cLocality'];
            $this->_mStudents->c_landmark = $req['cLandmark'];
            $this->_mStudents->c_country_id = $req['cCountryId'];
            $this->_mStudents->c_country_name = $req['cCountryName'];
            $this->_mStudents->c_state_id = $req['cStateId'];
            $this->_mStudents->c_state_name = $req['cStateName'];
            $this->_mStudents->c_district_id = $req['cDistrictId'];
            $this->_mStudents->c_district_name = $req['cDistrictName'];
            $this->_mStudents->c_pincode = $req['cPincode'];
            $this->_mStudents->hobbies = $req['Hobbies'];
            $this->_mStudents->bank_id = $req['bank_id'];
            $this->_mStudents->bank_name = $req['bank_name'];
            $this->_mStudents->account_no = $req['accountNo'];
            $this->_mStudents->ifsc_code = $req['ifscCode'];
            $this->_mStudents->branch_name = $req['branchName'];
            $this->_mStudents->is_transport = $req['isTransport'];
            $this->_mStudents->academic_year =   $fy;
            $this->_mStudents->ip_address = getClientIpAddress();
            $this->_mStudents->created_by   = authUser()->id;
            $this->_mStudents->school_id = authUser()->school_id;
            // print_var($this->_mStudents);
            // die;
            $this->_mStudents->save();

            $mStudentTransport = "";
            //insert single data and multi data for student transport
            if ($req['isTransport'] == true) {
                $mStudentTransport = new StudentTransport();
                //$insert = [
                $mStudentTransport->std_tbl_id = $this->_mStudents->id;
                $mStudentTransport->route_id = $req['routeId'];
                $mStudentTransport->pick_up_point_id = $req['pickUpPointId'];
                $mStudentTransport->academic_year = $fy;
                // $mStudentTransport->school_id = authUser()->school_id,
                // $mStudentTransport->created_by = authUser()->id,
                $mStudentTransport->ip_address = getClientIpAddress();
                $mStudentTransport->created_by   = authUser()->id;
                $mStudentTransport->school_id = authUser()->school_id;

                // print_var($mStudentTransport);
                $mStudentTransport->save();
            }

            //insert single data and multi data for student sibling
            $mStudentSibling = "";
            if ($req['siblingDetails'] != "") {
                foreach ($req['siblingDetails'] as $key => $ob) {

                    $mStudentSibling = new StudentSibling();
                    // $insert = [
                    $mStudentSibling->std_tbl_id = $this->_mStudents->id;
                    $mStudentSibling->sibling_name = $ob['siblingName'];
                    $mStudentSibling->sibling_admission_no = $ob['siblingAdmissionNo'];
                    $mStudentSibling->sibling_roll_no = $ob['siblingRollNo'];
                    $mStudentSibling->class_id = $ob['siblingClass'];
                    $mStudentSibling->section_id = $ob['siblingSection'];
                    $mStudentSibling->academic_year = $fy;
                    // $mStudentSibling->school_id = authUser()->school_id,
                    // $mStudentSibling->created_by = authUser()->id,
                    $mStudentSibling->ip_address = getClientIpAddress();
                    $mStudentSibling->created_by   = authUser()->id;
                    $mStudentSibling->school_id = authUser()->school_id;

                    // print_var($mEmpEduObject);
                    $mStudentSibling->save();

                    // $mStudentSibling["std_tbl_id"] =  $this->_mStudents->id;
                    // $mStudentSibling["sibling_name"] =  $ob['siblingName'];
                    // $mStudentSibling["sibling_class"] =  $ob['siblingClass'];
                    // $mStudentSibling["sibling_section"] =  $ob['siblingSection'];
                    // $mStudentSibling["siblingAdmissionNo"] =  $ob['sibling_admission_no'];
                    // $mStudentSibling["sibling_roll_no"] =  $ob['siblingRollNo'];
                    // $mStudentSibling["academic_year"] =  $fy;
                    // $mStudentSibling["school_id"] =  authUser()->school_id;
                    // $mStudentSibling["created_by"] =  authUser()->id;
                    // $mStudentSibling["ip_address"] =  getClientIpAddress();
                    // $stdSiblingData = $this->_mStudentSibling->insertData($mStudentSibling);
                    // print_var($stdTransportata->id);
                }
            }
            // DB::rollback();
            DB::commit();
            // dd();
            $result['basicDetails'] = $this->_mStudents;
            $result['transportDetails'] = $mStudentTransport;
            $result['siblingDetails'] = $mStudentSibling;
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Student Registration Done Successfully", $result, "API_4.1", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_4.1", responseTime(), "POST", $req->deviceId ?? "");
        }
    }


    //for online registration 
    public function onlineRegistration(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'schoolId' => 'integer',
            // 'admissionNo'                  => 'required',
            // 'rollNo'                       => 'required',
            'classId'                      => 'required|integer',
            // 'className'                    => 'required|string|max:20',
            // 'sectionId'                    => 'required|integer',
            // 'sectionName'                  => 'required|string|max:20',
            'firstName'                    => 'required|string|max:20',
            'lastName'                     => 'required|string|max:20',
            // 'Email'                        => 'string|email|max:255',
            // 'admissionDate'                => 'required|date',
            // 'Mobile'                       => 'required|numeric',
            'Dob'                          => 'required|date',
            // 'aadharNo'                     => 'required|numeric|digits:12',
            'disability'                   => 'required|string|max:10',
            // 'genderId'                     => 'required|integer',
            'genderName'                   => 'required|string|max:20',
            // 'categoryId'                   => 'required|integer',
            'categoryName'                 => 'required|string|max:100',
            // 'casteId'                      => 'required|integer',
            'casteName'                    => 'required|string|max:100',
            // 'bloodGroupId'                 => 'required|integer',
            'bloodGroupName'               => 'required|string|max:10',
            // 'religionId'                   => 'required|integer',
            'religionName'                 => 'required|string|max:50',
            // 'houseWardId'                  => 'required|integer',
            // 'houseWardName'                => 'required|string|max:50',
            'uploadImage'                  => 'file|mimes:jpg,png,jpeg|max:255',
            'pAddress1'                    => 'required|string|max:255',
            // 'pAddress2'                    => 'string|max:255',
            // 'pLocality'                    => 'string|max:255',
            // 'pLandmark'                    => 'string|max:255',
            // 'pCountry'                     => 'string|max:50',
            // 'pState'                       => 'string|max:50',
            // 'pDistrict'                    => 'string|max:50',
            'pPincode'                     => 'required|numeric|digits:6',
            // 'cAddress1'                    => 'string|max:255',
            // 'cAddress2'                    => 'string|max:255',
            // 'cLocality'                    => 'string|max:255',
            // 'cLandmark'                    => 'string|max:255',
            // 'cCountry'                     => 'string|max:50',
            // 'cState'                       => 'string|max:50',
            // 'cDistrict'                    => 'string|max:50',
            // 'cPincode'                     => 'numeric|digits:6',
            // 'fathersName'                  => 'string|max:50',
            // 'fathersQualificationName'     => 'string|max:50',
            // 'fathersOccupationName'        => 'string|max:50',
            // // 'fathersAnnualIncome'         => 'string|max:10',
            // 'mothersName'                  => 'string|max:50',
            // 'mothersQualificationName'     => 'string|max:50',
            // 'mothersOccupationName'        => 'string|max:50',
            // // 'mothersAnnualIncome'         => 'string|max:10',
            // 'bankName'                     => 'string|max:50',
            // 'accountNo'                    => 'numeric',
            // 'accountType'                  => 'string|max:20',
            // 'ifscCode'                     => 'string|max:20',
            // 'branchName'                   => 'string|max:50'

        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            // echo $req->schoolId;
            // die;
            DB::beginTransaction();
            $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
            $studentIdGeneration = IdGenerator::generate([
                'table' => 'students',
                'field' => 'admission_no',
                'length' => 20,
                'prefix' => 'REG/' . $fy . '/',
                // 'prefix' => date('Y').'/',
                'reset_on_prefix_change' => true
            ]);

            $isExists = $this->_mStudents->readOnlineStudentGroup($req);
            if (collect($isExists)->isNotEmpty())
                throw new Exception("Student already existing");

            $result = array();
            $stdImg = '';
            $fatherImg = '';
            $motherImg = '';
            $guardianImg = '';
            //Using trait file for user id and school id for std. online reg 
            $userId =  $this->_tCustome->forOnlineRegUser();
            $schoolId =  $req->schoolId;
            // $schoolId =  $this->_tCustome->forOnlineRegSchool();
            // $this->_mStudents->admission_no = $req['admissionNo']; 
            // $this->_mStudents->roll_no = '';
            if ($req->uploadImage != "") {
                $uploadImage = $req->uploadImage;
                $get_file_name = $req->firstName . '-' . $req->uploadImage->getClientOriginalName();
                $path = public_path('school/students/');
                $stdImg = 'school/students/' . $req->firstName . '-' . $uploadImage->getClientOriginalName();
                $req->file('uploadImage')->move($path, $get_file_name);
            }
            // if ($req->guardianImage != "") {
            //     $uploadImage = $req->guardianImage;
            //     $get_file_name = $req->firstName . '-' . $req->uploadImage->getClientOriginalName();
            //     $path = public_path('school/students/');
            //     $guardianImg = 'school/students/' . $req->firstName . '-' . $uploadImage->getClientOriginalName();
            //     $req->file('guardianImage')->move($path, $get_file_name);
            // }
            // if ($req->mothersImage != "") {
            //     $uploadImage = $req->mothersImage;
            //     $get_file_name = $req->firstName . '-' . $req->uploadImage->getClientOriginalName();
            //     $path = public_path('school/students/');
            //     $motherImg = 'school/students/' . $req->firstName . '-' . $uploadImage->getClientOriginalName();
            //     $req->file('mothersImage')->move($path, $get_file_name);
            // }
            // if ($req->fathersImage != "") {
            //     $uploadImage = $req->fathersImage;
            //     $get_file_name = $req->firstName . '-' . $req->uploadImage->getClientOriginalName();
            //     $path = public_path('school/students/');
            //     $fatherImg = 'school/students/' . $req->firstName . '-' . $uploadImage->getClientOriginalName();
            //     $req->file('fathersImage')->move($path, $get_file_name);
            // }
            $this->_mStudents->first_name = $req['firstName'];
            $this->_mStudents->middle_name = $req['middleName'];
            $this->_mStudents->last_name = $req['lastName'];
            $this->_mStudents->class_id = $req['classId'];
            $this->_mStudents->class_name = $req['className'];
            // $this->_mStudents->section_id = $req['sectionId'];
            $this->_mStudents->section_name = $req['sectionName'];
            $this->_mStudents->dob = $req['Dob'];
            // $this->_mStudents->admission_date = $req['admissionDate'];
            // $this->_mStudents->gender_id = $req['genderId'];
            $this->_mStudents->gender_name = $req['genderName'];
            // $this->_mStudents->blood_group_id = $req['bloodGroupId'];
            $this->_mStudents->blood_group_name = $req['bloodGroupName'];
            $this->_mStudents->email = $req['Email'];
            $this->_mStudents->mobile = $req['Mobile'];
            $this->_mStudents->aadhar_no = $req['aadharNo'];
            $this->_mStudents->disability = $req['disability'];
            // $this->_mStudents->category_id = $req['categoryId'];
            $this->_mStudents->category_name = $req['categoryName'];
            // $this->_mStudents->caste_id = $req['casteId'];
            $this->_mStudents->caste_name = $req['casteName'];
            // $this->_mStudents->religion_id = $req['religionId'];
            $this->_mStudents->religion_name = $req['religionName'];
            // $this->_mStudents->house_ward_id = $req['houseWardId'];
            // $this->_mStudents->house_ward_name = $req['houseWardName'];
            $this->_mStudents->upload_image = $stdImg;
            $this->_mStudents->last_school_name = $req['lastSchoolName'];
            $this->_mStudents->last_school_address = $req['lastSchoolAddress'];
            $this->_mStudents->admission_mid_session = $req['admissionMidSession'];
            $this->_mStudents->admission_month = $req['admissionMonth'];
            $this->_mStudents->fathers_name = $req['fathersName'];
            $this->_mStudents->fathers_mob_no = $req['fathersMobNo'];
            // $this->_mStudents->fathers_qualification_id = $req['fathersQualificationId'];
            $this->_mStudents->fathers_qualification_name = $req['fathersQualificationName'];
            // $this->_mStudents->fathers_occupation_id = $req['fathersOccupationId'];
            $this->_mStudents->fathers_occupation_name = $req['fathersOccupationName'];
            $this->_mStudents->fathers_email = $req['fathersEmail'];
            $this->_mStudents->fathers_aadhar = $req['fathersAadhar'];
            $this->_mStudents->fathers_image = $fatherImg;
            $this->_mStudents->fathers_annual_income = $req['fathersAnnualIncome'];
            $this->_mStudents->mothers_name = $req['mothersName'];
            $this->_mStudents->mothers_mob_no = $req['mothersMobNo'];
            // $this->_mStudents->mothers_qualification_id = $req['mothersQualificationId'];
            $this->_mStudents->mothers_qualification_name = $req['mothersQualificationName'];
            // $this->_mStudents->mothers_occupation_id = $req['mothersOccupationId'];
            $this->_mStudents->mothers_occupation_name = $req['mothersOccupationName'];
            $this->_mStudents->mothers_email = $req['mothersEmail'];
            $this->_mStudents->mothers_aadhar = $req['mothersAadhar'];
            $this->_mStudents->mothers_image = $motherImg;
            $this->_mStudents->mothers_annual_income = $req['mothersAnnualIncome'];
            $this->_mStudents->guardian_name = $req['guardianName'];
            $this->_mStudents->guardian_mob_no = $req['guardianMobNo'];
            // $this->_mStudents->guardian_qualification_id = $req['guardianQualificationId'];
            $this->_mStudents->guardian_qualification_name = $req['guardianQualificationName'];
            // $this->_mStudents->guardian_occupation_id = $req['guardianOccupationId'];
            $this->_mStudents->guardian_occupation_name = $req['guardianOccupationName'];
            $this->_mStudents->guardian_email = $req['guardianEmail'];
            $this->_mStudents->guardian_aadhar = $req['guardianAadhar'];
            $this->_mStudents->guardian_image = $guardianImg;
            $this->_mStudents->guardian_annual_income = $req['guardianAnnualIncome'];
            // $this->_mStudents->guardian_relation = $req['guardian_relation'];
            $this->_mStudents->p_address1 = $req['pAddress1'];
            $this->_mStudents->p_address2 = $req['pAddress2'];
            $this->_mStudents->p_locality = $req['pLocality'];
            $this->_mStudents->p_landmark = $req['pLandmark'];
            $this->_mStudents->p_country_id = $req['pCountryId'];
            // $this->_mStudents->p_country_name = $req['pCountryName'];
            $this->_mStudents->p_state_id = $req['pStateId'];
            // $this->_mStudents->p_state_name = $req['pStateName'];
            $this->_mStudents->p_district_id = $req['pDistrictId'];
            // $this->_mStudents->p_district_name = $req['pDistrictName'];
            $this->_mStudents->p_pincode = $req['pPincode'];
            $this->_mStudents->c_address1 = $req['cAddress1'];
            $this->_mStudents->c_address2 = $req['cAddress2'];
            $this->_mStudents->c_locality = $req['cLocality'];
            $this->_mStudents->c_landmark = $req['cLandmark'];
            $this->_mStudents->c_country_id = $req['cCountryId'];
            // $this->_mStudents->c_country_name = $req['cCountryName'];
            $this->_mStudents->c_state_id = $req['cStateId'];
            // $this->_mStudents->c_state_name = $req['cStateName'];
            $this->_mStudents->c_district_id = $req['cDistrictId'];
            // $this->_mStudents->c_district_name = $req['cDistrictName'];
            $this->_mStudents->c_pincode = $req['cPincode'];
            $this->_mStudents->hobbies = $req['Hobbies'];
            $this->_mStudents->bank_id = $req['bank_id'];
            $this->_mStudents->bank_name = $req['bank_name'];
            $this->_mStudents->account_no = $req['accountNo'];
            $this->_mStudents->ifsc_code = $req['ifscCode'];
            $this->_mStudents->branch_name = $req['branchName'];
            $this->_mStudents->is_transport = $req['isTransport'];
            $this->_mStudents->academic_year =   $fy;
            $this->_mStudents->ip_address = getClientIpAddress();
            $this->_mStudents->admission_no = $studentIdGeneration;
            $this->_mStudents->created_by   = $userId;
            $this->_mStudents->school_id = $schoolId;
            $this->_mStudents->status = '2';
            // print_var($this->_mStudents);
            $this->_mStudents->save();

            //insert student transport records
            if ($req['isTransport'] == true) {
                $mStudentTransport = new StudentTransport();
                $mStudentTransport->std_tbl_id = $this->_mStudents->id;
                $mStudentTransport->route_id = $req['routeId'];
                $mStudentTransport->pick_up_point_id = $req['pickUpPointId'];
                $mStudentTransport->academic_year = $fy;
                $mStudentTransport->ip_address = getClientIpAddress();
                $mStudentTransport->created_by   = $userId;
                $mStudentTransport->school_id = $schoolId;
                // print_var($mStudentTransport);
                $mStudentTransport->save();
            }

            //insert single data and multi data for student sibling 
            if ($req['siblingDetails'] != "") {
                foreach ($req['siblingDetails'] as $key => $ob) {
                    $mStudentSibling = new StudentSibling();
                    $mStudentSibling->std_tbl_id = $this->_mStudents->id;
                    $mStudentSibling->sibling_name = $ob['siblingName'];
                    $mStudentSibling->sibling_admission_no = $ob['siblingAdmissionNo'];
                    $mStudentSibling->sibling_roll_no = $ob['siblingRollNo'];
                    $mStudentSibling->class_id = $ob['siblingClass'];
                    $mStudentSibling->section_id = $ob['siblingSection'];
                    $mStudentSibling->academic_year = $fy;
                    $mStudentSibling->ip_address = getClientIpAddress();
                    $mStudentTransport->created_by   = $userId;
                    $mStudentTransport->school_id = $schoolId;
                    // print_var($mEmpEduObject);
                    $mStudentSibling->save();
                }
            }
            // DB::rollback();
            DB::commit();
            // dd();
            $result['basicDetails'] = $this->_mStudents;
            $result['transportDetails'] = $mStudentTransport;
            $result['siblingDetails'] = $mStudentSibling;
            return responseMsgs(true, "Student Registration Done Successfully", $result, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    /**
     * | Update Student table
     */
    public function edit(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'id'                           => 'required|numeric',
            'admissionNo'                  => 'required',
            'rollNo'                       => 'required',
            'classId'                      => 'required|integer',
            // 'className'                    => 'required|string|max:20',
            'sectionId'                    => 'required|integer',
            // 'sectionName'                  => 'required|string|max:20',
            'firstName'                    => 'required|string|max:20',
            'lastName'                     => 'required|string|max:20',
            'Email'                        => 'string|email|max:255',
            'admissionDate'                => 'required|date',
            'Mobile'                       => 'required|numeric',
            'Dob'                          => 'required|date',
            'aadharNo'                     => 'required|numeric|digits:12',
            'disability'                   => 'required|string|max:10',
            // 'genderId'                     => 'required|integer',
            'genderName'                   => 'required|string|max:20',
            // 'categoryId'                   => 'required|integer',
            'categoryName'                 => 'required|string|max:100',
            // 'casteId'                      => 'required|integer',
            'casteName'                    => 'required|string|max:100',
            // 'bloodGroupId'                 => 'required|integer',
            'bloodGroupName'               => 'required|string|max:10',
            // 'religionId'                   => 'required|integer',
            'religionName'                 => 'required|string|max:50',
            // 'houseWardId'                  => 'required|integer',
            'houseWardName'                => 'required|string|max:50'
            // 'uploadImage'                  => 'file|mimes:jpg,png,jpeg|max:255',
            // 'pAddress1'                    => 'string|max:255',
            // 'pAddress2'                    => 'string|max:255',
            // 'pLocality'                    => 'string|max:255',
            // 'pLandmark'                    => 'string|max:255',
            // 'pCountry'                     => 'string|max:50',
            // 'pState'                       => 'string|max:50',
            // 'pDistrict'                    => 'string|max:50',
            // 'pPincode'                     => 'numeric|digits:6',
            // 'cAddress1'                    => 'string|max:255',
            // 'cAddress2'                    => 'string|max:255',
            // 'cLocality'                    => 'string|max:255',
            // 'cLandmark'                    => 'string|max:255',
            // 'cCountry'                     => 'string|max:50',
            // 'cState'                       => 'string|max:50',
            // 'cDistrict'                    => 'string|max:50',
            // 'cPincode'                     => 'numeric|digits:6',
            // 'fathersName'                  => 'string|max:50',
            // 'fathersQualificationName'     => 'string|max:50',
            // 'fathersOccupationName'        => 'string|max:50',
            // // 'fathersAnnualIncome'         => 'string|max:10',
            // 'mothersName'                  => 'string|max:50',
            // 'mothersQualificationName'     => 'string|max:50',
            // 'mothersOccupationName'        => 'string|max:50',
            // // 'mothersAnnualIncome'         => 'string|max:10',
            // 'bankName'                     => 'string|max:50',
            // 'accountNo'                    => 'numeric',
            // 'accountType'                  => 'string|max:20',
            // 'ifscCode'                     => 'string|max:20',
            // 'branchName'                   => 'string|max:50'


            // 'rollNo'                       => 'required|string',
            // 'classId'                      => 'required|integer',
            // 'className'                    => 'required|string|max:20',
            // 'sectionId'                    => 'required|integer',
            // 'sectionName'                  => 'required|string|max:20',
            // 'firstName'                    => 'required|string|max:20',
            // 'middleName'                   => 'string|max:20',
            // 'lastName'                     => 'required|string|max:20',
            // 'Email'                        => 'string|email|max:255',
            // 'Mobile'                       => 'required|numeric|digits:10|regex:/[0-9]/',
            // 'Dob'                          => 'required|date',
            // 'aadharNo'                     => 'required|numeric|digits:12',
            // 'disability'                   => 'required|string|max:10',
            // 'genderId'                     => 'required|integer',
            // 'genderName'                   => 'required|string|max:20',
            // 'categoryId'                   => 'required|integer',
            // 'categoryName'                 => 'required|string|max:100',
            // 'casteId'                      => 'required|integer',
            // 'casteName'                    => 'required|string|max:100',
            // 'bloodGroupId'                 => 'required|integer',
            // 'bloodGroupName'               => 'required|string|max:10',
            // 'religionId'                   => 'required|integer',
            // 'religionName'                 => 'required|string|max:50',
            // 'houseWardId'                  => 'required|integer',
            // 'houseWardName'                => 'required|string|max:50'
            // 'uploadImage'                  => 'string|upload_image|mimes:jpg,png,jpeg|max:255',
            // 'pAddress1'                    => 'string|max:255',
            // 'pAddress2'                    => 'string|max:255',
            // 'pLocality'                    => 'string|max:255',
            // 'pLandmark'                    => 'string|max:255',
            // 'pCountry'                     => 'string|max:50',
            // 'pState'                       => 'string|max:50',
            // 'pDistrict'                    => 'string|max:50',
            // 'pPincode'                     => 'numeric|digits:6',
            // 'cAddress1'                    => 'string|max:255',
            // 'cAddress2'                    => 'string|max:255',
            // 'cLocality'                    => 'string|max:255',
            // 'cLandmark'                    => 'string|max:255',
            // 'cCountry'                     => 'string|max:50',
            // 'cState'                       => 'string|max:50',
            // 'cDistrict'                    => 'string|max:50',
            // 'cPincode'                     => 'numeric|digits:6',
            // 'fathersName'                  => 'string|max:50',
            // 'fathersQualificationName'     => 'string|max:50',
            // 'fathersOccupationName'        => 'string|max:50',
            // // 'fathersAnnualIncome'         => 'string|max:10',
            // 'mothersName'                  => 'string|max:50',
            // 'mothersQualificationName'     => 'string|max:50',
            // 'mothersOccupationName'        => 'string|max:50',
            // // 'mothersAnnualIncome'         => 'string|max:10',
            // 'bankName'                     => 'string|max:50',
            // 'accountNo'                    => 'numeric',
            // 'accountType'                  => 'string|max:20',
            // 'ifscCode'                     => 'string|max:20',
            // 'branchName'                   => 'string|max:50'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);

        try {
            $isExists = $this->_mStudents->readStudentGroup($req->admissionNo);
            if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
                throw new Exception("Student Already Existing");
            $getData = $this->_mStudents::findOrFail($req->id);

            // $this->_mStudents-> admission_no = $req['admissionNo'];
            $this->_mStudents->roll_no = $req['rollNo'];
            $this->_mStudents->first_name = $req['firstName'];
            $this->_mStudents->middle_name = $req['middleName'];
            $this->_mStudents->last_name = $req['lastName'];
            $this->_mStudents->class_id = $req['classId'];
            $this->_mStudents->class_name = $req['className'];
            $this->_mStudents->section_id = $req['sectionId'];
            $this->_mStudents->section_name = $req['sectionName'];
            $this->_mStudents->dob = $req['Dob'];
            $this->_mStudents->admission_date = $req['admissionDate'];
            $this->_mStudents->gender_id = $req['genderId'];
            $this->_mStudents->gender_name = $req['genderName'];
            $this->_mStudents->blood_group_id = $req['bloodGroupId'];
            $this->_mStudents->blood_group_name = $req['bloodGroupName'];
            $this->_mStudents->email = $req['Email'];
            $this->_mStudents->mobile = $req['Mobile'];
            $this->_mStudents->aadhar_no = $req['aadharNo'];
            $this->_mStudents->disability = $req['disability'];
            $this->_mStudents->category_id = $req['categoryId'];
            $this->_mStudents->category_name = $req['categoryName'];
            $this->_mStudents->caste_id = $req['casteId'];
            $this->_mStudents->caste_name = $req['casteName'];
            $this->_mStudents->religion_id = $req['religionId'];
            $this->_mStudents->religion_name = $req['religionName'];
            $this->_mStudents->house_ward_id = $req['houseWardId'];
            $this->_mStudents->house_ward_name = $req['houseWardName'];
            $this->_mStudents->upload_image = $req['uploadImage'];
            $this->_mStudents->last_school_name = $req['lastSchoolName'];
            $this->_mStudents->last_school_address = $req['lastSchoolAddress'];
            $this->_mStudents->admission_mid_session = $req['admissionMidSession'];
            $this->_mStudents->admission_month = $req['admissionMonth'];
            $this->_mStudents->fathers_name = $req['fathersName'];
            $this->_mStudents->fathers_mob_no = $req['fathersMobNo'];
            $this->_mStudents->fathers_qualification_id = $req['fathersQualificationId'];
            $this->_mStudents->fathers_qualification_name = $req['fathersQualificationName'];
            $this->_mStudents->fathers_occupation_id = $req['fathersOccupationId'];
            $this->_mStudents->fathers_occupation_name = $req['fathersOccupationName'];
            $this->_mStudents->fathers_email = $req['fathersEmail'];
            $this->_mStudents->fathers_aadhar = $req['fathersAadhar'];
            $this->_mStudents->fathers_image = '';
            $this->_mStudents->fathers_annual_income = $req['fathersAnnualIncome'];
            $this->_mStudents->mothers_name = $req['mothersName'];
            $this->_mStudents->mothers_mob_no = $req['mothersMobNo'];
            $this->_mStudents->mothers_qualification_id = $req['mothersQualificationId'];
            $this->_mStudents->mothers_qualification_name = $req['mothersQualificationName'];
            $this->_mStudents->mothers_occupation_id = $req['mothersOccupationId'];
            $this->_mStudents->mothers_occupation_name = $req['mothersOccupationName'];
            $this->_mStudents->mothers_email = $req['mothersEmail'];
            $this->_mStudents->mothers_aadhar = $req['mothersAadhar'];
            $this->_mStudents->mothers_image = '';
            $this->_mStudents->mothers_annual_income = $req['mothersAnnualIncome'];
            $this->_mStudents->guardian_name = $req['guardianName'];
            $this->_mStudents->guardian_mob_no = $req['guardianMobNo'];
            $this->_mStudents->guardian_qualification_id = $req['guardianQualificationId'];
            $this->_mStudents->guardian_qualification_name = $req['guardianQualificationName'];
            $this->_mStudents->guardian_occupation_id = $req['guardianOccupationId'];
            $this->_mStudents->guardian_occupation_name = $req['guardianOccupationName'];
            $this->_mStudents->guardian_email = $req['guardianEmail'];
            $this->_mStudents->guardian_aadhar = $req['guardianAadhar'];
            $this->_mStudents->guardian_image = '';
            $this->_mStudents->guardian_annual_income = $req['guardianAnnualIncome'];
            // $this->_mStudents->guardian_relation = $req['guardian_relation'];
            $this->_mStudents->p_address1 = $req['pAddress1'];
            $this->_mStudents->p_address2 = $req['pAddress2'];
            $this->_mStudents->p_locality = $req['pLocality'];
            $this->_mStudents->p_landmark = $req['pLandmark'];
            $this->_mStudents->p_country_id = $req['pCountryId'];
            $this->_mStudents->p_country_name = $req['pCountryName'];
            $this->_mStudents->p_state_id = $req['pStateId'];
            $this->_mStudents->p_state_name = $req['pStateName'];
            $this->_mStudents->p_district_id = $req['pDistrictId'];
            $this->_mStudents->p_district_name = $req['pDistrictName'];
            $this->_mStudents->p_pincode = $req['pPincode'];
            $this->_mStudents->c_address1 = $req['cAddress1'];
            $this->_mStudents->c_address2 = $req['cAddress2'];
            $this->_mStudents->c_locality = $req['cLocality'];
            $this->_mStudents->c_landmark = $req['cLandmark'];
            $this->_mStudents->c_country_id = $req['cCountryId'];
            $this->_mStudents->c_country_name = $req['cCountryName'];
            $this->_mStudents->c_state_id = $req['cStateId'];
            $this->_mStudents->c_state_name = $req['cStateName'];
            $this->_mStudents->c_district_id = $req['cDistrictId'];
            $this->_mStudents->c_district_name = $req['cDistrictName'];
            $this->_mStudents->c_pincode = $req['cPincode'];
            $this->_mStudents->hobbies = $req['Hobbies'];
            $this->_mStudents->bank_id = $req['bank_id'];
            $this->_mStudents->bank_name = $req['bank_name'];
            $this->_mStudents->account_no = $req['accountNo'];
            $this->_mStudents->ifsc_code = $req['ifscCode'];
            $this->_mStudents->branch_name = $req['branchName'];
            $this->_mStudents->is_transport = $req['isTransport'];

            $this->_mStudents->version_no = $getData->version_no + 1;
            $this->_mStudents->updated_at = Carbon::now();

            if (isset($req->status)) {                  // In Case of Deactivation or Activation 
                $status = $req->status == 'deactive' ? 0 : 1;
                $this->_mStudents->status = $status;
            }
            $data = $this->_mStudents;
            $this->_mStudents->update();
            // $feeHead = $this->_mStudents::findOrFail($req->id);
            // $feeHead->update($metaReqs);
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Records Updated Successfully", [$data], "API_4.2", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_4.2", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    /**
     * | Get Student Group By Id
     */
    public function show(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'id' => 'required|numeric'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $show = $this->_mStudents->getGroupById($req->id);
            if (collect($show)->isEmpty())
                throw new Exception("Data Not Found");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View Records", $show, "API_4.3", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_4.3", responseTime(), "POST", $req->deviceId ?? "");
        }
    }


    public function showStudentGroup(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'classId' => 'required|numeric',
            // 'id' => 'required|numeric'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $show = $this->_mStudents->getStudentGroupBySection($req);
            if (collect($show)->isEmpty())
                throw new Exception("Data Not Found");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View Records", $show, "API_3.3", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_3.3", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
    //         return responseMsgs(true, "", $show, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

    /**
     * | Retrieve All
     */
    public function retrieveAll(Request $req)
    {
        try {
            $getData = $this->_mStudents->retrieveAll();
            $perPage = $req->perPage ? $req->perPage : 10;
            $paginater = $getData->paginate($perPage);
            $list = [
                "current_page" => $paginater->currentPage(),
                "perPage" => $perPage,
                "last_page" => $paginater->lastPage(),
                "data" => collect($paginater->items())->map(function ($val) {
                    $path = "getImageLink?path=";
                    $val->ebook_docs = trim($val->ebook_docs) ? ($path . $val->ebook_docs) : "";
                    $val->cover_pic_docs = trim($val->cover_pic_docs) ? ($path . $val->cover_pic_docs) : "";
                    return $val;
                }),
                "total" => $paginater->total()
            ];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View All Records", $list, "API_4.5", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_4.5", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //Delete
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
            $std = $this->_mStudents::findOrFail($req->id);
            // return $std->status; die; 
            // if ($std->status == 0)
            //     throw new Exception("Records Already Deleted");
            $std->update($metaReqs);
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Changes Done Successfully", $req->status, "API_4.6", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_4.6", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    public function searchStdByAdmNo(Request $req)
    {
        //Description: Get records by id
        $validator = Validator::make($req->all(), [
            'admissionNo' => 'required|string'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $msg = '';
            $data = Student::select('id')
                ->where([['admission_no', '=', $req->admissionNo], ['status', '=', '1']])->first();
            if ($data != "") {
                $msg = "Student ID Already Existing";
                $data1 = $data;
            } else {
                $msg = "Student Not Found";
                $data1 = ['admission_no' => $req->admissionNo, 'message' => 'Admission No. not found', 'value' => 'false'];
            }
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View Searched Records", $data1, "API_4.8", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_4.8", responseTime(), "POST", $req->deviceId ?? "");
        }
    }


    public function searchStdByAdmNo1(Request $req)
    {
        //Description: Get records by id
        $validator = Validator::make($req->all(), [
            'admissionNo' => 'required|string'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $data = array();

            $searchAdmNo = new Student();
            $data1  = $searchAdmNo->searchAdmNo($req);
            $msg = $data1['message'];
            $data = $data1;
            // $data = $data1['emp_id'];
            // $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, $msg, $data, "API_ID_132", "", "146ms", "post", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_132", "", "", "post", $req->deviceId ?? "");
        }
    }

    public function viewStudent(Request $req)
    {
        //Description: Get all records 
        try {
            $data = Student::list();
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_131", "", "186ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_131", "", "", "get", $mDeviceId);
        }
    }


    // Get Student Id Details...........
    public function getIdCard(Request $req)
    {

        $validator = Validator::make($req->all(), [
            'admissionNo' => 'required|string',
            'classId' => 'required|numeric',
            // 'sectionId' => 'required|numeric'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $mStudents = Student::where('admission_no', $req->admissionNo)
                ->where('status', 1)
                ->first();
            if (collect($mStudents)->isEmpty())
                throw new Exception('Admission no is not existing');
            $studentId  = $mStudents->id;
            $getAll = $this->_mStudents->getStudentIdDetails($req, $studentId);
            if (collect($getAll)->isEmpty())
                throw new Exception("Data Not Found");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "", $getAll, "API_4.13", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_4.13", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
    //         return responseMsgs(true, "", $getAll, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

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
            // $isExists = $this->_mStudents->readRoleExist($req);
            // if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
            //     throw new Exception("Role Already Existing");
            $getData = $this->_mStudents::findOrFail($req->id);
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
            return responseMsgsT(true, "Records Updated Successfully", [], "API_4.14", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_4.14", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    /**
     * |add bulk data using csv
     */
    public function storeCSV(Request $req)
    {
        //validation
        $validator = Validator::make($req->all(), [
            'uploadCSV' => 'required|mimes:csv|max:2048'
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
                    while (($filedata = fgetcsv($file, 4096, ",")) !== FALSE) {
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

                    foreach ($importData_arr as $data) {
                        // getting class id...
                        $className = $data[4];
                        // ->where(DB::raw('upper(fee_head_name)'), 'LIKE', '%' . strtoupper($req->search) . '%');
                        $classObj = ClassMaster::where('class_name', $className)->firstOrFail();
                        // $classObj = ClassMaster::where(DB::raw('upper(class_name)'), 'LIKE', '%' . strtoupper($className) . '%')->firstOrFail();

                        // print_var($classObj);
                        // die;
                        $classId = $classObj->id;
                        // die;



                        // getting section id...
                        // $sectionName = $data[9];
                        // $sectionObj = Section::where('section_name', $sectionName)->firstOrFail();
                        // $sectionId = $sectionObj->id;
                        // print_var($sectionObj);
                        // die;
                        // $financialYear = $data[12]; 
                        // $fyObj = MsFinancialYear::where('financial_year', $financialYear)->firstOrFail();
                        // $fyId = $fyObj->id;

                        // getting gender id...
                        $genderId = null;
                        $genderName = $data[5];
                        if ($genderName == 'M') {
                            $genderId = 1;
                            $genderName = 'male';
                        } elseif ($genderName == 'F') {
                            $genderId = 2;
                            $genderName = 'female';
                        } else {
                            $genderId = 3;
                            $genderName = 'other';
                        }

                        $yr = explode('/', $data[1]);
                        // echo $yr[0];

                        // getting section id...
                        $yrObj = FinancialYear::where('abbr', $yr[0])->firstOrFail();
                        // print_var($yrObj);
                        $yrName = $yrObj->fy;


                        $admissionNo = $data[1] . '/' . $data[2];
                        $insertData = array(
                            // 'roll_no' => $data[2],
                            'first_name' => $data[0],
                            'academic_year' => $yrName,
                            'admission_no' => $admissionNo,
                            'fathers_name' => $data[3],
                            'class_id' => $classId,
                            'class_name' => $data[4],
                            'gender_id' => $genderId,
                            'gender_name' => $genderName,
                            'p_address1' => $data[6],
                            'mobile' => $data[7],
                            'admission_date' => $data[8],
                            // 'section_name' => $data[9],
                            // 'section_id' => $sectionId,
                            'created_by' => 1,
                            'ip_address' => getClientIpAddress()
                        );
                        // print_var($insertData);
                        // die;
                        // $insertData = array_merge($insertData, [
                        //     'json_logs' => trim(json_encode($insertData), ",")
                        // ]);
                        // Check for duplicate data before inserting
                        $duplicateData = Student::where('admission_no', $admissionNo)->first();
                        if (!$duplicateData) {
                            Student::create($insertData);
                            // $this->_mStudents->csv($insertData);
                        } else {
                            throw new Exception("Data Already Existing");
                        }
                    }
                }
            }
            return responseMsgs(true, "Successfully Uploaded", '', "", "3.1", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "3.1", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    /**
     * |add bulk data using csv
     */
    // public function storeCSV(Request $req)
    // {
    //     //validation
    //     $validator = Validator::make($req->all(), [
    //         'uploadCSV' => 'required'
    //     ]);
    //     if ($validator->fails())
    //         return responseMsgs(false, $validator->errors(), []);
    //     try {

    //         $file = $req->file('uploadCSV');
    //         $filename = $file->getClientOriginalName();
    //         $extension = $file->getClientOriginalExtension();
    //         $tempPath = $file->getRealPath();
    //         $fileSize = $file->getSize();
    //         $mimeType = $file->getMimeType();
    //         // Valid File Extensions
    //         $valid_extension = array("csv");

    //         // 2MB in Bytes
    //         $maxFileSize = 2097152;

    //         // Check file extension
    //         if (in_array(strtolower($extension), $valid_extension)) {
    //             // Check file size
    //             if ($fileSize <= $maxFileSize) {
    //                 // File upload location
    //                 $location = 'uploads';
    //                 // Upload file
    //                 $file->move($location, $filename);
    //                 // Import CSV to Database
    //                 $filepath = public_path($location . "/" . $filename);

    //                 // Reading file
    //                 $file = fopen($filepath, "r");
    //                 $importData_arr = array();
    //                 $i = 0;
    //                 while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
    //                     $num = count($filedata);
    //                     // Skip first row (Remove below comment if you want to skip the first row)
    //                     if ($i == 0) {
    //                         $i++;
    //                         continue;
    //                     }
    //                     for ($c = 0; $c < $num; $c++) {
    //                         $importData_arr[$i][] = $filedata[$c];
    //                     }
    //                     $i++;
    //                 }
    //                 fclose($file);
    //                 // Insert to MySQL database
    //                 foreach ($importData_arr as $importData) {
    //                     // dd($importData);
    //                     $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
    //                     $insertData = array(
    //                         "admission_no" => $importData[0],
    //                         "roll_no" => $importData[1],
    //                         "first_name" => $importData[2],
    //                         "middle_name" => $importData[3],
    //                         "last_name" => $importData[4],
    //                         "class_name" => $importData[5],
    //                         "section_name" => $importData[6],
    //                         "dob" => $importData[7],
    //                         "admission_date" => $importData[8],
    //                         "gender_name" => $importData[9],
    //                         "blood_group_name" => $importData[10],
    //                         "email" => $importData[11],
    //                         "mobile" => $importData[12],
    //                         "aadhar_no" => $importData[13],
    //                         "disability" => $importData[14],
    //                         "category_name" => $importData[15],
    //                         "caste_name" => $importData[16],
    //                         "religion_name" => $importData[17],
    //                         "house_ward_name" => $importData[18],
    //                         "academic_year" => $fy,
    //                         "school_id" => authUser()->school_id,
    //                         "created_by" => authUser()->id,
    //                         "ip_address" => getClientIpAddress(),
    //                         "class_id" => 0
    //                     );
    //                     // dd($insertData);
    //                     Student::csv($insertData);
    //                 }
    //             }
    //         }
    //         $queryTime = collect(DB::getQueryLog())->sum("time");
    //         return responseMsgsT(true, "Successfully Uploaded", $insertData, "API_4.9", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "API_4.9", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

    /**
     * | Login Student 
     * | Description: This user will be Super Admin and can create Admin or others users and grant them menu permission.
     */
    public function login(Request $req)
    {
        //validation
        $validator = Validator::make($req->all(), [
            //'email' => 'required|email',
            'admissionNo' => 'required',
            'dob' => 'required'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            //check email existing or not
            $mStudentMaster = Student::where('admission_no', $req->admissionNo)->first();
            $rolId = $mStudentMaster->role_id;

            $mStudentMasterRole = DB::table('roles')
                ->select(DB::raw("id,role_name"))
                ->where('id', $rolId)
                ->where('status', 1)
                ->first();
            $roleName =    $mStudentMasterRole->role_name;

            if (!$mStudentMaster) {
                $msg = "Oops! Given username does not exist";
                return responseMsg(false, $msg, "");
            }
            // check if user deleted or not
            if ($mStudentMaster->status == 0) {
                $msg = "Cant logged in!! You Have Been Suspended or Deleted !";
                return responseMsg(false, $msg, "");
            }
            $formatDate = date('Y-m-d', strtotime($mStudentMaster->dob));
            //check if user and password is existing  
            // if ($mStudentMaster && Hash::check($req->password, $mStudentMaster->password)) {
            if ($formatDate == $mStudentMaster->dob) {
                $token = $mStudentMaster->createToken('auth_token')->plainTextToken;
                $mStudentMaster->remember_token = $token;
                $mStudentMaster->save();
                $data1 = ['name' => $mStudentMaster->first_name, 'admissionNo' => $mStudentMaster->admission_no, 'token' => $token, 'token_type' => 'Bearer', 'roleName' => $roleName, 'roleId' => $mStudentMaster->role_id];
                //return responseMsgsT(true, "Login successfully", $data1, "API-5.2", "", responseTime(), "POST", $req->deviceId ?? "", $token);
            } else
                throw new Exception("Date of Birth is Incorrect");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            if (!$data1)
                throw new Exception("Record Not Found!");
            return responseMsgsT(true, "Login Successfully", $data1, "API_4.11", $queryTime, responseTime(), "POST", $req->deviceId ?? "", $token);
        } catch (Exception $e) {
            return responseMsgsT(false, $e->getMessage(), "", "API_4.11", "", "", "post", "", "");
        }
    }


    /** ------------------------------------------------ Reporting API  ---------------------------------------------*/
    /**
     * | Get All Online Registered Student
     */
    public function getAllOnlineStudent(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'financialYear' => 'required',
            'paymentStatus' => 'required'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $schoolId =  $this->_tCustome->forOnlineRegSchool();
            $std = $this->_mStudents->getAllStudent($req, $schoolId);
            return responseMsgs(true, "", $std, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    public function countActiveStudent(Request $req)
    {
        try {
            $rowCount = $this->_mStudents->countActive();
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Total Count of All Active Students", $rowCount, "API_3.5", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_3.5", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
}
