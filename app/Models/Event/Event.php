<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Event extends Model
{
  use HasFactory;
  protected $guarded = [];

  /*Add Records*/
  public function store(array $req)
  {
    Event::create($req);
  }

  /*Read Records by name*/
  public function readEventGroup($req)
  {
    $schoolId = authUser()->school_id;
    return Event::where('event_name', $req->eventName)
      ->where('event_date', $req->date)
      ->where('event_time', $req->time)
      ->where('status', 1)
      // ->where('school_id', $schoolId)
      ->get();
  }

  //Get Records by name
  public function searchByName($req)
  {
    $schoolId = authUser()->school_id;
    return Event::select(
      DB::raw("id,event_name,event_date,event_time,description,event_venue,organizer,upload_event_docs,
       CASE 
       WHEN status = '0' THEN 'Deactivated'  
       WHEN status = '1' THEN 'Active'
       END as status,
       TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
       TO_CHAR(created_at,'HH12:MI:SS AM') as time
   ")
    )
      ->where('event_name', 'ilike', $req->search . '%');
      // ->where('school_id', $schoolId);
    // ->where('status', 1)
    // ->get();
  }

  /*Read Records by ID*/
  public function getGroupById($id)
  {
    $schoolId = authUser()->school_id;
    $getData = Event::select(
      DB::raw("id,event_name,event_date,event_time,description,event_venue,organizer,upload_event_docs,
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
      // ->where('status', 1)
      ->first();

      $path = "getImageLink?path=";
      $eventDoc = $path . $getData->upload_event_docs;
      $getData->upload_event_docs = $eventDoc;

      return $getData; 
  }

  /*Read all Records by*/ 
  public function retrieve()
  {
    $schoolId = authUser()->school_id;
    return Event::select(
      DB::raw("id,event_name,event_date,event_time,description,event_venue,organizer,upload_event_docs,
    CASE 
    WHEN status = '0' THEN 'Deactivated'  
    WHEN status = '1' THEN 'Active'
    END as status,
    TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
    TO_CHAR(created_at,'HH12:MI:SS AM') as time
")
    )
      // ->where('school_id', $schoolId)
      ->orderBy('event_name');
    // ->get();
  }

  /*Read all Active Records*/
  public function active()
  {
    // $schoolId = authUser()->school_id;
    $viewAll = Event::select(
      DB::raw("id,event_name,event_date,event_time,description,event_venue,organizer,upload_event_docs,
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
      ->orderBy('event_name')
      ->get();

    $data = array();
    foreach ($viewAll as $v) {
      $dataArr = array();
      $path = 'api\getImageLink?path=';
      $file_name = $path . $v->upload_event_docs;
      $dataArr['id'] = $v->id;
      $dataArr['upload_event_docs'] = $file_name;
      $dataArr['event_name'] = $v->event_name;
      $dataArr['event_date'] = $v->event_date;
      $dataArr['event_time'] = $v->event_time;
      $dataArr['description'] = $v->description;
      $dataArr['event_venue'] = $v->event_venue;
      $dataArr['organizer'] = $v->organizer;
      $dataArr['status'] = $v->status;
      $dataArr['date'] = $v->date;
      $dataArr['time'] = $v->time;
      $data[] = $dataArr;
    }
    return $data;
  }







  /*Read Records by ID*/
  public function getGroupById1($id)
  {
    // $data = array();
    // echo $baseUrl = config('app.url'); 
    $schoolId = authUser()->school_id;
    $viewById = Event::select(
      DB::raw(" *,
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
      // ->where('status', 1)
      ->first();

    $path = 'api\getImageLink?path=';
    $file_name = $path . $viewById->upload_event_docs;
    $viewById->upload_event_docs = $file_name;
    // $data['eventData'] = $viewById;
    return $viewById;

    //     $data['eventData'] = $viewById;

    // $path = baseURL() . '/school/events/';
    // $file_name = $path . $viewById->upload_event_docs;
    // $viewById->upload_event_docs = $file_name;
    // $data['eventData'] = $viewById;


  }

  /*Read all Records by*/
  public function retrieve1()
  {
    $schoolId = authUser()->school_id;
    $data = array();
    // echo $baseUrl = config('app.url');

    $viewAll = Event::select(
      DB::raw(" *,
      CASE 
        WHEN status = '0' THEN 'Deactivated'  
        WHEN status = '1' THEN 'Active'
      END as status,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
	  ")
    )
      // ->where('school_id', $schoolId)
      ->orderBy('event_name')
      ->get();

    foreach ($viewAll as $v) {
      $dataArr = array();

      //$path = baseURL() . '/school/events/';
      $path = 'api\getImageLink?path=';
      $file_name = $path . $v->upload_event_docs;
      $dataArr['id'] = $v->id;
      $dataArr['upload_event_docs'] = $file_name;
      $dataArr['event_name'] = $v->event_name;
      $dataArr['event_date'] = $v->event_date;
      $dataArr['event_time'] = $v->event_time;
      $dataArr['description'] = $v->description;
      $dataArr['event_venue'] = $v->event_venue;
      $dataArr['organizer'] = $v->organizer;
      $dataArr['status'] = $v->status;
      $dataArr['date'] = $v->date;
      $dataArr['time'] = $v->time;
      $data[] = $dataArr;
    }


    // $data['eventData'] = $viewById;
    return $data;

    // return Event::select(
    //   '*',
    //   DB::raw("
    //   CASE 
    //   WHEN status = '0' THEN 'Deactivated'  
    //   WHEN status = '1' THEN 'Active'
    //   END as status,
    //   TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
    //   TO_CHAR(created_at,'HH12:MI:SS AM') as time
    // ")
    // )
    //   // ->where('status', 1)
    //   ->orderBy('event_name')
    //   ->get();
  }

  //Get Records by name
  public function searchByName1($name)
  {
    $schoolId = authUser()->school_id;
    $data = array();
    $viewAll = Event::select(
      '*',
      DB::raw("
           CASE 
           WHEN status = '0' THEN 'Deactivated'  
           WHEN status = '1' THEN 'Active'
         END as status,
         TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
         TO_CHAR(created_at,'HH12:MI:SS AM') as time
         ")
    )
      // ->where('school_id', $schoolId)
      ->where('event_name', 'like', $name . '%')
      // ->where('status', 1)
      ->get();

    foreach ($viewAll as $v) {
      $dataArr = array();

      //$path = baseURL() . '/school/events/';
      $path = 'api\getImageLink?path=';
      $file_name = $path . $v->upload_event_docs;
      $dataArr['id'] = $v->id;
      $dataArr['upload_event_docs'] = $file_name;
      $dataArr['event_name'] = $v->event_name;
      $dataArr['event_date'] = $v->event_date;
      $dataArr['event_time'] = $v->event_time;
      $dataArr['description'] = $v->description;
      $dataArr['event_venue'] = $v->event_venue;
      $dataArr['organizer'] = $v->organizer;
      $dataArr['status'] = $v->status;
      $dataArr['date'] = $v->date;
      $dataArr['time'] = $v->time;
      $data[] = $dataArr;
    }
    // $data['eventData'] = $viewById;
    return $data;
  }

  /*Read all Active Records*/
  public function active1()
  {
    $schoolId = authUser()->school_id;
    return Event::select(
      DB::raw("id,event_name,event_date,event_time,description,event_venue,organizer,upload_event_docs,
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
      ->orderBy('event_name')
      ->get();
  }
}
