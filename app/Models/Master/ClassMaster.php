<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Support\Collection;
// use Illuminate\Support\Carbon;
// use Illuminate\Support\Str;
use DB;
// use Exception;

class ClassMaster extends Model
{
  use HasFactory;

  protected $guarded = [];

  /*Add Records*/
  public function store(array $req)
  {
    ClassMaster::create($req);
  }

  /*Read Records by name*/
  public function readClassMasterGroup($req)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return ClassMaster::where(DB::raw('upper(class_name)'), strtoupper($req->className))
      ->where('status', 1)
      // ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->get();
  }

  /*Read Records by ID*/
  public function getGroupById($id)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return ClassMaster::select(
      DB::raw("id,class_name,
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
  public function retrieve()
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return ClassMaster::select(
      DB::raw("id,class_name,
      CASE 
        WHEN status = '0' THEN 'Deactivated'  
        WHEN status = '1' THEN 'Active'
      END as status,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
      ")
    )
      // ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->orderByDesc('id');
    // ->get();
  }

  /*Read all Records for public */
  public function activePublic()
  {
    return ClassMaster::select(
      DB::raw("id,class_name,
      CASE 
        WHEN status = '0' THEN 'Deactivated'  
        WHEN status = '1' THEN 'Active'
      END as status,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
      ")
    )
      ->where('status', 1)
      ->orderByDesc('id')
      ->get();
  }


  //Get Records by name
  public function searchByName($req)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return ClassMaster::select(
      DB::raw("id,class_name,
        CASE 
        WHEN status = '0' THEN 'Deactivated'  
        WHEN status = '1' THEN 'Active'
      END as status,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
      ")
    )
      ->where(DB::raw('upper(class_name)'), 'LIKE', '%' . strtoupper($req->search) . '%');
      // ->where('school_id', $schoolId);
    // ->where('created_by', $createdBy);
    // ->get();
  }

  /*Read all Active Records*/
  public function active()
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return ClassMaster::select(
      DB::raw("id,class_name,
      CASE 
        WHEN status = '0' THEN 'Deactivated'  
        WHEN status = '1' THEN 'Active'
      END as status,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
      ")
    )
      ->where('status', 1)
      // ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->orderBy('class_name')
      ->get();
  }
}
