<?php

namespace App\Http\Controllers\API\Transport;

use App\Http\Controllers\Controller;
use App\Models\Transport\Driver;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;

/**
 * | Created On- 15-06-2023 
 * | Created By- Lakshmi Kumari
 * | Description- Driver CRUDS Operations
 * | Code Status- Closed
 */

class DriverController extends Controller
{
    private $_mDrivers;

    public function __construct()
    {
        DB::enableQueryLog();
        $this->_mDrivers = new Driver();
    }
    // Add records
    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'driverName' => 'required|string',
            'mobile' => 'required|numeric|digits:10',
            'email' => 'required|string',
            'address' => 'required|string',
            'licenseNo' => 'required|string',
            'aadharNo' => 'required|string',
            'panNo' => 'required|string',
            'photoDoc' => 'required|mimes:jpeg,png,jpg|nullable',
            'aadharDoc' => 'required|mimes:pdf|nullable',
            'licenseDoc' => 'required|mimes:pdf|nullable',
            'panDoc' => 'required|mimes:pdf|nullable'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $img_file = '';
            $aadhar_file = '';
            $license_file = '';
            $pan_file = '';
            $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
            $isExists = $this->_mDrivers->readDriverGroup($req);
            if (collect($isExists)->isNotEmpty())
                throw new Exception("Driver Already existing");

            // if ($req->photoDoc != "") {
            //     $photoDoc = $req->photoDoc;
            //     $get_file_name = $req->licenseNo . '-' . $photoDoc->getClientOriginalName();
            //     $path = public_path('school/drivers/');
            //     $file_name = 'school/drivers/' . $get_file_name;
            //     $req->file('photoDoc')->move($path, $get_file_name);
            // }

            if ($req->photoDoc != "") {
                $get_file_name = $req->licenseNo . '-' . $req->photoDoc->getClientOriginalName();
                $path = public_path('school/drivers/');
                $img_file = 'school/drivers/' . $get_file_name;
                $req->file('photoDoc')->move($path, $get_file_name);
            }
            if ($req->aadharDoc != "") {
                $get_file_name = $req->licenseNo . '-' . $req->aadharDoc->getClientOriginalName();
                $path = public_path('school/drivers/');
                $aadhar_file = 'school/drivers/' . $get_file_name;
                $req->file('aadharDoc')->move($path, $get_file_name);
            }
            if ($req->licenseDoc != "") {
                $get_file_name = $req->licenseNo . '-' . $req->licenseDoc->getClientOriginalName();
                $path = public_path('school/drivers/');
                $license_file = 'school/drivers/' . $get_file_name;
                $req->file('licenseDoc')->move($path, $get_file_name);
            }
            if ($req->panDoc != "") {
                $get_file_name = $req->licenseNo . '-' . $req->panDoc->getClientOriginalName();
                $path = public_path('school/drivers/');
                $pan_file = 'school/drivers/' . $get_file_name;
                $req->file('panDoc')->move($path, $get_file_name);
            }
            $metaReqs = [
                'driver_name' => Str::title($req->driverName),
                'mobile' => $req->mobile,
                'email' => $req->email,
                'address' => $req->address,
                'license_no' => $req->licenseNo,
                'aadhar_no' => $req->aadharNo,
                'pan_no' => $req->panNo,
                'photo_doc' => $img_file,
                'aadhar_doc' => $aadhar_file,
                'license_doc' => $license_file,
                'pan_doc' => $pan_file,
                'school_id' => authUser()->school_id,
                'created_by' => authUser()->id,
                'ip_address' => getClientIpAddress(),
                'version_no' => 0
            ];
            $metaReqs = array_merge($metaReqs, [
                'json_logs' => trim(json_encode($metaReqs), ",")
            ]);
            $this->_mDrivers->store($metaReqs);
            $data = ['Driver Name' => $req->driverName];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Records Added Successfully", $data, "M_API_22.1", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_22.1", responseTime(), "POST", $req->deviceId ?? "");
        }
    }


    // Edit records
    public function edit(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'id' => 'required|numeric',
            'driverName' => 'required|string',
            'mobile' => 'required|numeric|digits:10',
            'email' => 'required|string',
            'address' => 'required|string',
            'licenseNo' => 'required|string',
            'aadharNo' => 'required|string',
            'panNo' => 'required|string',
            // 'photoDoc' => 'mimes:jpeg,png,jpg',
            // 'aadharDoc' => 'mimes:pdf',
            // 'licenseDoc' => 'mimes:pdf',
            // 'panDoc' => 'mimes:pdf'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $img_file = '';
            $aadhar_file = '';
            $license_file = '';
            $pan_file = '';

            $isExists = $this->_mDrivers->readDriverGroup($req);
            if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
                throw new Exception("Driver Already existing");

            $getData = $this->_mDrivers::findOrFail($req->id);

            // if ($req->eventDocs != "") {
            //     $files = $getData->upload_event_docs;
            //     unlink($files);
            //     $eventDocs = $req->eventDocs;
            //     $get_file_name = $req->date . '-' . $eventDocs->getClientOriginalName();
            //     $path = public_path('school/events/');
            //     $file_name = 'school/events/' . $get_file_name;
            //     $req->file('eventDocs')->move($path, $get_file_name);
            // }

            $metaReqs = [
                'driver_name' => Str::title($req->driverName),
                'mobile' => $req->mobile,
                'email' => $req->email,
                'address' => $req->address,
                'license_no' => $req->licenseNo,
                'aadhar_no' => $req->aadharNo,
                'pan_no' => $req->panNo,
                // 'photo_doc' => $img_file,
                // 'aadhar_doc' => $aadhar_file,
                // 'license_doc' => $license_file,
                // 'pan_doc' => $pan_file,
                'version_no' => $getData->version_no + 1,
                'updated_at' => Carbon::now()
            ];
            if ($req->photoDoc != "") {
                $files = $getData->photo_doc;
                // unlink($files);
                $get_file_name = $req->licenseNo . '-' . $req->photoDoc->getClientOriginalName();
                $path = public_path('school/drivers/');
                $img_file = 'school/drivers/' . $get_file_name;
                $req->file('photoDoc')->move($path, $get_file_name);
                $metaReqs = array_merge($metaReqs, [
                    'photo_doc' => $img_file
                ]);
            }
            if ($req->aadharDoc != "") {
                $files = $getData->aadhar_doc;
                // unlink($files);
                $get_file_name = $req->licenseNo . '-' . $req->aadharDoc->getClientOriginalName();
                $path = public_path('school/drivers/');
                $aadhar_file = 'school/drivers/' . $get_file_name;
                $req->file('aadharDoc')->move($path, $get_file_name);
                $metaReqs = array_merge($metaReqs, [
                    'aadhar_doc' => $aadhar_file
                ]);
            }
            if ($req->licenseDoc != "") {
                $files = $getData->license_doc;
                // unlink($files);
                $get_file_name = $req->licenseNo . '-' . $req->licenseDoc->getClientOriginalName();
                $path = public_path('school/drivers/');
                $license_file = 'school/drivers/' . $get_file_name;
                $req->file('licenseDoc')->move($path, $get_file_name);
                $metaReqs = array_merge($metaReqs, [
                    'license_doc' => $license_file
                ]);
            }
            if ($req->panDoc != "") {
                $files = $getData->pan_doc;
                // unlink($files);
                $get_file_name = $req->licenseNo . '-' . $req->panDoc->getClientOriginalName();
                $path = public_path('school/drivers/');
                $pan_file = 'school/drivers/' . $get_file_name;
                $req->file('panDoc')->move($path, $get_file_name);
                $metaReqs = array_merge($metaReqs, [
                    'pan_doc' => $pan_file,
                ]);
            }
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
            $data = ['Driver Name' => $req->driverName];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Records Updated Successfully", $data, "M_API_22.2", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_22.2", responseTime(), "POST", $req->deviceId ?? "");
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
            $show = $this->_mDrivers->getGroupById($req->id);
            if (collect($show)->isEmpty())
                throw new Exception("Data Not Found");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View Records", $show, "M_API_22.3", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_22.3", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //View All
    public function retrieveAll(Request $req)
    {
        try {
            $getData = $this->_mDrivers->retrieve();
            $perPage = $req->perPage ? $req->perPage : 10;
            $paginater = $getData->paginate($perPage);
            $list = [
                "current_page" => $paginater->currentPage(),
                "perPage" => $perPage,
                "last_page" => $paginater->lastPage(),
                "data" => collect($paginater->items())->map(function ($val) {
                    $path = "getImageLink?path=";
                    $val->photo_doc = trim($val->photo_doc) ? ($path . $val->photo_doc) : "";
                    $val->aadhar_doc = trim($val->aadhar_doc) ? ($path . $val->aadhar_doc) : "";
                    $val->license_doc = trim($val->license_doc) ? ($path . $val->license_doc) : "";
                    $val->pan_doc = trim($val->pan_doc) ? ($path . $val->pan_doc) : "";
                    return $val;
                }),
                "total" => $paginater->total()
            ];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View All Records", $list, "M_API_22.4", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_22.4", responseTime(), "POST", $req->deviceId ?? "");
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
            $delete = $this->_mDrivers::findOrFail($req->id);
            $delete->update($metaReqs);
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Changes Done Successfully", $req->status, "M_API_22.5", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_22.5", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //Active All
    public function activeAll(Request $req)
    {
        try {
            $getData = $this->_mDrivers->active();
            if (collect($getData)->isEmpty())
                throw new Exception("Data Not Found");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View All Active Records", $getData, "M_API_22.6", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_22.6", responseTime(), "POST", $req->deviceId ?? "");
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
            $getData = $this->_mDrivers->searchByName($req);
            $perPage = $req->perPage ? $req->perPage : 10;
            $paginater = $getData->paginate($perPage);
            $list = [
                "current_page" => $paginater->currentPage(),
                "perPage" => $perPage,
                "last_page" => $paginater->lastPage(),
                "data" => collect($paginater->items())->map(function ($val) {
                    $path = "getImageLink?path=";
                    $val->photo_doc = trim($val->photo_doc) ? ($path . $val->photo_doc) : "";
                    $val->aadhar_doc = trim($val->aadhar_doc) ? ($path . $val->aadhar_doc) : "";
                    $val->license_doc = trim($val->license_doc) ? ($path . $val->license_doc) : "";
                    $val->pan_doc = trim($val->pan_doc) ? ($path . $val->pan_doc) : "";
                    return $val;
                }),
                "total" => $paginater->total()
            ];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View All Records", $list, "M_API_22.7", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_22.7", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
}
