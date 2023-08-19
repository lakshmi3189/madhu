<?php

namespace App\Http\Controllers\API\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;


class MiscellaneousController extends Controller
{
    //global variable
    //private $_miscellaneous;

    //View All
    public function retrieveAll(Request $req)
    {
        try {
            $file = storage_path() . "/local-database/miscellaneous.json";
            return file_get_contents($file);
            // $Banks = $this->_months::orderByDesc('id')->where('status', '1')->get();
            // return responseMsgs(true, "", $file, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
}
