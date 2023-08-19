<?php

namespace App\Models\Gallery;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Str;

class Gallery extends Model
{
    use HasFactory;

    protected $guarded = [];
    // protected $table = 'e_books';

    /*Add Records*/
    public function store(array $req)
    {
        Gallery::create($req);
    }

    /*Read Records by name*/
    public function readGalleryGroup($req)
    {
        // $schoolId = authUser()->school_id;
        return Gallery::where(DB::raw('upper(category)'), strtoupper($req->category))
            ->where('status', 1)
            // ->where('school_id', $schoolId)
            ->get();
    }

    //Get Records by name
    public function searchByName($name)
    {
        // $schoolId = authUser()->school_id;
        return Gallery::select(
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
            ->where('category', 'ilike', $name . '%');
            // ->where('school_id', $schoolId)
            // ->where('status', 1)
            // ->get();
    }

    /*Read Records by ID*/
    public function getGroupById1($id)
    {
        // $schoolId = authUser()->school_id;
        return Gallery::select(
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
            // ->where('school_id', $schoolId)
            // ->where('status', 1)
            ->first();
    }

    public function getGroupById($name)
    {
        $schoolId = authUser()->school_id;
        $data = array();
        $categoryData = Gallery::where('category', Str::title($name))->get();
        if ($categoryData->isNotEmpty()) {
            $path = "getImageLink?path=";
            $categoryName = $categoryData->first()->category;
            $coverPic = $categoryData->first()->cover_pic_docs;
            $coverPicName = $path.$coverPic; 
            $pics = $categoryData->pluck('upload_pic_docs')->flatten();
            
            // Create an array of objects for each pic
            $picsObjects = $pics->map(function ($pic) {
                $path = "getImageLink?path=";
                return ['pic_url' => $path.$pic];
            });
            $data = [
                'category' => $categoryName,
                'coverPic' => $coverPicName,
                'pics' => $picsObjects,
            ];
        }
        return $data;
    }

    // Get Details By Category name
    public function getGroupByName($categoryName)
    {
        $schoolId = authUser()->school_id;
        $data = array();
        $categoryData = Gallery::where('category', Str::title($categoryName))->get();
        // return $categoryData; die;
        if ($categoryData->isNotEmpty()) {
            $path = "getImageLink?path=";
            $categoryName = $categoryData->first()->category;
            $coverPic = $categoryData->first()->cover_pic_docs;
            $coverPicName = $path.$coverPic; 
            $pics = $categoryData->pluck('upload_pic_docs')->flatten();
            
            // Create an array of objects for each pic
            $picsObjects = $pics->map(function ($pic) {
                $path = "getImageLink?path=";
                return [
                    'pic_url' => $path.$pic
                ];
            });
            $data = [
                'category' => $categoryName,
                'coverPic' => $coverPicName,
                'pics' => $picsObjects,
            ];
        }
        return $data;
    }

    /*Read all Records by*/
    public function retrieve1()
    {
    
        return Gallery::select(
            DB::raw(" DISTINCT category,cover_pic_docs,
        CASE 
        WHEN status = '0' THEN 'Deactivated'  
        WHEN status = '1' THEN 'Active'
        END as status,
        TO_CHAR(created_at::date,'dd-mm-yyyy') as date,
        TO_CHAR(created_at,'HH12:MI:SS AM') as time
	  ")
        )
            ->orderBy('category');
    }

    public function retrieve()
    {
        return Gallery::select(
            DB::raw("STRING_AGG(id::text, ',') as all_ids"),
            DB::raw("MIN(id) as id"),
            'category',
            'cover_pic_docs',
            DB::raw("CASE 
                WHEN status = '0' THEN 'Deactivated'  
                WHEN status = '1' THEN 'Active'
                END as status"),
            DB::raw("TO_CHAR(created_at::date,'dd-mm-yyyy') as date"),
            DB::raw("TO_CHAR(created_at,'HH12:MI:SS AM') as time")
        )
        ->groupBy('category', 'cover_pic_docs', 'status', 'created_at')
        ->orderBy('category');
        // ->get();
    }

    /*Read all Active Records*/
    public function active()
    {
        // $schoolId = authUser()->school_id;
        return Gallery::select(
            DB::raw("id,book_name,author_name,publish_by,price,
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
            ->orderBy('book_name')
            ->get();
    }
}
