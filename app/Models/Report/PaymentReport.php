<?php

namespace App\Models\Report;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentReport extends Model
{
    use HasFactory;

    public function retrieve($req)
    {
        return DB::table('students as a')
        ->select(
        DB::raw("a.admission_no,a.admission_date as registration_date, a.first_name as full_name, a.roll_no, a.class_name, b.month_name,  

        CASE 
        WHEN a.category_name IS NULL THEN 'NA'
        WHEN a.category_name IS NOT NULL THEN a.category_name
        END as category_name,

        CASE 
        WHEN b.is_paid = '0' THEN 'Unpaid'  
        WHEN b.is_paid = '1' THEN 'Paid'
        END as is_paid,

        CASE 
        WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        "),
      )
      ->join('fee_collections as b','b.student_id','=','a.id')
      ->where('a.class_id', $req->classId)
      ->where('b.is_paid', $req->isPaid)
    //   ->where('b.academic_year', $req->fy)
      ->orderBy('a.id');
    //   ->where('a.status', 1);
      // ->get();
    }
}
