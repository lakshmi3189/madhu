<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;


class MiscellaneousSubCategory extends Model
{
    use HasFactory;

    protected $guarded = [];

    /*Add Records*/
    public function store(array $req)
    {
        MiscellaneousSubCategory::create($req);
    }

    /*Read Records by name*/
    public function readMiscSubCateGroup($req)
    {
        // $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        return MiscellaneousSubCategory::where('misc_category_id', $req->miscCategoryId)
            ->where(DB::raw('upper(misc_sub_category_name)'), strtoupper($req->miscSubCategoryName))
            ->where('status', 1)
            // ->where('school_id', $schoolId)
            // ->where('created_by', $createdBy)
            ->get();
    }

    /*Read Records by ID*/
    public function getGroupById($id)
    {
        // $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        return DB::table('miscellaneous_sub_categories as a')
            ->select(
                DB::raw("b.id as miscid,b.misc_category_name,
                a.id,a.misc_sub_category_name,a.misc_category_id,
                CASE WHEN a.status = '0' THEN 'Deactivated'  
                WHEN a.status = '1' THEN 'Active'
                END as status,
                TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
                TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
                ")
            )
            ->join('miscellaneous_categories as b', 'b.id', '=', 'a.misc_category_id')
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
        return DB::table('miscellaneous_sub_categories as a')
            ->select(
                DB::raw("b.id as miscid,b.misc_category_name,
                a.id,a.misc_sub_category_name,a.misc_category_id,
                CASE WHEN a.status = '0' THEN 'Deactivated'  
                WHEN a.status = '1' THEN 'Active'
                END as status,
                TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
                TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
                ")
            )
            ->join('miscellaneous_categories as b', 'b.id', '=', 'a.misc_category_id')
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
        return DB::table('miscellaneous_sub_categories as a')
            ->join('miscellaneous_categories as b', 'b.id', '=', 'a.misc_category_id')
            ->where("a.misc_sub_category_name", "Ilike", DB::raw("'%" . $req->search . "%'"))
            ->orWhere("b.misc_category_name", "Ilike", DB::raw("'%" . $req->search . "%'"))
            ->select(
                DB::raw("b.id as miscid,b.misc_category_name,
                a.id,a.misc_sub_category_name,a.misc_category_id,
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
        return DB::table('miscellaneous_sub_categories as a')
            ->select(
                DB::raw("b.id as miscid,b.misc_category_name,
                a.id,a.misc_sub_category_name,a.misc_category_id,
                CASE WHEN a.status = '0' THEN 'Deactivated'  
                WHEN a.status = '1' THEN 'Active'
                END as status,
                TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
                TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
                ")
            )
            ->join('miscellaneous_categories as b', 'b.id', '=', 'a.misc_category_id')
            ->where('a.status', 1)
            // ->where('a.school_id', $schoolId)
            // ->where('a.created_by', $createdBy)
            ->orderBy('a.misc_sub_category_name')
            ->get();
    }
}
