<?php

namespace App\Models\Student;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSibling extends Model
{
    use HasFactory;
    protected $guarded = [];

    // //insert
    // public function insertData($req)
    // {
    //     // $userId = authUser()->id; 
    //     $mObject = new StudentSibling();
    //     $insert = [
    //         $mObject->emp_tbl_id = $req['emp_tbl_id'],
    //         $mObject->exam_passed_id = $req['exam_passed_id'] ?? null,
    //         $mObject->exam_passed_name = $req['exam_passed_name'],
    //         $mObject->board_uni_inst = $req['board_uni_inst'],
    //         $mObject->passing_year = $req['passing_year'],
    //         $mObject->div_grade_id = $req['div_grade_id'] ?? null,
    //         $mObject->div_grade_name = $req['div_grade_name'],
    //         $mObject->marks_obtained = $req['marks_obtained'],
    //         $mObject->total_marks = $req['total_marks'],
    //         $mObject->percentage = $req['percentage'],
    //         $mObject->upload_edu_doc = $req['upload_edu_doc'],
    //         $mObject->academic_year = $req['academic_year'],
    //         $mObject->school_id = $req['school_id'],
    //         $mObject->created_by = $req['created_by'],
    //         $mObject->ip_address = $req['ip_address']
    //     ];
    //     $mObject->save($insert);
    //     return $mObject;
    // }
}
