<?php

namespace App\Http\Controllers\API\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Models\Master\MenuGroupMap;
use Exception;
use DB;

/**
 * | Created On- 06-07-2023 
 * | Created By- Lakshmi Kumari
 * | Description- Menu Group Map CRUDS Operations
 * | Code Status- Closed
 */
class MenuGroupMapController extends Controller
{
    private $_mMenusGroupMap;

    public function __construct()
    {
        DB::enableQueryLog();
        $this->_mMenusGroupMap = new MenuGroupMap();
    }

    // Add records
    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'menuMap' => 'required|array',
            'menuMap.*.menuId' => 'required|numeric',
            'menuMap.*.subMenuId' => 'required|numeric',
            'menuMap.*.roleId' => 'required|numeric',
            'menuMap.*.isWrite' => 'numeric',
            'menuMap.*.isRead' => 'numeric',
            'menuMap.*.isUpdate' => 'numeric',
            'menuMap.*.isDeactivate' => 'numeric'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $isExists = $this->_mMenusGroupMap->readMenuGroup($req);
            if (collect($isExists)->isNotEmpty())
                throw new Exception("Menu Group Map Already existing");
            $arrFam = array();
            if ($req['menuMap'] != "") {

                foreach ($req['menuMap'] as $ob) {
                    $menuMaps = new MenuGroupMap;
                    $menuMaps->menu_id = $ob['menuId'];;
                    $menuMaps->sub_menu_id = $ob['subMenuId'];
                    $menuMaps->is_write = $ob['isWrite'];
                    $menuMaps->is_read = $ob['isRead'];
                    $menuMaps->is_update = $ob['isUpdate'];
                    $menuMaps->is_deactivate = $ob['isDeactivate'];
                    $menuMaps->school_id = authUser()->school_id;
                    $menuMaps->created_by = authUser()->id;
                    $menuMaps->ip_address = getClientIpAddress();
                    $menuMaps->version_no = '0';
                    $menuMaps->role_id = $ob['roleId'];
                    $menuMaps->save();
                    $arrFam[] = $menuMaps;
                }
            }


            // $metaReqs = [
            //     'menu_id' => $req->menuId,
            //     'sub_menu_id' => $req->subMenuId,
            //     'is_write' => $req->isWrite,
            //     'is_read' => $req->isRead,
            //     'is_update' => $req->isUpdate,
            //     'is_deactivate' => $req->isDeactivate,
            //     'school_id' => authUser()->school_id,
            //     'created_by' => authUser()->id,
            //     'ip_address' => getClientIpAddress(),
            //     'version_no' => 0,
            //     'role_id' => $req->roleId

            // ];
            // $metaReqs = array_merge($metaReqs, [
            //     'json_logs' => trim(json_encode($metaReqs), ",")
            // ]);
            // $this->_mMenusGroupMap->store($metaReqs);
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Records Added Successfully", $arrFam, "M_API_39.1", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_39.1", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    // Edit records
    public function edit(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'id' => 'required|numeric',
            'menuId' => 'required|numeric',
            'subMenuId' => 'required|numeric',
            'isWrite' => 'required|numeric',
            'isRead' => 'required|numeric',
            'isUpdate' => 'required|numeric',
            'isDeactivate' => 'required|numeric'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $isExists = $this->_mMenusGroupMap->readMenuGroup($req);
            if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
                throw new Exception("Menu Group Map Already existing");
            $getData = $this->_mMenusGroupMap::findOrFail($req->id);
            $metaReqs = [
                'menu_id' => $req->menuId,
                'sub_menu_id' => $req->subMenuId,
                'is_write' => $req->isWrite,
                'is_read' => $req->isRead,
                'is_update' => $req->isUpdate,
                'is_deactivate' => $req->isDeactivate,
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
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Records Updated Successfully", [], "M_API_33.2", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_33.2", responseTime(), "POST", $req->deviceId ?? "");
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
            $show = $this->_mMenusGroupMap->getGroupById($req->id);
            if (collect($show)->isEmpty())
                throw new Exception("Data Not Found");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View Records", $show, "M_API_33.3", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_33.3", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
    //View All
    public function retrieveAll(Request $req)
    {
        try {
            $getData = $this->_mMenusGroupMap->retrieve();
            // $perPage = $req->perPage ? $req->perPage : 10;
            // $paginater = $getData->paginate($perPage);
            // $list = [
            //     "current_page" => $paginater->currentPage(),
            //     "perPage" => $perPage,
            //     "last_page" => $paginater->lastPage(),
            //     "data" => $paginater->items(),
            //     "total" => $paginater->total()
            // ];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View All Records", $getData, "M_API_33.4", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_33.4", responseTime(), "POST", $req->deviceId ?? "");
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
            $delete = $this->_mMenusGroupMap::findOrFail($req->id);
            $delete->update($metaReqs);
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Changes Done Successfully", $req->status, "M_API_33.5", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_33.5", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //Active All
    public function activeAll(Request $req)
    {
        try {
            $getData = $this->_mMenusGroupMap->active();
            if (collect($getData)->isEmpty())
                throw new Exception("Data Not Found");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View All Active Records", $getData, "M_API_33.6", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_33.6", responseTime(), "POST", $req->deviceId ?? "");
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
            $getData = $this->_mMenusGroupMap->searchByName($req);
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
            return responseMsgsT(true, "View Searched Records", $list, "M_API_33.7", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_33.7", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
}
