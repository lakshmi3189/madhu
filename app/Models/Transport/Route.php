<?php

namespace App\Models\Transport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Route extends Model
{
  use HasFactory;
  protected $guarded = [];


  /*Add Records*/
  public function store(array $req)
  {
    Route::create($req);
  }

  /*Read Records by name*/
  public function readRouteGroup($req)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return Route::where('route_name', $req->routeName)
      ->where('status', 1)
      ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->get();
  }

  /*Read Records by ID*/
  public function getGroupById($id)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return Route::select(
      DB::raw("id,route_name,
      CASE 
        WHEN status = '0' THEN 'Deactivated'  
        WHEN status = '1' THEN 'Active'
      END as status,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
      ")
    )
      ->where('id', $id)
      ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      // ->where('status', 1)
      ->first();
  }

  /*Read all Records by*/
  public function retrieve()
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return Route::select(
      DB::raw("id,route_name,
      CASE 
        WHEN status = '0' THEN 'Deactivated'  
        WHEN status = '1' THEN 'Active'
      END as status,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
      ")
    )
      ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->orderByDesc('id');
    // ->get();
  }

  //Get Records by name
  public function searchByName($req)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return Route::select(
      DB::raw("id,route_name,
          CASE 
          WHEN status = '0' THEN 'Deactivated'  
          WHEN status = '1' THEN 'Active'
        END as status,
        TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(created_at,'HH12:MI:SS AM') as time
        ")
    )
      ->where(DB::raw('upper(route_name)'), 'LIKE', '%' . strtoupper($req->search) . '%')
      // ->where('route_name', 'like', $req->search . '%')
      ->where('school_id', $schoolId);
    // ->where('created_by', $createdBy);
    // ->get();
  }


  /*Read all Active Records*/
  public function active()
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return Route::select(
      DB::raw("id,route_name,
      CASE 
        WHEN status = '0' THEN 'Deactivated'  
        WHEN status = '1' THEN 'Active'
      END as status,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
      ")
    )
      ->where('status', 1)
      ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->orderBy('route_name')
      ->get();
  }


  /*Read all Active Records*/
  public function activePublic()
  {
    $schoolId = authUser()->school_id;
    return Route::select(
      DB::raw("id,route_name,
      CASE 
        WHEN status = '0' THEN 'Deactivated'  
        WHEN status = '1' THEN 'Active'
      END as status,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
      ")
    )
      ->where('status', 1)
      ->orderBy('route_name')
      ->where('school_id', $schoolId)
      ->get();
  }
}
