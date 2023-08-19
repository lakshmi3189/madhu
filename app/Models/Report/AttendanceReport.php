<?php

namespace App\Models\Report;

use App\Models\Student\Student;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceReport extends Model
{
    use HasFactory;

    public function retrieve($req)
    {
        return DB::table('students as a')
        ->select(
        DB::raw("a.admission_no,a.first_name, b.academic_year,a.roll_no, c.class_name, d.section_name, b.attendance_date, 
        CASE 
        WHEN b.attendance_status = '0' THEN 'Absent'
        WHEN b.attendance_status = '1' THEN 'Present'
        WHEN b.attendance_status = '2' THEN 'Leave'
        END as attendance_status, 
        
        CASE 
        WHEN b.description IS NULL THEN 'NA'
        WHEN b.description IS NOT NULL THEN b.description
        END as description, 
    
        CASE 
        WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        "),
      )
      ->join('student_attendances as b','b.student_id','=','a.id')
      ->join('class_masters as c', 'b.class_id' , '=', 'c.id')
      ->leftJoin('section_group_maps as d', 'b.section_id', '=','d.id')
      ->where('b.class_id', $req->classId)
      ->where('b.attendance_date',$req->date)
      ->where('b.academic_year', $req->fy)
      ->where('b.section_id',$req->sectionId)
      ->orderBy('b.id');
    //   ->where('a.status', 1);
      // ->get();
    }
}
