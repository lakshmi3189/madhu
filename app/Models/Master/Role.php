<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Role extends Model
{
  use HasFactory;

  protected $guarded = [];
  /*Add Records*/
  public function store(array $req)
  {
    Role::create($req);
  }
  /*Read Records by name*/
  public function readRoleGroup($req)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return Role::where(DB::raw('upper(role_name)'), strtoupper($req->roleName))
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
    return Role::select(
      DB::raw("id,role_name,
      CASE 
      WHEN status = '0' THEN 'Deactivated'  
      WHEN status = '1' THEN 'Active'
      END as status,
      CASE 
      WHEN is_display = '0' THEN 'No'  
      WHEN is_display = '1' THEN 'Yes'
      END as is_display,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
      ")
    )
      ->where('id', $id)
      ->where('is_display', 1)
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
    return Role::select(
      DB::raw("id,role_name,
      CASE 
      WHEN status = '0' THEN 'Deactivated'  
      WHEN status = '1' THEN 'Active'
      END as status,
      CASE 
      WHEN is_display = '0' THEN 'No'  
      WHEN is_display = '1' THEN 'Yes'
      END as is_display,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
      ")
    )
      ->where('is_display', 1)
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
    return Role::select(
      DB::raw("id,role_name,
      CASE 
      WHEN status = '0' THEN 'Deactivated'  
      WHEN status = '1' THEN 'Active'
      END as status,
      CASE 
      WHEN is_display = '0' THEN 'No'  
      WHEN is_display = '1' THEN 'Yes'
      END as is_display,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
      ")
    )
      ->where(DB::raw('upper(role_name)'), 'LIKE', '%' . strtoupper($req->search) . '%')
      ->where('is_display', 1)
      ->where('school_id', $schoolId);
    // ->where('created_by', $createdBy);
    // ->get();
  }

  /*Read all Active Records*/
  public function active()
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return Role::select(
      DB::raw("id,role_name,
      CASE 
      WHEN status = '0' THEN 'Deactivated'  
      WHEN status = '1' THEN 'Active'
      END as status,
      CASE 
      WHEN is_display = '0' THEN 'No'  
      WHEN is_display = '1' THEN 'Yes'
      END as is_display,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
      ")
    )
      ->where('status', 1)
      ->where('is_display', 1)
      ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->orderBy('role_name')
      ->get();
  }






  // /*Read Records by name*/
  // public function readRoleGroup($req)
  // {
  //   return Role::where('role_name', $req->roleName)
  //     ->where('status', 1)
  //     ->get();
  // }

  // //Get Records by name
  // public function searchByName($req)
  // {
  //   return Role::select(
  //     '*',
  //     DB::raw("
  //         CASE 
  //         WHEN status = '0' THEN 'Deactivated'  
  //         WHEN status = '1' THEN 'Active'
  //       END as status,
  //       TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
  //       TO_CHAR(created_at,'HH12:MI:SS AM') as time
  //       ")
  //   )
  //     ->where('role_name', 'like', $req->search . '%')
  //     ->where('status', 1)
  //     ->get();
  // }

  // /*Read Records by ID*/
  // public function getGroupById($id)
  // {
  //   return Role::select(
  //     '*',
  //     DB::raw("
  //     CASE 
  //       WHEN status = '0' THEN 'Deactivated'  
  //       WHEN status = '1' THEN 'Active'
  //     END as status,
  //     TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
  //     TO_CHAR(created_at,'HH12:MI:SS AM') as time
  //   ")
  //   )
  //     ->where('id', $id)
  //     // ->where('status', 1)
  //     ->first();
  // }

  // /*Read all Records by*/
  // public function retrieve()
  // {
  //   return Role::select(
  //     '*',
  //     DB::raw("
  //     CASE 
  //       WHEN status = '0' THEN 'Deactivated'  
  //       WHEN status = '1' THEN 'Active'
  //     END as status,
  //     TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
  //     TO_CHAR(created_at,'HH12:MI:SS AM') as time
  //   ")
  //   )
  //     // ->where('status', 1)
  //     ->orderBy('role_name')
  //     ->get();
  // }

  // /*Read all Active Records*/
  // public function active()
  // {
  //   return Role::select(
  //     '*',
  //     DB::raw("
  //     CASE 
  //       WHEN status = '0' THEN 'Deactivated'  
  //       WHEN status = '1' THEN 'Active'
  //     END as status,
  //     TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
  //     TO_CHAR(created_at,'HH12:MI:SS AM') as time
  //     ")
  //   )
  //     ->where('status', 1)
  //     ->orderBy('role_name')
  //     ->get();
  // }
}
