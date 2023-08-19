<?php

namespace App\Http\Controllers\API\Transport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

//add model
use App\Models\Transport\Vehicle;
use App\Models\Transport\Route;
use App\Models\Transport\TmPickupPoint;
use Exception;

/*=================================================== Transport =========================================================
Created By : Lakshmi kumari 
Created On : 18-May-2023 
Code Status : Open 
*/

class TransportController extends Controller
{
   //Route API Start
   /*
    Created By : Lakshmi kumari 
    Created On : 18-May-2023 
    API: Route CRUD
   */
    /**
     *  @OA\Post(
     *  path="/add_route",
     *  tags={"Route"},
     *  summary="Add Route",
     *  operationId="addRoute",
     *  @OA\Parameter(name="routeName",in="query",required=true,@OA\Schema(type="string",example="")),     
     *  @OA\Response(response=201,description="Success",@OA\MediaType(mediaType="application/json",)),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *  @OA\Response(response=400,description="Bad Request"),
     *  @OA\Response(response=404,description="not found"),     
     *)
    **/
    public function addRoute(Request $req){
       //Description: store master records
       try 
        {
           $data = array();
           $validator = Validator::make($req->all(),[
           'routeName'=>'required|string'
           ]);
           if ($validator->fails()) {
               $errors = $validator->errors();
               return response()->json([
                   'error' => $errors
               ], 400);
           }
           if ($validator->passes()) {
               $mObject = new Route();
               $data = $mObject->insertData($req);
               $mDeviceId = $req->deviceId ?? "";         
               return responseMsgs(true, "Records added successfully", $data, "API_ID_46","", "1135ms", "post", $mDeviceId);
           }
        } catch (Exception $e) {
           return responseMsgs(false, $e->getMessage(), $data, "API_ID_46","", "", "post", $mDeviceId);
        } 
    }

    /**
     * @OA\Get(
     * path="/view_route",
     * operationId="viewRoute",
     * tags={"Route"},
     * summary="View Route",
     * description="View Route",           
     * @OA\Response(response=200, description="Success",
     * @OA\JsonContent(@OA\Property(property="status", type="string", example="200"),
     *  @OA\Property(property="data",type="object"))))
    */
    public function viewRoute(Request $req){
        //Description : Get all records
        try {
            $data = Route::list(); 
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_47","", "475ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_47","", "", "get", $mDeviceId);
        }
    }

    public function viewRouteById(Request $req){ 
        //Description: Get records by id
        try {
            $listbyId = new Route();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_48","", "331ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_48","", "", "post", $mDeviceId);
        }     
    }

    /**
     * @OA\Post(
     * path="/edit_route",
     * tags={"Route"},
     * summary="Edit Route",
     * operationId="editRoute",
     * @OA\Parameter(name="id",in="query",required=true,@OA\Schema(type="string",example="1")),
     * @OA\Parameter(name="routeName",in="query",required=true,@OA\Schema(type="string",example="")),     
     * @OA\Response(response=200, description="Success",@OA\JsonContent(
     *    @OA\Property(property="status", type="integer", example=""),
     *    @OA\Property(property="data",type="object")
     *  )))
    **/
    public function editRoute(Request $req){
        //Description: edit records of a particular id 
        try {
            $mObject = new Route();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_49","", "394ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_49","", "", "post", $mDeviceId);
        } 

    }

    /**
     * @OA\Post(
     * path="/delete_route",
     * operationId="deleteRouteById",
     * tags={"Route"},
     * summary="Delete Route",
     * description="Delete Route",
     * @OA\RequestBody(required=true,@OA\JsonContent(required={"id"},
     * @OA\Property(property="id", type="string", format="string", example="1"),),),
     * @OA\Response(response=200, description="Success",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="integer", example=""),
     *    @OA\Property(property="data",type="object")
     * )))
    **/
    public function deleteRouteById(Request $req){
        //Description: delete record of a particular id
        try {
            $mObject = new Route();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_50","", "342ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_50","", "", "post", $mDeviceId);
        } 
    }

    public function deleteAllRoute(Request $req){
        //Description: delete all records 
        // try {
        //     $mObject = new Route();
        //     $data = $mObject->truncateData();
        //     $mDeviceId = $req->deviceId ?? "";
        //     return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_51","", "381ms", "delete", $mDeviceId);
        // } catch (Exception $e) {
        //     return responseMsgs(false, $e->getMessage(), $data, "API_ID_51","", "", "delete", $mDeviceId);
        // }    
    }
    // Route API End

    /* ========================================= Vehicle API Start ========================================================
	
	Created By : Umesh Kumar
	Created On : 20-May-2023 
	Code Status : Open 
	*/
    /**
     *  @OA\Post(
     *  path="/add_vehicle",
     *  tags={"Vehicle"},
     *  summary="Add Vehicle",
     *  operationId="addVehicle",
     *  @OA\Parameter(name="vehicleNo",in="query",required=true,@OA\Schema(type="string",example="JH01432")),  
     *  @OA\Parameter(name="vehicleModel",in="query",required=true,@OA\Schema(type="string",example="ABC")),
     *  @OA\Parameter(name="yearMade",in="query",required=true,@OA\Schema(type="string",example="2022")),
     *  @OA\Parameter(name="engineNo",in="query",required=true,@OA\Schema(type="string",example="ADGF3453")),
     *  @OA\Parameter(name="maxSeatingCapacity",in="query",required=true,@OA\Schema(type="integer",example="25")),       
     *  @OA\Parameter(name="registrationNo",in="query",required=true,@OA\Schema(type="string",example="SC776234")),
     *  @OA\Parameter(name="chassisNo",in="query",required=true,@OA\Schema(type="integer",example="63524575")),
     *  @OA\Parameter(name="gprs",in="query",required=true,@OA\Schema(type="string", example="true")),
     *
     * 
     *  @OA\Parameter(name="taxPaidDate",in="query",required=false,@OA\Schema(type="string",example="")),
     *  @OA\Parameter(name="taxExpiryDate",in="query",required=false,@OA\Schema(type="string",example="")),
     *  @OA\Parameter(name="pollutionControlDate",in="query",required=false,@OA\Schema(type="string",example="")),
     *  @OA\Parameter(name="pollutionExpiryDate",in="query",required=false,@OA\Schema(type="string",example="")),       
     *  @OA\Parameter(name="fitnessDate",in="query",required=false,@OA\Schema(type="string",example="")),
     *  @OA\Parameter(name="fitnessExpiryDate",in="query",required=false,@OA\Schema(type="string",example="")),
     *  @OA\Parameter(name="adacemicYear",in="query",required=false,@OA\Schema(type="string", example="")),
     * 
     *  @OA\RequestBody(required=false,@OA\MediaType(mediaType="multipart/form-data",
     *  @OA\Schema(@OA\Property(property="vehiclePhoto",description="upload image",type="file",format="binary")))),  
     *    
     *  @OA\Response(response=201,description="Success",@OA\MediaType(mediaType="application/json",)),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *  @OA\Response(response=400,description="Bad Request"),
     *  @OA\Response(response=404,description="not found"),     
     *)
    **/
    public function addVehicle(Request $req){
        //Description: store master records
        try 
         {
            $data = array();
            $validator = Validator::make($req->all(),[
            'vehicleNo'=>'required|string',
            'vehicleModel' => 'required|string',
            'yearMade' => 'required|date_format:Y',
            'engineNo' => 'required|string',
            'maxSeatingCapacity' => 'integer',
            'registrationNo' => 'required|string',
            'chassisNo' => 'required|integer',
            // 'vehiclePhoto' => 'file|mimes:jpg,jpeg',
            'gprs' => 'required|string',
            'taxPaidDate' => 'string',
            'taxExpiryDate' => 'string',
            'pollutionControlDate' => 'string',
            'pollutionExpiryDate' => 'string',
            'fitnessDate' => 'string',
            'fitnessExpiryDate' => 'string',
            'adacemicYear' => 'string',
            ]);           
                     
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json([
                    'error' => $errors
                ], 400);
            }
            if ($validator->passes()) {
                $mObject = new Vehicle();
                $data = $mObject->insertData($req);
		    //$mDeviceId = $req->deviceId ?? "";
                $mDeviceId = "";
                return responseMsgs(true, "Records added successfully", $data, "API_ID_229","", "1135ms", "post", $mDeviceId);
            }
         } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_229","", "", "post", $mDeviceId);
         } 
     }
 
     /**
      * @OA\Get(
      * path="/view_vehicle",
      * operationId="viewVehicle",
      * tags={"Vehicle"},
      * summary="View Vehicle",
      * description="View Vehicle",           
      * @OA\Response(response=200, description="Success",
      * @OA\JsonContent(@OA\Property(property="status", type="string", example="200"),
      *  @OA\Property(property="data",type="object"))))
     */
     public function viewVehicle(Request $req){
         //Description : Get all records
         try {
             $data = Vehicle::list(); 
             //$mDeviceId = $req->deviceId ?? "";
                $mDeviceId = "";
             return responseMsgs(true, "View all records", $data, "API_ID_230","", "475ms", "get", $mDeviceId);
         } catch (Exception $e) {
             return responseMsgs(false, $e->getMessage(), $data, "API_ID_230","", "", "get", $mDeviceId);
         }
     }
 
     public function viewVehicleById(Request $req){ 
         //Description: Get records by id
         try {
             $listbyId = new Vehicle();
             $data  = $listbyId->listById($req);
             //$mDeviceId = $req->deviceId ?? "";
                $mDeviceId = "";
             return responseMsgs(true, "View all records", $data, "API_ID_231","", "331ms", "post", $mDeviceId);
         } catch (Exception $e) {
             return responseMsgs(false, $e->getMessage(), $data, "API_ID_231","", "", "post", $mDeviceId);
         }     
     }
 
     /**
      * @OA\Post(
      * path="/edit_vehicle",
      * tags={"Vehicle"},
      * summary="Edit Vehicle",
      * operationId="editVehicle",
      * @OA\Parameter(name="id",in="query",required=true,@OA\Schema(type="string",example="1")),
     *  @OA\Parameter(name="vehicleNo",in="query",required=true,@OA\Schema(type="string",example="JH01432")),  
     *  @OA\Parameter(name="vehicleModel",in="query",required=true,@OA\Schema(type="string",example="ABC")),
     *  @OA\Parameter(name="yearMade",in="query",required=true,@OA\Schema(type="string",example="2022")),
     *  @OA\Parameter(name="engineNo",in="query",required=true,@OA\Schema(type="string",example="ADGF3453")),
     *  @OA\Parameter(name="maxSeatingCapacity",in="query",required=true,@OA\Schema(type="integer",example="25")),       
     *  @OA\Parameter(name="registrationNo",in="query",required=true,@OA\Schema(type="string",example="SC776234")),
     *  @OA\Parameter(name="chassisNo",in="query",required=true,@OA\Schema(type="integer",example="63524575")),
     *  @OA\Parameter(name="gprs",in="query",required=true,@OA\Schema(type="string",example="true")),
     * 
     *  @OA\Parameter(name="taxPaidDate",in="query",required=false,@OA\Schema(type="string",example="")),
     *  @OA\Parameter(name="taxExpiryDate",in="query",required=false,@OA\Schema(type="string",example="")),
     *  @OA\Parameter(name="pollutionControlDate",in="query",required=false,@OA\Schema(type="string",example="")),
     *  @OA\Parameter(name="pollutionExpiryDate",in="query",required=false,@OA\Schema(type="string",example="")),       
     *  @OA\Parameter(name="fitnessDate",in="query",required=false,@OA\Schema(type="string",example="")),
     *  @OA\Parameter(name="fitnessExpiryDate",in="query",required=false,@OA\Schema(type="string",example="")),
     *  @OA\Parameter(name="academicYear",in="query",required=false,@OA\Schema(type="string", example="")),
     * 
     *  @OA\RequestBody(required=false,@OA\MediaType(mediaType="multipart/form-data",
     *  @OA\Schema(@OA\Property(property="vehiclePhoto",description="upload image",type="file",format="binary")))),  
     * 
      * @OA\Response(response=200, description="Success",@OA\JsonContent(
      *    @OA\Property(property="status", type="integer", example=""),
      *    @OA\Property(property="data",type="object")
      *  )))
     **/
     public function editVehicle(Request $req){
         //Description: edit records of a particular id 
         
         try {
             $mObject = new Vehicle();
             $data = $mObject->updateData($req);
	       //$mDeviceId = $req->deviceId ?? "";
                $mDeviceId = "";
             return responseMsgs(true, "Records updated successfully", $data, "API_ID_232","", "394ms", "post", $mDeviceId);
         } catch (Exception $e) {
             return responseMsgs(false, $e->getMessage(), $data, "API_ID_232","", "", "post", $mDeviceId);
         } 
 
     }
 
     /**
      * @OA\Post(
      * path="/delete_vehicle",
      * operationId="deleteVehicleById",
      * tags={"Vehicle"},
      * summary="Delete Vehicle",
      * description="Delete Vehicle",
      * @OA\RequestBody(required=true,@OA\JsonContent(required={"id"},
      * @OA\Property(property="id", type="string", format="string", example="1"),),),
      * @OA\Response(response=200, description="Success",
      * @OA\JsonContent(
      * @OA\Property(property="status", type="integer", example=""),
      *    @OA\Property(property="data",type="object")
      * )))
     **/
     public function deleteVehicleById(Request $req){
         //Description: delete record of a particular id
         try {
             $mObject = new Vehicle();
             $data = $mObject->deleteData($req);
             $mDeviceId = $req->deviceId ?? "";
             return responseMsgs(true, "Records deleted successfully", $data, "API_ID_233","", "342ms", "post", $mDeviceId);
         } catch (Exception $e) {
             return responseMsgs(false, $e->getMessage(), $data, "API_ID_233","", "", "post", $mDeviceId);
         } 
     }
 
     public function deleteAllVehicle(Request $req){
         //Description: delete all records 
         try {
             $mObject = new Vehicle();
             $data = $mObject->truncateData();
             $mDeviceId = $req->deviceId ?? "";
             return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_234","", "381ms", "delete", $mDeviceId);
         } catch (Exception $e) {
             return responseMsgs(false, $e->getMessage(), $data, "API_ID_234","", "", "delete", $mDeviceId);
         }    
     }

    // ========================================= Vehicle API Start ===================================================

   // ========================================= Pickup Point API Start =========================================================
    /**
     *  @OA\Post(
     *  path="/add_pickup_point",
     *  tags={"PickupPoint"},
     *  summary="Add PickupPoint",
     *  operationId="addPickupPoint",
     *  @OA\Parameter(name="pickupPointName",in="query",required=true,@OA\Schema(type="string",example="PickUpPoint 1")),     
     *  @OA\Parameter(name="pickupPointAddress",in="query",required=true,@OA\Schema(type="string",example="Lalpur")),     
     *  @OA\Response(response=201,description="Success",@OA\MediaType(mediaType="application/json",)),
     *  @OA\Response(response=401,description="Unauthenticated"),
     *  @OA\Response(response=400,description="Bad Request"),
     *  @OA\Response(response=404,description="not found"),     
     *)
    **/
    public function addPickupPoint(Request $req){
        //Description: store master records
        try 
         {
            $data = array();
            $validator = Validator::make($req->all(),[
            'pickupPointName'=>'required|string',
            'pickupPointAddress'=>'required|string',

            ]);
            $mDeviceId = $req->deviceId ?? "";         
 
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json([
                    'error' => $errors
                ], 400);
            }
            if ($validator->passes()) {
                $mObject = new TmPickupPoint();
                $data = $mObject->insertData($req);
                return responseMsgs(true, "Records added successfully", $data, "API_ID_46","", "1135ms", "post", $mDeviceId);
            }
         } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_46","", "", "post", $mDeviceId);
         } 
     }

    /**
      * @OA\Get(
      * path="/view_pickup_point",
      * operationId="viewPickupPoint",
      * tags={"PickupPoint"},
      * summary="View PickupPoint",
      * description="View PickupPoint",           
      * @OA\Response(response=200, description="Success",
      * @OA\JsonContent(@OA\Property(property="status", type="string", example="200"),
      *  @OA\Property(property="data",type="object"))))
     */
    public function viewPickupPoint(Request $req){
        //Description : Get all records
        try {
            $data = TmPickupPoint::list(); 
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_47","", "475ms", "get", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_47","", "", "get", $mDeviceId);
        }
    }


    public function viewPickupPointById(Request $req){ 
        //Description: Get records by id
        try {
            $listbyId = new TmPickupPoint();
            $data  = $listbyId->listById($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "View all records", $data, "API_ID_48","", "331ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_48","", "", "post", $mDeviceId);
        }     
    }

    /**
     * @OA\Post(
     * path="/edit_pickup_point",
     * tags={"PickupPoint"},
     * summary="Edit PickupPoint",
     * operationId="editPickupPoint",
     * @OA\Parameter(name="id",in="query",required=true,@OA\Schema(type="string",example="1")),
     *  @OA\Parameter(name="pickupPointName",in="query",required=true,@OA\Schema(type="string",example="PickUpPoint 1")),     
     *  @OA\Parameter(name="pickupPointAddress",in="query",required=true,@OA\Schema(type="string",example="Lalpur")),   
     * @OA\Response(response=200, description="Success",@OA\JsonContent(
     *    @OA\Property(property="status", type="integer", example=""),
     *    @OA\Property(property="data",type="object")
     *  )))
    **/
    public function editPickupPoint(Request $req){
        //Description: edit records of a particular id 
        try {
            $mObject = new TmPickupPoint();
            $data = $mObject->updateData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records updated successfully", $data, "API_ID_49","", "394ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_49","", "", "post", $mDeviceId);
        } 

    }

 
     /**
      * @OA\Post(
      * path="/delete_pickup_point",
      * operationId="deletePickupPointById",
      * tags={"PickupPoint"},
      * summary="Delete PickupPoint",
      * description="Delete PickupPoint",
      * @OA\RequestBody(required=true,@OA\JsonContent(required={"id"},
      * @OA\Property(property="id", type="string", format="string", example="1"),),),
      * @OA\Response(response=200, description="Success",
      * @OA\JsonContent(
      * @OA\Property(property="status", type="integer", example=""),
      *    @OA\Property(property="data",type="object")
      * )))
     **/
    public function deletePickupPointById(Request $req){
        //Description: delete record of a particular id
        try {
            $mObject = new TmPickupPoint();
            $data = $mObject->deleteData($req);
            $mDeviceId = $req->deviceId ?? "";
            return responseMsgs(true, "Records deleted successfully", $data, "API_ID_50","", "342ms", "post", $mDeviceId);
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), $data, "API_ID_50","", "", "post", $mDeviceId);
        } 
    }

    public function deleteAllPickupPoint(Request $req){
        //Description: delete all records 
        // try {
        //     $mObject = new TmPickupPoint();
        //     $data = $mObject->truncateData();
        //     $mDeviceId = $req->deviceId ?? "";
        //     return responseMsgs(true, "All records has been deleted successfully", $data, "API_ID_51","", "381ms", "delete", $mDeviceId);
        // } catch (Exception $e) {
        //     return responseMsgs(false, $e->getMessage(), $data, "API_ID_51","", "", "delete", $mDeviceId);
        // }    
    }
   // ============================================== Pickup Point API End =======================================================
    
}
