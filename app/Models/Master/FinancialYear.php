<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Exception;

class FinancialYear extends Model
{
  // use HasFactory;
  // protected $guarded=[];

  // /*Add Records*/
  // public function store(array $req){
  //   FinancialYear::create($req);
  // }

  // /*Read Records by name*/
  // public function readFeeHeadGroup($FyName)
  // {
  //     return FinancialYear::where('fy_name', $FyName)
  //         ->where('status', 1)
  //         ->get();
  // }

  // /*Read Records by ID*/
  // public function getGroupById($id)
  // {
  //     return FinancialYear::where('id', $id)
  //         ->where('status', 1)
  //         ->firstOrFail();
  // }

  // /*Read Records by ID*/
  // public function getGroupById($id)
  // {
  //   return DB::table('fee_heads as a')
  //           ->select('a.id', 'a.fee_head','b.fee_head_type', 'a.description', 'a.academic_year', 'a.status')
  //           ->join('fee_head_types as b', 'b.id', '=', 'a.fee_head_type_id') 
  //           ->where('a.id',$id)       
  //           ->first();
  // }

  /*Read all Records by*/
  // public function retrieveAll()
  // {
  //   return DB::table('fee_heads as a')
  //           ->select('a.id', 'a.fee_head','b.fee_head_type', 'a.description', 'a.academic_year', 'a.status')
  //           ->join('fee_head_types as b', 'b.id', '=', 'a.fee_head_type_id')
  //           ->orderByDesc('id') 
  //           ->get();
  // }
  use HasFactory;
  protected $guarded = [];
  //insert
  public function insertData($req)
  {
    // echo 'testing';die;    
    $mObject = new FinancialYear();
    // print_r($req->fyName);die;
    $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
    $insert = [
      $mObject->fy_name   = $req['fyName'],
      $mObject->school_id   = authUser()->school_id,
      $mObject->created_by   = authUser()->id,
      $mObject->ip_address   = getClientIpAddress()
    ];
    // print_r($insert);die;
    $mObject->save($insert);
    return $mObject;
  }

  //view all 
  public static function list()
  {
    $viewAll = FinancialYear::select('id', 'fy_name')
      ->where('status', 1)
      ->orderByDesc('id')
      ->get();
    return $viewAll;
  }

  //view by id
  public function listById($req)
  {
    $data = FinancialYear::where('id', $req->id)
      ->first();
    return $data;
  }

  //update
  public function updateData($req)
  {
    $data = FinancialYear::find($req->id);
    if (!$data)
      throw new Exception("Record Not Found!");
    $edit = [
      'fy_name' => $req->fy_name
    ];
    $data->update($edit);
    return $data;
  }

  //delete 
  public function deleteData($req)
  {
    $data = FinancialYear::find($req->id);
    $data->is_deleted = "1";
    $data->save();
    return $data;
  }

  //truncate
  public function truncateData()
  {
    $data = FinancialYear::truncate();
    return $data;
  }
}
