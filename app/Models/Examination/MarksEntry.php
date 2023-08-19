<?php

namespace App\Models\Examination;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarksEntry extends Model
{
  use HasFactory;
  use HasFactory;

  protected $guarded = [];


  /*Add Records*/
  public function store(array $req)
  {
    MarksEntry::create($req);
  }
  /*Read Records by name*/
  public function readMarksEntryGroup($ob)
  {
    $schoolId = authUser()->school_id;
    return MarksEntry::where('class_id', $ob['classId'])
      ->where('subject_id', $ob['subjectId'])
      // ->where('section_id', $ob['sectionId'])
      // ->where('school_id', $schoolId)
      ->where('status', 1)
      ->get();
  }



  /*Read Records by ID*/
  public function getGroupById($req)
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
      ->where('a.id', $req->id)
      // ->where('a.school_id', $schoolId)
      ->first();
  }

  /*Read all Records by*/
  public function retrieve()
  {
    $schoolId = authUser()->school_id;
    return DB::table('marks_entries as a')
      ->select(
        DB::raw("b.class_name, c.subject_name, d.section_name, a.*,
        CASE WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        CASE WHEN a.is_main_subject = '1' THEN 'Yes'  
        WHEN a.is_main_subject = '0' THEN 'No'
        END as is_main_subject,
        CASE WHEN a.is_optional_subject = '1' THEN 'Yes'  
        WHEN a.is_optional_subject = '0' THEN 'No'
        END as is_optional_subject,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        ")
      )
      ->join('class_masters as b', 'b.id', '=', 'a.class_id')
      ->join('subject_group_maps as c', 'c.id', '=', 'a.subject_id')
      ->leftjoin('section_group_maps as d', 'd.id', '=', 'a.section_id')
      // ->where('a.school_id', $schoolId)
      // ->join('subjects as c', 'c.id', '=', 'a.subject_id')
      // ->join('sections as d', 'd.id', '=', 'a.section_id')
      ->orderBy('a.id');
    // ->where('status', 1)
    // ->get();
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

      ->where('b.class_name', $req->className)
      // ->where('a.school_id', $schoolId)
      ->where(function ($query) use ($req) {
        $query->where(function ($subQuery) use ($req) {
          $subQuery->whereRaw('LOWER(c.subject_name) LIKE ?', [strtolower($req->subjectName) . '%'])
            ->orWhereRaw('UPPER(c.subject_name) LIKE ?', [strtoupper($req->subjectName) . '%']);
        });
      })
      // ->where('status', 1)
      ->get();
  }

  /* Get class and section wise data*/
  public function sectionGroups($req)
  {
    $schoolId = authUser()->school_id;
    return DB::table('marks_entries as a')
      ->select(
        DB::raw("b.class_name, d.section_name, c.subject_name, a.fy_name, a.id as marks_entry_id, a.full_marks,a.pass_marks,        
        CASE WHEN a.is_main_subject = '1' THEN 'Yes'  
        WHEN a.is_main_subject = '0' THEN 'No'
        END as is_main_subject,
        CASE WHEN a.is_optional_subject = '1' THEN 'Yes'  
        WHEN a.is_optional_subject = '0' THEN 'No'
        END as is_optional_subject,
        CASE WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        ")
      )
      ->join('class_masters as b', 'b.id', '=', 'a.class_id')
      ->join('subject_group_maps as c', 'c.id', '=', 'a.subject_id')
      ->leftjoin('section_group_maps as d', 'd.id', '=', 'a.section_id')
      ->where('a.class_id', $req->classId)
      // ->where('a.section_id', $req->sectionId)
      // ->where('a.school_id', $schoolId)
      ->orderBy('a.id')
      // ->where('status', 1)
      ->get();
  }
}
