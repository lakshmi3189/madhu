<?php

namespace App\Models\Transport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BusFeeFine extends Model
{
  use HasFactory;

  /**
   * | Created On-31-05-2023 
   * | Created By- Ashutosh Kumar
   * | Bus Fee Fine Crud Operations
   */
  protected $guarded = [];


  /*Add Records*/
  public function store(array $req)
  {
    BusFeeFine::create($req);
  }

  /*Read All Records*/
  public function readBusFeeFineGroup($monthName)
  {
    return BusFeeFine::where('month_name', $monthName)
      ->where('status', 1)
      ->get();
  }
  /*Read Records by ID*/
  public function getBusFeeFineById($id)
  {
    return BusFeeFine::where('id', $id)
      ->where('status', 1)
      ->firstOrFail();
  }

  //Get Records by name
  public function searchByName($name)
  {
    return BusFeeFine::select(
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
      ->where('month_name', 'like', $name . '%')
      // ->where('status', 1)
      ->get();
  }

  /*Read Records by ID*/
  public function getGroupById($id)
  {
    return BusFeeFine::select(
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
  public function retrieveAll($id)
  {
    return BusFeeFine::select(
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
      ->orderBy('month_name')
      ->get();
  }


  /*Read all Active Records*/
  public function activeAll()
  {
    return BusFeeFine::select(
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
      ->orderBy('month_name')
      ->get();
  }
}
