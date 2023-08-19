<?php

namespace App\Models\TimeTable;

use DB;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TimeTable extends Model
{
  use HasFactory;

  protected $guarded = [];


  /*Add Records*/
  public function store(array $req)
  {
    TimeTable::create($req);
  }
  /*Read Records by name*/
  public function readTimeTableGroup($req)
  {
    $schoolId = authUser()->school_id;
    return TimeTable::where('class_id', $req->classId)
      ->where('subject_name', $req->subjectId)
      // ->where('subject_id', $req->subjectId)
      ->orWhere('section_id', $req->sectionId)
      ->where('emp_id', $req->empId)
      ->where('day', $req->day)
      ->where('start_time', $req->startTime)
      // ->where('school_id', $schoolId)
      ->where('status', 1)
      ->get();
  }

  /*Read Records by ID*/
  public function getGroupById($id)
  {
    $schoolId = authUser()->school_id;
    return DB::table('time_tables as a')
      ->select(
        DB::raw("b.class_name, d.section_name, CONCAT(e.first_name,' ',e.middle_name,' ',e.last_name) as full_name ,e.email,e.emp_no, a.*,
        CASE WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        ")
      )
      ->join('class_masters as b', 'b.id', '=', 'a.class_id')
      // ->join('subject_group_maps as c', 'c.id', '=', 'a.subject_id')
      ->leftJoin('section_group_maps as d', 'd.id', '=', 'a.section_id')
      // ->join('subjects as c', 'c.id', '=', 'a.subject_id')
      // ->join('sections as d', 'd.id', '=', 'a.section_id')
      ->join('employees as e', 'e.id', '=', 'a.emp_id')
      ->where('a.id', $id)
      // ->where('a.school_id', $schoolId)
      ->first();
  }

  /*Read all Records by*/
  public function retrieve()
  {
    $schoolId = authUser()->school_id;
    return DB::table('time_tables as a')
      ->select(
        DB::raw("a.*,
        b.class_name, d.section_name, 
        CONCAT(e.first_name,' ',e.middle_name,' ',e.last_name) as full_name ,e.email,e.emp_no, 
        CASE WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        ")
      )
      ->join('class_masters as b', 'b.id', '=', 'a.class_id')
      // ->join('subject_group_maps as c', 'c.id', '=', 'a.subject_id')
      ->leftJoin('section_group_maps as d', 'd.id', '=', 'a.section_id')
      // ->join('subjects as c', 'c.id', '=', 'a.subject_id')
      // ->join('sections as d', 'd.id', '=', 'a.section_id')
      ->join('employees as e', 'e.id', '=', 'a.emp_id')
      ->orderBy('a.id')
      // ->where('a.school_id', $schoolId)
      // ->where('status', 1)
      ->get();
  }

  //Get Records by name
  public function searchById($req)
  {
    $schoolId = authUser()->school_id;
    return DB::table('time_tables as a')
      ->select(
        DB::raw("b.class_name, c.subject_name, d.section_name, CONCAT(e.first_name,' ',e.middle_name,' ',e.last_name) as full_name ,e.email,e.emp_no, a.*,
        CASE WHEN a.status = '0' THEN 'Deactivated'  
        WHEN a.status = '1' THEN 'Active'
        END as status,
        TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
        ")
      )
      ->join('class_masters as b', 'b.id', '=', 'a.class_id')
      ->join('subject_group_maps as c', 'c.id', '=', 'a.subject_id')
      ->leftJoin('section_group_maps as d', 'd.id', '=', 'a.section_id')
      // ->join('subjects as c', 'c.id', '=', 'a.subject_id')
      // ->join('sections as d', 'd.id', '=', 'a.section_id')
      ->join('employees as e', 'e.id', '=', 'a.emp_id')
      ->where('b.id', $req->classId)
      ->orWhere('d.id', $req->sectionId)
      // ->where('a.school_id', $schoolId)
      // ->where('status', 1)
      ->get();
  }

  /*Read all Active Records*/
  public function active()
  {
    $schoolId = authUser()->school_id;
    return DB::table('time_tables as a')
      ->select(
        DB::raw("b.class_name, d.section_name, 
            CONCAT(e.first_name,' ',e.middle_name,' ',e.last_name) as full_name ,e.email, e.emp_no, a.*,
            CASE WHEN a.status = '0' THEN 'Deactivated'  
            WHEN a.status = '1' THEN 'Active'
            END as status,
            TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
            TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
            ")
      )
      ->join('class_masters as b', 'b.id', '=', 'a.class_id')
      // ->join('subject_group_maps as c', 'c.id', '=', 'a.subject_id')
      ->leftJoin('section_group_maps as d', 'd.id', '=', 'a.section_id')
      // ->join('subjects as c', 'c.id', '=', 'a.subject_id')
      // ->join('sections as d', 'd.id', '=', 'a.section_id')
      ->join('employees as e', 'e.id', '=', 'a.emp_id')
      ->where('a.status', 1)
      ->orderBy('a.id')
      // ->where('a.school_id', $schoolId)
      ->get();
  }





  // protected $fillable = [
  // 	'time_tbl_name'
  // ];

  // //insert
  // public function insertData($req) {      
  //   $mObject = new TimeTable();
  //   $insert = [
  //     $mObject->time_tbl_name   = Str::lower($req['time_tbl_name'])
  //   ];
  //   $mObject->save($insert);
  //   return $mObject;
  // }

  // //view all 
  // public static function list() {
  //   $viewAll = TimeTable::select('id','time_tbl_name')
  //   ->where('is_deleted',0)
  //   ->orderBy('id','desc')
  //   ->get();     
  //   return $viewAll;
  // }

  // //view by id
  // public function listById($req) {
  //   $data = TimeTable::where('id', $req->id)
  //         ->first();
  //     return $data;     
  // }   

  // //update
  // public function updateData($req) {
  //   $data = TimeTable::find($req->id);
  //   if (!$data)
  //         throw new Exception("Record Not Found!");
  //   $edit = [
  //     'time_tbl_name' => $req->time_tbl_name
  //   ];
  //   $data->update($edit);
  //   return $data;        
  // }

  // //delete 
  // public function deleteData($req) {
  //   $data = TimeTable::find($req->id);
  //   $data->is_deleted = "1";
  //   $data->save();
  //   return $data; 
  // }

  // //truncate
  // public function truncateData() {
  //   $data = TimeTable::truncate();
  //   return $data;        
  // }
}
