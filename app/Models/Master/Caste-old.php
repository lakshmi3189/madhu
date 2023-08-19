<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Caste extends Model
{
    use HasFactory;
    protected $fillable = [
		'caste_name'
    ];

    //insert
    public function insertData($req) {      
      $mObject = new Caste();
      $insert = [
        $mObject->caste_name   = Str::lower($req['caste_name'])
      ];
      $mObject->save($insert);
      return $mObject;
    }
    
    //view all 
    public static function list() {
      $viewAll = Caste::select('id','caste_name')
      ->where('is_deleted',0)
      ->orderBy('id','desc')
      ->get();      
      return $viewAll;
    }

    //view by id
    public function listById($req) {
      $data = Caste::where('id', $req->id)
            ->first();
        return $data;     
    }   

    //update
    public function updateData($req) {
      $data = Caste::find($req->id);
      if (!$data)
            throw new Exception("Record Not Found!");
      $edit = [
        'caste_name' => $req->caste_name
      ];
      $data->update($edit);
      return $data;        
    }

    //delete 
    public function deleteData($req) {
      $data = Caste::find($req->id);
      $data->is_deleted = "1";
      $data->save();
      return $data; 
    }

    //truncate
    public function truncateData() {
      $data = Caste::truncate();
      return $data;        
    }
}
