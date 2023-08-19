<?php

namespace App\Http\Controllers\API\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Models\Master\SubMenu;
use Exception;
use DB;

/**
 * | Created On- 16-06-2023 
 * | Created By- Lakshmi Kumari
 * | Description- Role CRUDS Operations
 * | Code Status- Closed
 */
class SubMenuController extends Controller
{
    private $_mSubMenus;

    public function __construct()
    {
        DB::enableQueryLog();
        $this->_mSubMenus = new SubMenu();
    }

    // Add records 
    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'subMenuName' => 'required|string',
            'orderNo' => 'required|numeric',
            'menuId' => 'required|numeric'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $isGroupExists = $this->_mSubMenus->readSubMenuGroup($req);
            if (collect($isGroupExists)->isNotEmpty())
                throw new Exception("Sub Menu Already Existing");
            $isGroupExists1 = $this->_mSubMenus->readSubMenuGroupOrder($req);
            if (collect($isGroupExists1)->isNotEmpty())
                throw new Exception("Sub Menu Order No Already Existing");

            $metaReqs = [
                'sub_menu_name' => Str::title($req->subMenuName),
                'menu_id' => $req->menuId,
                'order_no' => $req->orderNo,
                'school_id' => authUser()->school_id,
                'created_by' => authUser()->id,
                'ip_address' => getClientIpAddress(),
                'version_no' => 0
            ];
            $metaReqs = array_merge($metaReqs, [
                'json_logs' => trim(json_encode($metaReqs), ",")
            ]);
            $this->_mSubMenus->store($metaReqs);
            $data = ['Sub Menu Name' => $req->subMenuName];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Records Added Successfully", $data, "M_API_35.1", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_35.1", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    // Edit records
    public function edit(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'id' => 'required|numeric',
            'subMenuName' => 'required|string',
            'orderNo' => 'required|numeric',
            'menuId' => 'required|numeric'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $isExists = $this->_mSubMenus->readSubMenuGroup($req);
            if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
                throw new Exception("Sub Menu Already Existing");
            $getData = $this->_mSubMenus::findOrFail($req->id);
            $metaReqs = [
                'sub_menu_name' => Str::title($req->subMenuName),
                'menu_id' => $req->menuId,
                'order_no' => $req->orderNo,
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
            $data = ['Menu Name' => $req->subMenuName];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Records Updated Successfully", $data, "M_API_35.2", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_35.2", responseTime(), "POST", $req->deviceId ?? "");
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
            $show = $this->_mSubMenus->getGroupById($req->id);
            if (collect($show)->isEmpty())
                throw new Exception("Data Not Found");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View Records", $show, "M_API_35.3", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_35.3", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //View by id
    public function showAll(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'menuId' => 'required|numeric'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $show = $this->_mSubMenus->getSubMenuGroupByMenuId($req);
            if (collect($show)->isEmpty())
                throw new Exception("Data Not Found");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View Records", $show, "M_API_35.3", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_35.3", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //View All
    public function retrieveAll(Request $req)
    {
        try {
            $getData = $this->_mSubMenus->retrieve();
            // if (collect($getData)->isNotEmpty())
            //     throw new Exception("Data Not Found");
            $perPage = $req->perPage ? $req->perPage : 10;
            $paginater = $getData->paginate($perPage);
            // if (!$paginater->total())
            //     throw new Exception("Data Not Found");
            $list = [
                "current_page" => $paginater->currentPage(),
                "perPage" => $perPage,
                "last_page" => $paginater->lastPage(),
                "data" => $paginater->items(),
                "total" => $paginater->total()
            ];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View All Records", $list, "M_API_35.4", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_35.4", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //deactive / active
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
            $delete = $this->_mSubMenus::findOrFail($req->id);
            $delete->update($metaReqs);
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Changes Done Successfully", $req->status, "M_API_35.5", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_35.5", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //Active All
    public function activeAll(Request $req)
    {
        try {
            $getData = $this->_mSubMenus->active();
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View All Active Records", $getData, "M_API_35.6", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_35.6", responseTime(), "POST", $req->deviceId ?? "");
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
            $getData = $this->_mSubMenus->searchByName($req);
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
            return responseMsgsT(true, "View Searched Records", $list, "M_API_35.7", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_35.7", responseTime(), "POST", $req->deviceId ?? "");
        }
    }





    // // Add records
    // public function store(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'subMenuName' => 'required|string',
    //         'menuId' => 'required|numeric',
    //     ]);
    //     if ($validator->fails())
    //         return responseMsgs(false, $validator->errors(), []);

    //     try {
    //         $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
    //         $isExists = $this->_mSubMenus->readSubMenuGroup(Str::ucfirst($req->subMenuName));
    //         if (collect($isExists)->isNotEmpty())
    //             throw new Exception("Sub Menu Already Existing");

    //         $metaReqs = [
    //             'sub_menu_name' => Str::ucfirst($req->subMenuName),
    //             'menu_id' => $req->menuId,
    //             'academic_year' => $fy,
    //             'school_id' => authUser()->school_id,
    //             'created_by' => authUser()->id,
    //             'ip_address' => getClientIpAddress()
    //         ];
    //         $this->_mSubMenus->store($metaReqs);
    //         return responseMsgs(true, "Successfully Saved", [$metaReqs], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }


    // // Edit records
    // public function edit(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'id' => 'required|numeric',
    //         'subMenuName' => 'required|string',
    //         'menuId' => 'required|numeric'
    //     ]);
    //     if ($validator->fails())
    //         return responseMsgs(false, $validator->errors(), []);
    //     try {
    //         $isExists = $this->_mSubMenus->readSubMenuGroup(Str::ucfirst($req->subMenuName));
    //         if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
    //             throw new Exception("Sub Menu Already existing");

    //         $getData = $this->_mSubMenus::findOrFail($req->id);
    //         $metaReqs = [
    //             'sub_menu_name' => Str::ucfirst($req->subMenuName),
    //             'menu_id' => $req->menuId,
    //             'version_no' => $getData->version_no + 1,
    //             'updated_at' => Carbon::now()
    //         ];
    //         if (isset($req->status)) {                  // In Case of Deactivation or Activation 
    //             $status = $req->status == 'deactive' ? 0 : 1;
    //             $metaReqs = array_merge($metaReqs, [
    //                 'status' => $status
    //             ]);
    //         }
    //         $teachingTitle = $this->_mSubMenus::findOrFail($req->id);
    //         $teachingTitle->update($metaReqs);
    //         return responseMsgs(true, "Successfully Updated", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

    // /**
    //  * | Get Discont Group By Id
    //  */
    // public function show(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'id' => 'required|numeric'
    //     ]);
    //     if ($validator->fails())
    //         return responseMsgs(false, $validator->errors(), []);
    //     try {
    //         $show = $this->_mSubMenus->getGroupById($req->id);
    //         if (collect($show)->isEmpty())
    //             throw new Exception("Data Not Found");
    //         return responseMsgs(true, "", $show, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

    // //view by name
    // public function search(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'search' => 'required|string'
    //     ]);
    //     if ($validator->fails())
    //         return responseMsgs(false, $validator->errors(), []);
    //     try {
    //         $search = $this->_mSubMenus->searchByName(Str::ucfirst($req->subMenuName));
    //         if (collect($search)->isEmpty())
    //             throw new Exception("Sub Menu Does Not Exists");
    //         return responseMsgs(true, "", $search, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

    // //View All
    // public function retrieveAll(Request $req)
    // {
    //     try {
    //         // $teachingTitle = $this->_mSubMenus::orderByDesc('id')->where('status', '1')->get();
    //         $teachingTitle = $this->_mSubMenus->retrieveAll();
    //         return responseMsgs(true, "", $teachingTitle, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }
    // //Active All
    // public function activeAll(Request $req)
    // {
    //     try {
    //         $subMenu = $this->_mSubMenus->active();
    //         return responseMsgs(true, "", $subMenu, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }

    // //Delete
    // public function delete(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'status' => 'required|in:active,deactive'
    //     ]);
    //     if ($validator->fails())
    //         return responseMsgs(false, $validator->errors(), []);
    //     try {
    //         if (isset($req->status)) {                  // In Case of Deactivation or Activation 
    //             $status = $req->status == 'deactive' ? 0 : 1;
    //             $metaReqs =  [
    //                 'status' => $status
    //             ];
    //         }
    //         $subMenu = $this->_mSubMenus::findOrFail($req->id);
    //         //  if ($subMenu->status == 0)
    //         //      throw new Exception("Records Already Deleted");
    //         $subMenu->update($metaReqs);
    //         return responseMsgs(true, "Deleted Successfully", [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }
}
