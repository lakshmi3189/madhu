<?php

namespace App\Http\Controllers\api\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Section;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;


class SectionController extends Controller
{
    private $_mSections;
    private $_tCustome;


    public function __construct()
    {
        $this->_mSections = new Section();
    }

    // Add records
    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'sectionName' => 'required|string'

        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);

        try {

            $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));

            $isGroupExists = $this->_mSections->readSectionGroup(Str::ucfirst($req->sectionName));

            if (collect($isGroupExists)->isNotEmpty())
                throw new Exception("Section Name Already existing");
            $metaReqs = [
                'section_name' => Str::ucfirst($req->sectionName),
                'academic_year' => $fy,
                'school_id' => authUser()->school_id,
                'created_by' => authUser()->id,
                'ip_address' => getClientIpAddress()
            ];
            // print_r($metaReqs);die;
            $this->_mSections->store($metaReqs);
            return responseMsgs(true, "Successfully Saved", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
    // Edit records
    public function edit(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'sectionName' => 'required|string'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);

        try {
            $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
            $isGroupExists = $this->_mSections->readSectionGroup(Str::ucfirst($req->sectionName));
            if ($isGroupExists && $isGroupExists->where('id', '!=', $req->id)->isNotEmpty())
                throw new Exception("Section Name Already Existing");
            $getData = $this->_mSections::findOrFail($req->id);
            $metaReqs = [
                'section_name' => Str::ucfirst($req->sectionName),
                'version_no' => $getData->version_no + 1,
                'updated_at' => Carbon::now()
            ];
            if (isset($req->status)) {                  // In Case of Deactivation or Activation
                $status = $req->status == 'deactive' ? 0 : 1;
                $metaReqs = array_merge($metaReqs, [
                    'status' => $status
                ]);
            }
            $Sections = $this->_mSections::findOrFail($req->id);
            $Sections->update($metaReqs);
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
            // $Sections = $this->_mSections::findOrFail($req->id);
            $show = $this->_mSections->getGroupById($req->id);
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
            $search = $this->_mSections->searchByName(Str::ucfirst($req->search));
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
            // $SectionMaster = $this->_mSections::orderByDesc('id')->where('status','1')->get();
            $SectionMaster = $this->_mSections->retrieveAll();
            return responseMsgs(true, "", $SectionMaster, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //Active All
    public function activeAll(Request $req)
    {
        try {
            $getActiveAll = $this->_mSections->active();
            return responseMsgs(true, "", $getActiveAll, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
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
            $Sections = $this->_mSections::findOrFail($req->id);
            // if ($Sections->status == 0)
            //     throw new Exception("Records Already Deleted");
            $Sections->update($metaReqs);
            return responseMsgs(true, "Deleted Successfully", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
    //Delete
    public function delete4(Request $req)
    {
        try {
            $metaReqs = [
                'status' => $req->status
            ];
            $Sections = $this->_mSections::findOrFail($req->id);
            if ($Sections->status == 0)
                throw new Exception("Records Already Deleted");
            $Sections->update($metaReqs);
            return responseMsgs(true, "Deleted Successfully", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
}
