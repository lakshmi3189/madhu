<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\SubModule;
use App\Models\Master\Module;
use App\Models\Master\Role;
use App\Models\Admin\User;
use Illuminate\Support\Str;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'module_id',
        'sub_module_id'
    ];

    //insert
    public function insertData($req) {      
        $mObject = new Permission();
        $insert = [
          $mObject->user_id         = $req['user_id'],
          $mObject->module_id       = $req['module_id'],
          $mObject->sub_module_id   = $req['sub_module_id']
        ];
        $mObject->save($insert);
        return $mObject;
      }
      
      //view all 
      public static function list() {
        $viewAll = Permission::select('id','user_id','module_id','sub_module_id')
        ->where('is_deleted',0)
        ->orderBy('id','desc')
        ->get();     
        return $viewAll;
      }
  
      //view by id
      public function listById($req) {
        $data = Permission::where('id', $req->id)->first();
        return $data;     
      }   
  
      //update
      public function updateData($req) {
        $data = Permission::find($req->id);
        if (!$data)
              throw new Exception("Record Not Found!");
        $edit = [
          'user_id' => $req->user_id,
          'module_id' => $req->module_id,
          'sub_module_id' => $req->sub_module_id
        ];
        $data->update($edit);
        return $data;        
      }
  
      //delete 
      public function deleteData($req) {
        $data = Permission::find($req->id);
        $data->is_deleted = "1";
        $data->save();
        return $data; 
      }
  
      //truncate
      public function truncateData() {
        $data = Permission::truncate();
        return $data;        
      }
}
