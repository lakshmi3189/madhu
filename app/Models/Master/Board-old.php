<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Board extends Model
{
    use HasFactory;
    protected $fillable = [
		'board_name'
    ];

    //insert
    public function insertData($req) {      
      $mObject = new Board();
      $insert = [
        $mObject->board_name   =  Str::lower($req['board_name'])
      ];
      $mObject->save($insert);
      return $mObject;
    }
    
    //view all 
    public static function list() {
      $viewAll = Board::select('id','board_name')
      ->where('is_deleted',0)
      ->orderBy('id','desc')
      ->get();   
      return $viewAll;
    }

    //view by id
    public function listById($req) {
      $data = Board::where('id', $req->id)
            ->first();
        return $data;     
    }   

    //update
    public function updateData($req) {
      $data = Board::find($req->id);
      if (!$data)
            throw new Exception("Record Not Found!");
      $edit = [
        'board_name' => $req->board_name
      ];
      $data->update($edit);
      return $data;        
    }

    //delete 
    public function deleteData($req) {
      $data = Board::find($req->id);
      $data->is_deleted = "1";
      $data->save();
      return $data; 
    }

    //truncate
    public function truncateData() {
      $data = Board::truncate();
      return $data;        
    } 
}
