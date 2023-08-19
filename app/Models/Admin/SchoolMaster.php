<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

// class SchoolMaster extends Model extends Authenticatable
class SchoolMaster extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $guarded = [];

    /*Add Records*/
    public function store(array $req)
    {
        SchoolMaster::create($req);
    }

    /*Read Records by name*/
    public function readUserNameGroup($req)
    {
        return SchoolMaster::where('user_name', $req->userName)
            ->first();
    }

    /*Read Records by name*/
    public function readSchoolRegGroup($req)
    {
        return SchoolMaster::where(DB::raw('upper(school_name)'), strtoupper($req->schoolName))
            // where('school_name', $req->schoolName)
            ->where('contact_person_email', $req->email)
            ->where('user_name', $req->userName)
            ->where('status', 1)
            ->get();
    }

    //change password
    public function updatePassword($req)
    {
        $data = SchoolMaster::select('id', 'user_name', 'school_name', 'remember_token', 'contact_person_name', 'contact_person_mobile', 'contact_person_email')
            ->where('user_name', $req->userName)
            ->first();

        if ($data->remember_token != "") {
            $edit = [
                'password' => Hash::make($req->password)
            ];
            $data->update($edit);
            return $data;
        }
        return false;
    }

    /*Read Records by ID*/
    public function getGroupById($id)
    {
        return SchoolMaster::select(
            DB::raw("*,
             CASE 
                 WHEN status = '0' THEN 'Deactivated'  
                 WHEN status = '1' THEN 'Active'
             END as status,
             TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
             TO_CHAR(created_at,'HH12:MI:SS AM') as time
             ")
        )
            ->where('id', $id)
            ->where('status', 1)
            ->first();
    }
    /*Read Records by ID*/
    public function getGroupById1($id)
    {
        return SchoolMaster::select(
            '*',
            DB::raw(" 
            CASE 
                WHEN status = '0' THEN 'Deactivated'  
                WHEN status = '1' THEN 'Active'
            END as status,
            CASE 
                WHEN is_approved = '0' THEN 'No'  
                WHEN is_approved = '1' THEN 'Yes'
            END as isApproved,
            TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
            TO_CHAR(created_at,'HH12:MI:SS AM') as time
            ")
        )
            ->where('id', $id)
            // ->where('status', 1)
            ->first();
    }

    //Logout
    public function readLogout($req)
    {
        $data = SchoolMaster::select('*')
            ->where('user_name', $req->userName)
            ->where('remember_token', $req->token)
            ->first();
        $edit = [
            'remember_token' => null
        ];
        if ($data) {
            return $result = $data->update($edit);
        }
        return false;
    }

    /*Read all Active Records*/

    public function active()
    {
        return SchoolMaster::select('id', 'school_name')
            ->where('status', 1)
            ->get();
    }

    public function retrieve()
    {
        // return SchoolMaster::select(
        //     DB::raw("id,school_name, contact_person_name as name, contact_person_mobile as mobile,contact_person_email as email,
        //      user_name,school_code,logo,address,pincode,
        //     CASE WHEN status = '0' THEN 'Deactivated'  
        //     WHEN status = '1' THEN 'Active'
        //     END as status,
        //     TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
        //     TO_CHAR(created_at,'HH12:MI:SS AM') as time
        //     ")
        // );

        return DB::table('school_masters as a')
            ->select(
                DB::raw("a.id,a.school_name, a.contact_person_name as name, a.contact_person_mobile as mobile,
            a.contact_person_email as email,a.user_name,a.school_code,a.logo,a.address,a.pincode,
            b.role_name,
            CASE WHEN a.status = '0' THEN 'Deactivated'  
            WHEN a.status = '1' THEN 'Active'
            END as status,
            TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
            TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
            ")
            )
            ->leftjoin('roles as b', 'b.id', '=', 'a.role_id');


        // ->get();
    }

    // public function retrieve1()
    // {
    //     return DB::table('section_group_maps as a')
    //         ->select(
    //             DB::raw("b.class_name,a.*,
    //     CASE WHEN a.status = '0' THEN 'Deactivated'  
    //     WHEN a.status = '1' THEN 'Active'
    //     END as status,
    //     TO_CHAR(a.created_at::date,'dd-mm-yyyy') as date,
    //     TO_CHAR(a.created_at,'HH12:MI:SS AM') as time
    //     ")
    //         )
    //         ->join('class_masters as b', 'b.id', '=', 'a.class_id')
    //         ->where('a.status', 1)
    //         ->orderBy('a.id')
    //         ->get();
    // }
}
