<?php

namespace App\Models\Employee;

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
use App\Models\Employee\EmployeeEducation;
use App\Models\Employee\EmployeeExperience;
use App\Models\Employee\EmployeeFamily;
use App\Models\Admin\User;

use function PHPUnit\Framework\isEmpty;

/*
Created By : Lakshmi kumari 
Created On : 20-Apr-2023 
Code Status : Open 
*/

class Employee extends Model
{
    use HasFactory;
    protected $guarded = [];

    /*Add Records*/
    public function store(array $req)
    {
        Employee::create($req);
    }

    /*Read Records by name*/
    public function readEmployeeGroup($req)
    {
        $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        return Employee::where('emp_no', $req->empNo)
            ->where('status', 1)
            // ->where('school_id', $schoolId)
            // ->where('created_by', $createdBy)
            ->get();

        // if ($req->empNo && $req->aadharNo) {
        //     return Employee::where('emp_no', $req->empNo)
        //         ->where('aadhar_no', $req->aadharNo)
        //         ->where('status', 1)
        //         ->get();
        // }
    }

    //check aadhar no 
    public function readEmployeeAadharGroup($req)
    {
        $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        return Employee::where('aadhar_no', $req->aadharNo)
            ->where('status', 1)
            // ->where('school_id', $schoolId)
            // ->where('created_by', $createdBy)
            ->get();
    }

    //Search emp by using emp id
    public function searchEmpId($req)
    {
        $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        $checkExist = Employee::where([['emp_no', '=', $req->empNo], ['status', '=', '1']])
            ->where('school_id', $schoolId)
            // ->where('created_by', $createdBy)
            ->count();
        $data = array();
        if ($checkExist > 0) {
            $data =  ['emp_no' => $req->empNo, 'message' => 'Employee No. already existing', 'value' => 'true'];
        }
        if ($checkExist == 0) {
            $data = ['emp_no' => $req->empNo, 'message' => 'Employee No. not found', 'value' => 'false'];
        }
        return $data;
    }

    // public function getDuplicateAadhar($req)
    // {
    //     $checkExist = Employee::where([['aadhar_no', '=', $req->aadharNo], ['status', '=', '1']])->count();
    //     $data = array();
    //     if ($checkExist > 0) {
    //         $data =  ['aadhar_no' => $req->empNo, 'message' => 'Aadhar No. already existing', 'value' => 'true'];
    //     }
    //     // if ($checkExist == 0) {
    //     //     $data = ['aadhar_no' => $req->empNo, 'message' => 'Employee No. not found', 'value' => 'false'];
    //     // }
    //     return $data;
    // }

    private $data = array();
    /*Read Records by ID*/
    public function getGroupById($id)
    {
        // $data = array();
        $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        $empBasicDetails =  DB::table('employees as emp')
            ->select(
                DB::raw("emp.*,            
                dep.department_name,
                etype.emp_type_name,
                tt.teaching_title_name,
                cn.country_name as p_country_name,
                st.state_name as p_state_name,
                ct.city_name as p_district_name,
                cn.country_name as c_country_name,
                st.state_name as c_state_name,
                ct.city_name as c_district_name,
                bnk.bank_name,
                CASE 
                WHEN emp.status = '0' THEN 'Deactivated'  
                WHEN emp.status = '1' THEN 'Active'
                END as status,
                TO_CHAR(emp.created_at::date,'dd-mm-yyyy') as date,
                TO_CHAR(emp.created_at,'HH12:MI:SS AM') as time
                ")
            )
            ->leftjoin('departments as dep', 'dep.id', '=', 'emp.department_id')
            ->leftjoin('employment_types as etype', 'etype.id', '=', 'emp.employee_type_id')
            ->leftjoin('teaching_titles as tt', 'tt.id', '=', 'emp.teaching_title_id')
            ->leftjoin('countries as cn', 'cn.id', '=', 'emp.p_country_id')
            ->leftjoin('states as st', 'st.id', '=', 'emp.p_state_id')
            ->leftjoin('cities as ct', 'ct.id', '=', 'emp.p_district_id')
            ->leftjoin('banks as bnk', 'bnk.id', '=', 'emp.bank_id')
            ->where('emp.id', $id)
            ->where('emp.status', 1)
            // ->where('emp.school_id', $schoolId)
            // ->where('emp.created_by', $createdBy)
            ->first();   

        // $path = baseURL() . '/school/employees/';
        // // $file_name = '';
        // return $file_name = baseURL() . '/global-img/default-user-img.png';
        // if ($empBasicDetails->upload_image == '') {
        //     $file_name = baseURL() . '/global-img/default-user-img.png';
        // }
        // if ($empBasicDetails->upload_image != '') {
        //     $file_name = $path . $empBasicDetails->upload_image;
        // }

        // $filePath = baseURL() . '/school/employees/' . $empBasicDetails->upload_image;
        // $defaultPath = baseURL() . '/global-img/default-user-img.png';
        // $file_name = $path . $empBasicDetails->upload_image;
        // if ($empBasicDetails->upload_image == "") {
        //     $path =  $defaultPath;
        // }
        // if ($empBasicDetails->upload_image != "") {
        //     $path =  $filePath;
        // }
        // $empBasicDetails['upload_image'] = $file_name;

        //code-1
        // $path = baseURL() . '/api/getImageLink?path=school/employees/';
        // return $file_name = $path . $empBasicDetails->upload_image;
        // $empBasicDetails['upload_image'] = $file_name;

        //code-2
        $path = 'api\getImageLink?path=';
        $file_name = $path . $empBasicDetails->upload_image;
        $empBasicDetails->upload_image = $file_name;


        // $empEdu1 =  DB::table('employees as employees')
        //     ->select(
        //         DB::raw("employees.admission_no,            
        //         employee_educations.exam_passed_name,employee_educations.board_uni_inst,
        //         employee_educations.passing_year,employee_educations.div_grade_name,
        //         employee_educations.marks_obtained,employee_educations.total_marks,
        //         employee_educations.percentage,
        //         CASE 
        //         WHEN employees.status = '0' THEN 'Deactivated'  
        //         WHEN employees.status = '1' THEN 'Active'
        //         END as status,
        //         TO_CHAR(employees.created_at::date,'dd-mm-yyyy') as date,
        //         TO_CHAR(employees.created_at,'HH12:MI:SS AM') as time
        //         ")
        //     )
        //     ->join('employee_educations as employee_educations', 'employees.id', '=', 'employee_educations.emp_tbl_id')
        //     ->where(['employee_educations.emp_tbl_id' => $id], ['employee_educations.status' => 0])
        //     ->get();


        $empEdu = EmployeeEducation::select(
            'id',
            'emp_tbl_id',
            'exam_passed_id',
            'exam_passed_name',
            'board_uni_inst',
            'passing_year',
            'div_grade_id',
            'div_grade_name',
            'marks_obtained',
            'total_marks',
            'percentage',
            'upload_edu_doc',
            
            DB::raw("(CASE 
            WHEN status = '0' THEN 'Active' 
            WHEN status = '1' THEN 'Not Active'
            END) AS status,
            TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
            TO_CHAR(created_at,'HH12:MI:SS AM') as time")
        )
            ->where(['emp_tbl_id' => $id], ['status' => 1])
            // ->where('school_id', $schoolId)
            // ->where('created_by', $createdBy)
            ->get();
            // return $empEdu; die;
        if(!$empEdu->isEmpty()){
            foreach ($empEdu as $v) {
                
                $dataArr = array();
                $path = 'api\getImageLink?path=';
                $file_name = $path . $v->upload_edu_doc;
                $dataArr['id'] = $v->id;
                $dataArr['emp_tbl_id'] = $v->emp_tbl_id;
                $dataArr['upload_edu_doc'] = $file_name;
                $dataArr['exam_passed_id'] = $v->exam_passed_id;
                $dataArr['exam_passed_name'] = $v->exam_passed_name;
                $dataArr['board_uni_inst'] = $v->board_uni_inst;
                $dataArr['passing_year'] = $v->passing_year;
                $dataArr['div_grade_id'] = $v->div_grade_id;
                $dataArr['div_grade_name'] = $v->div_grade_name;
                $dataArr['marks_obtained'] = $v->marks_obtained;
                $dataArr['total_marks'] = $v->total_marks;
                $dataArr['percentage'] = $v->percentage;
                $dataArr['status'] = $v->status;
                $dataArr['date'] = $v->date;
                $dataArr['time'] = $v->time;
                $getEdu[] = $dataArr;
                // $data['family_details'] = $dataArr;
            }
        }else{
            $getEdu[] = $empEdu; 
        }

        // $path = 'api\getImageLink?path=';
        // $file_name = $path . $empEdu->upload_edu_doc;
        // $empEdu->upload_edu_doc = $file_name;

        /////get exp
        $empExp = EmployeeExperience::select(
            'id',
            'emp_tbl_id',
            'name_of_org',
            'position_head',
            'period_from',
            'period_to',
            'salary',
            'pay_grade',
            'upload_exp_letter',
            DB::raw("(CASE 
            WHEN status = '0' THEN 'Active' 
            WHEN status = '1' THEN 'Not Active'
            END) AS status,
            TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
            TO_CHAR(created_at,'HH12:MI:SS AM') as time")
        )
            ->where(['emp_tbl_id' => $id], ['status' => 1])
            // ->where('school_id', $schoolId)
            // ->where('created_by', $createdBy)
            ->get();

        if(!$empExp->isEmpty()){
            foreach ($empExp as $v) {
                $dataArr = array();
                $path = 'api\getImageLink?path=';
                $file_name = $path . $v->upload_exp_letter;
                $dataArr['id'] = $v->id;
                $dataArr['emp_tbl_id'] = $v->emp_tbl_id;
                $dataArr['upload_exp_letter'] = $file_name;
                $dataArr['name_of_org'] = $v->name_of_org;
                $dataArr['position_head'] = $v->position_head;
                $dataArr['period_from'] = $v->period_from;
                $dataArr['period_to'] = $v->period_to;
                $dataArr['salary'] = $v->salary;
                $dataArr['pay_grade'] = $v->pay_grade;
                $dataArr['status'] = $v->status;
                $dataArr['date'] = $v->date;
                $dataArr['time'] = $v->time;
                $getExp[] = $dataArr;
                // $data['family_details'] = $dataArr;
            }
        }
        else{
            $getExp[] = $empExp; 
        }

        ////get fam
        $empFamily = EmployeeFamily::select(
            'id',
            'emp_tbl_id',
            'f_member_name',
            'f_member_relation_id',
            'f_member_relation_name',
            'f_member_dob',
            'upload_f_member_image',
            DB::raw("(CASE 
                WHEN status = '0' THEN 'Active' 
                WHEN status = '1' THEN 'Not Active'
                END) AS status,
                TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
                TO_CHAR(created_at,'HH12:MI:SS AM') as time")
        )
            ->where(['emp_tbl_id' => $id], ['status' => 1])
            // ->where('school_id', $schoolId)
            // ->where('created_by', $createdBy)
            // ->where('emp_tbl_id', $req->id)
            // ->where('emp_tbl_id', $id)
            ->get();
// return $empFamily->isEmpty(); die;
            if(!$empFamily->isEmpty()){
     
                foreach ($empFamily as $v) {
                    $dataArr = array();
                    $path = 'api\getImageLink?path=';
                    $file_name = $path . $v->upload_f_member_image;
                    $dataArr['id'] = $v->id;
                    $dataArr['emp_tbl_id'] = $v->emp_tbl_id;
                    $dataArr['upload_f_member_image'] = $file_name;
                    $dataArr['f_member_name'] = $v->f_member_name;
                    $dataArr['f_member_relation_id'] = $v->f_member_relation_id;
                    $dataArr['f_member_relation_name'] = $v->f_member_relation_name;
                    $dataArr['f_member_dob'] = $v->f_member_dob;
                    $dataArr['price'] = $v->price;
                    $dataArr['status'] = $v->status;
                    $dataArr['date'] = $v->date;
                    $dataArr['time'] = $v->time;
                    $getFam[] = $dataArr;
                    // $data['family_details'] = $dataArr;
                }
            }else{
                $getFam[] = $empFamily;
            }
            // return $getFam; die;
            
        $data['basic_details'] = $empBasicDetails;
        $data['education_details'] = $getEdu;
        $data['experience_details'] = $getExp;
        $data['family_details'] = $getFam;
        return $data;


        /*code version 2: getting all records of employee from multiple table*/
        // return DB::table('employees as emp')
        //     ->select(
        //         DB::raw("emp.*,
        //         edu.exam_passed_name,edu.board_uni_inst,edu.passing_year,edu.div_grade_name,edu.marks_obtained,edu.total_marks,
        //         edu.percentage,
        //         exp.name_of_org,exp.position_head,exp.period_from,exp.period_to,exp.salary,exp.pay_grade,
        //         fam.f_member_name,fam.f_member_relation_name,fam.f_member_dob,
        //         dep.department_name,
        //         etype.emp_type_name,
        //         tt.teaching_title_name,
        //         cn.country_name as p_country_name,
        //         st.state_name as p_state_name,
        //         ct.city_name as p_district_name,
        //         cn.country_name as c_country_name,
        //         st.state_name as c_state_name,
        //         ct.city_name as c_district_name,
        //         bnk.bank_name,
        //         CASE 
        //         WHEN emp.status = '0' THEN 'Deactivated'  
        //         WHEN emp.status = '1' THEN 'Active'
        //         END as status,
        //         TO_CHAR(emp.created_at::date,'dd-mm-yyyy') as date,
        //         TO_CHAR(emp.created_at,'HH12:MI:SS AM') as time
        //         ")
        //     )
        //     ->leftjoin('employee_educations as edu', 'emp.id', '=', 'edu.emp_tbl_id')
        //     ->leftjoin('employee_experiences as exp', 'emp.id', '=', 'exp.emp_tbl_id')
        //     ->leftjoin('employee_families as fam', 'emp.id', '=', 'fam.emp_tbl_id')
        //     ->leftjoin('departments as dep', 'dep.id', '=', 'emp.department_id')
        //     ->leftjoin('employment_types as etype', 'etype.id', '=', 'emp.employee_type_id')
        //     ->leftjoin('teaching_titles as tt', 'tt.id', '=', 'emp.teaching_title_id')
        //     ->leftjoin('countries as cn', 'cn.id', '=', 'emp.p_country_id')
        //     ->leftjoin('states as st', 'st.id', '=', 'emp.p_state_id')
        //     ->leftjoin('cities as ct', 'ct.id', '=', 'emp.p_district_id')
        //     ->leftjoin('banks as bnk', 'bnk.id', '=', 'emp.bank_id')
        //     ->where('emp.id', $id)
        //     ->where('emp.status', 1)
        //     ->first();

        /* code version 1: getting all records of employee only  */
        // return Employee::select(
        //     '*',
        //     DB::raw("
        //     CASE 
        //     WHEN status = '0' THEN 'Deactivated'  
        //     WHEN status = '1' THEN 'Active'
        //     END as status,
        //     TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
        //     TO_CHAR(created_at,'HH12:MI:SS AM') as time
        //     ")
        // )
        //     ->where('id', $id)
        //     ->where('status', 1)
        //     ->first();
    }

    /*Read all Records by*/

    public function retrieve()
    {
        $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        return DB::table('employees as emp')
            ->select(
                DB::raw("
                CONCAT(emp.first_name,' ',emp.middle_name,' ',emp.last_name) as full_name,
                emp.*,
                dep.department_name,
                etype.emp_type_name,
                tt.teaching_title_name,              
                CASE 
                WHEN emp.status = '0' THEN 'Deactivated'  
                WHEN emp.status = '1' THEN 'Active'
                END as status,
                TO_CHAR(emp.created_at::date,'dd-mm-yyyy') as date,
                TO_CHAR(emp.created_at,'HH12:MI:SS AM') as time
            ")
            )
            ->leftjoin('departments as dep', 'dep.id', '=', 'emp.department_id')
            ->leftjoin('employment_types as etype', 'etype.id', '=', 'emp.employee_type_id')
            ->leftjoin('teaching_titles as tt', 'tt.id', '=', 'emp.teaching_title_id')
            ->orderByDesc('id');
            // ->where('emp.status', 1)
            // ->where('emp.school_id', $schoolId);
            // ->where('emp.created_by', $createdBy)
            // ->get();
    }

    public function retrieve1()
    {

        return DB::table('employees as emp')
            ->select(
                DB::raw("
                CONCAT(emp.first_name,' ',emp.middle_name,' ',emp.last_name) as full_name,
                emp.*,
                edu.exam_passed_name,edu.board_uni_inst,edu.passing_year,edu.div_grade_name,edu.marks_obtained,edu.total_marks,
                edu.percentage,
                exp.name_of_org,exp.position_head,exp.period_from,exp.period_to,exp.salary,exp.pay_grade,
                fam.f_member_name,fam.f_member_relation_name,fam.f_member_dob,
                dep.department_name,
                etype.emp_type_name,
                tt.teaching_title_name,
                cn.country_name as p_country_name,
                st.state_name as p_state_name,
                ct.city_name as p_district_name,
                cn.country_name as c_country_name,
                st.state_name as c_state_name,
                ct.city_name as c_district_name,
                bnk.bank_name,
                CASE 
                WHEN emp.status = '0' THEN 'Deactivated'  
                WHEN emp.status = '1' THEN 'Active'
                END as status,
                TO_CHAR(emp.created_at::date,'dd-mm-yyyy') as date,
                TO_CHAR(emp.created_at,'HH12:MI:SS AM') as time
            ")
            )
            ->leftjoin('employee_educations as edu', 'emp.id', '=', 'edu.emp_tbl_id')
            ->leftjoin('employee_experiences as exp', 'emp.id', '=', 'exp.emp_tbl_id')
            ->leftjoin('employee_families as fam', 'emp.id', '=', 'fam.emp_tbl_id')
            ->leftjoin('departments as dep', 'dep.id', '=', 'emp.department_id')
            ->leftjoin('employment_types as etype', 'etype.id', '=', 'emp.employee_type_id')
            ->leftjoin('teaching_titles as tt', 'tt.id', '=', 'emp.teaching_title_id')
            ->leftjoin('countries as cn', 'cn.id', '=', 'emp.p_country_id')
            ->leftjoin('states as st', 'st.id', '=', 'emp.p_state_id')
            ->leftjoin('cities as ct', 'ct.id', '=', 'emp.p_district_id')
            ->leftjoin('banks as bnk', 'bnk.id', '=', 'emp.bank_id')
            ->orderByDesc('id')
            ->where('emp.status', 1)
            ->get();

        // $data = array();
        // $empBasicDetails =  DB::table('employees as emp')
        //     ->select(
        //         DB::raw("emp.*,            
        //         dep.department_name,
        //         etype.emp_type_name,
        //         tt.teaching_title_name,
        //         cn.country_name as p_country_name,
        //         st.state_name as p_state_name,
        //         ct.city_name as p_district_name,
        //         cn.country_name as c_country_name,
        //         st.state_name as c_state_name,
        //         ct.city_name as c_district_name,
        //         bnk.bank_name,
        //         CASE 
        //         WHEN emp.status = '0' THEN 'Deactivated'  
        //         WHEN emp.status = '1' THEN 'Active'
        //         END as status,
        //         TO_CHAR(emp.created_at::date,'dd-mm-yyyy') as date,
        //         TO_CHAR(emp.created_at,'HH12:MI:SS AM') as time
        //         ")
        //     )
        //     ->leftjoin('departments as dep', 'dep.id', '=', 'emp.department_id')
        //     ->leftjoin('employment_types as etype', 'etype.id', '=', 'emp.employee_type_id')
        //     ->leftjoin('teaching_titles as tt', 'tt.id', '=', 'emp.teaching_title_id')
        //     ->leftjoin('countries as cn', 'cn.id', '=', 'emp.p_country_id')
        //     ->leftjoin('states as st', 'st.id', '=', 'emp.p_state_id')
        //     ->leftjoin('cities as ct', 'ct.id', '=', 'emp.p_district_id')
        //     ->leftjoin('banks as bnk', 'bnk.id', '=', 'emp.bank_id')
        //     ->where('emp.status', 1)
        //     ->first();
        // $path = 'api\getImageLink?path=';
        // $file_name = $path . $empBasicDetails->upload_image;
        // $empBasicDetails->upload_image = $file_name;

        // $data['basic_details'] = $empBasicDetails;
        // return $data;

        // return Employee::select(
        //     '*',
        //     DB::raw("
        //     CONCAT(first_name,' ',middle_name,' ',last_name) as full_name,
        //     CASE 
        //         WHEN status = '0' THEN 'Deactivated'  
        //         WHEN status = '1' THEN 'Active'
        //     END as status,
        //     TO_CHAR(dob::date,'dd-mm-yyyy') as dob,
        //     TO_CHAR(doj::date,'dd-mm-yyyy') as doj,
        //     TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
        //     TO_CHAR(created_at,'HH12:MI:SS AM') as time
        //     ")
        // )
        //     ->orderByDesc('id')
        //     ->where('status', 1)
        //     ->get();
    }

    public static function csv($data)
    {
        DB::table('employees')->insert($data);
    }


    /*Read Records by ID*/
    public function readRoleExist($req)
    {
        $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        return Employee::where('id', $req->id)
            ->where('role_id', $req->roleId)
            ->where('school_id', $schoolId)
            // ->where('created_by', $createdBy)
            ->where('status', 1)
            ->first();
    }

    /*Count all Active Records*/
    public function countActive()
    {
        return Employee::where('status',1)->count();
    }
}
