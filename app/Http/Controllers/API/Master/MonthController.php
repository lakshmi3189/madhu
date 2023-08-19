<?php

namespace App\Http\Controllers\api\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Month;
use Illuminate\Http\Request;
// use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;


class MonthController extends Controller
{
    //global variable
    private $_months;

    public function __construct()
    {
        $this->_months = new Month();
    }


    //View All Month By JSON File
    public function retrieveAll1(Request $req)
    {
        try {
            $file = storage_path() . "/local-database/months.json";
            //return file_get_contents($file);
            $months = file_get_contents($file);
            return responseMsgs(true, "", $months, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");  // doesn't need to return full response
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //View All Month By Table
    public function retrieveAll(Request $req)
    {
        try {
            $getAll = $this->_months->retrieve();
            return responseMsgs(true, "", $getAll, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
}
