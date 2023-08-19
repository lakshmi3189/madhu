<?php

namespace App\Http\Controllers\API\Transport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transport\BusFeeFine;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;


class BusFeeFineController extends Controller
{
    /**
     * | Created On-31-05-2023 
     * | Created By- Ashutosh Kumar
     * | BusFeeFine Crud Operations
     */

    private $_mBusFeeFines;

    public function __construct()
    {
        $this->_mBusFeeFines = new BusFeeFine();
    }

    /**
     * | Store BusFeeFine
     */
    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'monthName' => 'required|string',
            'dueDate' => 'required|string',
            'actualFineAmount' => 'required|integer',
            'fineAmount' => 'required|integer',
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);

        try {
            $isExists = $this->_mBusFeeFines->readBusFeeFineGroup(Str::ucfirst($req->monthName));
            if (collect($isExists)->isNotEmpty())
                throw new Exception("Bus Fee Fine Already existing");
            $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
            $metaReqs = [
                'month_name' => Str::ucfirst($req->monthName),
                'due_date' => $req->dueDate,
                'actual_fine_amount' => $req->actualFineAmount,
                'fine_amount' => $req->fineAmount,
                'academic_year' => $fy,
                'school_id' => authUser()->school_id,
                'created_by' => authUser()->id,
                'ip_address' => getClientIpAddress()
            ];
            $this->_mBusFeeFines->store($metaReqs);
            return responseMsgs(true, "Successfully Saved", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    /**
     * | Update BusFeeFine
     */
    public function edit(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'monthName' => 'required|string',
            'dueDate' => 'required|string',
            'actualFineAmount' => 'required|integer',
            'fineAmount' => 'required|integer',
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);

        try {
            $isExists = $this->_mBusFeeFines->readBusFeeFineGroup(Str::ucfirst($req->monthName));
            if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
                throw new Exception("Bus Fee Fine Already Existing");
            $getData = $this->_mBusFeeFines::findOrFail($req->id);
            $metaReqs = [
                'month_name' => Str::ucfirst($req->monthName),
                'due_date' => $req->dueDate,
                'actual_fine_amount' => $req->actualFineAmount,
                'fine_amount' => $req->fineAmount,
                'version_no' => $getData->version_no + 1,
                'updated_at' => Carbon::now()
            ];

            if (isset($req->status)) {                  // In Case of Deactivation or Activation 
                $status = $req->status == 'deactive' ? 0 : 1;
                $metaReqs = array_merge($metaReqs, [
                    'status' => $status
                ]);
            }

            $fine = $this->_mBusFeeFines::findOrFail($req->id);
            $fine->update($metaReqs);
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
            // $fine = $this->_mBusFeeFines::findOrFail($req->id);
            $show = $this->_mBusFeeFines->getGroupById($req->id);
            if (!$show)
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
            $search = $this->_mBusFeeFines->searchByName(Str::ucfirst($req->monthName));
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
            // $fine = $this->_mBusFeeFines::orderByDesc('id')->where('status', '1')->get();
            $fine = $this->_mBusFeeFines->retrieveAll($req->id);
            return responseMsgs(true, "", $fine, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //Active All
    public function active(Request $req)
    {
        try {
            $Banks = $this->_mBusFeeFines->activeAll();
            return responseMsgs(true, "", $Banks, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //Delete
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
            $delete = $this->_mBusFeeFines::findOrFail($req->id);
            // if ($state->status == 0)
            //     throw new Exception("Records Already Deleted");
            $delete->update($metaReqs);
            return responseMsgs(true, "Deleted Successfully", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

   
}
