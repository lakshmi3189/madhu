<?php

namespace App\Models\Admin;

// use Illuminate\Contracts\Auth\MustVerifyEmail; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
// use Auth;
use Exception;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'user_id',
        'user_type',
        'email',
        'password',
        'c_password',
        'remember_token',
        'ip_address'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast. 
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    //auto insert user 
    // public function autoInsertData($req) { 
    //     // $pass = Str::random(10);  
    //     $pass = "123"; 
    //     $mObject = new User();
    //     $ip = getClientIpAddress();
    //     $userTYPE = $req->user_type;
    //     $userTYPE = $req->userType;
    //     $userID = $req->user_id;
    //     $userID = $req->userId;
    //     $schoolID = $req->school_id;
    //     $schoolID = $req->schoolId;
    //     $insert = [
    //       $mObject->name        = $req['name'],
    //       $mObject->email       = $req['email'],          
    //       $mObject->password    = Hash::make($pass),
    //       $mObject->c_password  = $pass,
    //       $mObject->school_id  = $schoolID,
    //       $mObject->user_id  = $userID,
    //       $mObject->user_type  = $userTYPE,
    //       $mObject->ip_address  = $ip
    //     ];
    //     // $token = $insert->createToken('auth_token')->plainTextToken;
    //     $mObject->save($insert);          
    //     return $mObject;
    // }

    //insert registration
    public function insertData($req)
    {
        // $pass = Str::random(10);  
        $genUserID = mt_rand(10000, 99999);
        // echo $genUserID; die;
        $pass = $req->password;
        $userType = "Admin";
        $mObject = new User();
        $dataArr = array();
        $ip = getClientIpAddress();
        $insert = [
            $mObject->name        = $req['name'],
            $mObject->email       = $req['email'],
            $mObject->password    = Hash::make($pass),
            $mObject->c_password  = $pass,
            $mObject->user_id    = $genUserID,
            $mObject->user_type   = $userType,
            $mObject->ip_address  = $ip,
            //   $mObject->remember_token  = createToken('auth_token')->plainTextToken
        ];
        // print_r($insert);die;
        // $token = $mObject->createToken('auth_token')->plainTextToken;
        $mObject->save($insert);
        $dataArr['name'] = $mObject->name;
        $dataArr['email'] = $mObject->email;
        $dataArr['password'] = $mObject->c_password;
        $dataArr['userId'] = $mObject->user_id;
        // $dataArr['token'] = $token;
        return $dataArr;
    }

    //login
    public function viewProfile($req)
    {
        return User::select('id', 'name', 'email')
            ->where('id', $req->id)
            ->get();
    }

    //edit profile
    public function updateProfile($req)
    {
        $data = User::select('id', 'name', 'email')
            ->find($req->id);
        if (!$data)
            throw new Exception("Record Not Found!");
        $edit = [
            'name' => $req->name
        ];
        $data->update($edit);
        return $data;
    }

    //delete profile
    public function deleteData($req)
    {
        $data = User::select('id', 'name', 'email')
            ->find($req->id);
        $data->is_deleted = "1";
        $data->save();
        return $data;
    }

    //change password
    public function updatePassword($req)
    {
        $data = User::select('id', 'name', 'email')
            ->find($req->id);
        if (!$data)
            throw new Exception("Record Not Found!");
        $edit = [
            'password' => Hash::make($req->password),
            'c_password' => $req->password
        ];
        $data->update($edit);
        return $data;
    }





    //   //view all 
    //   public static function list() {
    //     $viewAll = User::select('id','name','email')->orderBy('id','desc')->get();    
    //     return $viewAll;
    //   }

    //   //view by id
    //   public function listById($req) {
    //     $data = User::where('id', $req->id)
    //           ->first();
    //       return $data;     
    //   }   

    //   //update
    //   public function updateData($req) {
    //     $data = User::find($req->id);
    //     if (!$data)
    //           throw new Exception("Record Not Found!");
    //     $edit = [
    //       'name' => $req->name
    //     ];
    //     $data->update($edit);
    //     return $data;        
    //   }

    //   //delete 
    //   public function deleteData($req) {
    //     $data = User::find($req->id);
    //     $data->is_deleted = "1";
    //     $data->save();
    //     return $data; 
    //   }

    //   //truncate
    //   public function truncateData() {
    //     $data = User::truncate();
    //     return $data;        
    //   }
}
