<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Exception;

class Attendance extends Model
{
  use HasFactory;
  protected $fillable = [
    'attendance_name'
  ];

  //insert
  public function insertData($req)
  {
    $mObject = new Attendance();
    $insert = [
      $mObject->attendance_name   = Str::ucfirst($req['attendance_name'])
    ];
    $mObject->save($insert);
    return $mObject;
  }

  //view all 
  public static function list()
  {
    $viewAll = Attendance::select('id', 'attendance_name')
      ->where('is_deleted', 0)
      ->orderBy('id', 'desc')
      ->get();
    return $viewAll;
  }

  //view by id
  public function listById($req)
  {
    $data = Attendance::where('id', $req->id)
      ->first();
    return $data;
  }

  //update
  public function updateData($req)
  {
    $data = Attendance::find($req->id);
    if (!$data)
      throw new Exception("Record Not Found!");
    $edit = [
      'attendance_name' => $req->attendance_name
    ];
    $data->update($edit);
    return $data;
  }

  //delete 
  public function deleteData($req)
  {
    $data = Attendance::find($req->id);
    $data->is_deleted = "1";
    $data->save();
    return $data;
  }

  //truncate
  public function truncateData()
  {
    $data = Attendance::truncate();
    return $data;
  }
}
