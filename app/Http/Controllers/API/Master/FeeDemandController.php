<?php

namespace App\Http\Controllers\API\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;
use App\BLL\GenerateFeeDemand;
use App\Models\Student\Student;
use App\Models\Master\FeeDemand;


class FeeDemandController extends Controller
{
    private $_mGenerateFeeDemands;
    private $_mFeeDemands;

    public function __construct()
    {
        $this->_mGenerateFeeDemands = new GenerateFeeDemand();
        $this->_mFeeDemands = new FeeDemand();
    }

    public function generateDemand(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'admissionNo' => 'required',
            'fy' => 'required'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);
        try {
            // echo $req->fy;
            // die;
            // $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
            $schoolId = authUser()->school_id;
            $createdBy = authUser()->id;
            $mStudents = Student::where('admission_no', $req->admissionNo)
                // ->where('academic_year', $req->fy)
                // ->where('school_id', $schoolId)
                // ->where('created_by', $createdBy)
                ->where('status', 1)
                ->first();
            if (collect($mStudents)->isEmpty())
                throw new Exception('Admission No. Not Existing');
            $studentId  = $mStudents->id;
            $classId  = $mStudents->class_id;

            $mFeeDemand = FeeDemand::where('student_id', $studentId)
                // ->where('fy_name', $req->fy)
                ->where('class_id', $classId)
                // ->where('school_id', $schoolId)
                // ->where('created_by', $createdBy)
                ->where('status', 1)
                ->get();
            if (collect($mFeeDemand)->isNotEmpty())
                throw new Exception('Fee Demand Already Existing');


            // $studentId = $req->studentId;
            $generateDemand = new GenerateFeeDemand;
            $response = $generateDemand->generate($studentId);
            // return $response;
            return responseMsgs(true, "Demand Generated Successfully", $response, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    /**
     * | Get month wise fee
     */
    public function show(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'admissionNo' => 'required|string',
            'monthName' => 'nullable|string'
            // 'fy' => 'required'
        ]);
        if ($validator->fails())
            return responseMsgs(false, $validator->errors(), []);

        try {
            $mStudents = Student::where('admission_no', $req->admissionNo)
                // ->where('academic_year', $req->fy)
                // ->where('school_id', authUser()->school_id)
                // ->where('created_by', authUser()->id)
                ->where('status', 1)
                ->first();

            if (collect($mStudents)->isEmpty())
                throw new Exception('Admission no is not existing');
            $studentId  = $mStudents->id;
            $monthName = $req->monthName;
            $show = $this->_mFeeDemands->getFees($studentId, $monthName);
            // dd($show);
            if (collect($show)->isEmpty())
                throw new Exception("Data Not Found");
            return responseMsgs(true, "Records", $show, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }

    //generate fy wise data for all std
    // public function generateFyWiseDemand(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'fy' => 'required'
    //     ]);
    //     if ($validator->fails())
    //         return responseMsgs(false, $validator->errors(), []);
    //     try {
    //         // echo $req->fy;

    //         // $fy =  getFinancialYear(Carbon::now()->format('Y-m-d'));
    //         $mStudents = Student::where('academic_year', $req->fy)
    //             ->where('status', 1)
    //             ->first();
    //         if (collect($mStudents)->isEmpty())
    //             throw new Exception('Academic year not existing');
    //         // echo "<pre>";
    //         // print_r($mStudents);
    //         // die;

    //         $fy = $req->fy;
    //         // $fy = $mStudents->academic_year;
    //         // echo "ok";

    //         // $studentId = $req->studentId;
    //         $generateDemand = new GenerateFeeDemand;
    //         $response = $generateDemand->generateFyData($fy);
    //         // return $response;
    //         return responseMsgs(true, "Demand Generated Successfully", $response, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     } catch (Exception $e) {
    //         return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
    //     }
    // }


    /**
     * | Retrieve All
     */
    public function retrieveAll(Request $req)
    {
        try {
            $feeDemand = $this->_mGenerateFeeDemands->retrieve();
            return responseMsgs(true, "", $feeDemand, "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        } catch (Exception $e) {
            return responseMsgs(false, $e->getMessage(), [], "", "1.0", responseTime(), "POST", $req->deviceId ?? "");
        }
    }
}
