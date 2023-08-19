<?php

namespace App\Http\Controllers\API\Master;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class IconController extends Controller
{
    //
    //global variable
    //private $_months;

    //View All
    public function retrieveAll(Request $req)
    {
        try {
            $file = storage_path() . "/local-database/icon.json";
            return file_get_contents($file);
            // return responseMsgs(true, "", $file, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
}
