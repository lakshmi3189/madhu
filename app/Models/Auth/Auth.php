<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Exception;

// class Auth extends Model extends Authenticatable
class Auth extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $guarded = [];

    //change password
    public function updatePassword($req)
    {
        $data = Auth::select('id', 'name', 'email', 'remember_token')
            ->where('email', $req->email)
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

    //Logout
    public function readLogout($req)
    {
        $data = Auth::select('*')
            ->where('email', $req->email)
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


    /*Read Records by ID*/
    public function getGroupById($id)
    {
        return Auth::select(
            DB::raw("name,email,
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
}
