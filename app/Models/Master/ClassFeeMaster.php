<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use DB;

class ClassFeeMaster extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * | Add Records
     */
    public function store(array $req)
    {
        ClassFeeMaster::create($req);
    }

    //generate fee demand class id and fy wise
    public function getClassFeeMasterByClassId($classId)
    {
        $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        return ClassFeeMaster::where('class_id', $classId)
            // ->where('section_id', $sectionId)
            ->where('academic_year', getFinancialYear(Carbon::now()->format('Y-m-d')))
            // ->where('school_id', $schoolId)
            // ->where('school_id', $schoolId)
            // ->where('created_by', $createdBy)
            ->where('status', 1)
            ->get();
    }

    /**
     * | Get Discount Group Maps
     */
    public function getClassFeeMasterGroupMaps($req)
    {
        $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        return ClassFeeMaster::where('class_id', $req->classId)
            ->where('fee_head_id', $req->feeHeadId)
            ->orWhere('section_id', $req->sectionId)
            ->where('discount', $req->discount)
            // ->where('academic_year', getFinancialYear(Carbon::now()->format('Y-m-d')))
            // ->where('school_id', $schoolId)
            // ->where('school_id', $schoolId)
            // ->where('created_by', $createdBy)
            ->where('status', 1)
            ->get();

        // return ClassFeeMaster::where('class_id', $req->classId)
        // ->where('session_month', $req->monthName)
        // ->where('academic_year', getFinancialYear(Carbon::now()->format('Y-m-d')))
        // ->where('school_id', authUser()->school_id)
        // ->where('status', 1)
        // ->get();
    }

    public function getGroupMapById($id)
    {
        $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        return DB::table('class_fee_masters as a')
            ->select(
                DB::raw("a.*, b.fee_head, c.class_name,se.section_name,
            CASE WHEN a.status = '0' THEN 'Deactivated'  
            WHEN a.status = '1' THEN 'Active'
            END as status,
            TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
            TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
            ")
            )
            ->join('fee_heads as b', 'b.id', '=', 'a.fee_head_id')
            ->join('class_masters as c', 'c.id', '=', 'a.class_id')
            // ->join('sections as se', 'se.id', '=', 'a.section_id')
            ->leftJoin('section_group_maps as se', 'se.id', '=', 'a.section_id')
            // ->where('a.academic_year', getFinancialYear(Carbon::now()->format('Y-m-d')))
            // ->where('a.school_id', $schoolId)
            // ->where('a.school_id', $schoolId)
            // ->where('a.created_by', $createdBy)
            ->where('a.id', $id)
            ->first();
    }

    public function retrieveAll()
    {
        $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        return DB::table('class_fee_masters as a')
            ->select(
                DB::raw("a.*, b.fee_head, c.class_name,d.id as feeHeadTypeId,d.fee_head_type,se.section_name,
            CASE WHEN a.status = '0' THEN 'Deactivated'  
            WHEN a.status = '1' THEN 'Active'
            END as status,
            TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
            TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
            ")
            )
            ->join('fee_heads as b', 'b.id', '=', 'a.fee_head_id')
            ->join('fee_head_types as d', 'd.id', '=', 'b.fee_head_type_id')
            ->join('class_masters as c', 'c.id', '=', 'a.class_id')
            ->leftJoin('section_group_maps as se', 'se.id', '=', 'a.section_id')
            // ->join('sections as se', 'se.id', '=', 'a.section_id')
            // ->where('a.academic_year', getFinancialYear(Carbon::now()->format('Y-m-d')))
            // ->where('a.school_id', $schoolId)
            // ->where('a.school_id', $schoolId)
            // ->where('a.created_by', $createdBy)
            ->orderByDesc('a.id');
    }

    public function active()
    {
        $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        return DB::table('class_fee_masters as a')
            ->select(
                DB::raw("a.*, b.fee_head, c.class_name,d.id as feeHeadTypeId,d.fee_head_type,se.section_name,
        CASE WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        ")
            )
            ->join('fee_heads as b', 'b.id', '=', 'a.fee_head_id')
            ->join('fee_head_types as d', 'd.id', '=', 'b.fee_head_type_id')
            ->join('class_masters as c', 'c.id', '=', 'a.class_id')
            ->leftJoin('section_group_maps as se', 'se.id', '=', 'a.section_id')
            // ->join('sections as se', 'se.id', '=', 'a.section_id')
            // ->where('a.academic_year', getFinancialYear(Carbon::now()->format('Y-m-d')))
            // ->where('a.school_id', $schoolId)
            // ->where('a.school_id', $schoolId)
            // ->where('a.created_by', $createdBy)
            ->where('a.status', 1)
            ->orderByDesc('a.id')
            ->get();
    }

    // public function getGroupMapById($id){
    //     return DB::table('class_fee_masters as a')
    //     ->select('a.*','b.fee_head','c.class_name'
    //     )
    //     ->join('fee_heads as b', 'b.id', '=', 'a.fee_head_id')
    //     ->join('class_tables as c', 'c.id', '=', 'a.class_id') 
    //     ->where('a.id',$id)       
    //     ->first();
    // }

    // public function retrieveAll(){
    //     return DB::table('class_fee_masters as a')
    //     ->select('a.*','b.fee_head','c.class_name')
    //     ->join('fee_heads as b', 'b.id', '=', 'a.fee_head_id')
    //     ->join('class_tables as c', 'c.id', '=', 'a.class_id') 
    //     ->orderByDesc('a.id')
    //     ->get();
    // } 

}
