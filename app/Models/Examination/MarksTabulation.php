<?php

namespace App\Models\Examination;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarksTabulation extends Model
{
  use HasFactory;

  protected $guarded = [];


  /*Add Records*/
  public function store(array $req)
  {
    MarksTabulation::create($req);
  }

  public function readMarksTabulationGroup($ob, $studentId)
  {
    $schoolId = authUser()->school_id;
    return MarksTabulation::where('term_id', $ob['termId'])
      ->where('student_id', $studentId)
      ->where('class_id', $ob['classId'])
      // ->where('section_id', $ob['sectionId'])
      ->where('marks_entry_id', $ob['marksEntryId'])
      ->where('status', 1)
      // ->where('school_id', $schoolId)
      ->get();

    // return MarksTabulation::where('term_id', $req->termId)
    //   ->where('student_id', $studentId)
    //   ->where('class_id', $req->classId)
    //   ->where('section_id', $req->sectionId)
    //   // ->where('marks_entry_id',  $req->marksEntryId)
    //   ->where('status', 1)
    //   ->get();
  }

  /*Read Records by ID*/
  public function getGroupById($id)
  {
    $schoolId = authUser()->school_id;
    return DB::table('marks_tabulations as a')
      ->select(
        DB::raw("a.fy_name,                 
        c.admission_no, b.class_name, d.section_name,        
        e.term_name, 
        g.subject_name,        
        f.full_marks, f.pass_marks,  
        a.obtained_marks,       
        CASE WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        ")
      )
      ->join('marks_entries as f', 'f.id', '=', 'a.marks_entry_id')
      ->join('subjects as g', 'g.id', '=', 'f.subject_id')
      ->join('class_masters as b', 'b.id', '=', 'f.class_id')
      ->join('students as c', 'c.id', '=', 'a.student_id')
      ->leftjoin('section_group_maps as d', 'd.id', '=', 'a.section_id')
      ->join('exam_terms as e', 'e.id', '=', 'a.term_id')
      ->orderBy('a.id')
      ->where('a.id', $id)
      ->where('a.status', 1)
      // ->where('a.school_id', $schoolId)
      ->get();
    // return DB::table('marks_tabulations as a')
    //   ->select(
    //     DB::raw("b.class_name, c.admission_no, d.section_name,e.term_name, g.subject_name,f.full_marks, f.pass_marks, a.*,
    //     CASE WHEN a.status = '0' THEN 'Deactivated'  
    //     WHEN a.status = '1' THEN 'Active'
    //     END as status,
    //     TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
    //     TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
    //     ")
    //   )
    //   ->join('class_masters as b', 'b.id', '=', 'a.class_id')
    //   ->join('students as c', 'c.id', '=', 'a.student_id')
    //   // ->join('sections as d', 'd.id', '=', 'a.section_id')
    //   ->join('section_group_maps as d', 'd.id', '=', 'a.section_id')
    //   ->join('exam_terms as e', 'e.id', '=', 'a.term_id')
    //   ->join('marks_entries as f', 'f.id', '=', 'a.marks_entry_id')
    //   ->join('subjects as g', 'g.id', '=', 'f.subject_id')
    //   ->orderBy('a.id')
    //   ->where('a.status', 1)
    //   ->first();
  }

  /*Read all Records by*/
  public function retrieve()
  {
    $schoolId = authUser()->school_id;
    return DB::table('marks_tabulations as a')
      ->select(
        DB::raw("a.fy_name,                 
        c.admission_no, b.class_name, d.section_name,        
        e.term_name, 
        g.subject_name,        
        f.full_marks, f.pass_marks,  
        a.obtained_marks,       
        CASE WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        ")
      )
      ->join('marks_entries as f', 'f.id', '=', 'a.marks_entry_id')
      // ->join('subjects as g', 'g.id', '=', 'f.subject_id')
      ->join('subject_group_maps as g', 'g.id', '=', 'f.subject_id')
      ->join('class_masters as b', 'b.id', '=', 'f.class_id')
      ->join('students as c', 'c.id', '=', 'a.student_id')
      ->leftjoin('section_group_maps as d', 'd.id', '=', 'a.section_id')
      ->join('exam_terms as e', 'e.id', '=', 'a.term_id')
      // ->where('a.school_id', $schoolId)
      ->orderBy('a.id');
    // ->where('a.status', 1)
    // ->get();
  }



  /*Read all Active Records*/
  public function active()
  {
    $schoolId = authUser()->school_id;
    return DB::table('marks_tabulations as a')
      ->select(
        DB::raw("a.fy_name,                 
        c.admission_no, b.class_name, d.section_name,        
        e.term_name, 
        g.subject_name,        
        f.full_marks, f.pass_marks,  
        a.obtained_marks,       
        CASE WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        ")
      )
      ->join('marks_entries as f', 'f.id', '=', 'a.marks_entry_id')
      ->join('subjects as g', 'g.id', '=', 'f.subject_id')
      ->join('class_masters as b', 'b.id', '=', 'f.class_id')
      ->join('students as c', 'c.id', '=', 'a.student_id')
      ->leftjoin('section_group_maps as d', 'd.id', '=', 'a.section_id')
      ->join('exam_terms as e', 'e.id', '=', 'a.term_id')
      ->where('a.status', 1)
      // ->where('a.school_id', $schoolId)
      ->orderBy('a.id')
      ->get();
  }
}
