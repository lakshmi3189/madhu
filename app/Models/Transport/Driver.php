<?php

namespace App\Models\Transport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Driver extends Model
{
  use HasFactory;
  protected $guarded = [];

  /*Add Records*/
  public function store(array $req)
  {
    Driver::create($req);
  }

  /*Read Records by name*/
  public function readDriverGroup($req)
  {
    $schoolId = authUser()->school_id;
    return Driver::where('license_no', $req->licenseNo)
      ->where('aadhar_no', $req->aadharNo)
      ->where('pan_no', $req->panNo)
      ->where('status', 1)
      // ->where('school_id', $schoolId)
      ->get();
  }

  //Get Records by name
  public function searchByName($req)
  {
    $schoolId = authUser()->school_id;
    return Driver::select(
      DB::raw("id,driver_name,mobile,email,address,license_no,aadhar_no,pan_no,photo_doc,aadhar_doc,license_doc,pan_doc,
       CASE 
       WHEN status = '0' THEN 'Deactivated'  
       WHEN status = '1' THEN 'Active'
       END as status,
       TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
       TO_CHAR(created_at,'HH12:MI:SS AM') as time
   ")
    )
      ->where('driver_name', 'ilike', $req->search . '%');
      // ->where('school_id', $schoolId);
    // ->where('status', 1)
    // ->get();
  }


  /*Read Records by ID*/ 
  public function getGroupById($id)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    $getData = Driver::select(
      DB::raw("id,driver_name,mobile,email,address,license_no,aadhar_no,pan_no,photo_doc,aadhar_doc,license_doc,pan_doc,
      CASE 
        WHEN status = '0' THEN 'Deactivated'  
        WHEN status = '1' THEN 'Active'
      END as status,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
	  ")
    )
      ->where('id', $id)
      // ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      // ->where('status', 1)
      ->first();

      // $path = 'api\getImageLink?path=';

      $path = "getImageLink?path=";
      $photoDoc = $path . $getData->photo_doc;
      $getData->photo_doc = $photoDoc;

      $aadharDoc = $path . $getData->aadhar_doc;
      $getData->aadhar_doc = $aadharDoc;

      $licenseDoc = $path . $getData->license_doc;
      $getData->license_doc = $licenseDoc;

      $panDoc = $path . $getData->pan_doc;
      $getData->pan_doc = $panDoc;

        

    return $getData; 

  }

  

  /*Read all Records by*/
  public function retrieve()
  {
    $schoolId = authUser()->school_id;
    return Driver::select(
      DB::raw("id,driver_name,mobile,email,address,license_no,aadhar_no,pan_no,photo_doc,aadhar_doc,license_doc,pan_doc,
    CASE 
    WHEN status = '0' THEN 'Deactivated'  
    WHEN status = '1' THEN 'Active'
    END as status,
    TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
    TO_CHAR(created_at,'HH12:MI:SS AM') as time
")
    )
      // ->where('school_id', $schoolId)
      ->orderBy('id');
    // ->get();
  }


  /*Read all Active Records*/
  public function active()
  {
    // $schoolId = authUser()->school_id;
    $viewAll = Driver::select(
      DB::raw("id,driver_name,mobile,email,address,license_no,aadhar_no,pan_no,photo_doc,aadhar_doc,license_doc,pan_doc,
      CASE 
      WHEN status = '0' THEN 'Deactivated'  
      WHEN status = '1' THEN 'Active'
      END as status,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
    ")
    )
      ->where('status', 1)
      // ->where('school_id', $schoolId)
      ->orderBy('driver_name')
      ->get();

    $data = array();
    foreach ($viewAll as $v) {
      $dataArr = array();
      $path = 'api\getImageLink?path=';
      $file_name = $path . $v->photo_doc;
      $dataArr['id'] = $v->id;
      $dataArr['photo_doc'] = $file_name;
      $dataArr['driver_name'] = $v->driver_name;
      $dataArr['mobile'] = $v->mobile;
      $dataArr['email'] = $v->email;
      $dataArr['address'] = $v->address;
      $dataArr['license_no'] = $v->license_no;
      $dataArr['aadhar_no'] = $v->aadhar_no;
      $dataArr['status'] = $v->status;
      $dataArr['date'] = $v->date;
      $dataArr['time'] = $v->time;
      $data[] = $dataArr;
    }
    return $data;
  }
  // /*Read all Records by*/
  // public function retrieveAll()
  // {
  //   $data = array();
  //   $schoolId = authUser()->school_id;
  //   // $createdBy = authUser()->id;
  //   $viewAll = Driver::select(
  //     '*',
  //     DB::raw("
  //     CASE 
  //       WHEN status = '0' THEN 'Deactivated'  
  //       WHEN status = '1' THEN 'Active'
  //     END as status,
  //     TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
  //     TO_CHAR(created_at,'HH12:MI:SS AM') as time
  //   ")
  //   )
  //     ->where('school_id', $schoolId)
  //     // ->where('created_by', $createdBy)
  //     // ->where('status', 1)
  //     ->orderByDesc('id')
  //     ->get();
  //   foreach ($viewAll as $v) {
  //     $dataArr = array();

  //     //$path = baseURL() . '/school/events/';
  //     $path = 'api\getImageLink?path=';
  //     $img = $path . $v->photo_doc;
  //     $aadhar = $path . $v->aadhar_doc;
  //     $pan = $path . $v->pan_doc;
  //     $license = $path . $v->license_doc;

  //     $file_name = $path . $v->upload_event_docs;
  //     $dataArr['id'] = $v->id;
  //     $dataArr['driver_name'] = $v->driver_name;
  //     $dataArr['mobile'] = $v->mobile;
  //     $dataArr['email'] = $v->email;
  //     $dataArr['address'] = $v->address;
  //     $dataArr['license_no'] = $v->license_no;
  //     $dataArr['aadhar_no'] = $v->aadhar_no;
  //     $dataArr['pan_no'] = $v->pan_no;
  //     $dataArr['photo_doc'] = $img;
  //     $dataArr['aadhar_doc'] = $aadhar;
  //     $dataArr['license_doc'] = $license;
  //     $dataArr['pan_doc'] = $pan;
  //     $dataArr['status'] = $v->status;
  //     $dataArr['date'] = $v->date;
  //     $dataArr['time'] = $v->time;
  //     $data[] = $dataArr;
  //   }
  //   // $data['eventData'] = $viewById;
  //   return $data;
  // }

  /*Read all Active Records*/
  // public function activeAll()
  // {
  //   $schoolId = authUser()->school_id;
  //   // $createdBy = authUser()->id;
  //   return Driver::select(
  //     '*',
  //     DB::raw("
  //     CASE 
  //       WHEN status = '0' THEN 'Deactivated'  
  //       WHEN status = '1' THEN 'Active'
  //     END as status,
  //     TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
  //     TO_CHAR(created_at,'HH12:MI:SS AM') as time
  //     ")
  //   )
  //     ->where('status', 1)
  //     ->where('school_id', $schoolId)
  //     // ->where('created_by', $createdBy)
  //     ->orderBy('driver_name')
  //     ->get();
  // }
}
