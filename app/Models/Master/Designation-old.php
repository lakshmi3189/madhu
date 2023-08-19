<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Designation extends Model
{
    use HasFactory;
    protected $fillable = [
		'designation_name'
    ];

    //insert
    public function insertData($req) {      
      $mObject = new Designation();
      $insert = [
        $mObject->designation_name   = Str::lower($req['designation_name'])
      ];
      $mObject->save($insert);
      return $mObject;
    }
    
    //view all 
    public static function list() {
      $viewAll = Designation::select('id','designation_name')
      ->where('is_deleted',0)
      ->orderBy('id','desc')
      ->get();     
      return $viewAll;
    }

    //view by id
    public function listById($req) {
      $data = Designation::where('id', $req->id)
            ->first();
        return $data;     
    }   

    //update
    public function updateData($req) {
      $data = Designation::find($req->id);
      if (!$data)
            throw new Exception("Record Not Found!");
      $edit = [
        'designation_name' => $req->designation_name
      ];
      $data->update($edit);
      return $data;        
    }

    //delete 
    public function deleteData($req) {
      $data = Designation::find($req->id);
      $data->is_deleted = "1";
      $data->save();
      return $data; 
    }

    //truncate
    public function truncateData() {
      $data = Designation::truncate();
      return $data;        
    } 
}
