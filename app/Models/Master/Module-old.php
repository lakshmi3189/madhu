<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\SubModule;
use Illuminate\Support\Str;

class Module extends Model
{
    use HasFactory;
    protected $fillable = [
        'module_name'
    ];

    //insert
    public function insertData($req) {      
        $mObject = new Module();
        $insert = [
          $mObject->module_name   = Str::lower($req['module_name'])
        ];
        $mObject->save($insert);
        return $mObject;
      }
      
      //view all 
      public static function list() {
        $viewAll = Module::select('id','module_name')->orderBy('id','desc')
        ->where('is_deleted',0)
        ->orderBy('id','desc')
        ->get();    
        return $viewAll;
      }
  
      //view by id
      public function listById($req) {
        $data = Module::where('id', $req->id)
              ->first();
          return $data;     
      }   
  
      //update
      public function updateData($req) {
        $data = Module::find($req->id);
        if (!$data)
              throw new Exception("Record Not Found!");
        $edit = [
          'module_name' => $req->module_name
        ];
        $data->update($edit);
        return $data;        
      }
  
      //delete 
      public function deleteData($req) {
        $data = Module::find($req->id);
        $data->is_deleted = "1";
        $data->save();
        return $data; 
      }
  
      //truncate
      public function truncateData() {
        $data = Module::truncate();
        return $data;        
      }

}
