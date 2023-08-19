<?php

namespace App\Models\Transport;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentBusfeeMaster extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * | Addd Record
     */
    public function store(array $req)
    {
        StudentBusfeeMaster::create($req);
    }

    /**
     * | Get Bus Fee by RefIds
     */
    public function getBusFeeByRefIds($req)
    {
        return StudentBusfeeMaster::where('student_id', $req->studentId)
            ->where('class_id', $req->classId)
            ->where('status', 1)
            ->get();
    }

    /**
     * | Get Bus Fee by id
     */
    public function getBusFeeById($id)
    {
        return DB::table('student_busfee_masters as b')
            ->select(
                DB::raw("concat(s.first_name,' ',s.middle_name,' ',s.last_name) as student_name,b.*,
                CASE WHEN b.status = '0' THEN 'Deactivated'  
                    WHEN b.status = '1' THEN 'Active'
                END as status,
                TO_CHAR(b.created_at::date,'dd-mm-yyyy') as date,
                TO_CHAR(b.created_at,'HH12:MI:SS AM') as time
              ")
            )
            ->join('students as s', 's.id', '=', 'b.student_id')
            // ->where('b.status', 1)
            ->where('b.id', $id)
            ->first();
    }

    /**
     * | Get All Bus Fee
     */
    public function getAllBusFee()
    {
        return DB::table('student_busfee_masters as b')
            ->select(
                DB::raw("concat(s.first_name,' ',s.middle_name,' ',s.last_name) as student_name,b.*,
                CASE WHEN b.status = '0' THEN 'Deactivated'  
                    WHEN b.status = '1' THEN 'Active'
                END as status,
                TO_CHAR(b.created_at::date,'dd-mm-yyyy') as date,
                TO_CHAR(b.created_at,'HH12:MI:SS AM') as time
            ")
            )
            ->join('students as s', 's.id', '=', 'b.student_id')
            ->where('b.status', 1)
            ->get();
    }
}
