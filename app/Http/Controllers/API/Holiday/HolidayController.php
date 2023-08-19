<?php

namespace App\Http\Controllers\API\Holiday;

use App\Http\Controllers\Controller;
use App\Models\Holiday\Holiday;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;

/**
 * | Created On- 16-06-2023 
 * | Created By- Lakshmi Kumari
 * | Description- Holiday CRUDS Operations
 * | Code Status- Closed
 */
class HolidayController extends Controller
{
    //global variable
    private $_mHolidays;

    public function __construct()
    {
        DB::enableQueryLog();
        $this->_mHolidays = new Holiday();
    }

    // Add records
    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'holiday' => 'required|string',
            'holidayStartDate' => 'required|string',
            "holidayEndDate" => 'required|string',
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $isGroupExists = $this->_mHolidays->readHolidayGroup($req);
            if (collect($isGroupExists)->isNotEmpty())
                throw new Exception("Holiday Name Already Existing");
            $metaReqs = [
                'holiday' => Str::title($req->holiday),
                'holiday_start_date' => $req->holidayStartDate,
                'holiday_end_date' => $req->holidayEndDate,
                'description' => $req->description,
                "school_id" => authUser()->school_id,
                'created_by' => authUser()->id,
                'ip_address' => getClientIpAddress(),
                'version_no' => 0
            ];
            $metaReqs = array_merge($metaReqs, [
                'json_logs' => trim(json_encode($metaReqs), ",")
            ]);
            $this->_mHolidays->store($metaReqs);
            $data = ['Holiday Name' => $req->holiday];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Records Added Successfully", $data, "API_10.1", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_10.1", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    // Edit records
    public function edit(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'id' => 'required|numeric',
            'holiday' => 'required|string',
            'holidayStartDate' => 'required|string',
            "holidayEndDate" => 'required|string',
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $isExists = $this->_mHolidays->readHolidayGroup($req);
            if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
                throw new Exception("Holiday Name Already Existing");
            $getData = $this->_mHolidays::findOrFail($req->id);
            $metaReqs = [
                'holiday' => Str::title($req->holiday),
                'holiday_start_date' => $req->holidayStartDate,
                'holiday_end_date' => $req->holidayEndDate,
                'description' => $req->description,
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
            $data = ['Holiday Name' => $req->holiday];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Records Updated Successfully", $data, "API_10.2", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_10.2", responseTime(), "POST", $req->deviceId ?? "");
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
            $show = $this->_mHolidays->getGroupById($req->id);
            if (collect($show)->isEmpty())
                throw new Exception("Data Not Found");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View Records", $show, "API_10.3", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_10.3", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
    //View All
    public function retrieveAll(Request $req)
    {
        try {
            $getData = $this->_mHolidays->retrieve();
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
            return responseMsgsT(true, "View All Records", $list, "API_10.4", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_10.4", responseTime(), "POST", $req->deviceId ?? "");
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
            $delete = $this->_mHolidays::findOrFail($req->id);
            $delete->update($metaReqs);
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Changes Done Successfully", $req->status, "API_10.5", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_10.5", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //Active All
    public function activeAll(Request $req)
    {
        try {
            $getData = $this->_mHolidays->active();
            if (collect($getData)->isEmpty())
                throw new Exception("Data Not Found");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View All Active Records", $getData, "API_10.6", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_10.6", responseTime(), "POST", $req->deviceId ?? "");
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
            $getData = $this->_mHolidays->searchByName($req);
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
            return responseMsgsT(true, "View Searched Records", $list, "API_10.7", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_10.7", responseTime(), "POST", $req->deviceId ?? "");
        }
    }


    public function storeCSV(Request $req)
    {
        //validation
        $validator = Validator::make($req->all(), [
            // 'holidayDocs' => 'required|mimes:csv'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $file = $req->file('holidayDocs');
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();
            // Valid File Extensions
            $valid_extension = array("csv");

            // 2MB in Bytes
            $maxFileSize = 2097152;

            // Check file extension
            if (in_array(strtolower($extension), $valid_extension)) {
                // Check file size
                if ($fileSize <= $maxFileSize) {
                    // File upload location
                    $location = 'uploads';
                    // Upload file
                    $file->move($location, $filename);
                    // Import CSV to Database
                    $filepath = public_path($location . "/" . $filename);

                    // Reading file
                    $file = fopen($filepath, "r");
                    $importData_arr = array();
                    $i = 0;
                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata);
                        // Skip first row (Remove below comment if you want to skip the first row)
                        if ($i == 0) {
                            $i++;
                            continue;
                        }
                        for ($c = 0; $c < $num; $c++) {
                            $importData_arr[$i][] = $filedata[$c];
                        }
                        $i++;
                    }
                    fclose($file);
                    // Insert to MySQL database
                    foreach ($importData_arr as $importData) {
                        // return $importData[0]; die;
                        // $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
                        // $metaReqs = array(
                        //     "holiday" => $importData[0],
                        //     "holiday_date" => $importData[1],
                        //     "description" => $importData[2],
                        //     "school_id" => authUser()->school_id,
                        //     "created_by" => authUser()->id,
                        //     "ip_address" => getClientIpAddress(),
                        //     "created_at" => Carbon::now(),
                        //     "updated_at" => Carbon::now()
                        // );
                        $metaReqs = [
                            'holiday' => Str::title($importData[0]),
                            'holiday_start_date' => $importData[1],
                            'holiday_end_date' => $importData[2],
                            'description' => $importData[3],
                            "school_id" => authUser()->school_id,
                            'created_by' => authUser()->id,
                            'ip_address' => getClientIpAddress(),
                            "created_at" => Carbon::now(),
                            "updated_at" => Carbon::now(),
                            'version_no' => 0
                        ];
                        // return $metaReqs; die;
                        $metaReqs = array_merge($metaReqs, [
                            'json_logs' => trim(json_encode($metaReqs), ",")
                        ]);
                        // dd($insertData);
                        $this->_mHolidays::csv($metaReqs);
                    }
                }
            }
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Records Added Successfully", [], "API_10.8", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_10.8", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
}
