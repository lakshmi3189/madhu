<?php

namespace App\Http\Controllers\API\Transport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use App\Models\Transport\DropPoint;

class DropPointController extends Controller
{
    //global variable
    private $_mDropPoints;

    public function __construct()
    {
        $this->_mDropPoints = new DropPoint();
    }

    // Add records
    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'dropPointName' => 'required|string',
            'dropPointAddress' => 'required|string',

        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);

        try {

            $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));

            $isGroupExists = $this->_mDropPoints->readDropPointGroup(Str::ucfirst($req->dropPointName));

            if (collect($isGroupExists)->isNotEmpty())
                throw new Exception("Drop Point Name Already Existing");
            $metaReqs = [
                'drop_point_name' => Str::ucfirst($req->dropPointName),
                'drop_point_address' => $req->dropPointAddress,
                'academic_year' => $fy,
                'school_id' => authUser()->school_id,
                'created_by' => authUser()->id,
                'ip_address' => getClientIpAddress()
            ];
            // print_r($metaReqs);die;
            $this->_mDropPoints->store($metaReqs);
            return responseMsgs(true, "Successfully Saved", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    // Edit records
    public function edit(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'id'           => 'required|numeric',
            'dropPointName' => 'required|string',
            'dropPointAddress' => 'required|string',
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);

        $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));

        try {
            $isExists = $this->_mDropPoints->readDropPointGroup(Str::ucfirst($req->dropPointName));
            if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
                throw new Exception("Drop Point Already existing");

            $getData = $this->_mDropPoints::findOrFail($req->id);
            $metaReqs = [
                'drop_point_name' => Str::ucfirst($req->dropPointName),
                'drop_point_address' => $req->dropPointAddress,
                'academic_year' => $fy,
                'school_id' => authUser()->school_id,
                'created_by' => authUser()->id,
                'ip_address' => getClientIpAddress(),
                'version_no' => $getData->version_no + 1,
                'updated_at' => Carbon::now()
            ];

            if (isset($req->status)) {                  // In Case of Deactivation or Activation 
                $status = $req->status == 'deactive' ? 0 : 1;
                $metaReqs = array_merge($metaReqs, [
                    'status' => $status
                ]);
            }

            $dropPoint = $this->_mDropPoints::findOrFail($req->id);
            $dropPoint->update($metaReqs);
            return responseMsgs(true, "Successfully Updated", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
    //View by id
    public function show(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'id' => 'required|numeric'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            // $dropPoint = $this->_mDropPoints::findOrFail($req->id);
            $show = $this->_mDropPoints->getGroupById($req->id);
            if (collect($show)->isEmpty())
            throw new Exception("Data Not Found");
            return responseMsgs(true, "", $show, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
    //View All
    public function retrieveAll(Request $req)
    {
        try {
            // $dropPoint = $this->_mDropPoints::orderByDesc('id')->where('status', '1')->get();
            $dropPoint = $this->_mDropPoints->retrieveAll();
            return responseMsgs(true, "", $dropPoint, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

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
            $delete = $this->_mDropPoints::findOrFail($req->id);
            // if ($state->status == 0)
            //     throw new Exception("Records Already Deleted");
            $delete->update($metaReqs);
            return responseMsgs(true, "Deleted Successfully", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }


}
