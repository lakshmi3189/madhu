<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class City extends Model
{
  use HasFactory;
  protected $guarded = [];

  /*Add Records*/
  public function store(array $req)
  {
    City::create($req);
  }

  /*Read Records by name*/
  public function readCityGroup($req)
  {
    return City::where(DB::raw('upper(city_name)'), strtoupper($req->cityName))
      ->where('country_id', $req->countryId)
      ->where('state_id', $req->stateId)
      ->where('status', 1)
      ->get();
  }



  /*Read Records by ID*/
  public function getGroupById($id)
  {
    // $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return DB::table('cities as a')
      ->select(
        DB::raw("b.id cntid,b.country_name, 
        c.id as stid,c.state_name,
        a.country_id,a.state_id,a.city_name,
        CASE 
        WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        ")
      )
      ->join('countries as b', 'b.id', '=', 'a.country_id')
      ->join('states as c', 'c.id', '=', 'a.state_id')
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
    return DB::table('cities as a')
      ->select(
        DB::raw("b.id cntid,b.country_name, 
        c.id as stid,c.state_name,
        a.id,a.country_id,a.state_id,a.city_name,
        CASE WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        
        ")
      )
      ->join('countries as b', 'b.id', '=', 'a.country_id')
      ->join('states as c', 'c.id', '=', 'a.state_id')
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
    return DB::table('cities as a')
      ->join('countries as b', 'b.id', '=', 'a.country_id')
      ->join('states as c', 'c.id', '=', 'a.state_id')
      ->where("a.city_name", "Ilike", DB::raw("'%" . $req->search . "%'"))
      ->orWhere("b.country_name", "Ilike", DB::raw("'%" . $req->search . "%'"))
      ->orWhere("c.state_name", "Ilike", DB::raw("'%" . $req->search . "%'"))
      ->select(
        DB::raw("b.id as cntid,b.country_name, 
        c.state_name,c.id as stid,
        a.country_id, a.state_id,a.city_name,
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
    return DB::table('cities as a')
      ->select(
        DB::raw("b.country_name, 
        c.state_name,
        a.country_id as country_id,a.state_id as state_id,a.city_name,a.id,
        CASE WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        
        ")
      )
      ->join('countries as b', 'b.id', '=', 'a.country_id')
      ->join('states as c', 'c.id', '=', 'a.state_id')
      ->where('a.status', 1)
      // ->where('a.school_id', $schoolId)
      // ->where('a.created_by', $createdBy)
      ->orderBy('a.city_name')
      ->get();
  }
}
