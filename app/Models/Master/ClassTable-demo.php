<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use DB;
use Exception;

class ClassTable extends Model
{
    use HasFactory;
    protected $fillable = [
      'class_name',
      'class_name_display'
      ];
    
    //insert
    public function insertData($req) {      
      $mObject = new ClassTable();
      $classNameDisplay = $req->classNameDisplay;
      $ip = getClientIpAddress();
      $insert = [
        $mObject->class_name   = $req['className'],
        $mObject->class_name_display   = Str::upper($req['classNameDisplay']), 
        $mObject->created_by = 'Admin',
        $mObject->ip_address = $ip
      ]; 
      $checkExist = ClassTable::where([['class_name_display','=',$classNameDisplay],['is_deleted','=','0']])->count(); 
      $checkDeleted = ClassTable::where([['class_name_display','=',$classNameDisplay],['is_deleted','=','1']])->count();
      // print_r($checkDeleted); die; 
      if($checkExist > 0){
        throw new Exception("Class name is already existing!");
      }
      if($checkDeleted >= 0){
          $mObject->save($insert);
      } 
      return $mObject;
    }
    
    //view all 
    public static function list() {
      // $viewAll = ClassTable::select('id','class_name')
      $viewAll = ClassTable::select(DB::raw("
      id, class_name,class_name_display,
      (CASE 
      WHEN is_deleted = '0' THEN 'Active' 
      WHEN is_deleted = '1' THEN 'Not Active'
      END) AS status, 
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
      "))
      ->where('is_deleted',0)
      ->orderBy('class_name_display','asc')
      ->get();    
      return $viewAll;
    }

    //view by id
    public function listById($req) {
      $data = ClassTable::where('id', $req->id)
            ->first();
        return $data;     
    }   

    //update
    public function updateData($req) {
      $data = ClassTable::find($req->id);
      $getVersion  = $data->version_no; 
      $incVersion =  number_format($getVersion + 1) ;
      $id = $req->id;
      $classNameDisplay = str::upper($req->classNameDisplay);
      if (!$data)
            throw new Exception("Records Not Found!");
      $edit = [
        // 'class_name' => str::upper($req->className),
        // 'class_name_display' => $req->classNameDisplay,
        'class_name_display' => str::upper($req->classNameDisplay),
        'class_name' => $req->className,
        'updated_at' => Carbon::now(),
        'version_no' => $incVersion
      ];
      //validation 
      // $checkExist = ClassTable::where([['id','=',$id],['class_name','=',$className],['is_deleted','=','0']])->count(); 
      $checkExist = ClassTable::where([['class_name_display','=',$classNameDisplay],['is_deleted','=','0']])->count(); 
      if($checkExist > 0){
        throw new Exception("Class name is already existing!");
      }
      if(ClassTable::where('id',$id)->exists()){
        $data->update($edit);
      }      
      // $data->update($edit);
      return $edit;        
    }

    //delete 
    public function deleteData($req) {
      $data = ClassTable::find($req->id);
      if ((!$data)||($data->is_deleted == "1"))
          throw new Exception("Records Not Found!");
      $data->is_deleted = "1";
      $data->save();
      return $data; 
    }

    //truncate
    public function truncateData() {
      $data = ClassTable::truncate();
      return $data;        
    }  

    
}
