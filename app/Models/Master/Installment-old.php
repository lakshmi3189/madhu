<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Installment extends Model
{
    use HasFactory;
    protected $fillable = [
		'installment_name'
    ];

    //insert
    public function insertData($req) {      
      $mObject = new Installment();
      $insert = [
        $mObject->installment_name   = Str::lower($req['installment_name'])
      ];
      $mObject->save($insert);
      return $mObject;
    }
    
    //view all 
    public static function list() {
      $viewAll = Installment::select('id','installment_name')
      ->where('is_deleted',0)
      ->orderBy('id','desc')
      ->get();       
      return $viewAll;
    }

    //view by id
    public function listById($req) {
      $data = Installment::where('id', $req->id)
            ->first();
        return $data;     
    }   

    //update
    public function updateData($req) {
      $data = Installment::find($req->id);
      if (!$data)
            throw new Exception("Record Not Found!");
      $edit = [
        'installment_name' => $req->installment_name
      ];
      $data->update($edit);
      return $data;        
    }

    //delete 
    public function deleteData($req) {
      $data = Installment::find($req->id);
      $data->is_deleted = "1";
      $data->save();
      return $data; 
    }

    //truncate
    public function truncateData() {
      $data = Installment::truncate();
      return $data;        
    }
}
