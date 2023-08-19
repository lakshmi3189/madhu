<?php

namespace App\Models\Examination;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamTerm extends Model
{
  use HasFactory;

  protected $guarded = [];

  /*Add Records*/
  public function store(array $req)
  {
    ExamTerm::create($req);
  }

  /*Read Records by name*/
  public function readExamTermGroup($req)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return ExamTerm::where(DB::raw('upper(term_name)'), strtoupper($req->termName))
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
    return ExamTerm::select(
      DB::raw("id,term_name,
      CASE 
      WHEN status = '0' THEN 'Deactivated'  
      WHEN status = '1' THEN 'Active'
      END as status,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
    ")
    )
      // ->where('term_name', 'LIKE', $req->search . '%')
      ->where(DB::raw('upper(term_name)'), 'LIKE', '%' . strtoupper($req->search) . '%')
      ->where('school_id', $schoolId);
    // ->where('created_by', $createdBy);
    // ->get();
  }

  /*Read Records by ID*/
  public function getGroupById($id)
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return ExamTerm::select(
      DB::raw("id,term_name,
      CASE 
      WHEN status = '0' THEN 'Deactivated'  
      WHEN status = '1' THEN 'Active'
      END as status,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
	  ")
    )
      ->where('id', $id)
      ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->first();
  }

  /*Read all Records by*/
  public function retrieve()
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return ExamTerm::select(
      DB::raw("id,term_name,
      CASE 
      WHEN status = '0' THEN 'Deactivated'  
      WHEN status = '1' THEN 'Active'
      END as status,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
	  ")
    )
      ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->orderByDesc('id');
    // ->get();
  }

  /*Read all Active Records*/
  public function active()
  {
    $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return ExamTerm::select('id', 'term_name')
      ->where('status', 1)
      ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->orderBy('term_name')
      ->get();
  }
}
