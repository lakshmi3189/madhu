<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Facades\Config;

class FeeDemand extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function store(array $req)
    {
        FeeDemand::create($req);
    }

    /* Get all records month wise for fee collection */
    public function getFees($studentId, $monthName = null)
    {
        $data = array();
        $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        $from = DB::table('fee_demands AS fee_demands')
            ->join('class_fee_masters as class_fee_masters', 'class_fee_masters.id', '=', 'fee_demands.class_fee_master_id')
            ->join('students as students', 'students.id', '=', 'fee_demands.student_id')
            ->join('class_masters as class_masters', 'class_masters.id', '=', 'class_fee_masters.class_id')
            ->leftjoin('discount_group_maps as discount_group_maps', 'discount_group_maps.student_id', '=', 'fee_demands.student_id')
            // ->join('subjects as c', 'c.id', '=', 's.subject_id')
            ->leftJoin('section_group_maps as sections', 'sections.id', '=', 'class_fee_masters.section_id')
            ->join('fee_heads as fee_heads', 'fee_heads.id', '=', 'fee_demands.fee_head_id')
            ->where('students.id', $studentId)
            // ->where('fee_demands.school_id', $schoolId)
            // ->where('fee_demands.created_by', $createdBy)
            ->where('students.status', 1);
        // if ($monthName) {

        //     // $from = $from->where('fee_demands.month_name', $monthName);
        // }

        $selectData = $from->select(
            DB::raw(" fee_demands.month_name,fee_demands.fee_head_id,fee_demands.amount,fee_heads.fee_head,discount_group_maps.discount_percent,
            sections.section_name,                
            class_masters.class_name,
            students.admission_no,CONCAT(students.first_name,' ',students.middle_name,' ',students.last_name) as full_name,                
          CASE WHEN students.status = '0' THEN 'Deactivated'  
          WHEN students.status = '1' THEN 'Active'
          END as status,
          TO_CHAR(fee_demands.created_at::date,'dd-mm-yyyy') as date,
          TO_CHAR(fee_demands.created_at,'HH12:MI:SS AM') as time
          ")
        )->get();
        $selectMonth = Config::get("month");
        // dd($selectData);

        $selectFeeHead = $from->select(
            DB::raw(" Distinct(fee_heads.fee_head) AS fee_head 
          ")
        )->get();

        // print_var($selectFeeHead);
        // die;       
        // dd(DB::getQueryLog());

        $data["monthly_fee"] = collect($selectMonth)->map(function ($val, $key) use ($selectData, $selectFeeHead) {
            $monthName = $val;
            $testData = $selectData->where("month_name", $monthName);
            $finalData["month_name"] = $monthName;
            $finalData["fee"] = (array)null;
            $finalData["total"] = $testData->sum("amount");

            foreach ($selectFeeHead as $val1) {
                if ($testData->where("fee_head", $val1->fee_head)->sum("amount") != 0) {
                    $finalData["fee"][] = [
                        "amount" => $testData->where("fee_head", $val1->fee_head)->sum("amount"),
                        "fee_head" => $val1->fee_head,
                        "fee_ids" => $testData->where("fee_head", $val1->fee_head)->pluck("fee_head_id")->implode(","),
                    ];
                }
            }
            return $finalData;
        });

        $data["grandTotal"] = $data["monthly_fee"]->sum("total");
        $data["studentDetails"] = [
            "sectionName" => ($selectData[0]->section_name) ?? "",
            "className" => ($selectData[0]->class_name) ?? "",
            "admissionNo" => ($selectData[0]->admission_no) ?? "",
            "fullName" => ($selectData[0]->full_name) ?? "",
            "discount_percent" => ($selectData[0]->discount_percent) ?? "",
            "status" => ($selectData[0]->status) ?? "",
        ];
        return collect($data);
    }
}
