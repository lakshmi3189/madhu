<?php

namespace App\Models\Employee;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Exception;

class EmployeeFamily extends Model
{
    use HasFactory;
    protected $guarded = [];

    //insert
    public function insertData($req) {         
        // $userId = authUser()->id; 
        $mObject = new EmployeeFamily();        
        $insert = [
          $mObject->emp_tbl_id = $req['emp_tbl_id'],    
          $mObject->f_member_name = $req['f_member_name'],
          $mObject->f_member_relation_name = $req['f_member_relation_name'],
            $mObject->f_member_dob = $req['f_member_dob'],
            $mObject->upload_f_member_image = $req['upload_f_member_image'], 
            $mObject->academic_year = $req['academic_year'],          
            $mObject->school_id = $req['school_id'],          
            $mObject->created_by = $req['created_by'],          
            $mObject->ip_address = $req['ip_address']
        ];
        $mObject->save($insert);
        return $mObject;
      }

  /*Add Records*/
  public function store(array $req)
  {
    EmployeeFamily::create($req);
  }

  /*Read Records by name*/
  public function readFamilyGroup($empId,$req)
  {
      $schoolId = authUser()->school_id;
      // $createdBy = authUser()->id;
      return EmployeeFamily::where('emp_tbl_id', $empId)
          ->where('f_member_name', $req->fMemberName)
          ->where('f_member_relation_name', $req->fMemberRelation)
          // ->where('status', 1)
          // ->where('school_id', $schoolId)
          // ->where('created_by', $createdBy)
          ->get();
  }

  public function retrieve($req)
  {
    $empFamily = EmployeeFamily::select(
      'id',
      'emp_tbl_id',
      'f_member_name',
      'f_member_relation_id',
      'f_member_relation_name',
      'f_member_dob',
      'upload_f_member_image',
      DB::raw("(CASE 
          WHEN status = '0' THEN 'Deactivated' 
          WHEN status = '1' THEN 'Active'
          END) AS status,
          TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
          TO_CHAR(created_at,'HH12:MI:SS AM') as time")
  )
      ->where(['emp_tbl_id' => $req->empId], ['status' => 1])
      // ->where('school_id', $schoolId)
      ->get();

      if(!$empFamily->isEmpty()){
     
        foreach ($empFamily as $v) {
            $dataArr = array();
            $path = 'api\getImageLink?path=';
            $file_name = $path . $v->upload_f_member_image;
            $dataArr['id'] = $v->id;
            $dataArr['emp_tbl_id'] = $v->emp_tbl_id;
            $dataArr['upload_f_member_image'] = $file_name;
            $dataArr['f_member_name'] = $v->f_member_name;
            $dataArr['f_member_relation_id'] = $v->f_member_relation_id;
            $dataArr['f_member_relation_name'] = $v->f_member_relation_name;
            $dataArr['f_member_dob'] = $v->f_member_dob;
            $dataArr['price'] = $v->price;
            $dataArr['status'] = $v->status;
            $dataArr['date'] = $v->date;
            $dataArr['time'] = $v->time;
            $getFam[] = $dataArr;
            // $data['family_details'] = $dataArr;
        }
    }else{
        $getFam[] = $empFamily;
    }
    return $getFam;

  }

  public function getGroupById($id)
  {
    $empFam = EmployeeFamily::select(
      'id',
      'emp_tbl_id',
      'f_member_name',
      'f_member_relation_id',
      'f_member_relation_name',
      'f_member_dob',
      'upload_f_member_image',
        
        DB::raw("(CASE 
        WHEN status = '0' THEN 'Deactivated' 
        WHEN status = '1' THEN 'Active'
        END) AS status,
        TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(created_at,'HH12:MI:SS AM') as time")
      )
      ->where(['id' => $id], ['status' => 1])
      // ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->first();
      // return $empEdu; die;
   
      $path = 'api\getImageLink?path=';
      $file_name = $path . $empFam->upload_f_member_image;
      $empFam->upload_f_member_image = $file_name;
      return $empFam;

  }
































      //using id generator
      // public function empId(){
      //   $empData = new Employee();
      //   $emp_id['empId'] = $empData->emp_id;
      //   $id = IdGenerator::generate([
      //     'table' => 'class_tables',
      //     'field' => 'student_id',
      //     'length' => 11,
      //     'prefix' => $req->class.'/'.date('y').'/',
      //     'reset_on_prefix_change' => true,
      //   ]);        
      // }
      
      //view all 
      // public static function list() {
      //   $viewAll = EmployeeFamily::select('id','name','emp_id','email','mobile')->orderBy('id','desc')->get();    
      //   return $viewAll;
      // }
  
      // //view by id
      // public function listById($req) {
      //   $data = EmployeeFamily::where('id', $req->id)
      //         ->first();
      //     return $data;     
      // }   
  
      // //update
      // public function updateData($req) {
      //   $data = EmployeeFamily::find($req->id);
      //   if (!$data)
      //         throw new Exception("Record Not Found!");
      //   $edit = [
      //     'name' => $req->name
      //   ];
      //   $data->update($edit);
      //   return $data;        
      // }
  
      // //delete 
      // public function deleteData($req) {
      //   $data = EmployeeFamily::find($req->id);
      //   $data->is_deleted = "1";
      //   $data->save();
      //   return $data; 
      // }
  
      // //truncate
      // public function truncateData() {
      //   $data = EmployeeFamily::truncate();
      //   return $data;        
      // } 
}
