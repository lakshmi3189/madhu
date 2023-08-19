<?php

namespace App\Models\Holiday;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Holiday extends Model
{
  use HasFactory;
  protected $guarded = [];

  /*Add Records*/
  public function store(array $req)
  {
    Holiday::create($req);
  }


  /*Read Records by name*/
  public function readHolidayGroup($req)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return Holiday::where(DB::raw('upper(holiday)'), strtoupper($req->holiday))
      ->where('holiday_start_date', $req->holidayStartDate)
      // ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->where('status', 1)
      ->get();
  }

  /*Read Records by ID*/
  public function getGroupById($id)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return Holiday::select(
      DB::raw("id,holiday,description,holiday_start_date, holiday_end_date,
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
    return Holiday::select(
      DB::raw("id,holiday,description,
      CASE 
      WHEN status = '0' THEN 'Deactivated'  
      WHEN status = '1' THEN 'Active'
      END as status,
      TO_CHAR(holiday_start_date::date,'dd-mm-yyyy') as holiday_start_date,
      TO_CHAR(holiday_end_date::date,'dd-mm-yyyy') as holiday_end_date,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
      ")
    )
      // ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->orderByDesc('holiday_start_date');
    // ->get();
  }

  //Get Records by name
  public function searchByName($req)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return Holiday::select(
      DB::raw("id,holiday,description,
      CASE 
      WHEN status = '0' THEN 'Deactivated'  
      WHEN status = '1' THEN 'Active'
      END as status,
      TO_CHAR(holiday_start_date::date,'dd-mm-yyyy') as holiday_start_date,
      TO_CHAR(holiday_end_date::date,'dd-mm-yyyy') as holiday_end_date,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
      ")
    )
      ->where(DB::raw('upper(holiday)'), 'LIKE', '%' . strtoupper($req->search) . '%');
      // ->where('school_id', $schoolId);
    // ->where('created_by', $createdBy);
    // ->get();
  }



  /*Read all Active Records*/
  public function active()
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return Holiday::select(
      DB::raw("id,holiday,description,
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
      ->orderByDesc('holiday_start_date')
      ->get();
  }


  public static function csv($data)
  {
    DB::table('holidays')->insert($data);
  }
}
