<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Month extends Model
{
    use HasFactory;
    protected $guarded = [];

    /*Read all Records by*/
    public function retrieve()
    {
        return Month::select('*')
            ->get();
    }
}
