<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class FeeHead extends Model
{
  use HasFactory;
  protected $guarded = [];

  /*Add Records*/
  public function store(array $req)
  {
    FeeHead::create($req);
  }

  /*Read Records by name*/
  public function readFeeHeadGroup($req)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return FeeHead::where(DB::raw('upper(fee_head)'), strtoupper($req->feeHead))
      ->where('status', 1)
      // ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->get();
    // return FeeHead::where('fee_head_type_id', $req->feeHeadTypeId)
    //   ->where(DB::raw('upper(fee_head)'), strtoupper($req->feeHead))
    //   ->where('status', 1)
    //   ->where('school_id', $schoolId)
    //   ->where('created_by', $createdBy)
    //   ->get();
  }


  /*Read Records by ID*/
  public function getGroupById($id)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return DB::table('fee_heads as a')
      ->select(
        DB::raw("b.id as fhtid,b.fee_head_type, 
        a.id,a.fee_head,a.fee_head_type_id,a.description,
        CASE WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        ")
      )
      ->join('fee_head_types as b', 'b.id', '=', 'a.fee_head_type_id')
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
    return DB::table('fee_heads as a')
      ->select(
        DB::raw("b.id as fhtid,b.fee_head_type, 
        a.id,a.fee_head,a.fee_head_type_id,a.description,
        CASE WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        ")
      )
      ->join('fee_head_types as b', 'b.id', '=', 'a.fee_head_type_id')
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
    return DB::table('fee_heads as a')
      ->join('fee_head_types as b', 'b.id', '=', 'a.fee_head_type_id')
      // ->where(DB::raw('upper(a.fee_head)'), 'LIKE', '%' . strtoupper($req->search) . '%')
      // ->orWhere(DB::raw('upper(b.fee_head_type)'), 'LIKE', '%' . strtoupper($req->search) . '%')
      ->where("a.fee_head", "Ilike", DB::raw("'%" . $req->search . "%'"))
      ->orWhere("b.fee_head_type", "Ilike", DB::raw("'%" . $req->search . "%'"))
      ->select(
        DB::raw("b.id as fhtid,b.fee_head_type, 
        a.id,a.fee_head,a.fee_head_type_id,a.description,
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
    return DB::table('fee_heads as a')
      ->select(
        DB::raw("b.id as fhtid,b.fee_head_type, 
      a.id,a.fee_head,a.fee_head_type_id,a.description,
      CASE WHEN a.status = '0' THEN 'Deactivated'  
      WHEN a.status = '1' THEN 'Active'
      END as status,
      TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
      ")
      )
      ->join('fee_head_types as b', 'b.id', '=', 'a.fee_head_type_id')
      ->where('a.status', 1)
      // ->where('a.school_id', $schoolId)
      // ->where('a.created_by', $createdBy)
      ->orderBy('a.fee_head')
      ->get();
  }
}
