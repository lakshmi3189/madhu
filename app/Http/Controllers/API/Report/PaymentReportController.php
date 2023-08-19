<?php

namespace App\Http\Controllers\API\Report;

use App\Http\Controllers\Controller;
use App\Models\Report\PaymentReport;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentReportController extends Controller
{
    //
    /**
     * | Created On-31-07-2023 
     * | Created By- Umesh Kumar
     * | Code Status - Open
     */

     private $_mPaymentReports;

     public function __construct()
     {
         DB::enableQueryLog();
         $this->_mPaymentReports = new PaymentReport();
     }

    //View All
    public function retrieveAll(Request $req)
    {
        $validator = Validator::make($req->all(), [
             'fy'            => 'required',
             'classId'     => 'required|numeric',
             'isPaid'          => 'required'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $getData = $this->_mPaymentReports->retrieve($req);
            $perPage = $req->perPage ? $req->perPage : 10;
            $paginater = $getData->paginate($perPage);
            // if (isEmpty($paginater))
            //     throw new Exception("Data Not Found");
            $list = [
                "current_page" => $paginater->currentPage(),
                "perPage" => $perPage,
                "last_page" => $paginater->lastPage(),
                "data" => $paginater->items(),
                "total" => $paginater->total()
            ];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View All Records", $list, "API_4.1", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_4.1", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
}
