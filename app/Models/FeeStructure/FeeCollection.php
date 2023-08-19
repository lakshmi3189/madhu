<?php

namespace App\Models\FeeStructure;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

use function PHPUnit\Framework\isEmpty;

class FeeCollection extends Model
{
  use HasFactory;
  protected $guarded = [];

  /*Add Records*/
  public function store(array $req)
  {
    FeeCollection::create($req);
  }

  public function getReceiptNoExist($req)
  {
    return FeeCollection::where(DB::raw('upper(receipt_no)'), strtoupper($req->receiptNo))->get();
    // return FeeCollection::where('receipt_no', $req->receiptNo)->get();
  }


  public function readFeeCollGroup($feeData, $req, $studentFY)
  // public function readFeeCollGroup($feeData, $studentId, $studentFY)
  {
    return  FeeCollection::where('month_name', $feeData['monthName'])
      ->where('admission_no', $req->admissionNo)
      ->where('academic_year', $studentFY)
      ->where('class_name', $feeData['className'])
      // ->where('month_id', $feeData['monthId'])
      ->where('fee_head_name', $feeData['feeHeadName'])
      // ->where('status', 1)
      ->get();
    // echo 'ok';
    // print_var($rt);
    // die;
  }

  /*Read Records by name*/
  public function readAllFeeCollectionGroup($ob, $studentId, $studentFY = null)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return FeeCollection::where('month_name', $ob['monthName'])
      ->where('student_id', $studentId)
      // ->where('academic_year', $studentFY)
      // ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->where('status', 1)
      ->get();
  }

  public function readFeeCollectionGroup($req, $stdId, $fy = null)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return FeeCollection::where('month_name', $req->monthName)
      ->where('student_id', $stdId)
      // ->where('academic_year', $fy)
      // ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->where('status', 1)
      ->get();
  }

  //Get Records by name
  public function searchByName($name)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return FeeCollection::select(
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
      ->where('month_name', 'ilike', $name . '%')
      // ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      // ->where('status', 1)
      ->get();
  }

  /*Read Records by ID*/
  public function getGroupById($id)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return FeeCollection::select(
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
      // ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      // ->where('status', 1)
      ->first();
  }



  /*Read Records by ID*/
  public function getGroupByReceipt($req)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;

    return DB::table('fee_collections as a')
      ->select(
        DB::raw("a.month_name, a.total_fee, a.fee_head_name,a.fee_amount,a.payment_mode_name,                
        b.admission_no,b.roll_no, 
        c.class_name, 
        d.section_name,        
        e.payment_date, 
        g.fee_head,
        CASE WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        ")
      )

      ->join('students as b', 'b.id', '=', 'a.student_id')
      ->join('class_masters as c', 'c.id', '=', 'b.class_id')
      ->leftJoin('section_group_maps as d', 'd.id', '=', 'b.section_id')
      ->join('payments as e', 'e.student_id', '=', 'a.student_id')
      ->join('class_fee_masters as f', 'f.class_id', '=', 'b.class_id')
      ->join('fee_heads as g', 'g.id', '=', 'f.fee_head_id')
      // ->where('a.school_id', $schoolId)
      ->orderBy('a.month_name')
      // ->where('a.status', 1)
      ->get();



    // return FeeCollection::select(
    //   DB::raw("*,
    //   CASE 
    //     WHEN status = '0' THEN 'Deactivated'  
    //     WHEN status = '1' THEN 'Active'
    //   END as status,
    //   TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
    //   TO_CHAR(created_at,'HH12:MI:SS AM') as time
    // ")
    // )
    //   ->join('students as c', 'c.id', '=', 'a.student_id')
    //   ->where('receipt_no', $req->receiptNo)
    //   ->where('school_id', $schoolId)
    //   // ->where('created_by', $createdBy)
    //   // ->where('status', 1)
    //   ->get();
  }

  /*Read all Records by*/
  public function retrieve()
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return FeeCollection::select(
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
      // ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->orderByDesc('id')
      ->get();
  }


  /*Read all Active Records*/
  public function active()
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return FeeCollection::select(
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
      // ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->orderByDesc('id')
      ->get();
  }

  /* Get all monthly fees receipt wise  */
  public function getGroupByReceiptTest($req)
  {
    $data = array();
    $schoolId = authUser()->school_id;

    $from = DB::table('fee_collections AS fee_collections')
      ->join('fee_demands as fee_demands', 'fee_demands.student_id', '=', 'fee_collections.student_id')
      ->join('class_fee_masters as class_fee_masters', 'class_fee_masters.id', '=', 'fee_demands.class_fee_master_id')
      ->join('students as students', 'students.id', '=', 'fee_demands.student_id')
      // ->join('class_masters as class_masters', 'class_masters.id', '=', 'class_fee_masters.class_id')
      // ->leftJoin('section_group_maps as sections', 'sections.id', '=', 'class_fee_masters.section_id')
      // ->join('fee_heads as fee_heads', 'fee_heads.id', '=', 'fee_demands.fee_head_id')
      ->join('payments as payments', 'payments.student_id', '=', 'fee_collections.student_id')
      ->where('fee_collections.receipt_no', $req->receiptNo);
    // ->where('fee_collections.school_id', $schoolId);

    $selectData = $from->select(
      DB::raw("fee_collections.month_name,fee_collections.total_fee,fee_collections.fee_head_name,
      fee_collections.fee_amount,fee_collections.payment_mode_name,fee_collections.class_name,
          -- fee_heads.fee_head,
          -- sections.section_name,                
          -- class_masters.class_name,
          students.admission_no,CONCAT(students.first_name,' ',students.middle_name,' ',students.last_name) as full_name,                
          CASE WHEN students.status = '0' THEN 'Deactivated'  
          WHEN students.status = '1' THEN 'Active'
          END as status,
          CASE WHEN fee_collections.is_paid = '0' THEN 'No'  
          WHEN fee_collections.is_paid = '1' THEN 'Yes'
          END as is_paid,
          TO_CHAR(fee_collections.created_at::date,'dd-mm-yyyy') as date,
          TO_CHAR(fee_collections.created_at,'HH12:MI:SS AM') as time
      ")
    )->get();
    $selectMonth = $from->select(
      DB::raw(" Distinct(fee_collections.month_name) AS month_name 
        ")
    )->get();
    // dd($selectMonth);

    // $selectMonth = Config::get("month");
    // dd($selectMonth);

    $selectFeeHead = $from->select(
      DB::raw(" Distinct(fee_heads.fee_head) AS fee_head 
        ")
    )->get();
    // print_var($selectFeeHead);
    // die;
    // dd(DB::getQueryLog());

    $data["studentDetails"] = [
      "fullName" => ($selectData[0]->full_name) ?? "",
      "admissionNo" => ($selectData[0]->admission_no) ?? "",
      "className" => ($selectData[0]->class_name) ?? "",
      "sectionName" => ($selectData[0]->section_name) ?? "",
      "rollNo" => ($selectData[0]->roll_no) ?? "",
      "status" => ($selectData[0]->status) ?? "",
    ];
    $data["feeCollection"] = collect($selectMonth)->map(function ($val, $key) use ($selectData, $selectFeeHead) {
      $monthName = $val->month_name;
      $testData = $selectData->where("month_name", $monthName);
      $finalData["month_name"] = $monthName;
      $finalData["paymentStatus"] = $testData->where("is_paid", "No")->isEmpty() ? "Yes" : "No"; //is_paid
      $finalData["fee"] = (array)null;
      $finalData["total"] = $testData->sum("fee_amount");
      // $finalData["total"] = $testData->sum("total_fee");
      // $finalData["total"] = $testData->sum("total_fee");
      foreach ($selectFeeHead as $val1) {
        if ($testData->where("fee_head", $val1->fee_head)->sum("total_fee") != 0) {
          $finalData["fee"][] = [
            "amount" => $testData->where("fee_head", $val1->fee_head)->sum("total_fee"),
            "fee_head" => $val1->fee_head,
            "fee_ids" => $testData->where("fee_head", $val1->fee_head)->pluck("fee_head_id")->implode(","),
          ];
        }
      }
      return $finalData;
    });

    $data["grandTotal"] = $data["feeCollection"]->sum("total");

    return collect($data);
  }
}
