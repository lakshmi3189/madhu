<?php

namespace App\Http\Controllers\API\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\FinancialYear;
use DB;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class FinancialYearController extends Controller
{
    //View All
    public function retrieveAll(Request $req)
    {
        return DB::table('financial_years')->select('id', 'fy')->orderByDesc('id')->get();
    }

    //View All
    public function retrieveAll2(Request $req)
    {
        try {
            $file = storage_path() . "/local-database/financialYear.json";
            return file_get_contents($file);

            return responseMsgs(true, "", $file, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //old code
    // private $_mFinancialYear;

    // public function __construct()
    // {
    //     $this->_mFinancialYear = new FinancialYear();
    // }
    // /**
    //  * | Created On-26-05-2023 
    //  * | Created By- Lakshmi Kumari
    //  * | Financial Year Crud Operations
    // */

    // // Add records
    // public function store(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'fyName' => 'required|string'
    //     ]);
    //     if ($validator->fails())
    //         return responseMsgs(false, $validator->errors(), []);

    //     try {
    //         $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
    //         $isExists = $this->_mFinancialYear->readFeeHeadTypeGroup($req->fyName);
    //         if (collect($isExists)->isNotEmpty())
    //             throw new Exception("Financial Year Already Existing!");

    //         $metaReqs=[
    //             'fy_name' => $req->fyName,
    //             'school_id' => authUser()->school_id,
    //             'created_by' => authUser()->id,
    //             'ip_address' => getClientIpAddress()
    //         ];
    //         $this->_mFinancialYear->store($metaReqs);
    //         return responseMsgs(true, "Successfully Saved", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }


    // // Edit records
    // public function edit(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'id' => 'required|numeric',
    //         'feeHeadType' => 'required|string',
    //         'isAnnual' => 'required|numeric',
    //         'isOptional' => 'required|numeric',
    //         'isLateFineApplicable' => 'required|numeric',
    //         'status' => 'nullable|in:active,deactive'
    //     ]);
    //     if ($validator->fails())
    //         return responseMsgs(false, $validator->errors(), []);
    //     try {
    //         $isExists = $this->_mFinancialYear->readFeeHeadTypeGroup($req->feeHeadType);
    //         if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
    //             throw new Exception("Fee Head Type Already Existing");
    //         $getData = $this->_mFinancialYear::findOrFail($req->id);            
    //         $metaReqs = [ 
    //             'fee_head_type' => $req->feeHeadType,
    //             'is_annual' => $req->isAnnual,
    //             'is_optional' => $req->isOptional,
    //             'is_latefee_applicable' => $req->isLateFineApplicable,
    //             'version_no' => $getData->version_no + 1,
    //             'updated_at' => Carbon::now()
    //         ];

    //         if (isset($req->status)) {                  // In Case of Deactivation or Activation 
    //             $status = $req->status == 'deactive' ? 0 : 1;
    //             $metaReqs = array_merge($metaReqs, [
    //                 'status' => $status
    //             ]);
    //         }

    //         $feeHeadType = $this->_mFinancialYear::findOrFail($req->id);
    //         $feeHeadType->update($metaReqs);
    //         return responseMsgs(true, "Successfully Updated", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

    // //View by id
    // public function show(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'id' => 'required|numeric'
    //     ]);
    //     if ($validator->fails())
    //         return responseMsgs(false, $validator->errors(), []);
    //     try {
    //         $feeHeadType = $this->_mFinancialYear::findOrFail($req->id);
    //         return responseMsgs(true, "", $feeHeadType, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

    // //View All
    // public function retrieveAll(Request $req)
    // {
    //     try {
    //         $feeHeadType = $this->_mFinancialYear::orderByDesc('id')->where('status','1')->get();
    //         return responseMsgs(true, "", $feeHeadType, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

    // //Delete
    // public function delete(Request $req)
    // {
    //     try { 
    //         $metaReqs = [
    //             'status' => $req->status
    //         ];
    //     $feeHeadType = $this->_mFinancialYear::findOrFail($req->id);
    //     if($feeHeadType->status==0)
    //         throw new Exception("Records Already Deleted");
    //     $feeHeadType->update($metaReqs);
    //     return responseMsgs(true, "Deleted Successfully", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }        
    // }


}
