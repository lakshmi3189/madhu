<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Certificate extends Model
{
    use HasFactory;
    protected $fillable = [
		'certificate_name'
    ];

    //insert
    public function insertData($req) {      
      $mObject = new Certificate();
      $insert = [
        $mObject->certificate_name   = Str::lower($req['certificate_name'])
      ];
      $mObject->save($insert);
      return $mObject;
    }
    
    //view all 
    public static function list() {
      $viewAll = Certificate::select('id','certificate_name')
      ->where('is_deleted',0)
      ->orderBy('id','desc')
      ->get();     
      return $viewAll;
    }

    //view by id
    public function listById($req) {
      $data = Certificate::where('id', $req->id)
            ->first();
        return $data;     
    }   

    //update
    public function updateData($req) {
      $data = Certificate::find($req->id);
      if (!$data)
            throw new Exception("Record Not Found!");
      $edit = [
        'certificate_name' => $req->certificate_name
      ];
      $data->update($edit);
      return $data;        
    }

    //delete 
    public function deleteData($req) {
      $data = Certificate::find($req->id);
      $data->is_deleted = "1";
      $data->save();
      return $data; 
    }

    //truncate
    public function truncateData() {
      $data = Certificate::truncate();
      return $data;        
    }
}
