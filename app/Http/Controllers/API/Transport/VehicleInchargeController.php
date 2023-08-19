<?php

namespace App\Http\Controllers\API\Transport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use App\Models\Transport\VehicleIncharge;
use DB;

class VehicleInchargeController extends Controller
{
    //global variable
    private $_mVehicleIncharges;

    public function __construct()
    {
        $this->_mVehicleIncharges = new VehicleIncharge();
    }
    // Add records
    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'inchargeName' => 'required|string',
            'email' => 'required|string|email',
            'mobile' => 'required|numeric|digits:10',
            'aadharNo' => 'required|numeric|digits:12',
            'aadharDoc' => 'mimes:pdf,jpeg,jpg|max:2048|nullable',
            'address' => 'required|string'

        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);

        try {
            $aadhar_file = '';
            $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));

            $isGroupExists = $this->_mVehicleIncharges->readVehicleInchargesGroup($req);

            if (collect($isGroupExists)->isNotEmpty())
                throw new Exception("Vehicle Incharge Already Existing");

            if ($req->aadharDoc != "") {
                $get_file_name = $req->aadharNo . '-' . $req->aadharDoc->getClientOriginalName();
                $path = public_path('school/vehicle-incharge/');
                $aadhar_file = 'school/vehicle-incharge/' . $get_file_name;
                $req->file('aadharDoc')->move($path, $get_file_name);
            }
            $metaReqs = [
                'incharge_name' => Str::title($req->inchargeName),
                'email' => $req->email,
                'mobile' => $req->mobile,
                'aadhar_no' => $req->aadharNo,
                'aadhar_doc' => $aadhar_file,
                'address' => $req->address,
                'country_id' => $req->countryId,
                'state_id' => $req->stateId,
                'city_id' => $req->cityId,
                'academic_year' => $fy,
                'school_id' => authUser()->school_id,
                'created_by' => authUser()->id,
                'ip_address' => getClientIpAddress()
            ];
            // print_r($metaReqs);die;
            $this->_mVehicleIncharges->store($metaReqs);
            return responseMsgs(true, "Successfully Saved", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
    // Edit records
    public function edit(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'inchargeName' => 'required|string',
            'email' => 'required|string|email',
            'mobile' => 'required|numeric|digits:10',
            'aadharNo' => 'required|numeric|digits:12',
            'aadharDoc' => 'mimes:pdf,doc,docx|max:2048|nullable',
            'address' => 'required|string'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));

        try {

            $isExists = $this->_mVehicleIncharges->readVehicleInchargesGroup($req);
            if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
                throw new Exception("Vehicle Incharge Already existing");
            $getData = $this->_mVehicleIncharges::findOrFail($req->id);

            $metaReqs = [
                'incharge_name' => Str::title($req->inchargeName),
                'email' => $req->email,
                'mobile' => $req->mobile,
                'aadhar_no' => $req->aadharNo,
                'address' => $req->address,
                'country_id' => $req->countryId,
                'state_id' => $req->stateId,
                'city_id' => $req->cityId,
                'version_no' => $getData->version_no + 1,
                'updated_at' => Carbon::now()
            ];
            if ($req->aadharDoc != "") {
                $get_file_name = $req->licenseDoc . '-' . $req->aadharDoc->getClientOriginalName();
                $path = public_path('school/vehicle-incharge/');
                $aadhar_file = 'school/vehicle-incharge/' . $get_file_name;
                $req->file('aadharDoc')->move($path, $get_file_name);
                $metaReqs = array_merge($metaReqs, [
                    'aadhar_doc' => $aadhar_file,
                ]);
            }

            if (isset($req->status)) {                  // In Case of Deactivation or Activation
                $status = $req->status == 'deactive' ? 0 : 1;
                $metaReqs = array_merge($metaReqs, [
                    'status' => $status
                ]);
            }
            $incharge = $this->_mVehicleIncharges::findOrFail($req->id);
            $incharge->update($metaReqs);
            return responseMsgs(true, "Successfully Updated", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
    /**
     * | Get Vehicle Incharge By Id
     */
    public function show(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'id' => 'required|numeric'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $show = $this->_mVehicleIncharges->getGroupById($req->id);
            if (!$show)
                throw new Exception("Data Not Found");
            return responseMsgs(true, "", $show, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    public function retrieveAll(Request $req)
    {
        try {
            $getData = $this->_mVehicleIncharges->retrieve();
            $perPage = $req->perPage ? $req->perPage : 10;
            $paginater = $getData->paginate($perPage);
            $list = [
                "current_page" => $paginater->currentPage(),
                "perPage" => $perPage,
                "last_page" => $paginater->lastPage(),
                "data" => collect($paginater->items())->map(function ($val) {
                    $path = "getImageLink?path=";
                    $val->aadhar_doc = trim($val->aadhar_doc) ? ($path . $val->aadhar_doc) : "";
                    
                    return $val;
                }),
                "total" => $paginater->total()
            ];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View All Records", $list, "API_8.4", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_8.4", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    public function retrieveAll1(Request $req)
    {
        try {
            $getData = $this->_mVehicleIncharges->retrieve();
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
            $delete = $this->_mVehicleIncharges::findOrFail($req->id);
            // if ($state->status == 0)
            //     throw new Exception("Records Already Deleted");
            $delete->update($metaReqs);
            return responseMsgs(true, "Deleted Successfully", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //Search by name
    public function search(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'search' => 'required|string'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $search = $this->_mVehicleIncharges->searchByName(Str::title($req->inchargeName));
            if (!$search)
                throw new Exception("Record Not Found");
            return responseMsgs(true, "", $search, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
}
