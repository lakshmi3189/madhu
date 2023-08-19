<?php

namespace App\Http\Controllers\API\FeeStructure;

use App\Http\Controllers\Controller;
use App\Models\FeeStructure\FeeCollection;
use App\Models\Student\Student;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\Master\ReceiptCounter;
use DB;


class FeeCollectionController extends Controller
{
    /**
     * | Created On-27-06-2023 
     * | Created By- Lakshmi Kumari 
     * | Code Status : Open 
     */

    private $_mFeeCollections;
    private $_mReceiptCounters;

    public function __construct()
    {
        DB::enableQueryLog();
        $this->_mFeeCollections = new FeeCollection();
        $this->_mReceiptCounters = new ReceiptCounter();
    }
    // Add records
    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'admissionNo' => 'required',
            'paymentModeId' => 'required|numeric',
            'grandTotal' => 'required|numeric',
            'paymentDate' => 'required|string',
            'isPaid' => 'required|numeric',
            'feeCollection' => 'required|array',
            // 'feeCollection.*.admissionNo' => 'required',
            'feeCollection.*.monthName' => 'required',
            // 'feeCollection.*.totalFee' => 'required|numeric',
            'feeCollection.*.isPaid' => 'required|numeric',
            'feeCollection.*.paymentDate' => 'required|string',
            'feeCollection.*.paymentModeId' => 'required|numeric',
            'feeCollection.*.className' => 'string',
            'feeCollection.*.paymentMode' => 'string',
            'feeCollection.*.feeHeadId' => 'numeric',
            'feeCollection.*.feeHeadName' => 'string',
            'feeCollection.*.feeAmount' => 'numeric'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            DB::beginTransaction();
            $data = array();
            $receipt = $this->_mReceiptCounters->generateReceiptNumber();
            $mStudents = Student::where('admission_no', $req->admissionNo)
                ->where('status', 1)
                ->first();
            if (collect($mStudents)->isEmpty())
                throw new Exception('Admission no is not existing');
            $studentId  = $mStudents->id;
            $studentFY  = $mStudents->academic_year;
            if ($req['feeCollection'] != "") {
                foreach ($req['feeCollection'] as $feeData) {
                    $isGroupExists = $this->_mFeeCollections->readFeeCollGroup($feeData, $req, $studentFY);
                    if (collect($isGroupExists)->isNotEmpty())
                        throw new Exception("Fee Already Existing");
                    $feeCollection = new FeeCollection();
                    $feeCollection->student_id = $studentId;
                    $feeCollection->month_name = $feeData['monthName'];
                    $feeCollection->admission_no = $req->admissionNo;
                    $feeCollection->class_name = $feeData['className'];
                    $feeCollection->payment_mode_name = $feeData['paymentMode'];
                    $feeCollection->fee_head_id = $feeData['feeHeadId'];
                    $feeCollection->fee_head_name = $feeData['feeHeadName'];
                    $feeCollection->fee_amount = $feeData['feeAmount'];
                    // $feeCollection->admission_no = Str::title($req['admissionNo']);
                    // $feeCollection->total_fee = $feeData['totalFee'];
                    // $feeCollection->grand_total = $feeData['grandTotal'];                   
                    // $feeCollection->discount_description = $feeData['discountDescription'];
                    // $feeCollection->payment_mode = $feeData['paymentMode'];
                    $feeCollection->is_paid = $feeData['isPaid'];
                    $feeCollection->payment_mode_id = $feeData['paymentModeId'];
                    $feeCollection->payment_date = $feeData['paymentDate'];
                    $feeCollection->academic_year = $studentFY;
                    $feeCollection->receipt_no = $receipt;
                    $feeCollection->ip_address = getClientIpAddress();
                    $feeCollection->created_by = authUser()->id;
                    $feeCollection->school_id = authUser()->school_id;
                    $feeCollection->json_logs = trim($feeCollection->json_logs . "," . json_encode($feeCollection), ",");
                    // print_var($feeCollection);
                    // die;
                    $feeCollection->save();
                }
            }
            $data = ['receiptNo' => $receipt];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            DB::commit();
            return responseMsgs(true, "Successfully Saved", $data, "", "API_15.1", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            DB::rollback();
            return responseMsgs(false, $e->getMessage(), [], "API_15.1", "", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //show data by receipt no 
    public function showByReceiptNo(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'receiptNo' => 'required|string'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $show = $this->_mFeeCollections->getReceiptNoExist($req);
            if (collect($show)->isEmpty())
                throw new Exception("Data Not Found");

            $receiptNo = $req->input('receiptNo');

            // Fetch the feeCollection data based on the provided receipt number
            $feeCollection = FeeCollection::where('receipt_no', $receiptNo)->get();

            // Check if data is found and return the response accordingly
            if ($feeCollection->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No data found for the provided receipt number.',
                    'data' => []
                ]);
            }
            $studentId = $feeCollection->first()->student_id;
            $student = Student::select('admission_no', 'first_name', 'class_name', 'section_name', 'roll_no')->where('id', $studentId)->first();

            // Group the feeCollection data by "month_name"
            $monthWiseData = $feeCollection->groupBy('month_name')->map(function ($monthData) {
                // $tt = 0;
                $totalFees = $monthData->sum('fee_amount');
                // $tt += $totalFees;
                // $totalFees = $monthData->sum('total_fee');
                // $totalRecFees = $monthData->sum('received_amount');
                // $discount = $monthData->first()->discount;
                // $netPay = ($totalRecFees - (($totalRecFees * $discount) / 100));
                // $totalDueFees = $monthData->sum('due_amount');
                $paymentDate = Carbon::parse($monthData->first()->payment_date)->format('d-m-y');
                // $monthPaid = $monthData->first()->month_name;
                // $isPaid = $monthData->first()->is_paid;
                // $isMonth = $monthData->first()->is_month;
                $details = $monthData->map(function ($item) {
                    return [
                        // 'amount' => $item->total_fee,
                        'amount' => $item->fee_amount,
                        // 'inWords' => getIndianCurrency($item->fee_amount),
                        'feeHeadName' => $item->fee_head_name,
                        // 'amount' => $item->fee_amount,
                        // 'receivedAmount' => $item->received_amount,
                        // 'dueAmount' => $item->due_amount,
                    ];
                });
                return [
                    'monthName' => $monthData->first()->month_name,
                    'totalFees' => $totalFees,

                    // 'receivedAmount' => $totalRecFees,
                    // 'dueAmount' => $totalDueFees,
                    'paymentDate' => $paymentDate,
                    // 'discount' => $discount,
                    // 'netPay' => $netPay,
                    // 'monthPaid' => $monthPaid,
                    // 'isPaid' => $isPaid,
                    // 'isMonthPresent' => $isMonth,
                    'details' => $details->toArray(),
                ];
            });
            $result["stdDetails"] = $student;
            $result["feeDetails"] = $monthWiseData->values()->toArray();
            // $result["total"] = getIndianCurrency($tt);
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgs(true, "View All Records", $result, "", "API_15.2",  $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "API_15.2", "", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    // search fees by admission no
    public function searchFeesByAdmNo(Request $req)
    {
        //Description: Get records by id
        $validator = Validator::make($req->all(), [
            'admissionNo' => 'required|string'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $mStudents = Student::where('admission_no', $req->admissionNo)
                ->where('status', 1)
                ->first();
            if (collect($mStudents)->isEmpty())
                throw new Exception('Admission no is not existing');
            $studentId  = $mStudents->id;
            $msg = '';
            $data = $this->_mFeeCollections::select('id', 'student_id', 'month_name', 'is_paid', 'receipt_no')
                ->where([['student_id', '=', $studentId], ['status', '=', '1']])->get();

            if ($data != "") {
                $msg = "Fee Already Existing";
                $data1 = $data;
            } else {
                $msg = "Fees Not Found";
                $data1 = ['admission_no' => $req->admissionNo, 'message' => 'Admission No. not found', 'value' => 'false'];
            }
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgs(true, $msg, $data1, "API_15.3", "", $queryTime, responseTime(), "post", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "API_15.3", "",  $queryTime, responseTime(), "post", $req->deviceId ?? "");
        }
    }



    // Edit records
    // public function edit(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'id' => 'numeric',
    //         'admissionNo' => "required|string",
    //         'monthName' => 'required|string',
    //         "totalFee" => "required|numeric",
    //         'grandTotal' => 'required|numeric'
    //     ]);
    //     if ($validator->fails())
    //         return responseMsgs(false, $validator->errors(), []);
    //     try {
    //         $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
    //         $mStudents = Student::where('admission_no', $req->admissionNo)
    //             ->where('status', 1)
    //             ->first();
    //         if (collect($mStudents)->isEmpty())
    //             throw new Exception('Admission no is not existing');
    //         $studentId  = $mStudents->id;
    //         $isExists = $this->_mFeeCollections->readFeeCollectionGroup($req, $studentId, $fy);
    //         if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
    //             throw new Exception("Fee Collection Already existing");
    //         $getData = $this->_mFeeCollections::findOrFail($req->id);
    //         $metaReqs = [
    //             'student_id' => $studentId,
    //             'month_name' => $req->monthName,
    //             'total_fee' => $req->totalFee,
    //             'grand_total' => $req->grandTotal,
    //             'academic_year' => $fy,
    //             'version_no' => $getData->version_no + 1,
    //             'updated_at' => Carbon::now()
    //         ];
    //         $metaReqs = array_merge($metaReqs, [
    //             'json_logs' => trim($getData->json_logs . "," . json_encode($metaReqs), ",")
    //         ]);
    //         if (isset($req->status)) {              // In Case of Deactivation or Activation 
    //             $status = $req->status == 'deactive' ? 0 : 1;
    //             $metaReqs = array_merge($metaReqs, [
    //                 'status' => $status
    //             ]);
    //         }
    //         $editData = $this->_mFeeCollections::findOrFail($req->id);
    //         $editData->update($metaReqs);
    //         return responseMsgs(true, "Successfully Updated", [$metaReqs], "", "API_15.2", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "API_15.2", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

    /**
     * | Get Discont Group By Id
     */
    // public function show(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'id' => 'required|numeric'
    //     ]);
    //     if ($validator->fails())
    //         return responseMsgs(false, $validator->errors(), []);
    //     try {
    //         $show = $this->_mFeeCollections->getGroupById($req->id);
    //         if (collect($show)->isEmpty())
    //             throw new Exception("Data Not Found");
    //         return responseMsgs(true, "", $show, "", "API_15.3", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "API_15.3", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

    //View All
    // public function retrieveAll(Request $req)
    // {
    //     try {
    //         $getData = $this->_mFeeCollections->retrieve();
    //         return responseMsgs(true, "", $getData, "", "API_15.4", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "API_15.4", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

    //Delete
    // public function delete(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'status' => 'required|in:active,deactive'
    //     ]);
    //     if ($validator->fails())
    //         return responseMsgs(false, $validator->errors(), []);
    //     try {
    //         if (isset($req->status)) {                  // In Case of Deactivation or Activation 
    //             $status = $req->status == 'deactive' ? 0 : 1;
    //             $metaReqs =  [
    //                 'status' => $status
    //             ];
    //         }
    //         $delete = $this->_mFeeCollections::findOrFail($req->id);
    //         //  if ($teachingTitle->status == 0)
    //         //      throw new Exception("Records Already Deleted");
    //         $delete->update($metaReqs);
    //         return responseMsgs(true, "Deleted Successfully", [], "", "API_15.5", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "API_15.5", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }




    /**
     * | show fees by receipt no
     */
    public function showReceipt(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'receiptNo' => 'required|string'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $show = $this->_mFeeCollections->getGroupByReceipt($req);
            if (collect($show)->isEmpty())
                throw new Exception("Data Not Found");
            return responseMsgs(true, "", $show, "", "API_15.9", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_15.9", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    /**
     * | show fees by receipt no
     */
    public function showReceiptTest(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'receiptNo' => 'required|string'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $show = $this->_mFeeCollections->getGroupByReceiptTest($req);
            if (collect($show)->isEmpty())
                throw new Exception("Data Not Found");
            return responseMsgs(true, "", $show, "", "API_15.9", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_15.9", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
}
