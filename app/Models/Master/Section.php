<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Exception;
use DB;

class Section extends Model
{
  use HasFactory;

  protected $guarded = [];

  /*Add Records*/
  public function store(array $req)
  {
    Section::create($req);
  }
  /*Read Records by name*/
  public function readSectionGroup($sectionName)
  {
    return Section::where('section_name', $sectionName)
      ->where('status', 1)
      ->get();
  }

  //Get Records by name
  public function searchByName($name)
  {
    return Section::select(
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
      ->where('section_name', 'like', $name . '%')
      // ->where('status', 1)
      ->get();
  }

  /*Read Records by ID*/
  public function getGroupById($id)
  {
    return Section::select(
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
  public function retrieveAll()
  {
    return Section::select(
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
      ->orderBy('section_name')
      ->get();
  }

  // //view by id
  // public function listById($req) {
  //     $data = Section::where('id', $req->id)
  //           ->first();
  //       return $data;
  //   }

  /*Read all Active Records*/
  public function active()
  {
    return Section::select(
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
      ->orderBy('section_name')
      ->get();
  }
}
