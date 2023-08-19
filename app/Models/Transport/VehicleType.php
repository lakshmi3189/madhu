<?php

namespace App\Models\Transport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class VehicleType extends Model
{
  use HasFactory;
  // protected $table = 'vehicle_types';
  protected $guarded = [];

  /*Add Records*/
  public function store(array $req)
  {
    VehicleType::create($req);
  }

  /*Read Records by name*/
  public function readVehicleTypeGroup($req)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return VehicleType::where(DB::raw('upper(vehicle_type_name)'), strtoupper($req->termName))
      ->where('status', 1)
      ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->get();
  }

  //Get Records by name
  public function searchByName($req)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return VehicleType::select(
      DB::raw("id, vehicle_type_name, max_seating_no,
      CASE 
      WHEN status = '0' THEN 'Deactivated'  
      WHEN status = '1' THEN 'Active'
      END as status,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
      ")
    )
      // ->where('term_name', 'LIKE', $req->search . '%')
      ->where(DB::raw('upper(vehicle_type_name)'), 'LIKE', '%' . strtoupper($req->search) . '%')
      ->where('school_id', $schoolId);
    // ->where('created_by', $createdBy);
    // ->get();
  }

  /*Read Records by ID*/
  public function getGroupById($id)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return VehicleType::select(
      DB::raw("id, vehicle_type_name, max_seating_no,
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
      ->first();
  }

  /*Read all Records by*/
  public function retrieve()
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return VehicleType::select(
      DB::raw("id, vehicle_type_name, max_seating_no,
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

  /*Read all Active Records*/
  public function active()
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return VehicleType::select('id', 'vehicle_type_name')
      ->where('status', 1)
      ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->orderBy('vehicle_type_name')
      ->get();
  }













  // Read Records by name
  // public function readVehicleTypeGroup($vehiclesTypeName)
  // {
  //     return VehicleType::where('vehicle_type_name', $vehiclesTypeName)
  //         ->where('status', 1)
  //         ->get();
  // }

  //Get Records by name
  // public function searchByName1($name)
  // {
  //   return VehicleType::select(
  //     '*',
  //     DB::raw("
  //     CASE 
  //     WHEN status = '0' THEN 'Deactivated'  
  //     WHEN status = '1' THEN 'Active'
  //     END as status,
  //     TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
  //     TO_CHAR(created_at,'HH12:MI:SS AM') as time
  //     ")
  //   )
  //     ->where('vehicle_type_name', 'like', $name . '%')
  //     // ->where('status', 1)
  //     ->get();
  // }

  // /*Read Records by ID*/
  // public function getGroupById1($id)
  // {
  //   return VehicleType::select(
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
  // public function retrieveAll()
  // {
  //   return VehicleType::select(
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
  //     ->get();
  // }
  // /*Read all Active Records*/
  // public function activeAll()
  // {
  //   return VehicleType::select(
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
  //     ->orderBy('vehicle_type_name')
  //     ->get();
  // }
}
