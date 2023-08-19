<?php

namespace App\Http\Controllers\API\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\FeeDefinition;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class FeeDefinitionController extends Controller
{
    private $_mFeeDefinitions;

    public function __construct()
    {
        $this->_mFeeDefinitions = new FeeDefinition();
    }
    /**
     * | Created On-23-05-2023 
     * | Created By- Lakshmi Kumari
     * | Fee Head Type Crud Operations
    */

    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'classId' => 'required|numeric',
            'janFee' => 'required|numeric',
            // 'janBusFee' => 'required|numeric',           
            'febFee' => 'required|numeric',
            // 'febBusFee' => 'required|numeric',
            'marFee' => 'required|numeric',
            // 'marBusFee' => 'required|numeric',
            'aprFee' => 'required|numeric',
            // 'aprBusFee' => 'required|numeric',
            'mayFee' => 'required|numeric',
            // 'mayBusFee' => 'required|numeric',
            'junFee' => 'required|numeric',
            // 'junBusFee' => 'required|numeric',
            'julFee' => 'required|numeric',
            // 'julBusFee' => 'required|numeric',
            'augFee' => 'required|numeric',
            // 'augBusFee' => 'required|numeric',
            'sepFee' => 'required|numeric',
            // 'sepBusFee' => 'required|numeric',
            'octFee' => 'required|numeric',
            // 'octBusFee' => 'required|numeric',
            'novFee' => 'required|numeric',
            // 'novBusFee' => 'required|numeric',
            'decFee' => 'required|numeric',
            // 'decBusFee' => 'required|numeric'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);

        try {
            // $isExists = $this->_mFeeDefinitions->readFeeDefinitionById($req->feeDefinition);
            // if (collect($isExists)->isNotEmpty())
            //     throw new Exception("Fee Definition Already existing");
            $isMappingExists = $this->_mFeeDefinitions->getFeeDefinitionGroupMaps($req);
            if (collect($isMappingExists)->isNotEmpty())
                throw new Exception('Record Already Existing');
            $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
            $metaReqs=[                
                'class_id' => $req->classId,
                'jan_fee' => $req->janFee,
                // 'jan_bus_fee' => $req->janBusFee,
                'feb_fee' => $req->febFee,
                // 'feb_bus_fee' => $req->febBusFee,
                'mar_fee' => $req->marFee,
                // 'mar_bus_fee' => $req->marBusFee,
                'apr_fee' => $req->aprFee,
                // 'apr_bus_fee' => $req->aprBusFee,
                'may_fee' => $req->mayFee,
                // 'may_bus_fee' => $req->mayBusFee,
                'jun_fee' => $req->junFee,
                // 'jun_bus_fee' => $req->junBusFee,
                'jul_fee' => $req->julFee,
                // 'jul_bus_fee' => $req->julBusFee,
                'aug_fee' => $req->augFee,
                // 'aug_bus_fee' => $req->augBusFee,
                'sep_fee' => $req->sepFee,
                // 'sep_bus_fee' => $req->sepBusFee,
                'oct_fee' => $req->octFee,
                // 'oct_bus_fee' => $req->octBusFee,
                'nov_fee' => $req->novFee,
                // 'nov_bus_fee' => $req->novBusFee,
                'dec_fee' => $req->decFee,
                // 'dec_bus_fee' => $req->decBusFee,
                'academic_year' => $fy,
                'school_id' => authUser()->school_id,
                'created_by' => authUser()->id,
                'ip_address' => getClientIpAddress()
            ];
            // print_r($metaReqs); die;
            $this->_mFeeDefinitions->store($metaReqs);
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
            // 'clasId' => 'required|numeric',
            'janFee' => 'required|numeric',
            // 'janBusFee' => 'required|numeric',           
            'febFee' => 'required|numeric',
            // 'febBusFee' => 'required|numeric',
            'marFee' => 'required|numeric',
            // 'marBusFee' => 'required|numeric',
            'aprFee' => 'required|numeric',
            // 'aprBusFee' => 'required|numeric',
            'mayFee' => 'required|numeric',
            // 'mayBusFee' => 'required|numeric',
            'junFee' => 'required|numeric',
            // 'junBusFee' => 'required|numeric',
            'julFee' => 'required|numeric',
            // 'julBusFee' => 'required|numeric',
            'augFee' => 'required|numeric',
            // 'augBusFee' => 'required|numeric',
            'sepFee' => 'required|numeric',
            // 'sepBusFee' => 'required|numeric',
            'octFee' => 'required|numeric',
            // 'octBusFee' => 'required|numeric',
            'novFee' => 'required|numeric',
            // 'novBusFee' => 'required|numeric',
            'decFee' => 'required|numeric',
            // 'decBusFee' => 'required|numeric',
            'status' => 'nullable|in:active,deactive'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);

        try {
            // $isExists = $this->_mFeeDefinitions->readFeeDefinitionById($req->feeDefinition);
            // if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
            //     throw new Exception("Fee Definition Already Existing");

            $isMappingExists = $this->_mFeeDefinitions->getFeeDefinitionGroupMaps($req);
            if (collect($isMappingExists)->isNotEmpty() && $isMappingExists->where('id', '!=', $req->id)->isNotEmpty())
                throw new Exception('Record Already Existing');

            $getData = $this->_mFeeDefinitions::findOrFail($req->id);
            $metaReqs = [ 
                'jan_fee' => $req->janFee,
                // 'jan_bus_fee' => $req->janBusFee,
                'feb_fee' => $req->febFee,
                // 'feb_bus_fee' => $req->febBusFee,
                'mar_fee' => $req->marFee,
                // 'mar_bus_fee' => $req->marBusFee,
                'apr_fee' => $req->aprFee,
                // 'apr_bus_fee' => $req->aprBusFee,
                'may_fee' => $req->mayFee,
                // 'may_bus_fee' => $req->mayBusFee,
                'jun_fee' => $req->junFee,
                // 'jun_bus_fee' => $req->junBusFee,
                'jul_fee' => $req->julFee,
                // 'jul_bus_fee' => $req->julBusFee,
                'aug_fee' => $req->augFee,
                // 'aug_bus_fee' => $req->augBusFee,
                'sep_fee' => $req->sepFee,
                // 'sep_bus_fee' => $req->sepBusFee,
                'oct_fee' => $req->octFee,
                // 'oct_bus_fee' => $req->octBusFee,
                'nov_fee' => $req->novFee,
                // 'nov_bus_fee' => $req->novBusFee,
                'dec_fee' => $req->decFee,
                // 'dec_bus_fee' => $req->decBusFee,
                'version_no' => $getData->version_no + 1,
                'updated_at' => Carbon::now()
            ];

            if (isset($req->status)) {                  // In Case of Deactivation or Activation 
                $status = $req->status == 'deactive' ? 0 : 1;
                $metaReqs = array_merge($metaReqs, [
                    'status' => $status
                ]);
            }

            $feeDefinition = $this->_mFeeDefinitions::findOrFail($req->id);
            $feeDefinition->update($metaReqs);
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
            // $feeDefinition = $this->_mFeeDefinitions::findOrFail($req->id);
            $show = $this->_mFeeDefinitions->getGroupById($req->id);
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
            // $feeDefinition = $this->_mFeeDefinitions::orderByDesc('id')->get();
            $feeDefinitionGroupMap = $this->_mFeeDefinitions->retrieveAll();
            return responseMsgs(true, "All Records", $feeDefinitionGroupMap, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
}
