<?php

namespace App\Models\Master;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
  use HasFactory;
  protected $guarded = [];

  /*Add Records*/
  public function store(array $req)
  {
    Menu::create($req);
  }

  /*Read Records by name*/
  public function readMenuGroup($req)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return Menu::where(DB::raw('upper(menu_name)'), strtoupper($req->menuName))
      ->where('status', 1)
      ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->get();
  }

  public function readMenuOrderGroup($req)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return Menu::where('order_no', $req->orderNo)
      ->where('status', 1)
      ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->get();
  }

  /*Read Records by ID*/
  public function getGroupById($id)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return Menu::select(
      DB::raw("id,menu_name,order_no,icon_name,
      CASE 
      WHEN status = '0' THEN 'Deactivated'  
      WHEN status = '1' THEN 'Active'
      END as status,
      CASE 
      WHEN is_sub_menu = '0' THEN 'No'  
      WHEN is_sub_menu = '1' THEN 'Yes'
      END as is_sub_menu,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
      ")
    )
      ->where('id', $id)
      ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->first();
  }

  /*Read all Records by*/
  public function retrieve()
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return Menu::select(
      DB::raw("id,menu_name,order_no,icon_name,
      CASE 
      WHEN status = '0' THEN 'Deactivated'  
      WHEN status = '1' THEN 'Active'
      END as status,
      CASE 
      WHEN is_sub_menu = '0' THEN 'No'  
      WHEN is_sub_menu = '1' THEN 'Yes'
      END as is_sub_menu,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
      ")
    )
      ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->orderByDesc('id');
    // ->get();
  }

  //Get Records by name
  public function searchByName($req)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return Menu::select(
      DB::raw("id,menu_name,order_no,icon_name,
      CASE 
      WHEN status = '0' THEN 'Deactivated'  
      WHEN status = '1' THEN 'Active'
      END as status,
      CASE 
      WHEN is_sub_menu = '0' THEN 'No'  
      WHEN is_sub_menu = '1' THEN 'Yes'
      END as is_sub_menu,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
      ")
    )
      ->where(DB::raw('upper(menu_name)'), 'LIKE', '%' . strtoupper($req->search) . '%')
      ->where('school_id', $schoolId);
    // ->where('created_by', $createdBy);
    // ->get();
  }

  /*Read all Active Records*/
  public function active()
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return Menu::select(
      DB::raw("id,menu_name,order_no,icon_name,
      CASE 
      WHEN status = '0' THEN 'Deactivated'  
      WHEN status = '1' THEN 'Active'
      END as status,
      CASE 
      WHEN is_sub_menu = '0' THEN 'No'  
      WHEN is_sub_menu = '1' THEN 'Yes'
      END as is_sub_menu,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
      ")
    )
      ->where('status', 1)
      ->where('is_sub_menu', 1)
      ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->orderBy('menu_name')
      ->get();
  }
}
