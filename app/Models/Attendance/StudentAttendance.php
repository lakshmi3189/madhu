<?php

namespace App\Models\Attendance;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
  use HasFactory;
  protected $guarded = [];


  /*Add Records*/
  public function store(array $req)
  {
    StudentAttendance::create($req);
  }
  /*Read Records by name*/
  public function readStudentAttendanceGroup($req)
  {
    $schoolId = authUser()->school_id;
    return StudentAttendance::where('class_id', $req->classId)
      ->orWhere('section_id', $req->sectionId)
      // ->where('student_id', $req->studentId)
      ->where('attendance_date', $req->attendanceDate)
      ->where('status', 1)
      // ->where('school_id', $schoolId)
      ->get();
  }

  /*Read Records by ID*/
  public function getGroupById($id)
  {
    $schoolId = authUser()->school_id;
    return DB::table('student_attendances as a')
      ->select(
        DB::raw("b.class_name, d.section_name, CONCAT(c.first_name,' ',c.middle_name,' ',c.last_name) as full_name ,c.admission_no,c.roll_no, a.*,
        CASE WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        ")
      )
      ->join('class_masters as b', 'b.id', '=', 'a.class_id')
      ->join('students as c', 'c.id', '=', 'a.student_id')
      ->leftjoin('sections as d', 'd.id', '=', 'a.section_id')
      ->where('a.id', $id)
      // ->where('a.school_id', $schoolId)
      ->first();
  }

  /*Read all Records by*/
  public function retrieveAll()
  {
    $schoolId = authUser()->school_id;
    return DB::table('student_attendances as a')
      ->select(
        DB::raw("b.class_name, CONCAT(c.first_name,' ',c.middle_name,' ',c.last_name) as full_name ,c.admission_no,c.roll_no, a.*,
        CASE WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        ")
      )
      ->join('class_masters as b', 'b.id', '=', 'a.class_id')
      ->join('students as c', 'c.id', '=', 'a.student_id')
      // ->leftJoin('sections as d', 'd.id', '=', 'a.section_id')
      ->orderBy('a.id')
      // ->where('a.school_id', $schoolId)
      // ->where('status', 1)
      ->get();
  }

  //Get Records by name
  public function searchByName($req)
  {
    $schoolId = authUser()->school_id;
    return DB::table('marks_entries as a')
      ->select(
        DB::raw("b.class_name, c.subject_name, d.section_name, a.*,
        CASE WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        ")
      )
      ->join('class_masters as b', 'b.id', '=', 'a.class_id')
      ->join('subjects as c', 'c.id', '=', 'a.subject_id')
      ->leftjoin('sections as d', 'd.id', '=', 'a.section_id')
      // ->where('b.class_name', 'like', $req->className.'%')
      // ->where('c.subject_name', 'like', $req->subjectName.'%')
      // ->where('a.school_id', $schoolId)
      ->where('b.class_name', $req->className)
      ->where(function ($query) use ($req) {
        $query->where(function ($subQuery) use ($req) {
          $subQuery->whereRaw('LOWER(c.subject_name) LIKE ?', [strtolower($req->subjectName) . '%'])
            ->orWhereRaw('UPPER(c.subject_name) LIKE ?', [strtoupper($req->subjectName) . '%']);
        });
      })
      // ->where('status', 1)
      ->get();
  }

  /*Read all Active Records*/
  public function active()
  {
    $schoolId = authUser()->school_id;
    return DB::table('student_attendances as a')
      ->select(
        DB::raw("b.class_name, d.section_name, CONCAT(c.first_name,' ',c.middle_name,' ',c.last_name) as full_name ,c.admission_no,c.roll_no, a.*,
        CASE WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        ")
      )
      ->join('class_masters as b', 'b.id', '=', 'a.class_id')
      ->join('students as c', 'c.id', '=', 'a.student_id')
      ->leftjoin('sections as d', 'd.id', '=', 'a.section_id')
      ->where('a.status', 1)
      // ->where('a.school_id', $schoolId)
      ->orderBy('a.id')
      ->get();
  }
}
