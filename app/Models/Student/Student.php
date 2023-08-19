<?php

namespace App\Models\Student;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Exception;
use DB;
use App\Models\Student\StudentSibling;
use App\Models\Admin\StudentTransport;
use App\Models\Admin\User;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/*
Created By : Lakshmi kumari 
Created On : 19-May-2023 
Code Status : Open 
*/

class Student extends Model
{
  use HasApiTokens, HasFactory, Notifiable;
  //use HasFactory;
  protected $guarded = [];

  /**---------------------------------------------------------------------------------------------
   * |Also used in BLL 
   */
  // //Read all students    
  // public function readStudentGroup($admissionNo)
  // {
  //   return Student::where('admission_no', $admissionNo)
  //     ->where('status', 1)
  //     ->where('school_id', authUser()->school_id)
  //     ->get();
  // }

  //read online student validation
  public function readOnlineStudentGroup($req)
  {
    return Student::where('admission_no', $req->admissionNo)
      ->where('status', 1)
      // ->where('school_id', $req->schoolId)
      ->get();
  }

  //Read all students    
  public function readStudentGroup($req)
  {

    return Student::where('admission_no', $req->admissionNo)
      ->where('status', 1)
      // ->where('school_id', authUser()->school_id)
      ->get();
  }

  // //Read all students financial year wise
  // public function getStudentsByFy($fy)
  // {
  //   return Student::where('academic_year', $fy)
  //     // ->where('academic_year', getFinancialYear(Carbon::now()->format('Y-m-d')))
  //     ->where('school_id', authUser()->school_id)
  //     ->where('status', 1)
  //     ->get();
  // }
  /*-------------------------------------------------------------------------------------------*/

  /*Add Records*/
  public function store(array $req)
  {
    Student::create($req);
  }

  /*Read Records by ID*/
  public function getGroupById($id)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return Student::select(
      '*',
      DB::raw("
      CASE 
        WHEN status = '0' THEN 'Deactivated'  
        WHEN status = '1' THEN 'Active'
        END as status,
        TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(created_at,'HH12:MI:SS AM') as time
	  ")
    )
      ->where('id', $id)
      // ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      // ->where('status', 1)
      ->first();
  }


  /*Read all Records by*/
  public function retrieveAll()
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return Student::select(
      '*',
      DB::raw("
      CASE 
        WHEN status = '0' THEN 'Deactivated'  
        WHEN status = '1' THEN 'Active'
        WHEN status = '2' THEN 'Deactivated'
        WHEN status = '3' THEN 'Deactivated'
        END as status,
        TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(created_at,'HH12:MI:SS AM') as time
	  ")
    );
    // ->where('school_id', $schoolId)
    // ->orWhere('created_by', $createdBy)
    // ->where('status', 1)
    // ->get();
  }

  public function getStudentGroupBySection($req)
  {
    // return $req->id; 
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    // c.section_name ,
    return DB::table('students as a')
      ->select(
        DB::raw("b.class_name,CONCAT(a.first_name,'',a.middle_name,' ',a.last_name) as full_name ,
        a.admission_no,a.roll_no,a.id,               
        CASE WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        ")
      )
      ->join('class_masters as b', 'b.id', '=', 'a.class_id')
      // ->leftjoin('section_group_maps as c', 'c.id', '=', 'a.section_id')
      ->where('a.class_id', $req->classId)
      // ->orWhere('a.section_id', $req->id)
      ->where('a.status', 1)
      // ->where('a.school_id', $schoolId)
      // ->where('a.created_by', $createdBy)
      ->get();
  }

  public function getStudentGroupBySection2($req)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return DB::table('students as a')
      ->select(
        DB::raw("b.class_name,CONCAT(a.first_name,' ',a.middle_name,' ',a.last_name) as full_name ,
        a.admission_no,a.roll_no,c.section_name,a.id,        
        CASE WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        ")
      )
      ->join('class_masters as b', 'b.id', '=', 'a.class_id')
      ->leftjoin('section_group_maps as c', 'c.id', '=', 'a.section_id')
      ->where('a.class_id', $req->classId)
      ->orWhere('a.section_id', $req->id)
      ->where('a.status', 1)
      // ->where('a.school_id', $schoolId)
      // ->where('a.created_by', $createdBy)
      ->get();
  }

  public static function csv($data)
  {

    // $value = DB::table('users')->where('username', $data['username'])->get();
    // if ($value->count() == 0) {
    DB::table('students')->insert($data);
    // }
  }


  //Search student by using admission no
  public function searchAdmNo($req)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    $checkExist = Student::where([['admission_no', '=', $req->admissionNo], ['status', '=', '1']])
      // ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->count();
    $data = array();
    if ($checkExist > 0) {
      $data =  ['admission_no' => $req->admissionNo, 'message' => 'Admission No. already existing', 'value' => 'true'];
    }
    if ($checkExist == 0) {
      $data = ['admission_no' => $req->admissionNo, 'message' => 'Admission No. not found', 'value' => 'false'];
    }
    return $data;
  }

  public function getStudentIdDetails($req, $id)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return DB::table('students as a')
      ->select(
        DB::raw("b.class_name, c.section_name,
        CONCAT(a.first_name,' ',a.middle_name,' ',a.last_name) as full_name ,a.admission_no,a.roll_no,
        TO_CHAR(a.dob::date, 'DD-MM-YYYY') as dob,a.blood_group_name,a.email, a.p_address1,a.mobile,a.academic_year,
          CASE WHEN a.status = '0' THEN 'Deactivated'  
          WHEN a.status = '1' THEN 'Active'
          END as status,
          TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
          TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
          ")
      )
      ->join('class_masters as b', 'b.id', '=', 'a.class_id')
      ->leftjoin('section_group_maps as c', 'c.id', '=', 'a.section_id')
      // ->join('sections as c', 'c.id', '=', 'a.section_id')
      ->where('a.class_id', $req->classId)
      // ->where('a.section_id', $req->sectionId)
      ->where('a.id', $id)
      ->where('a.status', 1)
      // ->where('a.school_id', $schoolId)
      // ->where('a.created_by', $createdBy)
      ->orderBy('a.id')
      ->get();
  }

  /*Read Records by ID*/
  public function readRoleExist($req)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return Student::where('id', $req->id)
      ->where('role_id', $req->roleId)
      // ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->where('status', 1)
      ->first();
  }

  /** ----------------Reporting Method----------------------------------------------------------- */

  /*Read all Records by*/
  public function getAllStudent($req, $schoolId)
  {
    //$schoolId = authUser()->school_id;
    return DB::table('students as a')
      ->select(
        DB::raw("b.class_name, c.section_name,
          CONCAT(a.first_name,' ',a.middle_name,' ',a.last_name) as full_name ,a.admission_no,a.roll_no,
          TO_CHAR(a.dob::date, 'DD-MM-YYYY') as dob,a.blood_group_name,a.email, a.p_address1,a.mobile,a.academic_year,
          e.sub_total,e.payment_date,
          CASE WHEN e.is_paid = '0' THEN 'Not Paid'  
          WHEN e.is_paid = '1' THEN 'Paid'
          END as payment_status,
          CASE WHEN a.status = '0' THEN 'Deactivated'  
          WHEN a.status = '1' THEN 'Active'
          END as status,
          TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
          TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
          ")
      )
      ->join('class_masters as b', 'b.id', '=', 'a.class_id')
      ->leftjoin('section_group_maps as c', 'c.id', '=', 'a.section_id')
      // ->join('fee_collections as d', 'd.student_id', '=', 'a.id')
      ->join('payments as e', 'e.student_id', '=', 'a.id')
      ->where('a.status', 1)
      // ->where('a.school_id', $schoolId)
      ->where('a.academic_year', $req->financialYear)
      ->where('e.is_paid', $req->paymentStatus)
      ->orderBy('a.class_id')
      ->get();
  }




  /*Count all Active Records*/
  public function countActive()
  {
    return Student::where('status', 1)->count();
  }
























  //insert students all details
  // public function insertData($req) { 
  //     $ip = getClientIpAddress();
  //     $admissionNo = $req->admissionNo; //getting admissionNo from request for existing student
  //     $user_created_by = 'Admin'; //user id or role from users table
  //     $school_id = '123'; //need to use idGenerator
  //     // $userId = authUser()->id; 
  //     $file_name = '';
  //     $userType = 'Student'; //to identify users type like:std,emp,etc from users table

  //     $mObject = new Student(); 

  //     $baseUrl = baseURL(); //called it from helper file - getting server ip
  //     $localBaseURL = "http://127.0.0.1:8000"; //using for localhost / local ip
  //     $uploadImage = "";
  //     $result = array();
  //     $academicYear = '2023-2024';

  //     $stdSibling = $stdTransport = "";
  //     // $baseUrl1 = config('app.url')
  //     // $path = $baseUrl.'/school/employees/'.$emp_no;

  //     if($req->uploadImage!=""){
  //       $uploadImage = $req->uploadImage;
  //       $file_name = $uploadImage->getClientOriginalName();
  //       $path = public_path('school/students/'.$admissionNo);
  //       // $path = $baseUrl.'/school/employees/'.$emp_no;
  //       $move = $req->file('uploadImage')->move($path,$file_name);         
  //     } 
  //     $mObject->admission_no = $admissionNo;
  //     $mObject->roll_no = $req['rollNo'];
  //     $mObject->first_name = $req['firstName'];
  //     $mObject->middle_name = $req['middleName'];
  //     $mObject->last_name = $req['lastName'];        
  //     $mObject->class_id = $req['classId'];
  //     $mObject->class_name = $req['className'];
  //     $mObject->section_id = $req['sectionId'];
  //     $mObject->section_name = $req['sectionName'];
  //     $mObject->dob = $req['Dob'];
  //     $mObject->admission_date = $req['admissionDate'];
  //     $mObject->gender_id = $req['gender_id'];
  //     $mObject->gender_name = $req['gender_name'];
  //     $mObject->blood_group_id = $req['blood_group_id'];
  //     $mObject->blood_group_name = $req['blood_group_name'];
  //     $mObject->email = $req['Email'];
  //     $mObject->mobile = $req['Mobile'];
  //     $mObject->aadhar_no = $req['aadharNo'];
  //     $mObject->disability = $req['disability'];
  //     $mObject->category_id = $req['category_id'];
  //     $mObject->category_name = $req['category_name'];
  //     $mObject->caste_id = $req['caste_id'];
  //     $mObject->caste_name = $req['caste_name'];
  //     $mObject->religion_id = $req['religion_id'];
  //     $mObject->religion_name = $req['religion_name'];
  //     $mObject->house_ward_id = $req['house_ward_id'];
  //     $mObject->house_ward_name = $req['house_ward_name'];
  //     $mObject->upload_image = $file_name;
  //     $mObject->last_school_name = $req['lastSchoolName'];
  //     $mObject->last_school_address = $req['lastSchoolAddress'];
  //     $mObject->admission_mid_session = $req['admissionMidSession'];
  //     $mObject->admission_month = $req['admissionMonth'];
  //     $mObject->fathers_name = $req['fathersName'];
  //     $mObject->fathers_mob_no = $req['fathersMobNo'];
  //     $mObject->fathers_qualification_id = $req['fathers_qualification_id'];
  //     $mObject->fathers_qualification_name = $req['fathers_qualification_name'];
  //     $mObject->fathers_occupation_id = $req['fathers_occupation_id'];
  //     $mObject->fathers_occupation_name = $req['fathers_occupation_name'];
  //     $mObject->fathers_email = $req['fathersEmail'];
  //     $mObject->fathers_aadhar = $req['fathersAadhar'];
  //     $mObject->fathers_image = "";
  //     $mObject->fathers_annual_income = $req['fathersAnnualIncome'];
  //     $mObject->mothers_name = $req['mothersName'];
  //     $mObject->mothers_mob_no = $req['mothersMobNo'];
  //     $mObject->mothers_qualification_id = $req['mothers_qualification_id'];
  //     $mObject->mothers_qualification_name = $req['mothers_qualification_name'];
  //     $mObject->mothers_occupation_id = $req['mothers_occupation_id'];
  //     $mObject->mothers_occupation_name = $req['mothers_occupation_name'];
  //     $mObject->mothers_email = $req['mothersEmail'];
  //     $mObject->mothers_aadhar = $req['mothersAadhar'];
  //     $mObject->mothers_image = "";
  //     $mObject->mothers_annual_income = $req['mothersAnnualIncome'];
  //     $mObject->guardian_name = $req['guardianName'];
  //     $mObject->guardian_mob_no = $req['guardianMobNo'];
  //     $mObject->guardian_qualification_id = $req['guardian_qualification_id'];
  //     $mObject->guardian_qualification_name = $req['guardian_qualification_name'];
  //     $mObject->guardian_occupation_id = $req['guardian_occupation_id'];
  //     $mObject->guardian_occupation_name = $req['guardian_occupation_name'];
  //     $mObject->guardian_email = $req['guardianEmail'];
  //     $mObject->guardian_aadhar = $req['guardianAadhar'];
  //     $mObject->guardian_image = "";
  //     $mObject->guardian_annual_income = $req['guardianAnnualIncome'];
  //     $mObject->guardian_relation_id = $req['guardian_relation_id'];
  //     $mObject->guardian_relation_name = $req['guardian_relation_name'];
  //     $mObject->p_address1 = $req['pAddress1'];
  //     $mObject->p_address2 = $req['pAddress2'];
  //     $mObject->p_locality = $req['pLocality'];
  //     $mObject->p_landmark = $req['pLandmark'];
  //     $mObject->p_country_id = $req['p_country_id'];
  //     $mObject->p_country_name = $req['p_country_name'];
  //     $mObject->p_state_id = $req['p_state_id'];
  //     $mObject->p_state_name = $req['p_state_name'];
  //     $mObject->p_district_id = $req['p_district_id'];
  //     $mObject->p_district_name = $req['p_district_name'];
  //     $mObject->p_pincode = $req['pPincode'];
  //     $mObject->c_address1 = $req['cAddress1'];
  //     $mObject->c_address2 = $req['cAddress2'];
  //     $mObject->c_locality = $req['cLocality'];
  //     $mObject->c_landmark = $req['cLandmark'];
  //     $mObject->c_country_id = $req['c_country_id'];
  //     $mObject->c_country_name = $req['c_country_name'];
  //     $mObject->c_state_id = $req['c_state_id'];
  //     $mObject->c_state_name = $req['c_state_name'];
  //     $mObject->c_district_id = $req['c_district_id'];
  //     $mObject->c_district_name = $req['c_district_name'];
  //     $mObject->c_pincode = $req['cPincode'];
  //     $mObject->hobbies = $req['Hobbies'];
  //     $mObject->bank_id = $req['bank_id'];
  //     $mObject->bank_name = $req['bank_name'];
  //     $mObject->account_no = $req['accountNo'];
  //     $mObject->ifsc_code = $req['ifscCode'];
  //     $mObject->branch_name = $req['branchName'];
  //     $mObject->is_transport = $req['isTransport'];
  //     $mObject->created_by = $user_created_by;
  //     $mObject->ip_address = $ip;
  //     $mObject->school_id = $school_id;
  //     $mObject->academic_year = $academicYear;
  //     // print_r($mObject); die;
  //     $mObject->save(); 

  //     //add user
  //     $pass = Str::random(10);                
  //     $mObjectU = new User();
  //     $insert = [
  //       $mObjectU->name        = $req['firstName'].' '.$req['middleName'].' '.$req['lastName'],
  //       $mObjectU->email       = $req['Email'],          
  //       $mObjectU->password    = Hash::make($pass),
  //       $mObjectU->c_password  = $pass,
  //       $mObjectU->school_id   = $school_id,
  //       $mObjectU->user_id     = $admissionNo,
  //       $mObjectU->user_type   = $userType,
  //       $mObjectU->ip_address  = $ip
  //     ];
  //     // print_r($insert);die;
  //     $mObjectU->save($insert);
  //     // $userData = array();
  //     // $userData = $mObjectU->$pass; 

  //     //insert single data and multi data for student transport
  //     if($req['transport_details']!=""){
  //       // foreach ($req['transport_details'] as $ob) {
  //           $stdTransport = new StudentTransport;
  //           $stdTransport->std_tbl_id = $mObject->id;
  //           $stdTransport->roll_no = $req['rollNo'];
  //           $stdTransport->full_name = $req['firstName'].' '.$req['middleName'].' '.$req['lastName'];
  //           $stdTransport->email = $req['Email'];
  //           $stdTransport->mobile = $req['Mobile'];
  //           $stdTransport->route_id = $req['routeId'];
  //           $stdTransport->route_name = $req['routeName'];
  //           $stdTransport->pick_up_point_name = $req['pickUpPointName'];
  //           $stdTransport->bus_no = $req['busNo'];
  //           $stdTransport->applicable_from = $req['applicableFrom'];
  //           $stdTransport->created_by = $user_created_by;
  //           $stdTransport->school_id = $school_id;  
  //           $stdTransport->academic_year = $academicYear;  
  //           $stdTransport->ip_address = $ip;              
  //           $stdTransport->save();          
  //       // }
  //     }       


  //     //insert single data and multi data for student sibling 
  //     if($req['sibling_details']!=""){
  //       foreach ($req['sibling_details'] as $ob) {
  //         $stdSibling = new StudentSibling; 
  //         $stdSibling->std_tbl_id = $mObject->id;
  //         $stdSibling->sibling_name = $ob['siblingName'];
  //         $stdSibling->sibling_class = $ob['siblingClass'];
  //         $stdSibling->sibling_section = $ob['siblingSection'];
  //         $stdSibling->sibling_admission_no = $ob['siblingAdmissionNo'];
  //         $stdSibling->sibling_roll_no = $ob['siblingRollNo'];
  //         $stdSibling->created_by = $user_created_by;
  //         $stdSibling->school_id = $school_id;  
  //         $stdSibling->academic_year = $academicYear;  
  //         $stdSibling->ip_address = $ip;              
  //         $stdSibling->save();          
  //       }
  //     } 


  //     $result['basic_details'] = $mObject;
  //     $result['sibling_details'] = $stdSibling;
  //     $result['transport_details'] = $stdTransport;
  //     return $result;
  // }



  // //view all 
  // public static function list() { 
  //   //select all employees data     
  //   $viewAll = Student::select( 
  //   'id','admission_no','first_name','middle_name','last_name','class_id','class_name','section_id','section_name',
  //   DB::raw("CONCAT_WS(first_name,' ',middle_name,' ',last_name) as full_name,
  //   (CASE 
  //   WHEN status = '0' THEN 'Active' 
  //   WHEN status = '1' THEN 'Not Active'
  //   END) AS status,
  //   TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
  //   TO_CHAR(created_at,'HH12:MI:SS AM') as time"),
  //   'email','mobile','dob','aadhar_no','disability',
  //   'gender_id','gender_name','category_id','category_name',            
  //   'blood_group_id','blood_group_name','upload_image',
  //   'p_address1','p_address2','p_locality','p_landmark','p_country_id','p_country_name',
  //   'p_state_id','p_state_name','p_district_id','p_district_name','p_pincode',            
  //   'c_address1','c_address2','c_locality','c_landmark','c_country_id','c_country_name',
  //   'c_state_id','c_state_name','c_district_id','c_district_name','c_pincode',
  //   'fathers_name','fathers_qualification_id','fathers_qualification_name',
  //   'fathers_occupation_id','fathers_occupation_name','fathers_annual_income',
  //   'mothers_name','mothers_qualification_id','mothers_qualification_name',
  //   'mothers_occupation_id','mothers_occupation_name','mothers_annual_income',
  //   'bank_id','bank_name','account_no','ifsc_code','branch_name','school_id'
  //   )
  //   ->where('status',0)
  //   ->orderByDesc('id')
  //   ->get();
  //   // print_r($viewAll); die; 

  //   $baseUrl = baseURL(); //called it from helper file
  //   $getAllData = array();
  //   foreach($viewAll as $v){
  //     $path = '';
  //     $admission_no = $v->admission_no;
  //     $fileUrl = $baseUrl.'/school/employees/'.$admission_no.'/' ;
  //     $filePath = $fileUrl.$v->upload_image;
  //     $defaultPath = $baseUrl.'/global-img/default-user-img.png';
  //     if($v->upload_image==""){ $path =  $defaultPath; }
  //     if($v->upload_image!=""){ $path =  $filePath; }
  //     $dataArr = array();
  //     $dataArr['id'] = $v->id;
  //     $dataArr['admissionNno'] = $admission_no;
  //     // $dataArr['full_name'] = $v->full_name;
  //     $dataArr['firstName'] = $v->first_name;
  //     $dataArr['middleName'] = $v->middle_name;
  //     $dataArr['lastName'] = $v->last_name;
  //     $dataArr['fullName'] = $v['first_name'].' '.$v['middle_name'].' '.$v['last_name'];
  //     $dataArr['genderName'] = $v->gender_name;
  //     $dataArr['categoryName'] = $v->category_name;
  //     $dataArr['Dob'] = $v->dob;
  //     $dataArr['Mobile'] = $v->mobile;
  //     $dataArr['classId'] = $v->class_id;
  //     $dataArr['className'] = $v->class_name;
  //     $dataArr['sectionId'] = $v->section_id;
  //     $dataArr['sectionName'] = $v->section_name;
  //     $dataArr['Email'] = $v->email;
  //     $dataArr['bloodGroupName'] = $v->blood_group_name;
  //     $dataArr['uploadImages'] = $path;
  //     $dataArr['pAddress1'] = $v->p_address1;
  //     $dataArr['pAddress2'] = $v->p_address2;
  //     $dataArr['pLocality'] = $v->p_locality;
  //     $dataArr['pLandmark'] = $v->p_landmark;
  //     $dataArr['pCountryName'] = $v->p_country_name;
  //     $dataArr['pStateName'] = $v->p_state_name;
  //     $dataArr['pDistrictName'] = $v->p_district_name;
  //     $dataArr['pPincode'] = $v->p_pincode;
  //     $dataArr['cAddress1'] = $v->c_address1;
  //     $dataArr['cAddress2'] = $v->c_address2;
  //     $dataArr['cLocality'] = $v->c_locality;
  //     $dataArr['cLandmark'] = $v->c_landmark;
  //     $dataArr['cCountryName'] = $v->c_country_name;
  //     $dataArr['cStateName'] = $v->c_state_name;
  //     $dataArr['cDistrictName'] = $v->c_district_name;
  //     $dataArr['cPincode'] = $v->c_pincode;
  //     $dataArr['fathersName'] = $v->fathers_name;
  //     $dataArr['fathersQualificationName'] = $v->fathers_qualification_name;
  //     $dataArr['fathersOccupationName'] = $v->fathers_occupation_name;
  //     $dataArr['fathersAnnualIncome'] = $v->fathers_annual_income;
  //     $dataArr['mothersName'] = $v->mothers_name;
  //     $dataArr['mothersOccupationName'] = $v->mothers_occupation_name;
  //     $dataArr['mothersAnnualIncome'] = $v->mothers_annual_income;
  //     $dataArr['bankName'] = $v->bank_name;
  //     $dataArr['accountNo'] = $v->account_no;
  //     $dataArr['ifscCode'] = $v->ifsc_code;
  //     $dataArr['branchName'] = $v->branch_name;
  //     $dataArr['status'] = $v->status;
  //     $dataArr['date'] = $v->date;
  //     $dataArr['time'] = $v->time;
  //     $getAllData[]=$dataArr;
  //   } 
  //   // print_r($getAllData); die;     
  //   return $getAllData;
  // }

}
