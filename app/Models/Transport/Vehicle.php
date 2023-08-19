<?php

namespace App\Models\Transport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Vehicle extends Model
{
    use HasFactory;
    protected $table = 'vehicles';
    protected $guarded = [];

    /*Add Records*/
    public function store(array $req)
    {
        Vehicle::create($req);
    }
    //Read Records by name
    public function readVehicleGroup($req)
    {
        $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        return Vehicle::where('registration_no', $req->registrationNo)
            ->where('chasis_no', $req->chasisNo)
            ->where('status', 1)
            ->where('school_id', $schoolId)
            // ->where('created_by', $createdBy)
            ->get();
    }

    //Get Records by name
    public function searchByName($req)
    {
        $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        return DB::table('vehicles as a')
            ->join('vehicle_types as b', 'b.id', '=', 'a.vehicle_types_id')
            ->where("a.registration_no", "Ilike", DB::raw("'%" . $req->search . "%'"))
            ->orWhere("b.vehicle_type_name", "Ilike", DB::raw("'%" . $req->search . "%'"))
            ->select(
                DB::raw("b.id,b.vehicle_type_name,b.max_seating_no,
                a.id,a.registration_no,a.chasis_no,a.vehicle_types_id,
                CASE WHEN a.status = '0' THEN 'Deactivated'  
                WHEN a.status = '1' THEN 'Active'
                END as status,
                TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
                TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
            ")
            )
            ->where('a.school_id', $schoolId);
        // ->where('a.created_by', $createdBy);
        // ->get();
    }

    /*Read all Active Records*/
    public function active()
    {
        $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        return DB::table('vehicles as a')
            ->select(
                DB::raw("b.id,b.vehicle_type_name,b.max_seating_no,
                a.id,a.registration_no,a.chasis_no,a.vehicle_types_id,
                CASE WHEN a.status = '0' THEN 'Deactivated'  
                WHEN a.status = '1' THEN 'Active'
                END as status,
                TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
                TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
            ")
            )
            ->join('vehicle_types as b', 'b.id', '=', 'a.vehicle_types_id')
            ->where('a.status', 1)
            ->where('a.school_id', $schoolId)
            // ->where('a.created_by', $createdBy)
            ->orderBy('a.registration_no')
            ->get();
    }

    /*Read Records by ID*/
    public function getGroupById($id)
    {
        $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        return DB::table('vehicles as a')
            ->select(
                DB::raw("b.id as vehid,b.vehicle_type_name,b.max_seating_no,
                a.id,a.registration_no,a.chasis_no,a.vehicle_types_id,
                CASE WHEN a.status = '0' THEN 'Deactivated'  
                WHEN a.status = '1' THEN 'Active'
                END as status,
                TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
                TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
                ")
            )
            ->join('vehicle_types as b', 'b.id', '=', 'a.vehicle_types_id')
            ->where('a.id', $id)
            ->where('a.school_id', $schoolId)
            // ->where('a.created_by', $createdBy)
            ->first();
    }

    /*Read all Records by*/
    public function retrieve()
    {
        $schoolId = authUser()->school_id;
        // $createdBy = authUser()->id;
        return DB::table('vehicles as a')
            ->select(
                DB::raw("b.vehicle_type_name,b.max_seating_no,
                a.id,a.registration_no,a.chasis_no,
                CASE WHEN a.status = '0' THEN 'Deactivated'  
                WHEN a.status = '1' THEN 'Active'
                END as status,
                TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
                TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
                ")
            )
            ->join('vehicle_types as b', 'b.id', '=', 'a.vehicle_types_id')
            ->where('a.school_id', $schoolId)
            // ->where('a.created_by', $createdBy)
            ->orderByDesc('a.id');
        // ->where('status', 1)
        // ->get();
    }
}
