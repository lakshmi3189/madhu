<?php

namespace App\Models\Master;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubMenu extends Model
{
    use HasFactory;
    protected $guarded = [];

    /*Add Records*/
    public function store(array $req)
    {
        SubMenu::create($req);
    }


    /*Read Records by name*/
    public function readSubMenuGroup($req)
    {
        $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        return SubMenu::where('menu_id', $req->menuId)
            ->where(DB::raw('upper(sub_menu_name)'), strtoupper($req->subMenuName))
            ->where('status', 1)
            ->where('school_id', $schoolId)
            // ->where('created_by', $createdBy)
            ->get();
    }

    /*Read Records by name*/
    public function readSubMenuGroupOrder($req)
    {
        $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        return SubMenu::where('order_no', $req->orderNo)
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
        return DB::table('sub_menus as a')
            ->select(
                DB::raw("b.id as menuid,b.menu_name,
                a.id,a.sub_menu_name,a.menu_id,a.order_no,
                CASE WHEN a.status = '0' THEN 'Deactivated'  
                WHEN a.status = '1' THEN 'Active'
                END as status,
                TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
                TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
                ")
            )
            ->join('menus as b', 'b.id', '=', 'a.menu_id')
            ->where('a.id', $id)
            ->where('a.school_id', $schoolId)
            // ->where('a.created_by', $createdBy)
            ->first();
    }

    /*Read all Records by*/
    public function retrieve()
    {
        $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        return DB::table('sub_menus as a')
            ->select(
                DB::raw("b.menu_name,
            a.id,a.sub_menu_name,a.menu_id,a.order_no,
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
            ->orderByDesc('a.id');
        // ->where('status', 1)
        // ->get();
    }

    //Get Records by name
    public function searchByName($req)
    {
        $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        return DB::table('sub_menus as a')
            ->select(
                DB::raw("b.id as menuid,b.menu_name,
            a.id,a.sub_menu_name,a.menu_id,a.order_no,
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


    /*Read Records by ID*/
    public function getSubMenuGroupByMenuId($req)
    {
        $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        return DB::table('sub_menus as a')
            ->select(
                DB::raw("b.id as menuid,b.menu_name,
                a.id,a.sub_menu_name,a.menu_id,a.order_no,
                CASE WHEN a.status = '0' THEN 'Deactivated'  
                WHEN a.status = '1' THEN 'Active'
                END as status,
                TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
                TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
                ")
            )
            ->join('menus as b', 'b.id', '=', 'a.menu_id')
            ->where('a.menu_id', $req->menuId)
            ->where('a.school_id', $schoolId)
            // ->where('a.created_by', $createdBy)
            ->get();
    }

    // /*Read Records by name*/
    // public function readSubMenuGroup($subMenu)
    // {
    //     return SubMenu::where('sub_menu_name', $subMenu)
    //         ->where('status', 1)
    //         ->get();
    // }

    // /*Read Records by ID*/
    // public function getGroupById($id)
    // {
    //     return DB::table('sub_menus as a')
    //         ->select(
    //             DB::raw("b.menu_name, a.*,
    //           CASE WHEN a.status = '0' THEN 'Deactivated'  
    //           WHEN a.status = '1' THEN 'Active'
    //           END as status,
    //           TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
    //           TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
    //           ")
    //         )
    //         ->join('menus as b', 'b.id', '=', 'a.menu_id')
    //         ->where('a.id', $id)
    //         ->first();
    // }
    // /*Read all Records by*/
    // public function retrieveAll()
    // {
    //     return DB::table('sub_menus as a')
    //         ->select(
    //             DB::raw("b.menu_name, a.*,
    //         CASE WHEN a.status = '0' THEN 'Deactivated'  
    //         WHEN a.status = '1' THEN 'Active'
    //         END as status,
    //         TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
    //         TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
    //         ")
    //         )
    //         ->join('menus as b', 'b.id', '=', 'a.menu_id')
    //         ->orderByDesc('a.id')
    //         // ->where('a.status', 1)
    //         ->get();
    // }

    // //Get Records by name
    // public function searchByName($name)
    // {
    //     return DB::table('sub_menus as a')
    //         ->select(
    //             DB::raw("b.menu_name,a.*,
    //         CASE WHEN a.status = '0' THEN 'Deactivated'  
    //         WHEN a.status = '1' THEN 'Active'
    //         END as status,
    //         TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
    //         TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
    //         ")
    //         )
    //         ->join('menus as b', 'b.id', '=', 'a.menu_id')
    //         ->where('sub_menu_name', 'like', $name . '%')
    //         // ->where('a.status', 1)
    //         ->get();
    // }

    // /*Read all Active Records*/
    // public function active()
    // {
    //     return DB::table('sub_menus as a')
    //         ->select(
    //             DB::raw("b.menu_name, a.*,
    //         CASE WHEN a.status = '0' THEN 'Deactivated'  
    //         WHEN a.status = '1' THEN 'Active'
    //         END as status,
    //         TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
    //         TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
    //         ")
    //         )
    //         ->join('menus as b', 'b.id', '=', 'a.menu_id')
    //         ->orderBy('a.sub_menu_name')
    //         ->where('a.status', 1)
    //         ->get();
    // }
}
