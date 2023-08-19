<?php

namespace App\Models\Master;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionGroupMap extends Model
{
  use HasFactory;

  protected $guarded = [];

  /*Add Records*/
  public function store(array $req)
  {
    SectionGroupMap::create($req);
  }

  /*Read Records by name*/
  public function readSectionGroupMap($req)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return SectionGroupMap::where('class_id', $req->classId)
      ->where(DB::raw('upper(section_name)'), strtoupper($req->sectionName))
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
      ->where('id', $req->sectionGroupMapId)
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
    return DB::table('section_group_maps as a')
      ->select(
        DB::raw("b.id as clsid,b.class_name,
        a.id,a.section_name,a.class_id,
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
    return DB::table('section_group_maps as a')
      ->select(
        DB::raw("b.class_name,
        a.id,a.section_name,
        CASE WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        ")
      )
      ->join('class_masters as b', 'b.id', '=', 'a.class_id')
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
    return DB::table('section_group_maps as a')
      ->join('class_masters as b', 'b.id', '=', 'a.class_id')
      ->where("a.section_name", "Ilike", DB::raw("'%" . $req->search . "%'"))
      ->orWhere("b.class_name", "Ilike", DB::raw("'%" . $req->search . "%'"))
      ->select(
        DB::raw("b.class_name,
        a.id,a.section_name,
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
    return DB::table('section_group_maps as a')
      ->select(
        DB::raw("b.class_name,
        a.id,a.section_name,
        CASE WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        ")
      )
      ->join('class_masters as b', 'b.id', '=', 'a.class_id')
      ->where('a.status', 1)
      // ->where('a.school_id', $schoolId)
      // ->where('a.created_by', $createdBy)
      ->orderBy('a.section_name')
      ->get();
  }

  /*Read Records by ID*/
  public function getSectionByClassId($req)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return DB::table('section_group_maps as a')
      ->select(
        DB::raw("b.id as clsid,b.class_name,
        a.id,a.section_name,a.class_id,
        CASE WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        ")
      )
      ->join('class_masters as b', 'b.id', '=', 'a.class_id')
      ->where('a.class_id', $req->classId)
      // ->where('a.school_id', $schoolId)
      // ->where('a.created_by', $createdBy)
      ->orderBy('a.section_name')
      ->get();
  }
}
