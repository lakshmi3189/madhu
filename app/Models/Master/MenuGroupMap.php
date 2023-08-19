<?php

namespace App\Models\Master;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuGroupMap extends Model
{
  use HasFactory;
  protected $guarded = [];

  /*Add Records*/
  public function store(array $req)
  {
    MenuGroupMap::create($req);
  }

  /*Read Records by name*/
  public function readMenuGroup($req)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return MenuGroupMap::where('menu_id', $req->menuId)
      ->where('sub_menu_id', $req->subMenuId)
      ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->get();
  }

  /*Read Records by ID*/
  public function getGroupById($id)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return DB::table('menu_group_maps as a')
      ->select(
        DB::raw("b.id as menuid,b.menu_name,b.order_no,
              a.id,a.sub_menu_id,a.menu_id,a.is_write,a.is_read,a.is_update,a.is_deactivate,a.role_id,
              c.sub_menu_name,c.order_no,
              CASE WHEN a.status = '0' THEN 'Deactivated'  
              WHEN a.status = '1' THEN 'Active'
              END as status,
              TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
              TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
              ")
      )
      ->join('menus as b', 'b.id', '=', 'a.menu_id')
      ->join('sub_menus as c', 'b.id', '=', 'a.sub_menu_id')
      ->where('a.id', $id)
      ->where('a.school_id', $schoolId)
      // ->where('a.created_by', $createdBy)
      ->first();
  }

  public function retrieve()
  {
    // echo $this->getAllSubMenu($id = 5);
    // die;
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    $finalData = array();
    $getMenus =  DB::table('menus')
      ->select(
        DB::raw("id,menu_name,icon_name,order_no,is_sub_menu,
          CASE WHEN status = '0' THEN 'Deactivated'  
          WHEN status = '1' THEN 'Active'
          END as status,
          TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
          TO_CHAR(created_at,'HH12:MI:SS AM') as time
          ")
      )
      ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->where('status', 1)
      ->orderBy('order_no')
      ->get();
    // print_var($getMenus);
    // die;
    foreach ($getMenus as $key => $val1) {
      if ($val1->is_sub_menu != 0) {
        $link = '';
      } else {
        $link = preg_replace("/\s/i", "-", strtolower(trim($val1->menu_name)));
      }
      $finalData["menu"][$key] = [
        "id" => $val1->order_no,
        "icon" => $val1->icon_name,
        "label" => $val1->menu_name,
      ];
      $sub = collect();
      if (($sub = $this->getAllSubMenu($val1->id)) && !$sub->isEmpty()) {
        $sub = $sub->map(function ($val) {
          $val->link = preg_replace("/\s/i", "-", strtolower($val->sub_menu_name));
          return $val;
        });
        $finalData["menu"][$key]["subMenu"] = $sub;
      } else {

        $finalData["menu"][$key]["link"] = $link;
      }
    }
    return $finalData;
  }
  public function getAllSubMenu($menuId = null)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    $getSubMenus =  DB::table('sub_menus as a')
      ->select(
        DB::raw("b.id as menuid,b.menu_name,
          a.id,a.sub_menu_name,a.menu_id,
          CASE WHEN a.status = '0' THEN 'Deactivated'  
          WHEN a.status = '1' THEN 'Active'
          END as status,
          TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
          TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
          ")
      )
      ->join('menus as b', 'b.id', '=', 'a.menu_id')
      ->where('a.school_id', $schoolId)
      // ->where('a.created_by', $createdBy)
      ->where('a.menu_id', $menuId)
      ->where('a.status', 1)
      ->orderBy('a.id')
      ->get();
    return $getSubMenus;
  }

  public function retrieveMenu()
  {
    // echo $this->getAllSubMenu($id = 5);
    // die;
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    $finalData = array();
    $getMenus =  DB::table('menu_group_maps as a')
      ->select(
        DB::raw("a.id,a.menu_id,a.sub_menu_id,a.is_write,a.is_read,a.is_update,a.is_deactivate,a.role_id,
        b.id as rolId, b.role_name,
          CASE WHEN a.status = '0' THEN 'Deactivated'  
          WHEN a.status = '1' THEN 'Active'
          END as status,
          TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
          TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
          ")
      )
      ->join('roles as b', 'b.id', '=', 'a.role_id')
      ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->where('status', 1)
      ->orderBy('order_no')
      ->get();
    // print_var($getMenus);
    // die;
    foreach ($getMenus as $key => $val1) {
      if ($val1->is_sub_menu != 0) {
        $link = '';
      } else {
        $link = preg_replace("/\s/i", "-", strtolower(trim($val1->menu_name)));
      }
      $finalData["menu"][$key] = [
        "id" => $val1->order_no,
        "icon" => $val1->icon_name,
        "label" => $val1->menu_name,
      ];
      $sub = collect();
      if (($sub = $this->getAllSubMenuList($val1->id)) && !$sub->isEmpty()) {
        $sub = $sub->map(function ($val) {
          $val->link = preg_replace("/\s/i", "-", strtolower($val->sub_menu_name));
          return $val;
        });
        $finalData["menu"][$key]["subMenu"] = $sub;
      } else {

        $finalData["menu"][$key]["link"] = $link;
      }
    }
    return $finalData;
  }

  public function getAllSubMenuList($menuId = null)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    $getSubMenus =  DB::table('sub_menus as a')
      ->select(
        DB::raw("b.id as menuid,b.menu_name,
          a.id,a.sub_menu_name,a.menu_id,
          CASE WHEN a.status = '0' THEN 'Deactivated'  
          WHEN a.status = '1' THEN 'Active'
          END as status,
          TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
          TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
          ")
      )
      ->join('menus as b', 'b.id', '=', 'a.menu_id')
      ->where('a.school_id', $schoolId)
      // ->where('a.created_by', $createdBy)
      ->where('a.menu_id', $menuId)
      ->where('a.status', 1)
      ->orderBy('a.id')
      ->get();
    return $getSubMenus;
  }

  /*Read all Records by*/
  public function retrieve1()
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return DB::table('sub_menus as a')
      ->select(
        DB::raw("b.id as menuid,b.menu_name,
          a.id,a.sub_menu_name,a.menu_id,
          CASE WHEN a.status = '0' THEN 'Deactivated'  
          WHEN a.status = '1' THEN 'Active'
          END as status,
          TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
          TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
          ")
      )
      ->join('menus as b', 'b.id', '=', 'a.menu_id')
      ->where('a.school_id', $schoolId)
      // ->where('a.created_by', $createdBy)
      ->orderByDesc('a.id')
      // ->where('status', 1)
      ->get();
  }

  //Get Records by name
  public function searchByName($req)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return DB::table('sub_menus as a')
      ->select(
        DB::raw("b.id as menuid,b.menu_name,
          a.id,a.sub_menu_name,a.menu_id,
          CASE WHEN a.status = '0' THEN 'Deactivated'  
          WHEN a.status = '1' THEN 'Active'
          END as status,
          TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
          TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
          ")
      )
      ->join('menus as b', 'b.id', '=', 'a.menu_id')
      ->where('a.school_id', $schoolId);
    // ->where('a.created_by', $createdBy);
    // ->get();
  }

  /*Read all Active Records*/
  public function active()
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return DB::table('sub_menus as a')
      ->select(
        DB::raw("b.id as menuid,b.menu_name,
      a.id,a.sub_menu_name,a.menu_id,
      CASE WHEN a.status = '0' THEN 'Deactivated'  
      WHEN a.status = '1' THEN 'Active'
      END as status,
      TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
      ")
      )
      ->join('menus as b', 'b.id', '=', 'a.menu_id')
      ->where('a.status', 1)
      ->where('a.school_id', $schoolId)
      // ->where('a.created_by', $createdBy)
      ->orderBy('a.sub_menu_name')
      ->get();
  }
}
