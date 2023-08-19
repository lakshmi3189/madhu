<?php

namespace App\Models\Employee;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeEducation extends Model
{
  use HasFactory;
  protected $table = 'employee_educations';
  protected $guarded = [];


  /*Add Records*/
  public function store(array $req)
  {
    EmployeeEducation::create($req);
  }

  /*Read Records by name*/
  public function readEducationGroup($empId,$req)
  {
      $schoolId = authUser()->school_id;
      // $createdBy = authUser()->id;
      return EmployeeEducation::where('emp_tbl_id', $empId)
          ->where('exam_passed_name', $req->examPassed)
          // ->where('status', 1)
          // ->where('school_id', $schoolId)
          // ->where('created_by', $createdBy)
          ->get();
  }


  //insert
  public function insertData($req)
  {
    // $userId = authUser()->id; 
    $mObject = new EmployeeEducation();
    $insert = [
      $mObject->emp_tbl_id = $req['emp_tbl_id'],
      $mObject->exam_passed_id = $req['exam_passed_id'] ?? null,
      $mObject->exam_passed_name = $req['exam_passed_name'],
      $mObject->board_uni_inst = $req['board_uni_inst'],
      $mObject->passing_year = $req['passing_year'],
      $mObject->div_grade_id = $req['div_grade_id'] ?? null,
      $mObject->div_grade_name = $req['div_grade_name'],
      $mObject->marks_obtained = $req['marks_obtained'],
      $mObject->total_marks = $req['total_marks'],
      $mObject->percentage = $req['percentage'],
      $mObject->upload_edu_doc = $req['upload_edu_doc'],
      $mObject->academic_year = $req['academic_year'],
      $mObject->school_id = $req['school_id'],
      $mObject->created_by = $req['created_by'],
      $mObject->ip_address = $req['ip_address']
    ];
    $mObject->save($insert);
    return $mObject;
  }

  public function retrieve($req)
  {
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
      WHEN status = '0' THEN 'Deactivated' 
      WHEN status = '1' THEN 'Active'
      END) AS status,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time")
    )
      ->where(['emp_tbl_id' => $req->empId], ['status' => 1])
      // ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->get();
      
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
        }
    }else{
      $getEdu[] = $empEdu; 
    }

    return $getEdu;
  }

  public function getGroupById($id)
  {
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
        WHEN status = '0' THEN 'Deactivated' 
        WHEN status = '1' THEN 'Active'
        END) AS status,
        TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(created_at,'HH12:MI:SS AM') as time")
      )
      ->where(['id' => $id], ['status' => 1])
      // ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->first();
      // return $empEdu; die;
   
      $path = 'api\getImageLink?path=';
      $file_name = $path . $empEdu->upload_edu_doc;
      $empEdu->upload_edu_doc = $file_name;
      return $empEdu;

  }


}
