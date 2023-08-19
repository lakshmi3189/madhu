<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class FeeDefinition extends Model
{
    use HasFactory;
    protected $guarded=[];

    /**
    * | Add Records
    */
   public function store(array $req){
    FeeDefinition::create($req);
   }

   /**
     * | Get Discount Group Maps
     */
    public function getFeeDefinitionGroupMaps($req)
    {
        return FeeDefinition::where('class_id', $req->classId)
            ->where('status', 1)
            ->get();
    }

    public function getGroupById($id){
        return DB::table('fee_definitions as a')
        ->select('a.*','c.class_name')
        ->join('class_tables as c', 'c.id', '=', 'a.class_id') 
        ->where('a.id',$id)       
        ->first();
    }

    public function retrieveAll(){
        return DB::table('fee_definitions as a')
        ->select('a.*','c.class_name')
        ->join('class_tables as c', 'c.id', '=', 'a.class_id') 
        ->orderByDesc('a.id')
        ->get();
    }




}
