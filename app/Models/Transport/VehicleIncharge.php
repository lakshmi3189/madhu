<?php

namespace App\Models\Transport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class VehicleIncharge extends Model
{
    use HasFactory;
    protected $table = 'vehicle_incharges';
    protected $guarded = [];

    /*Add Records*/
    public function store(array $req)
    {
        VehicleIncharge::create($req);
    }
    //Read Records by name
    public function readVehicleInchargesGroup($req)
    {
        $schoolId = authUser()->school_id;
        return VehicleIncharge::where('incharge_name', $req->inchargeName)
            ->where('mobile', $req->mobile)
            ->orWhere('aadhar_no', $req->aadharNo)
            ->orWhere('email', $req->email)
            // ->where('school_id', $schoolId)
            ->where('status', 1)
            ->get();
    }

    /*Read Records by ID*/
    public function getGroupById($id)
    {
        $schoolId = authUser()->school_id;
        $viewById = DB::table('vehicle_incharges as a')
            ->select(
                DB::raw("b.country_name, s.state_name, c.city_name, a.*,
                    CASE WHEN a.status = '0' THEN 'Deactivated'  
                    WHEN a.status = '1' THEN 'Active'
                    END as status,
                    TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
                    TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
                    ")
            )
            ->join('countries as b', 'b.id', '=', 'a.country_id')
            ->join('states as s', 's.id', '=', 'a.state_id')
            ->join('cities as c', 'c.id', '=', 'a.city_id')
            ->where('a.id', $id)
            // ->where('school_id', $schoolId)
            ->first();

        // $path = 'api\getImageLink?path=';
        $path = 'getImageLink?path=';
        $aadhar = $path . $viewById->aadhar_doc;
        $viewById->aadhar_doc = $aadhar;
        return $viewById;
    }

    public function retrieve()
  {
    $schoolId = authUser()->school_id;
    return DB::table('vehicle_incharges as a')
        ->select(
            DB::raw("b.country_name, s.state_name, c.city_name, a.*,
                CASE WHEN a.status = '0' THEN 'Deactivated'  
                WHEN a.status = '1' THEN 'Active'
                END as status,
                TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
                TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
                ")
        )
        ->join('countries as b', 'b.id', '=', 'a.country_id')
        ->join('states as s', 's.id', '=', 'a.state_id')
        ->join('cities as c', 'c.id', '=', 'a.city_id')
        // ->where('school_id', $schoolId)
        ->orderByDesc('a.id');
    // ->get();
  }

    /*Read all Records by*/
    public function retrieve1()
    {
        $schoolId = authUser()->school_id;
        $data = array();
        $viewAll = DB::table('vehicle_incharges as a')
            ->select(
                DB::raw("b.country_name, s.state_name, c.city_name, a.*,
                    CASE WHEN a.status = '0' THEN 'Deactivated'  
                    WHEN a.status = '1' THEN 'Active'
                    END as status,
                    TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
                    TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
                    ")
            )
            ->join('countries as b', 'b.id', '=', 'a.country_id')
            ->join('states as s', 's.id', '=', 'a.state_id')
            ->join('cities as c', 'c.id', '=', 'a.city_id')
            // ->where('school_id', $schoolId)
            ->orderByDesc('a.id')
            ->get();
        
        foreach ($viewAll as $v) {
            $dataArr = array();

            //$path = baseURL() . '/school/events/';
            $path = 'api\getImageLink?path=';
            $aadhar = $path . $v->aadhar_doc;

            // $file_name = $path . $v->upload_event_docs;
            $dataArr['id'] = $v->id;
            $dataArr['country_name'] = $v->country_name;
            $dataArr['state_name'] = $v->state_name;
            $dataArr['city_name'] = $v->city_name;
            $dataArr['incharge_name'] = $v->incharge_name;
            $dataArr['email'] = $v->email;
            $dataArr['aadhar_no'] = $v->aadhar_no;
            $dataArr['mobile'] = $v->mobile;
            $dataArr['address'] = $v->address;
            $dataArr['aadhar_doc'] = $aadhar;
            $dataArr['status'] = $v->status;
            $dataArr['date'] = $v->date;
            $dataArr['time'] = $v->time;
            $data[] = $dataArr;
        }
        // $data['eventData'] = $viewById;
        return $data;
    }

    //Get Records by name
    public function searchByName($name)
    {
        $schoolId = authUser()->school_id;
        $data = array();
        $viewAll = DB::table('vehicle_incharges as a')
            ->select(
                DB::raw("b.country_name, s.state_name, c.city_name, a.*,
                CASE WHEN a.status = '0' THEN 'Deactivated'  
                WHEN a.status = '1' THEN 'Active'
                END as status,
                TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
                TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
                ")
            )
            ->join('countries as b', 'b.id', '=', 'a.country_id')
            ->join('states as s', 's.id', '=', 'a.state_id')
            ->join('cities as c', 'c.id', '=', 'a.city_id')
            ->orderByDesc('a.id')
            // ->where('school_id', $schoolId)
            ->get();

        foreach ($viewAll as $v) {
            $dataArr = array();

            //$path = baseURL() . '/school/events/';
            $path = 'api\getImageLink?path=';
            $aadhar = $path . $v->aadhar_doc;

            $file_name = $path . $v->upload_event_docs;
            $dataArr['id'] = $v->id;
            $dataArr['country_name'] = $v->country_name;
            $dataArr['state_name'] = $v->state_name;
            $dataArr['city_name'] = $v->city_name;
            $dataArr['incharge_name'] = $v->incharge_name;
            $dataArr['email'] = $v->email;
            $dataArr['aadhar_no'] = $v->aadhar_no;
            $dataArr['mobile'] = $v->mobile;
            $dataArr['address'] = $v->address;
            $dataArr['aadhar_doc'] = $aadhar;
            $dataArr['status'] = $v->status;
            $dataArr['date'] = $v->date;
            $dataArr['time'] = $v->time;
            $data[] = $dataArr;
        }
        // $data['eventData'] = $viewById;
        return $data;
    }
}
