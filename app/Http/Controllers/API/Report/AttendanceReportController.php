<?php

namespace App\Http\Controllers\API\Report;

use App\Http\Controllers\Controller;
use App\Models\Report\AttendanceReport;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

class AttendanceReportController extends Controller
{
    /**
     * | Created On-31-07-2023 
     * | Created By- Umesh Kumar
     * | Code Status - Open
     */

     private $_mAttendanceReports;

     public function __construct()
     {
         DB::enableQueryLog();
         $this->_mAttendanceReports = new AttendanceReport();
     }

    //View All
    public function retrieveAll(Request $req)
    {
        $validator = Validator::make($req->all(), [
             'fy'            => 'required',
             'classId'     => 'required|numeric',
             'sectionId'       => 'required|numeric',
             'date'          => 'required|date'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $getData = $this->_mAttendanceReports->retrieve($req);
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
