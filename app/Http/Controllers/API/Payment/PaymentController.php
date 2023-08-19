<?php

namespace App\Http\Controllers\API\Fee;

use App\Http\Controllers\Controller;
use App\Models\Fee\FeeCollection;
use App\Models\Payment\Payment;
use App\Models\Student\Student;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    //global variable 
    private $_mPayments;

    public function __construct()
    {
        $this->_mPayments = new Payment();
    }

    //View All
    // public function retrieveAll(Request $req)
    // {
    //     try {
    //         $marksEntry = $this->_mPayments->retrieve();
    //         return responseMsgs(true, "", $marksEntry, "", "13.2", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "13.2", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

    // Add records
    // public function store(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'admissionNo' => "required|string",
    //         'feeCollection.*.monthName' => 'required|string',
    //         "feeCollection.*.totalFee" => "required|numeric",
    //         'feeCollection.*.grandTotal' => 'required|numeric',
    //         'feeCollection.*.isPaid' => "required|boolean",
    //         'feeCollection.*.paymentDate' => 'required|date',
    //         'feeCollection.*.paymentModeId' => 'required|numeric'

    //     ]);
    //     if ($validator->fails())
    //         return responseMsgs(false, $validator->errors(), []);
    //     try {
    //         $data = array();
    //         $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
    //         DB::beginTransaction();
    //         $mStudents = Student::where('admission_no', $req->admissionNo)
    //             ->where('status', 1)
    //             ->first();
    //         if (collect($mStudents)->isEmpty())
    //             throw new Exception('Admission no is not existing');
    //         $studentId  = $mStudents->id;
    //         foreach ($req['feeCollection'] as $ob) {
    //             // Gettting feeCollection Id
    //             $mFees = FeeCollection::where('student_id', $studentId)
    //                 ->where('month_name', $ob['monthName'])
    //                 ->where('status', 1)
    //                 ->first();
    //             if ($mFees) {
    //                 $FeeCollectionId  = $mFees->id;
    //                 $isExists = $this->_mPayments->readPaymentGroup($FeeCollectionId, $studentId, $fy, $ob);
    //                 if (collect($isExists)->isNotEmpty())
    //                     throw new Exception("Payment Already Existing");
    //             }
    //             $fee = new FeeCollection();
    //             $fee["month_name"] =  $ob['monthName'];
    //             $fee['student_id'] = $studentId;
    //             $fee["total_fee"] =  $ob['totalFee'];
    //             $fee["grand_total"] =  $ob['grandTotal'];
    //             $fee['academic_year'] = $fy;
    //             $fee['ip_address'] = getClientIpAddress();
    //             $fee['school_id'] = authUser()->school_id;
    //             $fee['created_by'] = authUser()->id;
    //             $fee['json_logs'] = "ABC";
    //             $fee->save();

    //             $pay = new Payment();
    //             $pay['student_id'] = $studentId;
    //             $pay["fee_collection_id"] =  $fee->id;
    //             $pay["is_paid"] =  $ob['isPaid'];
    //             $pay['payment_date'] = $ob['paymentDate'];
    //             $pay['payment_mode_id'] = $ob['paymentModeId'];
    //             $pay['academic_year'] = $fy;
    //             $pay['ip_address'] = getClientIpAddress();
    //             $pay['school_id'] = authUser()->school_id;
    //             $pay['created_by'] = authUser()->id;
    //             $pay['json_logs'] = "ABC";
    //             $pay->save();
    //         }
    //         // DB::rollback();
    //         DB::commit();
    //         // dd();
    //         return responseMsgs(true, "Successfully Saved", [], "", "13.1", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "13.1", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }


}
