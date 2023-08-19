<?php

namespace App\Models\Master;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectGroupMap extends Model
{
  use HasFactory;
  protected $guarded = [];

  /*Add Records*/
  public function store(array $req)
  {
    SubjectGroupMap::create($req);
  }

  /*Read Records by name*/
  public function readSubjectGroupMap($ob)
  {
    $schoolId = authUser()->school_id;
    return SubjectGroupMap::where('class_id', $ob['classId'])
      ->where('subject_name', $ob['subjectName'])
      // ->orWhere('section_group_map_id', $ob['sectionId'])
      // ->where('school_id', $schoolId)
      ->where('status', 1)
      ->get();
  }


  /*Read Records by name*/
  public function readSubjectGroupMap1($req)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return SubjectGroupMap::where('class_id', $req->classId)
      // ->orWhere('section_group_map_id', $req->sectionData)
      ->where(DB::raw('upper(subject_name)'), strtoupper($req->subject))
      ->where('status', 1)
      // ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->get();
  }

  public function readSectionNotInGroupMap($req)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return SectionGroupMap::where('class_id', $req->classId)
      ->where('id', $req->sectionData)
      ->where('status', 1)
      // ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->count();
  }

  /*Read Records by ID*/
  public function getGroupById($id)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return DB::table('subject_group_maps as a')
      ->select(
        DB::raw("b.id as clsid,b.class_name,
        a.id,a.subject_name,a.class_id,a.section_group_map_id,
        CASE WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        ")
      )
      ->join('class_masters as b', 'b.id', '=', 'a.class_id')
      ->where('a.id', $id)
      // ->where('a.school_id', $schoolId)
      // ->where('a.created_by', $createdBy)
      ->first();
  }

  /*Read all Records by*/
  public function retrieve()
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return DB::table('subject_group_maps as a')
      ->select(
        DB::raw("b.id as clsid,b.class_name,
        a.id,a.subject_name,a.class_id,a.section_group_map_id,
        c.section_name,c.id as sgmid,
        CASE WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        ")
      )
      ->join('class_masters as b', 'b.id', '=', 'a.class_id')
      ->leftjoin('section_group_maps as c', 'c.id', '=', 'a.section_group_map_id')
      // ->where('a.school_id', $schoolId)
      // ->where('a.created_by', $createdBy)
      ->orderByDesc('a.id');
    // ->where('status', 1)
    // ->get();
  }

  //Get Records by name
  public function searchByName($req)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return DB::table('subject_group_maps as a')
      ->join('class_masters as b', 'b.id', '=', 'a.class_id')
      ->leftjoin('section_group_maps as c', 'c.id', '=', 'a.section_group_map_id')
      ->where("a.subject_name", "Ilike", DB::raw("'%" . $req->search . "%'"))
      ->orWhere("b.class_name", "Ilike", DB::raw("'%" . $req->search . "%'"))
      ->orWhere("c.section_name", "Ilike", DB::raw("'%" . $req->search . "%'"))
      ->select(
        DB::raw("b.id as clsid,b.class_name,
        a.id,a.subject_name,a.class_id,a.section_group_map_id,
        c.section_name,c.id as sgmid,
        CASE WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        ")
      );
    // ->where('a.school_id', $schoolId);
    // ->where('a.created_by', $createdBy);
    // ->get();
  }

  /*Read all Active Records*/
  public function active()
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return DB::table('subject_group_maps as a')
      ->select(
        DB::raw("b.id as clsid,b.class_name,
        a.id,a.subject_name,a.class_id,a.section_group_map_id,
        c.section_name,c.id as sgmid,
        CASE WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        ")
      )
      ->join('class_masters as b', 'b.id', '=', 'a.class_id')
      ->leftjoin('section_group_maps as c', 'c.id', '=', 'a.section_group_map_id')
      // ->where('a.school_id', $schoolId)
      // ->where('a.created_by', $createdBy)
      ->orderBy('a.subject_name')
      ->get();
  }

  public function getClassGroupById($req)
  {
    $finalData = array();
    $selectSection = DB::table('subject_group_maps as a')
      ->select(
        DB::raw("
          Distinct(b.section_name) AS section_name, b.id
        ")
      )
      ->leftjoin('section_group_maps as b', 'b.id', '=', 'a.section_group_map_id')
      ->where('a.class_id', $req->classId)
      ->orderBy('b.section_name')
      ->get();

    // $finalData[] = '';
    foreach ($selectSection as $val1) {
      $finalData["section"][] = [
        "sectionId" => $val1->id,
        "sectionData" => $val1->section_name
      ];
      $finalData = array_merge($finalData, [
        'subject' => $this->getAllSubject($req->classId, $val1->id = null)
      ]);
    }
    return $finalData;
    // print_var($finalData);
    // die;
  }

  public function getAllSubject($classId, $sectionId = null)
  {
    $from = DB::table('subject_group_maps as a')
      ->join('class_masters as b', 'b.id', '=', 'a.class_id')
      ->leftjoin('section_group_maps as c', 'c.id', '=', 'a.section_group_map_id')
      ->where('a.class_id', $classId)
      // ->where('a.section_group_map_id', $sectionId)
      ->orderBy('c.section_name');

    $selectData = $from->select(
      DB::raw(" a.id,a.subject_name,a.class_id,b.class_name,a.section_group_map_id,c.section_name,
      CASE WHEN a.status = '0' THEN 'Deactivated'  
      WHEN a.status = '1' THEN 'Active'
      END as status,
      TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
      ")
    )->get();
    return $selectData;
  }



  public function allSubject()
  {
    return SubjectGroupMap::select('subject_name')->distinct()->orderBy('subject_name')->get();
  }


  // public function getSubject(){

  // }






  /*Read Records by name*/
  public function readSubjectGroupMap2($req)
  {
    return SubjectGroupMap::where('class_id', $req->classId)
      ->orWhere('section_group_map_id', $req->section)
      ->where('subject_name', $req->subject)
      ->where('status', 1)
      ->get();
  }

  // readSectionGroupMap

  // /*Read Records by ID*/
  // public function getGroupById1($id)
  // {
  //   return DB::table('subject_group_maps as a')
  //     ->select(
  //       DB::raw("b.class_name,a.*,
  //         CASE WHEN a.status = '0' THEN 'Deactivated'  
  //         WHEN a.status = '1' THEN 'Active'
  //         END as status,
  //         TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
  //         TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
  //         ")
  //     )
  //     ->join('class_masters as b', 'b.id', '=', 'a.class_id')
  //     ->where('a.id', $id)
  //     ->first();
  // }


  // get class wise subject and sections...
  // public function getClassGroupById1($req)
  // {
  //   return DB::table('subject_group_maps as a')
  //     ->select(
  //       DB::raw("b.class_name, c.section_name, a.*,
  //       CASE WHEN a.status = '0' THEN 'Deactivated'  
  //       WHEN a.status = '1' THEN 'Active'
  //       END as status,
  //       TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
  //       TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
  //       ")
  //     )
  //     ->join('class_masters as b', 'b.id', '=', 'a.class_id')
  //     ->join('section_group_maps as c', 'c.id', '=', 'a.section_group_map_id')
  //     ->where('a.class_id', $req->classId)
  //     ->orderBy('c.section_name')
  //     ->get();
  // }






  // /*Read all Records by*/
  // public function retrieve1()
  // {
  //   return DB::table('subject_group_maps as a')
  //     ->select(
  //       DB::raw("b.class_name,c.section_name,a.*,
  //         CASE WHEN a.status = '0' THEN 'Deactivated'  
  //         WHEN a.status = '1' THEN 'Active'
  //         END as status,
  //         TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
  //         TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
  //         ")
  //     )
  //     ->join('class_masters as b', 'b.id', '=', 'a.class_id')
  //     ->join('section_group_maps as c', 'c.id', '=', 'a.section_group_map_id')

  //     ->orderBy('a.id')
  //     // ->where('status', 1)
  //     ->get();
  // }

  // //Get Records by name
  // public function searchByClassId1($req)
  // {
  //   return DB::table('subject_group_maps as a')
  //     ->select(
  //       DB::raw("b.class_name, a.*,
  //         CASE WHEN a.status = '0' THEN 'Deactivated'  
  //         WHEN a.status = '1' THEN 'Active'
  //         END as status,
  //         TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
  //         TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
  //         ")
  //     )
  //     ->join('class_masters as b', 'b.id', '=', 'a.class_id')
  //     ->where('b.class_name', $req->className)
  //     // ->where('status', 1)
  //     ->get();
  // }

  // /*Read all Active Records*/
  // public function active1()
  // {
  //   return DB::table('subject_group_maps as a')
  //     ->select(
  //       DB::raw("b.class_name,a.*,
  //         CASE WHEN a.status = '0' THEN 'Deactivated'  
  //         WHEN a.status = '1' THEN 'Active'
  //         END as status,
  //         TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
  //         TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
  //         ")
  //     )
  //     ->join('class_masters as b', 'b.id', '=', 'a.class_id')
  //     ->where('a.status', 1)
  //     ->orderBy('a.id')
  //     ->get();
  // }
}
