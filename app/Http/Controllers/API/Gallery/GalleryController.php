<?php

namespace App\Http\Controllers\API\Gallery;

use App\Http\Controllers\Controller;
use App\Models\Gallery\Gallery;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    private $_mGallery;

    public function __construct()
    {
        DB::enableQueryLog();
        $this->_mGallery = new Gallery();
    }

    // Add records
    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'category' => 'required|string',
            'coverPicDoc' => 'required|mimes:jpg,png,jpeg|file|max:10240',      // Only one coverPic allowed
            'picUpload' => 'array',                                           // uploadPic should be an array of files
            'picUpload.*' => 'mimes:jpg,png,jpeg|file|max:10240',             // Each item in the uploadPic array should be a file
        ]);
        if ($validator->fails()){
            return responseMsgs(false, $validator->errors(), []);
        }
        try 
        {
            $file_name = '';
            $file_name1 = '';
            $timestamp = now()->timestamp; 
            $uploadPicPaths = [];
            $isGroupExists = $this->_mGallery->readGalleryGroup($req);
            if (collect($isGroupExists)->isNotEmpty())
                throw new Exception("Category Already Existing");

            if ($req->coverPicDoc != "") {
                $coverPicDoc = $req->file('coverPicDoc');
                $get_file_name = $timestamp. '-' . $coverPicDoc->getClientOriginalName();
                $path = public_path('school/gallery/'.$req->category.'/cover/');
                $file_name1 = 'school/gallery/' .$req->category.'/cover/'. $get_file_name;
                $coverPicDoc->move($path, $get_file_name);
            }

            $metaReqs = [
                'category' => Str::title($req->category),
                'cover_pic_docs' => $file_name1,
            ];

            if ($req->picUpload != "") {
                $picUpload = $req->file('picUpload');  
                
                foreach ($picUpload as $uploadPic) {
                    $get_file_name = $timestamp. '-' . $uploadPic->getClientOriginalName();
                    $path = public_path('school/gallery/'.$req->category.'/images/');
                    $file_name = 'school/gallery/' . $req->category.'/images/'. $get_file_name;
                    $uploadPic->move($path, $get_file_name);
                    $uploadPicPaths[] = $file_name; 

                    $metaReqs = array_merge($metaReqs, [
                        'upload_pic_docs' => $file_name,
                        'school_id' => authUser()->school_id,
                        'created_by' => authUser()->id,
                        'ip_address' => getClientIpAddress(),
                        // 'json_logs' => trim(json_encode($metaReqs), ",")
                    ]);
                    $metaReqs = array_merge($metaReqs, [
                        'json_logs' => trim(json_encode($metaReqs), ",")
                    ]);
                    $this->_mGallery->store($metaReqs);
                    $metaReqs['json_logs'] = "";
                }
            }else{
                $metaReqs = [
                    'category' => Str::title($req->category),
                    'cover_pic_docs' => $file_name1,
                    // 'upload_pic_docs' => json_encode($uploadPicPaths),
                    'school_id' => authUser()->school_id,
                    'created_by' => authUser()->id,
                    'ip_address' => getClientIpAddress()
                ];
                $metaReqs = array_merge($metaReqs, [
                    'json_logs' => trim(json_encode($metaReqs), ",")
                ]);
                $this->_mGallery->store($metaReqs);
            }            
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Records Added Successfully", $metaReqs, "M_API_2.1", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_2.1", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //show data by id
    public function show(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'id' => 'required|numeric'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $id = Gallery::where('id', $req->id)->first();
            // return $id->category; die;
            $show = $this->_mGallery->getGroupById($id->category);
            if (collect($show)->isEmpty())
                throw new Exception("Data Not Found");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View Records", $show, "M_API_2.3", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_2.3", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //show data by id
    public function showByName(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'categoryName' => 'required|string'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $data = array();
            $categoryData = Gallery::where('category', Str::title($req->categoryName))->get();
            if (collect($categoryData)->isEmpty())
                throw new Exception("Data Not Found");
            $data = $this->_mGallery->getGroupByName($req->categoryName);
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View Records", $data, "M_API_2.3", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_2.3", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    // Edit records
    public function edit(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'id'       => 'required|numeric',
            'category' => 'required|string',
            'coverPicDoc' => 'required|mimes:jpg,png|file|max:2048',      // Only one coverPic allowed
            'picUpload' => 'array',                                  // uploadPic should be an array of files
            'picUpload.*' => 'mimes:jpg,png,jpeg|file|max:2048',    // Each item in the uploadPic array should be a file
        ]);
        if ($validator->fails()){
            return responseMsgs(false, $validator->errors(), []);
        }
        try {
            $file_name = '';
            $file_name1 = '';
            $timestamp = now()->timestamp; 
            $uploadPicPaths = [];
            $isGroupExists = $this->_mGallery->readGalleryGroup($req);
            if (collect($isGroupExists)->isNotEmpty())
                throw new Exception("Category Already Existing");

            if ($req->coverPicDoc != "") {
                $coverPicDoc = $req->file('coverPicDoc');
                $get_file_name = $timestamp. '-' . $coverPicDoc->getClientOriginalName();
                $path = public_path('school/gallery/'.$req->category.'/cover/');
                $file_name1 = 'school/gallery/' . $get_file_name;
                $coverPicDoc->move($path, $get_file_name);
            }

            $metaReqs = [
                'category' => Str::title($req->category),
                'cover_pic_docs' => $file_name1,
            ];

            if ($req->picUpload != "") {
                $picUpload = $req->file('picUpload');  
                
                foreach ($picUpload as $uploadPic) {
                    $get_file_name = $timestamp. '-' . $uploadPic->getClientOriginalName();
                    $path = public_path('school/gallery/'.$req->category.'/images/');
                    $file_name = 'school/gallery/' . $get_file_name;
                    $uploadPic->move($path, $get_file_name);
                    $uploadPicPaths[] = $file_name; 

                    $metaReqs = array_merge($metaReqs, [
                        'upload_pic_docs' => $file_name,
                        'school_id' => authUser()->school_id,
                        'created_by' => authUser()->id,
                        'ip_address' => getClientIpAddress(),
                        'json_logs' => trim(json_encode($metaReqs), ",")
                    ]);
                    return $metaReqs; die;
                    $this->_mGallery->store($metaReqs);
                }
            }else{
                $metaReqs = [
                    'category' => Str::title($req->category),
                    'cover_pic_docs' => $file_name1,
                    // 'upload_pic_docs' => json_encode($uploadPicPaths),
                    'school_id' => authUser()->school_id,
                    'created_by' => authUser()->id,
                    'ip_address' => getClientIpAddress()
                ];
                $metaReqs = array_merge($metaReqs, [
                    'json_logs' => trim(json_encode($metaReqs), ",")
                ]);
                $this->_mGallery->update($metaReqs);
            }    
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Records Updated Successfully", $metaReqs, "M_API_2.2", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_2.2", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //View All
    public function retrieveAll(Request $req)
    {
        try {
            $path = 'api\getImageLink?path=';
            $getData = $this->_mGallery->retrieve();
            // return $getData->get(); 
            $perPage = $req->perPage ? $req->perPage : 10;
            $paginater = $getData->paginate($perPage);
            // if ($paginater == "")
            //     throw new Exception("Data Not Found");
            
            // $list = [
            //     "current_page" => $paginater->currentPage(),
            //     "perPage" => $perPage,
            //     "last_page" => $paginater->lastPage(),
            //     "data" => $paginater->items(),
            //     "total" => $paginater->total()
            // ];

            $list = [
                "current_page" => $paginater->currentPage(),
                "perPage" => $perPage,
                "last_page" => $paginater->lastPage(),
                "data" => collect($paginater->items())->map(function ($val) {
                    $path = "getImageLink?path=";
                    // $val->ebook_docs = trim($val->ebook_docs) ? ($path . $val->ebook_docs) : "";
                    $val->cover_pic_docs = trim($val->cover_pic_docs) ? ($path . $val->cover_pic_docs) : "";
                    return $val;
                }),
                "total" => $paginater->total()
            ];

            
            // $list = array_merge($list, [
            //     $file_name = $path . $list->ebook_docs,

            //     // 'json_logs' => trim(json_encode($list), ",")
            // ]);
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View All Records", $list, "M_API_2.4", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_2.4", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //search by name
    public function search(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'search' => 'required|string'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);

        try {
            $getData = $this->_mGallery->searchByName($req->search);
            $perPage = $req->perPage ? $req->perPage : 10;
            $paginater = $getData->paginate($perPage);
            $list = [
                "current_page" => $paginater->currentPage(),
                "perPage" => $perPage,
                "last_page" => $paginater->lastPage(),
                "data" => collect($paginater->items())->map(function ($val) {
                    $path = "getImageLink?path=";
                    $val->ebook_docs = trim($val->ebook_docs) ? ($path . $val->ebook_docs) : "";
                    $val->cover_pic_docs = trim($val->cover_pic_docs) ? ($path . $val->cover_pic_docs) : "";
                    return $val;
                }),
                "total" => $paginater->total()
            ];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View All Records", $list, "M_API_2.4", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_2.4", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
}
