<?php

namespace App\Models\Transport;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupPoint extends Model
{
  use HasFactory;

  protected $guarded = [];

  /*Add Records*/
  public function store(array $req)
  {
    PickupPoint::create($req);
  }

  /*Read Records by name*/
  public function readPickupPointGroup($req)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return PickupPoint::where(DB::raw('upper(pickup_point_name)'), strtoupper($req->pickupPointName))
      ->where('pickup_point_address', $req->pickupPointAddress)
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
    return PickupPoint::select(
      DB::raw("id,pickup_point_name,pickup_point_address,
      CASE 
      WHEN status = '0' THEN 'Deactivated'  
      WHEN status = '1' THEN 'Active'
      END as status,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
      ")
    )
      ->where(DB::raw('upper(pickup_point_name)'), 'LIKE', '%' . strtoupper($req->search) . '%')
      ->orWhere(DB::raw('upper(pickup_point_address)'), 'LIKE', '%' . strtoupper($req->search) . '%')
      ->where('school_id', $schoolId);
    // ->where('created_by', $createdBy);
    // ->get();
  }

  /*Read Records by ID*/
  public function getGroupById($id)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return PickupPoint::select(
      DB::raw("id,pickup_point_name,pickup_point_address,
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
    return PickupPoint::select(
      DB::raw("id,pickup_point_name,pickup_point_address,
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
    return PickupPoint::select('id', 'pickup_point_name', 'pickup_point_address')
      ->where('status', 1)
      ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->orderBy('pickup_point_name')
      ->get();
  }

  /*Read all Active Records*/
  public function activePublic()
  {
    $schoolId = authUser()->school_id;
    return PickupPoint::select('id', 'pickup_point_name', 'pickup_point_address')
      ->where('status', 1)
      ->orderBy('pickup_point_name')
      ->where('school_id', $schoolId)
      ->get();
  }
}
