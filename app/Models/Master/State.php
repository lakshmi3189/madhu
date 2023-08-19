<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class State extends Model
{
  use HasFactory;

  protected $guarded = [];


  /*Add Records*/
  public function store(array $req)
  {
    State::create($req);
  }

  /*Read Records by name*/
  public function readStateGroup($req)
  {
    //  $schoolId = authUser()->school_id;
    //  $createdBy = authUser()->id;
    return State::where('country_id', $req->countryId)
      ->where(DB::raw('upper(state_name)'), strtoupper($req->stateName))
      ->where('status', 1)
      //  ->where('school_id', $schoolId)
      //  ->where('created_by', $createdBy)
      ->get();
  }



  /*Read Records by ID*/
  public function getGroupById($id)
  {
    // $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return DB::table('states as a')
      ->select(
        DB::raw("b.id as cntid,b.country_name,
         a.id,a.state_name,a.country_id,
         CASE WHEN a.status = '0' THEN 'Deactivated'  
         WHEN a.status = '1' THEN 'Active'
         END as status,
         TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
         TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
         ")
      )
      ->join('countries as b', 'b.id', '=', 'a.country_id')
      ->where('a.id', $id)
      // ->where('a.school_id', $schoolId)
      // ->where('a.created_by', $createdBy)
      ->first();
  }

  /*Read all Records by*/
  public function retrieve()
  {
    // $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return DB::table('states as a')
      ->select(
        DB::raw("b.id as cntid,b.country_name,
        a.id,a.state_name,a.country_id,
         CASE WHEN a.status = '0' THEN 'Deactivated'  
         WHEN a.status = '1' THEN 'Active'
         END as status,
         TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
         TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
         ")
      )
      ->join('countries as b', 'b.id', '=', 'a.country_id')
      // ->where('a.school_id', $schoolId)
      // ->where('a.created_by', $createdBy)
      ->orderByDesc('a.id');
    // ->where('status', 1)
    // ->get();
  }

  //Get Records by name
  public function searchByName($req)
  {
    // $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return DB::table('states as a')
      ->join('countries as b', 'b.id', '=', 'a.country_id')
      ->where("a.state_name", "Ilike", DB::raw("'%" . $req->search . "%'"))
      ->orWhere("b.country_name", "Ilike", DB::raw("'%" . $req->search . "%'"))
      ->select(
        DB::raw("b.country_name,
         a.id,a.state_name,
         CASE WHEN a.status = '0' THEN 'Deactivated'  
         WHEN a.status = '1' THEN 'Active'
         END as status,
         TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
         TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
         ")
      );
    // ->where('a.school_id', $schoolId)
    // ->where('a.created_by', $createdBy);
    // ->get();
  }

  /*Read all Active Records*/
  public function active()
  {
    // $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return DB::table('states as a')
      ->select(
        DB::raw("b.country_name,b.id as country_id,
         a.id,a.state_name,
         CASE WHEN a.status = '0' THEN 'Deactivated'  
         WHEN a.status = '1' THEN 'Active'
         END as status,
         TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
         TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
         ")
      )
      ->join('countries as b', 'b.id', '=', 'a.country_id')
      ->where('a.status', 1)
      // ->where('a.school_id', $schoolId)
      // ->where('a.created_by', $createdBy)
      ->orderBy('a.state_name')
      ->get();
  }
}
