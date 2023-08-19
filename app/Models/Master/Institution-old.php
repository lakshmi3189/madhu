<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = [
		'institution_code'
    ];

    //insert
    public function insertData($req) {      
      $mObject = new Institution();
      $insert = [
        $mObject->institution_code   = Str::lower($req['institution_code'])
      ];
      $mObject->save($insert);
      return $mObject;
    }
    
    //view all 
    public static function list() {
      $viewAll = Institution::select('id','institution_code')
      ->where('is_deleted',0)
      ->orderBy('id','desc')
      ->get();      
      return $viewAll;
    }

    //view by id
    public function listById($req) {
      $data = Institution::where('id', $req->id)
            ->first();
        return $data;     
    }   

    //update
    public function updateData($req) {
      $data = Institution::find($req->id);
      if (!$data)
            throw new Exception("Record Not Found!");
      $edit = [
        'institution_code' => $req->institution_code
      ];
      $data->update($edit);
      return $data;        
    }

    //delete 
    public function deleteData($req) {
      $data = Institution::find($req->id);
      $data->is_deleted = "1";
      $data->save();
      return $data; 
    }

    //truncate
    public function truncateData() {
      $data = Institution::truncate();
      return $data;        
    }

}
