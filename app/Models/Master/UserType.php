<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class UserType extends Model
{
  use HasFactory;
  protected $guarded = [];

  /*Add Records*/
  public function store(array $req)
  {
    UserType::create($req);
  }
  /*Read Records by name*/
  public function readUserTypeGroup($req)
  {
    return UserType::where(DB::raw('upper(user_type_name)'), strtoupper($req->userTypeName))
      // where('user_type_name', $req->userTypeName)
      ->where('status', 1)
      ->get();
  }

  //Get Records by name
  public function searchByName($name)
  {
    return UserType::select(
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
      ->where('user_type_name', 'like', $name . '%')
      ->where('status', 1)
      ->get();
  }

  /*Read Records by ID*/
  public function getGroupById($id)
  {
    return UserType::select(
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
      ->where('status', 1)
      ->first();
  }

  /*Read all Records by*/
  public function retrieveAll()
  {
    return UserType::select(
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
      ->orderBy('user_type_name')
      ->get();
  }


  /*Read all Active Records*/
  public function active()
  {
    return UserType::select(
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
      ->orderBy('user_type_name')
      ->get();
  }
}
