<?php

namespace App\Models\Transport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
class DropPoint extends Model
{
    use HasFactory;
    protected $guarded = [];


        /*Add Records*/
        public function store(array $req){
            DropPoint::create($req);
        }
        /*Read Records by name*/
        public function readDropPointGroup($dropPointName)
        {
            return DropPoint::where('drop_point_name', $dropPointName)
                ->where('status', 1)
                ->get();
        }
    
      /*Read Records by ID*/
      public function getGroupById($id)
      {
        return DropPoint::select(
          '*',
          DB::raw("
          CASE 
            WHEN status = '0' THEN 'Deactivated'  
            WHEN status = '1' THEN 'Active'
          END as status,
          TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
          TO_CHAR(created_at,'HH12:MI:SS AM') as time
          ")
        )
          ->where('id', $id)
          // ->where('status', 1)
          ->first();
      }
    
      /*Read all Records by*/
      public function retrieveAll()
      {
        return DropPoint::select(
          '*',
          DB::raw("
          CASE 
            WHEN status = '0' THEN 'Deactivated'  
            WHEN status = '1' THEN 'Active'
          END as status,
          TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
          TO_CHAR(created_at,'HH12:MI:SS AM') as time
          ")
        )
          // ->where('status', 1)
          ->get();
      }

}
