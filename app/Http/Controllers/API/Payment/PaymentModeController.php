<?php

namespace App\Http\Controllers\API\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment\PaymentMode;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class PaymentModeController extends Controller
{
    /**
     * | Created On-28-06-2023 
     * | Created By- Umesh Kumar
     * | Code Status : Open 
     */

    private $_mPaymentModes;

    public function __construct()
    {
        $this->_mPaymentModes = new PaymentMode();
    }
    // Add records
    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'paymentModeName' => 'required|string'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $isExists = $this->_mPaymentModes->readPaymentModeGroup($req->paymentModeName);
            if (collect($isExists)->isNotEmpty())
                throw new Exception("Payment Mode Already Existing");
            $metaReqs = [
                'payment_mode_name' => $req->paymentModeName,
                'ip_address' => getClientIpAddress(),
                'version_no' => 0
            ];
            $metaReqs = array_merge($metaReqs, [
                'json_logs' => trim(json_encode($metaReqs), ",")
            ]);
            // return $metaReqs; die; 
            $this->_mPaymentModes->store($metaReqs);
            $data1 = ['Payment Mode' => $req->paymentModeName];
            return responseMsgs(true, "Successfully Saved", $data1, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    // Edit records
    public function edit(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'id' => 'numeric',
            'paymentModeName' => 'required|string'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $isExists = $this->_mPaymentModes->readPaymentModeGroup($req->paymentModeName);
            if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
                throw new Exception("Payment Mode Already existing");
            $getData = $this->_mPaymentModes::findOrFail($req->id);
            $metaReqs = [
                'payment_mode_name' => $req->paymentModeName,
                'version_no' => $getData->version_no + 1,
                'updated_at' => Carbon::now()
            ];
            $metaReqs = array_merge($metaReqs, [
                'json_logs' => trim($getData->json_logs . "," . json_encode($metaReqs), ",")
            ]);
            if (isset($req->status)) {          // In Case of Deactivation or Activation 
                $status = $req->status == 'deactive' ? 0 : 1;
                $metaReqs = array_merge($metaReqs, [
                    'status' => $status
                ]);
            }
            $editData = $this->_mPaymentModes::findOrFail($req->id);
            $editData->update($metaReqs);
            return responseMsgs(true, "Successfully Updated", [$metaReqs], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    /**
     * | Get Discont Group By Id
     */
    public function show(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'id' => 'required|numeric'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $show = $this->_mPaymentModes->getGroupById($req->id);
            if (collect($show)->isEmpty())
                throw new Exception("Data Not Found");
            return responseMsgs(true, "", $show, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //view by name
    public function search(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'search' => 'required|string'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $search = $this->_mPaymentModes->searchByName($req->search);
            if (collect($search)->isEmpty())
                throw new Exception("Data Not Found");
            return responseMsgs(true, "", $search, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //View All
    public function retrieveAll(Request $req)
    {
        try {
            $getData = $this->_mPaymentModes->retrieve();
            return responseMsgs(true, "", $getData, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
    //Active All
    public function activeAll(Request $req)
    {
        try {
            $data = $this->_mPaymentModes->active();
            return responseMsgs(true, "", $data, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //Delete
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
            $delete = $this->_mPaymentModes::findOrFail($req->id);
            //  if ($teachingTitle->status == 0)
            //      throw new Exception("Records Already Deleted");
            $delete->update($metaReqs);
            return responseMsgs(true, "Deleted Successfully", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
}
