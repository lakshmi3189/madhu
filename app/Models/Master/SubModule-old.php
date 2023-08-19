<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Module;
use Illuminate\Support\Str;

class SubModule extends Model
{
    use HasFactory;
    protected $fillable = [
        'module_id',
        'sub_module_name'
    ];

    //insert
    public function insertData($req) {      
        $mObject = new SubModule();
        $insert = [
            $mObject->module_id   = $req['module_id'],
            $mObject->sub_module_name   = Str::lower($req['sub_module_name'])
        ];
        $mObject->save($insert);
        return $mObject;
      }
      
      //view all 
      public static function list() {
        //$viewAll = SubModule::select('id','sub_module_name')->orderBy('id','desc')->get(); 
        $viewAll=SubModule::select('modules.module_name','sub_modules.sub_module_name')
        ->join('modules', 'modules.id', '=', 'sub_modules.module_id') 
        ->where('is_deleted',0)
        ->orderBy('sub_modules.id','desc')       
        ->get();
        return $viewAll;
      }
  
      //view by id
      public function listById($req) {
        // $data = SubModule::where('id', $req->id)
        //       ->first();
        $id =  $req->id;
        $data=SubModule::select('modules.module_name','sub_modules.sub_module_name')
        ->join('modules', 'modules.id', '=', 'sub_modules.module_id')
        ->where('sub_modules.id', '=', $id)        
        ->get();
        return $data;    
      }   
  
      //update
      public function updateData($req) {
        $data = SubModule::find($req->id);
        if (!$data)
              throw new Exception("Record Not Found!");
        $edit = [
          'sub_module_name' => $req->sub_module_name
        ];
        $data->update($edit);
        return $data;        
      }
  
      //delete 
      public function deleteData($req) {
        $data = SubModule::find($req->id);
        $data->is_deleted = "1";
        $data->save();
        return $data; 
      }
  
      //truncate
      public function truncateData() {
        $data = SubModule::truncate();
        return $data;        
      }
}
