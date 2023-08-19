<?php

namespace App\Models\Master;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subject extends Model
{
  use HasFactory;
  protected $guarded = [];

  /*Add Records*/
  public function store(array $req)
  {
    Subject::create($req);
  }
  /*Read Records by name*/
  public function readSubjectGroup($subjectName)
  {
    return Subject::where('subject_name', $subjectName)
      ->where('status', 1)
      ->get();
  }

  //Get Records by name
  public function searchByName($name)
  {
    return Subject::select(
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
      ->where('subject_name', 'like', $name . '%')
      // ->where('status', 1)
      ->get();
  }

  /*Read Records by ID*/
  public function getGroupById($id)
  {
    return Subject::select(
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
      ->where('id', $id)
      // ->where('status', 1)
      ->first();
  }

  /*Read all Records by*/
  public function retrieve()
  {
    return Subject::select(
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
      // ->where('status', 1)
      ->orderByDesc('id')
      ->get();
  }

  /*Read all Active Records*/
  public function active()
  {
    return Subject::select(
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
      ->where('status', 1)
      ->orderBy('subject_name')
      ->get();
  }









  // protected $fillable = [
  // 'subject_name'
  // ];

  // //insert
  // public function insertData($req) {      
  //   $mObject = new Subject();
  //   $insert = [
  //     $mObject->subject_name   = Str::lower($req['subject_name'])
  //   ];
  //   $mObject->save($insert);
  //   return $mObject;
  // }

  // //view all 
  // public static function list() {
  //   $viewAll = Subject::select('id','subject_name')
  //   ->where('is_deleted',0)
  //   ->orderBy('subject_name','asc')
  //   ->get();     
  //   return $viewAll;
  // }

  // //view by id
  // public function listById($req) {
  //   $data = Subject::where('id', $req->id)
  //         ->first();
  //     return $data;     
  // }   

  // //update
  // public function updateData($req) {
  //   $data = Subject::find($req->id);
  //   if (!$data)
  //         throw new Exception("Record Not Found!");
  //   $edit = [
  //     'subject_name' => $req->subject_name
  //   ];
  //   $data->update($edit);
  //   return $data;        
  // }

  // //delete 
  // public function deleteData($req) {
  //   $data = Subject::find($req->id);
  //   $data->is_deleted = "1";
  //   $data->save();
  //   return $data; 
  // }

  // //truncate
  // public function truncateData() {
  //   $data = Subject::truncate();
  //   return $data;        
  // } 
}
