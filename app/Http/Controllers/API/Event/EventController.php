<?php

namespace App\Http\Controllers\api\Event;

use App\Http\Controllers\Controller;
use App\Models\Event\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use DB;

/**
 * | Created On- 15-06-2023 
 * | Created By- Lakshmi Kumari
 * | Description- Event CRUDS Operations
 * | Code Status- Closed
 */

class EventController extends Controller
{
    private $_mEvents;

    public function __construct()
    {
        DB::enableQueryLog();
        $this->_mEvents = new Event();
    }

    /**
     * | Add data
     */
    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'eventName' => 'required|string',
            'date' => 'required|string',
            'time' => 'required|string',
            'description' => 'string',
            'eventVenue' => 'required|string',
            'organizer' => 'required|string',
            'eventDocs' => 'required|mimes:pdf'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $file_name = '';
            $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
            $isGroupExists = $this->_mEvents->readEventGroup($req);
            if (collect($isGroupExists)->isNotEmpty())
                throw new Exception("Event Name Already Existing");

            if ($req->eventDocs != "") {
                $eventDocs = $req->eventDocs;
                $get_file_name = $req->date . '-' . $eventDocs->getClientOriginalName();
                $path = public_path('school/events/');
                $file_name = 'school/events/' . $get_file_name;
                $req->file('eventDocs')->move($path, $get_file_name);
            }

            $metaReqs = [
                'event_name' => $req->eventName,
                'event_date' => $req->date,
                'event_time' => $req->time,
                'description' => $req->description,
                'event_venue' => $req->eventVenue,
                'organizer' => $req->organizer,
                'upload_event_docs' => $file_name,
                'academic_year' => $fy,
                'school_id' => authUser()->school_id,
                'created_by' => authUser()->id,
                'ip_address' => getClientIpAddress(),
                'version_no' => 0
            ];
            $metaReqs = array_merge($metaReqs, [
                'json_logs' => trim(json_encode($metaReqs), ",")
            ]);
            $this->_mEvents->store($metaReqs);
            $data = ['Event Name' => $req->eventName];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Records Added Successfully", $data, "API_8.1", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_8.1", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    /**
     * | Update data
     */
    public function edit(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'id' => 'required|numeric',
            'eventName' => 'required|string',
            'date' => 'required|string',
            'time' => 'required|string',
            'description' => 'string',
            'eventVenue' => 'required|string',
            'organizer' => 'required|string',
            // 'eventDocs' => 'required|string'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $file_name = '';
            $isExists = $this->_mEvents->readEventGroup($req);
            if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
                throw new Exception("Event Name Already Existing");

            $getData = $this->_mEvents::findOrFail($req->id);

            $metaReqs = [
                'event_name' => $req->eventName,
                'event_date' => $req->date,
                'event_time' => $req->time,
                'description' => $req->description,
                'event_venue' => $req->eventVenue,
                'organizer' => $req->organizer,
                // 'upload_event_docs' => $file_name,
                // 'school_id' => authUser()->school_id,
                'version_no' => $getData->version_no + 1,
                'updated_at' => Carbon::now()
            ];
            if ($req->eventDocs != "") {
                $files = $getData->upload_event_docs;
                unlink($files);
                $eventDocs = $req->eventDocs;
                $get_file_name = $req->date . '-' . $eventDocs->getClientOriginalName();
                $path = public_path('school/events/');
                $file_name = 'school/events/' . $get_file_name;
                $req->file('eventDocs')->move($path, $get_file_name);
                $metaReqs = array_merge($metaReqs, [
                    'upload_event_docs' => $file_name
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
            $data = ['Event Name' => $req->eventName];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Records Updated Successfully", $data, "API_8.2", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_8.2", responseTime(), "POST", $req->deviceId ?? "");
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
            $show = $this->_mEvents->getGroupById($req->id);
            if (collect($show)->isEmpty())
                throw new Exception("Data Not Found");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View Records", $show, "API_8.3", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_8.3", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
    //View All
    public function retrieveAll(Request $req)
    {
        try {
            $getData = $this->_mEvents->retrieve();
            $perPage = $req->perPage ? $req->perPage : 10;
            $paginater = $getData->paginate($perPage);
            $list = [
                "current_page" => $paginater->currentPage(),
                "perPage" => $perPage,
                "last_page" => $paginater->lastPage(),
                "data" => collect($paginater->items())->map(function ($val) {
                    $path = "getImageLink?path=";
                    $val->upload_event_docs = trim($val->upload_event_docs) ? ($path . $val->upload_event_docs) : "";
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
            $delete = $this->_mEvents::findOrFail($req->id);
            $delete->update($metaReqs);
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Changes Done Successfully", $req->status, "API_8.5", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_8.5", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //Active All
    public function activeAll(Request $req)
    {
        try {
            $getData = $this->_mEvents->active();
            if (collect($getData)->isEmpty())
                throw new Exception("Data Not Found");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View All Active Records", $getData, "API_8.6", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_8.6", responseTime(), "POST", $req->deviceId ?? "");
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
            $getData = $this->_mEvents->searchByName($req);
            $perPage = $req->perPage ? $req->perPage : 10;
            $paginater = $getData->paginate($perPage);
            $list = [
                "current_page" => $paginater->currentPage(),
                "perPage" => $perPage,
                "last_page" => $paginater->lastPage(),
                "data" => collect($paginater->items())->map(function ($val) {
                    $path = "getImageLink?path=";
                    $val->upload_event_docs = trim($val->upload_event_docs) ? ($path . $val->upload_event_docs) : "";
                    return $val;
                }),
                "total" => $paginater->total()
            ];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View All Records", $list, "API_8.7", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "API_8.7", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
}
