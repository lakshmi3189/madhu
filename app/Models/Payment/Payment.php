<?php

namespace App\Models\Payment;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
  use HasFactory;

  protected $guarded = [];

  /*Add Records*/
  public function store(array $req)
  {
    Payment::create($req);
  }

  /*Read Records by name*/
  public function readPaymentGroup($feeCollId, $stdId, $fy, $ob)
  {
    return Payment::where('fee_collection_id', $feeCollId)
      ->where('student_id', $stdId)
      ->where('academic_year', $fy)
      ->where('is_paid', $ob['isPaid'])
      //   ->where('bank_approved',1)
      ->where('status', 1)
      ->get();
  }

  //Get Records by name
  public function searchByName($name)
  {
    return Payment::select(
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
      ->where('month_name', 'ilike', $name . '%')
      // ->where('status', 1)
      ->get();
  }

  /*Read Records by ID*/
  public function getGroupById($id)
  {
    return Payment::select(
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
      // ->where('status', 1)
      ->first();
  }

  /*Read all Records by*/
  public function retrieve()
  {
    return DB::table('payments as a')
      ->select(
        DB::raw("c.admission_no,b.month_name, b.total_fee, b.grand_total, a.*,
        CASE WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        ")
      )
      ->join('fee_collections as b', 'b.id', '=', 'a.fee_collection_id')
      ->join('students as c', 'c.id', '=', 'a.student_id')
      ->orderBy('a.id')
      ->where('a.status', 1)
      ->get();
  }

  // public function retrieve()
  // {
  //   return DB::table('students as a')
  //     ->select(
  //       DB::raw("c.month_name,c.total_fee,c.grand_total, b.*,
  //       CASE WHEN a.status = '0' THEN 'Deactivated'  
  //       WHEN a.status = '1' THEN 'Active'
  //       END as status,
  //       TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
  //       TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
  //       ")
  //     )
  //     ->join('payments as b', 'b.student_id', '=', 'a.id')
  //     ->join('fee_collections as c' , 'c.id' , '=','b.fee_collection_id')
  //     ->join('fee_demands as d','d.student_id', '=','a.id')
  //     ->join('fee_heads as e','e.id','=','d.fee_head_id')
  //     ->orderBy('b.id')
  //     ->where('b.status', 1)
  //     ->get();
  // }


  /*Read all Active Records*/
  public function active()
  {
    return Payment::select(
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
      ->where('status', 1)
      ->orderByDesc('id')
      ->get();
  }
}
