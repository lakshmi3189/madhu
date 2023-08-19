<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DiscountGroupMap extends Model
{
    use HasFactory;
    protected $guarded = [];

    //Store Record    
    public function store(array $req)
    {
        DiscountGroupMap::create($req);
    }

    /*Read Records by name*/
    public function getDiscountGroupMaps($req)
    {
        $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        return DiscountGroupMap::where('student_id', $req->stdId)
            ->where('discount_group_id', $req->discountGroupId)
            // ->where('class_fee_id', $req->classFeeId)
            ->where('status', 1)
            // ->where('school_id', $schoolId)
            // ->where('created_by', $createdBy)
            ->get();
    }

    /*Read Records by ID*/
    public function getGroupById($id)
    {
        $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        return DB::table('discount_group_maps as a')
            ->select(
                DB::raw("b.id as dgid, b.discount_group,
                a.id,a.discount_percent,
                concat(c.first_name,' ',c.middle_name,' ',c.last_name) as student_name, c.admission_no,
                CASE WHEN a.status = '0' THEN 'Deactivated'  
                WHEN a.status = '1' THEN 'Active'
                END as status,
                TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
                TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
                ")
            )
            ->join('discount_groups as b', 'b.id', '=', 'a.discount_group_id')
            ->join('students as c', 'c.id', '=', 'a.student_id')
            ->where('a.id', $id)
            // ->where('a.school_id', $schoolId)
            // ->where('a.created_by', $createdBy)
            ->first();
    }

    /*Read all Records by*/
    public function retrieve()
    {
        $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        return DB::table('discount_group_maps as a')
            ->select(
                DB::raw("b.discount_group,
                a.id,a.discount_percent,
                concat(c.first_name,' ',c.middle_name,' ',c.last_name) as student_name, c.admission_no,
                CASE WHEN a.status = '0' THEN 'Deactivated'  
                WHEN a.status = '1' THEN 'Active'
                END as status,
                TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
                TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
                ")
            )
            ->join('discount_groups as b', 'b.id', '=', 'a.discount_group_id')
            ->join('students as c', 'c.id', '=', 'a.student_id')
            // ->where('a.school_id', $schoolId)
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
        return DB::table('discount_group_maps as a')
            ->join('discount_groups as b', 'b.id', '=', 'a.discount_group_id')
            ->join('students as c', 'c.id', '=', 'a.student_id')
            ->where("a.discount_percent", "Ilike", DB::raw("'%" . $req->search . "%'"))
            ->orWhere("b.discount_group", "Ilike", DB::raw("'%" . $req->search . "%'"))
            ->orWhere("c.admission_no", "Ilike", DB::raw("'%" . $req->search . "%'"))
            ->select(
                DB::raw("b.id as dgid, b.discount_group,
                a.id,a.discount_percent,
                concat(c.first_name,' ',c.middle_name,' ',c.last_name) as student_name, c.admission_no,
                CASE WHEN a.status = '0' THEN 'Deactivated'  
                WHEN a.status = '1' THEN 'Active'
                END as status,
                TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
                TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
                ")
            );
            // ->where('a.school_id', $schoolId);
        // ->where('a.created_by', $createdBy);
        // ->get();
    }

    /*Read all Active Records*/
    public function active()
    {
        $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        return DB::table('section_group_maps as a')
            ->select(
                DB::raw("b.class_name,
                a.id,a.section_name,
                CASE WHEN a.status = '0' THEN 'Deactivated'  
                WHEN a.status = '1' THEN 'Active'
                END as status,
                TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
                TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
                ")
            )
            ->join('class_masters as b', 'b.id', '=', 'a.class_id')
            ->where('a.status', 1)
            // ->where('a.school_id', $schoolId)
            // ->where('a.created_by', $createdBy)
            ->orderBy('a.section_name')
            ->get();
    }

    // Get Discounts by Student Id
    public function getDiscountsByStudentId($studentId)
    {
        $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        return DiscountGroupMap::where('student_id', $studentId)
            ->where('status', 1)
            // ->where('school_id', $schoolId)
            // ->where('created_by', $createdBy)
            ->get();
    }
}
