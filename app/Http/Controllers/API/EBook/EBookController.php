<?php

namespace App\Http\Controllers\API\EBook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\EBook\EBook;
use Exception;
use DB;


/**
 * | Created On- 16-06-2023 
 * | Created By- Lakshmi Kumari
 * | Description- EBook CRUDS Operations
 * | Code Status- Closed
 */

class EBookController extends Controller
{
    private $_mEBook;

    public function __construct()
    {
        DB::enableQueryLog();
        $this->_mEBook = new EBook();
    }

    // Add records
    public function store(Request $req)
    {

        $validator = Validator::make($req->all(), [
            'bookName' => 'required|string',
            'authorName' => 'required|string',
            'publishBy' => 'required|string',
            'publishedDate' => 'required',
            'price' => 'required|integer',
            'ebookDocs' => 'required',
            'coverPicDocs' => 'required'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $file_name = '';
            $file_name1 = '';
            $isGroupExists = $this->_mEBook->readEBookGroup($req);
            if (collect($isGroupExists)->isNotEmpty())
                throw new Exception("Book Name Already Existing");

            if ($req->ebookDocs != "") {
                $ebookDocs = $req->ebookDocs;
                $get_file_name = $req->publishedDate . '-' . $ebookDocs->getClientOriginalName();
                $path = public_path('school/e-book/');
                $file_name = 'school/e-book/' . $get_file_name;
                $req->file('ebookDocs')->move($path, $get_file_name);
            }

            if ($req->coverPicDocs != "") {
                $coverPicDocs = $req->coverPicDocs;
                $get_file_name = $req->publishedDate . '-' . $coverPicDocs->getClientOriginalName();
                $path = public_path('school/e-book/');
                $file_name1 = 'school/e-book/' . $get_file_name;
                $req->file('coverPicDocs')->move($path, $get_file_name);
            }

            $metaReqs = [
                'book_name' => Str::title($req->bookName),
                'author_name' => $req->authorName,
                'publish_by' => $req->publishBy,
                'published_date' => $req->publishedDate,
                'price' => $req->price,
                'ebook_docs' => $file_name,
                'cover_pic_docs' => $file_name1,
                'school_id' => authUser()->school_id,
                'created_by' => authUser()->id,
                'ip_address' => getClientIpAddress(),
                'version_no' => 0
            ];
            $metaReqs = array_merge($metaReqs, [
                'json_logs' => trim(json_encode($metaReqs), ",")
            ]);
            // print_var($metaReqs);
            // die;
            $this->_mEBook->store($metaReqs);
            $data = ['Book Name' => $req->bookName];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Records Added Successfully", $data, "M_API_2.1", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_2.1", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    // Edit records
    public function edit(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'id' => 'required|numeric',
            'bookName' => 'required|string',
            'authorName' => 'required|string',
            'publishBy' => 'required|string',
            'publishedDate' => 'required',
            'price' => 'required|integer'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            $file_name = '';
            $file_name1 = '';
            $isExists = $this->_mEBook->readEBookGroup($req);

            if ($isExists && $isExists->where('id', '!=', $req->id)->isNotEmpty())
                throw new Exception("Book Name Already Existing");

            $getData = $this->_mEBook::findOrFail($req->id);

            if ($req->ebookDocs != "") {
                $files = $getData->ebook_docs;
                unlink($files);
                $ebookDocs = $req->ebookDocs;
                $get_file_name = $req->publishedDate . '-' . $ebookDocs->getClientOriginalName();
                $path = public_path('school/e-book/');
                $file_name = 'school/e-book/' . $get_file_name;
                $req->file('ebookDocs')->move($path, $get_file_name);
            }

            if ($req->coverPicDocs != "") {
                $files = $getData->cover_pic_docs;
                unlink($files);
                $coverPicDocs = $req->coverPicDocs;
                $get_file_name = $req->publishedDate . '-' . $coverPicDocs->getClientOriginalName();
                $path = public_path('school/e-book/');
                $file_name1 = 'school/e-book/' . $get_file_name;
                $req->file('coverPicDocs')->move($path, $get_file_name);
            }

            $metaReqs = [
                // 'book_name' => Str::title($req->bookName),
                // 'author_name' => $req->authorName,
                'publish_by' => $req->publishBy,
                'published_date' => $req->publishedDate,
                'price' => $req->price,
                'ebook_docs' => $file_name,
                'cover_pic_docs' => $file_name1,
                // 'school_id' => authUser()->school_id,
                'version_no' => $getData->version_no + 1,
                'updated_at' => Carbon::now()
            ];
            $metaReqs = array_merge($metaReqs, [
                'json_logs' => trim($getData->json_logs . "," . json_encode($metaReqs), ",")
            ]);
            if (isset($req->status)) {                  // In Case of Deactivation or Activation 
                $status = $req->status == 'deactive' ? 0 : 1;
                $metaReqs = array_merge($metaReqs, [
                    'status' => $status
                ]);
            }
            $getData->update($metaReqs);
            $data = ['Book Name' => $req->bookName];
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Records Updated Successfully", $data, "M_API_2.2", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_2.2", responseTime(), "POST", $req->deviceId ?? "");
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
            $show = $this->_mEBook->getGroupById($req->id);
            if (collect($show)->isEmpty())
                throw new Exception("Data Not Found");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View Records", $show, "M_API_2.3", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_2.3", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
    //View All
    public function retrieveAll(Request $req)
    {
        try {
            $getData = $this->_mEBook->retrieve();
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

    //Activate / Deactivate
    public function delete(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'status' => 'required|in:active,deactive'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            if (isset($req->status)) {                  // In Case of Deactivation or Activation 
                $status = $req->status == 'deactive' ? 0 : 1;
                $metaReqs =  [
                    'status' => $status
                ];
            }
            $delete = $this->_mEBook::findOrFail($req->id);
            $delete->update($metaReqs);
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "Changes Done Successfully", $req->status, "M_API_2.5", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_2.5", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //Active All
    public function activeAll(Request $req)
    {
        try {
            $getData = $this->_mEBook->active();
            if (collect($getData)->isEmpty())
                throw new Exception("Data Not Found");
            $queryTime = collect(DB::getQueryLog())->sum("time");
            return responseMsgsT(true, "View All Active Records", $getData, "M_API_2.6", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "M_API_2.6", responseTime(), "POST", $req->deviceId ?? "");
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
            $getData = $this->_mEBook->searchByName($req);
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

        // try {
        //     $getData = $this->_mEBook->searchByName($req);
        //     $perPage = $req->perPage ? $req->perPage : 10;
        //     $paginater = $getData->paginate($perPage);
        //     // if ($paginater == "")
        //     //     throw new Exception("Data Not Found");
        //     $list = [
        //         "current_page" => $paginater->currentPage(),
        //         "perPage" => $perPage,
        //         "last_page" => $paginater->lastPage(),
        //         "data" => $paginater->items(),
        //         "total" => $paginater->total()
        //     ];
        //     $queryTime = collect(DB::getQueryLog())->sum("time");
        //     return responseMsgsT(true, "View Searched Records", $list, "M_API_2.7", $queryTime, responseTime(), "POST", $req->deviceId ?? "");
        // } catch (Exception $e) {
        //     return responseMsgs(false, $e->getMessage(), [], "", "M_API_2.7", responseTime(), "POST", $req->deviceId ?? "");
        // }
    }
}
