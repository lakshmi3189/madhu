<?php

namespace App\Models\Payment;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMode extends Model
{
  use HasFactory;
  protected $guarded = [];

  /*Add Records*/
  public function store(array $req)
  {
    PaymentMode::create($req);
  }

  /*Read Records by name*/
  public function readPaymentModeGroup($paymentModeName)
  {
    // $schoolId = authUser()->school_id;
    return PaymentMode::where('payment_mode_name', $paymentModeName)
      ->where('status', 1)
      // ->where('school_id', $schoolId)
      ->get();
  }

  //Get Records by name
  public function searchByName($name)
  {
    // $schoolId = authUser()->school_id;
    return PaymentMode::select(
      '*',
      DB::raw("
          CASE 
          WHEN status = '0' THEN 'Deactivated'  
          WHEN status = '1' THEN 'Active'
        END as status,
        TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(created_at,'HH12:MI:SS AM') as time
        ")
    )
      ->where('payment_mode_name', 'ilike', $name . '%')
      // ->where('school_id', $schoolId)
      // ->where('status', 1)
      ->get();
  }

  /*Read Records by ID*/
  public function getGroupById($id)
  {
    // $schoolId = authUser()->school_id;
    return PaymentMode::select(
      '*',
      DB::raw("
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
      // ->where('status', 1)
      ->first();
  }

  /*Read all Records by*/
  public function retrieve()
  {
    // $schoolId = authUser()->school_id;
    return PaymentMode::select(
      '*',
      DB::raw("
      CASE 
      WHEN status = '0' THEN 'Deactivated'  
      WHEN status = '1' THEN 'Active'
      END as status,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
	  ")
    )
      // ->where('status', 1)
      ->orderBy('payment_mode_name')
      // ->where('school_id', $schoolId)
      ->get();
  }


  /*Read all Active Records*/
  public function active()
  {
    // $schoolId = authUser()->school_id;
    return PaymentMode::select(
      DB::raw("id,payment_mode_name,
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
      ->orderBy('payment_mode_name')
      ->get();
  }
}
