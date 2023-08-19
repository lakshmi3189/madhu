<?php

namespace App\Http\Controllers\API\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\ClassFeeMaster;
use App\Models\Master\FeeHead;
use DB;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class ClassFeeMasterController extends Controller
{
    private $_mClassFeeMasters;
    private $_mFeeHeads;

    public function __construct()
    {
        DB::enableQueryLog();
        $this->_mClassFeeMasters = new classFeeMaster();
        $this->_mFeeHeads = new FeeHead();
    }
    /**
     * | Created On-23-05-2023 
     * | Created By- Lakshmi Kumari
     * | Fee Head Type Crud Operations
     */

    public function showByClass(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'classId' => 'required|numeric',
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);

        try {
            // return('test'); die;
            $classFeeMastersData = $this->_mClassFeeMasters->getClassFeeMasterByClassId($req->classId);
            return responseMsgs(true, "Records", $classFeeMastersData, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'classId' => 'required|numeric',
            'feeHeadId' => 'required|numeric',
            // 'sectionId' => 'numeric',
            // 'janFee' => 'required',
            // 'febFee' => 'required',
            // 'marFee' => 'required',
            // 'aprFee' => 'required',
            // 'mayFee' => 'required',
            // 'junFee' => 'required',
            // 'julFee' => 'required',
            // 'augFee' => 'required',
            // 'sepFee' => 'required',
            // 'octFee' => 'required',
            // 'novFee' => 'required',
            // 'decFee' => 'required'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);

        try {
            $isMappingExists = $this->_mClassFeeMasters->getClassFeeMasterGroupMaps($req);
            if (collect($isMappingExists)->isNotEmpty())
                throw new Exception('Record Already Existing');

            $netFee = 0;
            $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));

            // $feeHeads = $this->_mFeeHeads->getGroupById($req->feeHeadId);
            // $headTypeId = ['1' => 'yearly', '2' => 'monthly', '3' => 'one time', '4' => 'quarterly', '5' => 'half yearly'];
            // // print_var($headTypeId);
            // $jan = $feb = $mar = $apr = $may = $jun = $jul = $aug = $sep = $oct = $nov = $dec = 0;
            // //yearly check
            // if ($headTypeId == 1) {
            //     $month = $req->monthName;
            // }

            $feeAmount = ($req->janFee + $req->febFee + $req->marFee + $req->aprFee +
                $req->mayFee + $req->junFee + $req->julFee + $req->augFee +
                $req->sepFee + $req->octFee + $req->novFee + $req->decFee
            );
            if (isset($req->discount)) {
                $netFee =  (($feeAmount) - (($feeAmount) * ($req->discount / 100)));
            }
            // echo $netFee; die;
            $metaReqs = [
                'class_id' => $req->classId,
                // 'section_id' => $req->sectionId,
                'fee_head_id' => $req->feeHeadId,
                'fee_amount' => $feeAmount,
                'discount' => $req->discount,
                'net_fee' => $netFee,
                'jan_fee' => $req->janFee,
                'feb_fee' => $req->febFee,
                'mar_fee' => $req->marFee,
                'apr_fee' => $req->aprFee,
                'may_fee' => $req->mayFee,
                'jun_fee' => $req->junFee,
                'jul_fee' => $req->julFee,
                'aug_fee' => $req->augFee,
                'sep_fee' => $req->sepFee,
                'oct_fee' => $req->octFee,
                'nov_fee' => $req->novFee,
                'dec_fee' => $req->decFee,
                'academic_year' => $fy,
                'school_id' => authUser()->school_id,
                'created_by' => authUser()->id,
                'ip_address' => getClientIpAddress()
            ];
            $this->_mClassFeeMasters->store($metaReqs);
            return responseMsgs(true, "Successfully Saved", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }


    /**
     * | Update Fee Head Type
     */
    public function edit(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'id' => 'required|numeric',
            'classId' => 'required|numeric',
            'feeHeadId' => 'required|numeric',
            // 'sectionId' => 'required|numeric',
            // 'janFee' => 'required',
            // 'febFee' => 'required',
            // 'marFee' => 'required',
            // 'aprFee' => 'required',
            // 'mayFee' => 'required',
            // 'junFee' => 'required',
            // 'julFee' => 'required',
            // 'augFee' => 'required',
            // 'sepFee' => 'required',
            // 'octFee' => 'required',
            // 'novFee' => 'required',
            // 'decFee' => 'required',
            'status' => 'nullable|in:active,deactive'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);

        try {
            $isMappingExists = $this->_mClassFeeMasters->getClassFeeMasterGroupMaps($req);
            if (collect($isMappingExists)->isNotEmpty() && $isMappingExists->where('id', '!=', $req->id)->isNotEmpty())
                throw new Exception('Record Already Existing!');

            $feeAmount = ($req->janFee + $req->febFee + $req->marFee + $req->aprFee +
                $req->mayFee + $req->junFee + $req->julFee + $req->augFee +
                $req->sepFee + $req->octFee + $req->novFee + $req->decFee
            );
            if (isset($req->discount)) {
                $netFee =  (($feeAmount) - (($feeAmount) * ($req->discount / 100)));
            }

            // $getData = $this->_mClassFeeMasters::findOrFail($req->id);
            $getData = $this->_mClassFeeMasters::where('id', $req->id)->first();
            // dd($discountGroupMap);
            if (collect($getData)->isEmpty())
                throw new Exception('Data Not Found!');
            $metaReqs = [
                'fee_amount' => $feeAmount,
                'discount' => $req->discount,
                'class_id' => $req->classId,
                // 'section_id' => $req->sectionId,
                'fee_head_id' => $req->feeHeadId,
                'net_fee' => $netFee,
                'jan_fee' => $req->janFee,
                'feb_fee' => $req->febFee,
                'mar_fee' => $req->marFee,
                'apr_fee' => $req->aprFee,
                'may_fee' => $req->mayFee,
                'jun_fee' => $req->junFee,
                'jul_fee' => $req->julFee,
                'aug_fee' => $req->augFee,
                'sep_fee' => $req->sepFee,
                'oct_fee' => $req->octFee,
                'nov_fee' => $req->novFee,
                'dec_fee' => $req->decFee,
                'version_no' => $getData->version_no + 1,
                'updated_at' => Carbon::now()
            ];

            if (isset($req->status)) {                  // In Case of Deactivation or Activation 
                $status = $req->status == 'deactive' ? 0 : 1;
                $metaReqs = array_merge($metaReqs, [
                    'status' => $status
                ]);
            }

            $classFeeMaster = $this->_mClassFeeMasters::findOrFail($req->id);
            $classFeeMaster->update($metaReqs);
            return responseMsgs(true, "Successfully Updated", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
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
            // $classFeeMaster = $this->_mClassFeeMasters::findOrFail($req->id);
            $show = $this->_mClassFeeMasters->getGroupMapById($req->id);
            // dd($discountGroupMap);
            if (collect($show)->isEmpty())
                throw new Exception("Data Not Found");
            return responseMsgs(true, "Records", $show, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    /**
     * | Retrieve All
     */
    public function retrieveAll(Request $req)
    {
        try {
            // $classFeeMaster = $this->_mClassFeeMasters::orderByDesc('id')->get();
            $getData = $this->_mClassFeeMasters->retrieveAll();
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
            return responseMsgs(true, "All Records", $list, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    /**
     * | Retrieve All
     */
    public function activeAll(Request $req)
    {
        try {
            // $classFeeMaster = $this->_mClassFeeMasters::orderByDesc('id')->get();
            $classFeeMastersGroupMap = $this->_mClassFeeMasters->active();
            return responseMsgs(true, "All Records", $classFeeMastersGroupMap, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
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
            $delete = $this->_mClassFeeMasters::findOrFail($req->id);
            // if ($delete->status == 0)
            //     throw new Exception("Records Already Deleted");
            $delete->update($metaReqs);
            return responseMsgs(true, "Deleted Successfully", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
}
