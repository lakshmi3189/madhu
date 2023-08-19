<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Leave extends Model
{
    use HasFactory;
    protected $fillable = [
		'leave_name'
    ];

    //insert
    public function insertData($req) {      
      $mObject = new Leave();
      $insert = [
        $mObject->leave_name   = Str::lower($req['leave_name'])
      ];
      $mObject->save($insert);
      return $mObject;
    }
    
    //view all 
    public static function list() {
      $viewAll = Leave::select('id','leave_name')
      ->where('is_deleted',0)
      ->orderBy('id','desc')
      ->get();     
      return $viewAll;
    }

    //view by id
    public function listById($req) {
      $data = Leave::where('id', $req->id)
            ->first();
        return $data;     
    }   

    //update
    public function updateData($req) {
      $data = Leave::find($req->id);
      if (!$data)
            throw new Exception("Record Not Found!");
      $edit = [
        'leave_name' => $req->leave_name
      ];
      $data->update($edit);
      return $data;        
    }

    //delete 
    public function deleteData($req) {
      $data = Leave::find($req->id);
      $data->is_deleted = "1";
      $data->save();
      return $data; 
    }

    //truncate
    public function truncateData() {
      $data = Leave::truncate();
      return $data;        
    }
}
