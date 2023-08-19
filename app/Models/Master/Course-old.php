<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;
    protected $fillable = [
        'course_name',
        'duration_in_month',
        'duration_in_year'
    ]; 
    
    //insert
    public function insertData($req) {      
        $mObject = new Course();
        $insert = [
          $mObject->course_name   = Str::lower($req['course_name']),
          $mObject->duration_in_month   = $req['duration_in_month'],
          $mObject->duration_in_year   = $req['duration_in_year']
        ];
        $mObject->save($insert);
        return $mObject;
      }
      
      //view all 
      public static function list() {
      $viewAll = Course::select('id','course_name','duration_in_month','duration_in_year')
      ->where('is_deleted',0)
      ->orderBy('id','desc')
      ->get();       
        return $viewAll;
      }
  
      //view by id
      public function listById($req) {
        $data = Course::where('id', $req->id)
              ->first();
          return $data;     
      }   
  
      //update
      public function updateData($req) {
        $data = Course::find($req->id);
        if (!$data)
              throw new Exception("Record Not Found!");
        $edit = [
          'course_name' => $req->course_name,
          'duration_in_month' => $req->duration_in_month,
          'duration_in_year' => $req->duration_in_year
        ];
        $data->update($edit);
        return $data;        
      }
  
      //delete 
      public function deleteData($req) {
        $data = Course::find($req->id);
        $data->is_deleted = "1";
        $data->save();
        return $data; 
      }
  
      //truncate
      public function truncateData() {
        $data = Course::truncate();
        return $data;        
      }
}
