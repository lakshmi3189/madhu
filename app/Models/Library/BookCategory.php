<?php

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class BookCategory extends Model
{
  use HasFactory;
  protected $guarded = [];

  /*Add Records*/
  public function store(array $req)
  {
    BookCategory::create($req);
  }

  /*Read Records by name*/
  public function readGroup($req)
  {
    // $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return BookCategory::where(DB::raw('upper(book_category)'), strtoupper($req->bookCategory))
      ->where('status', 1)
      // ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->get();
  }

  /*Read Records by ID*/
  public function getGroupById($id)
  {
    // $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return BookCategory::select(
      DB::raw("id,book_category,
      CASE 
        WHEN status = '0' THEN 'Deactivated'  
        WHEN status = '1' THEN 'Active'
      END as status,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
      ")
    )
      ->where('id', $id)
      // ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      // ->where('status', 1)
      ->first();
  }

  /*Read all Records by*/
  public function retrieve()
  {
    // $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return BookCategory::select(
      DB::raw("id,book_category,
      CASE 
        WHEN status = '0' THEN 'Deactivated'  
        WHEN status = '1' THEN 'Active'
      END as status,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
      ")
    )
      // ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->orderByDesc('id');
    // ->get();
  }

  //Get Records by name
  public function searchByName($req)
  {
    // $schoolId = authUser()->school_id;
    $createdBy = authUser()->id;
    return BookCategory::select(
      DB::raw("id,book_category,
        CASE 
        WHEN status = '0' THEN 'Deactivated'  
        WHEN status = '1' THEN 'Active'
      END as status,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
      ")
    )
      ->where(DB::raw('upper(book_category)'), 'LIKE', '%' . strtoupper($req->search) . '%');
    // ->where('school_id', $schoolId)
    // ->where('created_by', $createdBy);
    // ->get();
  }

  /*Read all Active Records*/
  public function active()
  {
    // $schoolId = authUser()->school_id;
    // $createdBy = authUser()->id;
    return BookCategory::select(
      DB::raw("id,book_category,
      CASE 
        WHEN status = '0' THEN 'Deactivated'  
        WHEN status = '1' THEN 'Active'
      END as status,
      TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
      TO_CHAR(created_at,'HH12:MI:SS AM') as time
      ")
    )
      ->where('status', 1)
      // ->where('school_id', $schoolId)
      // ->where('created_by', $createdBy)
      ->orderBy('book_category')
      ->get();
  }







  // /*Read Records by name*/
  // public function readBankGroup($bankName)
  // {
  //   return Bank::where('bank_name', $bankName)
  //     ->where('status', 1)
  //     ->get();
  // }

  // //Get Records by name
  // public function searchByName($name)
  // {
  //   return Bank::select(
  //     '*',
  //     DB::raw("
  //         CASE 
  //         WHEN status = '0' THEN 'Deactivated'  
  //         WHEN status = '1' THEN 'Active'
  //       END as status,
  //       TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
  //       TO_CHAR(created_at,'HH12:MI:SS AM') as time
  //       ")
  //   )
  //     ->where('bank_name', 'like', $name . '%')
  //     // ->where('status', 1)
  //     ->get();
  // }

  // /*Read Records by ID*/
  // public function getGroupById($id)
  // {
  //   return Bank::select(
  //     '*',
  //     DB::raw("
  //     CASE 
  //       WHEN status = '0' THEN 'Deactivated'  
  //       WHEN status = '1' THEN 'Active'
  //     END as status,
  //     TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
  //     TO_CHAR(created_at,'HH12:MI:SS AM') as time
  //   ")
  //   )
  //     ->where('id', $id)
  //     // ->where('status', 1)
  //     ->first();
  // }

  // /*Read all Records by*/
  // public function retrieve()
  // {
  //   return Bank::select(
  //     '*',
  //     DB::raw("
  //     CASE 
  //       WHEN status = '0' THEN 'Deactivated'  
  //       WHEN status = '1' THEN 'Active'
  //     END as status,
  //     TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
  //     TO_CHAR(created_at,'HH12:MI:SS AM') as time
  //   ")
  //   )
  //     // ->where('status', 1)
  //     ->orderBy('bank_name')
  //     ->get();
  // }


  // /*Read all Active Records*/
  // public function active()
  // {
  //   return Bank::select(
  //     '*',
  //     DB::raw("
  //         CASE 
  //           WHEN status = '0' THEN 'Deactivated'  
  //           WHEN status = '1' THEN 'Active'
  //         END as status,
  //         TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
  //         TO_CHAR(created_at,'HH12:MI:SS AM') as time
  //         ")
  //   )
  //     ->where('status', 1)
  //     ->orderBy('bank_name')
  //     ->get();
  // }
}
