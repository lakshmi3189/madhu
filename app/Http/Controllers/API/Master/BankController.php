<?php

namespace App\Http\Controllers\api\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

use App\Models\Master\Bank;
use Exception;
use DB;

/**
 * | Created On- 16-06-2023 
 * | Created By- Lakshmi Kumari
 * | Description- Bank CRUDS Operations
 * | Code Status- Closed
 */
class BankController extends Controller
{
    //global variable
    private $_mBanks;

    public function __construct()
    {
        DB::enableQueryLog();
        $this->_mBanks = new Bank();
    }

    // Add records
    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'bankName' => 'required|string',  //regex:/^[a-zA-z]+$/',
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $isGroupExists = $this->_mBanks->readBankGroup($req);
            if (collect($isGroupExists)->isNotEmpty())
                throw new Exception("Bank Name Already Existing");
            $metaReqs = [
                'bank_name' => Str::title($req->bankName),
                // 'school_id' => authUser()->school_id,
                'created_by' => authUser()->id,
                'ip_address' => getClientIpAddress(),
                'version_no' => 0
            ];
            $metaReqs = array_merge($metaReqs, [
                'json_logs' => trim(json_encode($metaReqs), ",")
            ]);
            $this->_mBanks->store($metaReqs);
            $data = ['Bank Name' => $req->bankName];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Records Added Successfully", $data, "M_API_16.1", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_16.1", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    // Edit records
    public function edit(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'id' => 'required|numeric',
            'bankName' => 'required|string',  //regex:/^[a-zA-z]+$/',

        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $isExists = $this->_mBanks->readBankGroup($req);
            if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
                throw new Exception("Bank Name Already Existing");
            $getData = $this->_mBanks::findOrFail($req->id);
            $metaReqs = [
                'bank_name' => Str::title($req->bankName),
                'version_no' => $getData->version_no + 1,
                'updated_at' => Carbon::now()
            ];
            $metaReqs = array_merge($metaReqs, [
                'json_logs' => trim($getData->json_logs . "," . json_encode($metaReqs), ",")
            ]);
            if (isset($req->status)) {                  // In Case of Deactivation or Activation 
                $status = $req->status == 'deactive' ? 0 : 1;
                $metaReqs = array_merge($metaReqs, [
                    'status' => $status
                ]);
            }
            $getData->update($metaReqs);
            $data = ['Bank Name' => $req->bankName];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Records Updated Successfully", $data, "M_API_16.2", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_16.2", responseTime(), "POST", $req->deviceId ?? "");
        }
    }


    //show data by id
    public function show(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'id' => 'required|numeric'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $show = $this->_mBanks->getGroupById($req->id);
            if (collect($show)->isEmpty())
                throw new Exception("Data Not Found");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View Records", $show, "M_API_16.3", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_16.3", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
    //View All
    public function retrieveAll(Request $req)
    {
        try {
            $getData = $this->_mBanks->retrieve();
            $perPage = $req->perPage ? $req->perPage : 10;
            $paginater = $getData->paginate($perPage);
            // if ($paginater == "")
            //     throw new Exception("Data Not Found");
            $list = [
                "current_page" => $paginater->currentPage(),
                "perPage" => $perPage,
                "last_page" => $paginater->lastPage(),
                "data" => $paginater->items(),
                "total" => $paginater->total()
            ];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View All Records", $list, "M_API_16.4", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_16.4", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //Activate / Deactivate
    public function delete(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'status' => 'required|in:active,deactive'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            if (isset($req->status)) {                  // In Case of Deactivation or Activation 
                $status = $req->status == 'deactive' ? 0 : 1;
                $metaReqs =  [
                    'status' => $status
                ];
            }
            $delete = $this->_mBanks::findOrFail($req->id);
            $delete->update($metaReqs);
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Changes Done Successfully", $req->status, "M_API_16.5", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_16.5", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //Active All
    public function activeAll(Request $req)
    {
        try {
            $getData = $this->_mBanks->active();
            if (collect($getData)->isEmpty())
                throw new Exception("Data Not Found");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View All Active Records", $getData, "M_API_16.6", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_16.6", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //search by name
    public function search(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'search' => 'required|string'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $getData = $this->_mBanks->searchByName($req);
            $perPage = $req->perPage ? $req->perPage : 10;
            $paginater = $getData->paginate($perPage);
            // if ($paginater == "")
            //     throw new Exception("Data Not Found");
            $list = [
                "current_page" => $paginater->currentPage(),
                "perPage" => $perPage,
                "last_page" => $paginater->lastPage(),
                "data" => $paginater->items(),
                "total" => $paginater->total()
            ];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View Searched Records", $list, "M_API_16.7", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_16.7", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
}
