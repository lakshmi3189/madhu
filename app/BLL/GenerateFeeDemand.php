<?php

namespace App\BLL;

use App\Models\Master\ClassFeeMaster;
use App\Models\Master\DiscountGroupMap;
use App\Models\Master\DiscountGroup;
use App\Models\Master\FeeHeadType;
use App\Models\Master\FeeHead;
use App\Models\Master\FeeDemand;
use App\Models\Student\Student;
use Illuminate\Support\Carbon;
use DB;



class GenerateFeeDemand
{
    private $_studentId;
    private $_mStudent;
    private $_classId;
    private $_mClassFeeMaster;
    private $_classFee;
    private $_mDiscountGroupMaps;
    private $_studentDiscounts;
    private $_mFeeDemands;

    //created on : 12-06-2023
    //created by : lakshmi kumari
    //finanacial year wise demand generate
    private $_fy;
    private $_getFy;
    private $_mDiscountGroup;
    private $_mFeeHead;
    private $_mFeeHeadType;


    public function __construct()
    {
        $this->_mStudent = new Student();
        $this->_mClassFeeMaster = new ClassFeeMaster();
        $this->_mDiscountGroupMaps = new DiscountGroupMap();
        $this->_mFeeDemands = new FeeDemand();

        //financial year wise demand generate
        $this->_mDiscountGroup = new DiscountGroup();
        $this->_mFeeHeadType = new FeeHeadType();
        $this->_mFeeHead = new FeeHead();
    }

    //generate demand admission no wise 
    public function generate($studentId)
    {
        // echo 'std-id- ' . $studentId;
        // die;
        $this->_studentId = $studentId;
        $this->readParams();
    }

    public function readParams()
    {
        $studentDtls = $this->_mStudent::findOrFail($this->_studentId);
        // print_var($studentDtls);
        // die;
        $this->_classId = $studentDtls->class_id;
        $this->_classFee = $this->_mClassFeeMaster->getClassFeeMasterByClassId($this->_classId);
        // print_var($this->_classFee);
        $this->_classFee = $this->_classFee->toArray();
        $this->insertFeeDemand();
    }

    public function insertFeeDemand()
    {
        $month = [
            'jan_fee', 'feb_fee', 'mar_fee', 'apr_fee', 'may_fee', 'jun_fee',
            'jul_fee', 'aug_fee', 'sep_fee', 'oct_fee', 'nov_fee', 'dec_fee'
        ];
        // return dd($month);
        // return authUser()->school_id;

        foreach ($month as $month) {
            if ($month == 'jan_fee') {
                foreach ($this->_classFee as $fee) {
                    $reqs = [
                        'fy_name' => $fee['academic_year'],
                        'month_name' => 'January',
                        'amount' => $fee['jan_fee'],
                        'student_id' => $this->_studentId,
                        'class_id' => $fee['class_id'],
                        'class_fee_master_id' => $fee['id'],
                        'fee_head_id' => $fee['fee_head_id'],
                        'discount_percent' => $fee['discount'],
                        'demand_date' => Carbon::now(),
                        'school_id' => authUser()->school_id,
                        'created_by' => authUser()->id,
                        'ip_address' => getClientIpAddress()
                    ];
                    $this->_mFeeDemands->store($reqs);
                }
            }

            if ($month == 'feb_fee') {
                foreach ($this->_classFee as $fee) {
                    $reqs = [
                        'fy_name' => $fee['academic_year'],
                        'month_name' => 'February',
                        'amount' => $fee['feb_fee'],
                        'student_id' => $this->_studentId,
                        'class_id' => $fee['class_id'],
                        'class_fee_master_id' => $fee['id'],
                        'fee_head_id' => $fee['fee_head_id'],
                        'discount_percent' => $fee['discount'],
                        'demand_date' => Carbon::now(),
                        'school_id' => authUser()->school_id,
                        'created_by' => authUser()->id,
                        'ip_address' => getClientIpAddress()
                    ];
                    $this->_mFeeDemands->store($reqs);
                }
            }

            if ($month == 'mar_fee') {
                foreach ($this->_classFee as $fee) {
                    $reqs = [
                        'fy_name' => $fee['academic_year'],
                        'month_name' => 'March',
                        'amount' => $fee['mar_fee'],
                        'student_id' => $this->_studentId,
                        'class_id' => $fee['class_id'],
                        'class_fee_master_id' => $fee['id'],
                        'fee_head_id' => $fee['fee_head_id'],
                        'discount_percent' => $fee['discount'],
                        'demand_date' => Carbon::now(),
                        'school_id' => authUser()->school_id,
                        'created_by' => authUser()->id,
                        'ip_address' => getClientIpAddress()
                    ];
                    $this->_mFeeDemands->store($reqs);
                }
            }

            if ($month == 'apr_fee') {
                foreach ($this->_classFee as $fee) {
                    $reqs = [
                        'fy_name' => $fee['academic_year'],
                        'month_name' => 'April',
                        'amount' => $fee['apr_fee'],
                        'student_id' => $this->_studentId,
                        'class_id' => $fee['class_id'],
                        'class_fee_master_id' => $fee['id'],
                        'fee_head_id' => $fee['fee_head_id'],
                        'discount_percent' => $fee['discount'],
                        'demand_date' => Carbon::now(),
                        'school_id' => authUser()->school_id,
                        'created_by' => authUser()->id,
                        'ip_address' => getClientIpAddress()
                    ];
                    $this->_mFeeDemands->store($reqs);
                }
            }
            if ($month == 'may_fee') {
                foreach ($this->_classFee as $fee) {
                    $reqs = [
                        'fy_name' => $fee['academic_year'],
                        'month_name' => 'May',
                        'amount' => $fee['may_fee'],
                        'student_id' => $this->_studentId,
                        'class_id' => $fee['class_id'],
                        'class_fee_master_id' => $fee['id'],
                        'fee_head_id' => $fee['fee_head_id'],
                        'discount_percent' => $fee['discount'],
                        'demand_date' => Carbon::now(),
                        'school_id' => authUser()->school_id,
                        'created_by' => authUser()->id,
                        'ip_address' => getClientIpAddress()
                    ];
                    $this->_mFeeDemands->store($reqs);
                }
            }
            if ($month == 'jun_fee') {
                foreach ($this->_classFee as $fee) {
                    $reqs = [
                        'fy_name' => $fee['academic_year'],
                        'month_name' => 'June',
                        'amount' => $fee['jun_fee'],
                        'student_id' => $this->_studentId,
                        'class_id' => $fee['class_id'],
                        'class_fee_master_id' => $fee['id'],
                        'fee_head_id' => $fee['fee_head_id'],
                        'discount_percent' => $fee['discount'],
                        'demand_date' => Carbon::now(),
                        'school_id' => authUser()->school_id,
                        'created_by' => authUser()->id,
                        'ip_address' => getClientIpAddress()
                    ];
                    $this->_mFeeDemands->store($reqs);
                }
            }

            if ($month == 'jul_fee') {
                foreach ($this->_classFee as $fee) {
                    $reqs = [
                        'fy_name' => $fee['academic_year'],
                        'month_name' => 'July',
                        'amount' => $fee['jul_fee'],
                        'student_id' => $this->_studentId,
                        'class_id' => $fee['class_id'],
                        'class_fee_master_id' => $fee['id'],
                        'fee_head_id' => $fee['fee_head_id'],
                        'discount_percent' => $fee['discount'],
                        'demand_date' => Carbon::now(),
                        'school_id' => authUser()->school_id,
                        'created_by' => authUser()->id,
                        'ip_address' => getClientIpAddress()
                    ];
                    $this->_mFeeDemands->store($reqs);
                }
            }
            if ($month == 'aug_fee') {
                foreach ($this->_classFee as $fee) {
                    $reqs = [
                        'fy_name' => $fee['academic_year'],
                        'month_name' => 'August',
                        'amount' => $fee['aug_fee'],
                        'student_id' => $this->_studentId,
                        'class_id' => $fee['class_id'],
                        'class_fee_master_id' => $fee['id'],
                        'fee_head_id' => $fee['fee_head_id'],
                        'discount_percent' => $fee['discount'],
                        'demand_date' => Carbon::now(),
                        'school_id' => authUser()->school_id,
                        'created_by' => authUser()->id,
                        'ip_address' => getClientIpAddress()
                    ];
                    $this->_mFeeDemands->store($reqs);
                }
            }
            if ($month == 'sep_fee') {
                foreach ($this->_classFee as $fee) {
                    $reqs = [
                        'fy_name' => $fee['academic_year'],
                        'month_name' => 'September',
                        'amount' => $fee['sep_fee'],
                        'student_id' => $this->_studentId,
                        'class_id' => $fee['class_id'],
                        'class_fee_master_id' => $fee['id'],
                        'fee_head_id' => $fee['fee_head_id'],
                        'discount_percent' => $fee['discount'],
                        'demand_date' => Carbon::now(),
                        'school_id' => authUser()->school_id,
                        'created_by' => authUser()->id,
                        'ip_address' => getClientIpAddress()
                    ];
                    $this->_mFeeDemands->store($reqs);
                }
            }
            if ($month == 'oct_fee') {
                foreach ($this->_classFee as $fee) {
                    $reqs = [
                        'fy_name' => $fee['academic_year'],
                        'month_name' => 'October',
                        'amount' => $fee['oct_fee'],
                        'student_id' => $this->_studentId,
                        'class_id' => $fee['class_id'],
                        'class_fee_master_id' => $fee['id'],
                        'fee_head_id' => $fee['fee_head_id'],
                        'discount_percent' => $fee['discount'],
                        'demand_date' => Carbon::now(),
                        'school_id' => authUser()->school_id,
                        'created_by' => authUser()->id,
                        'ip_address' => getClientIpAddress()
                    ];
                    $this->_mFeeDemands->store($reqs);
                }
            }
            if ($month == 'nov_fee') {
                foreach ($this->_classFee as $fee) {
                    $reqs = [
                        'fy_name' => $fee['academic_year'],
                        'month_name' => 'November',
                        'amount' => $fee['nov_fee'],
                        'student_id' => $this->_studentId,
                        'class_id' => $fee['class_id'],
                        'class_fee_master_id' => $fee['id'],
                        'fee_head_id' => $fee['fee_head_id'],
                        'discount_percent' => $fee['discount'],
                        'demand_date' => Carbon::now(),
                        'school_id' => authUser()->school_id,
                        'created_by' => authUser()->id,
                        'ip_address' => getClientIpAddress()
                    ];
                    $this->_mFeeDemands->store($reqs);
                }
            }

            if ($month == 'dec_fee') {
                foreach ($this->_classFee as $fee) {
                    $reqs = [
                        'fy_name' => $fee['academic_year'],
                        'month_name' => 'December',
                        'amount' => $fee['dec_fee'],
                        'student_id' => $this->_studentId,
                        'class_id' => $fee['class_id'],
                        'class_fee_master_id' => $fee['id'],
                        'fee_head_id' => $fee['fee_head_id'],
                        'discount_percent' => $fee['discount'],
                        'demand_date' => Carbon::now(),
                        'school_id' => authUser()->school_id,
                        'created_by' => authUser()->id,
                        'ip_address' => getClientIpAddress()
                    ];
                    $this->_mFeeDemands->store($reqs);
                }
            }
        }
    }
    //end generate demand admission no wise 

    /* for fy wise auto generate
    //generate fynancial year wise data
    public function generateFyData($fy)
    {
        $this->_fy = $fy; //get fy
        $this->readFyWiseParams(); //get all data to generate demand    
        $this->readFee();
    }

    public function readFyWiseParams()
    {
        $studentDtls = $this->_mStudent::where('academic_year', $this->_fy)->get(); //array object
        // var_dump($studentDtls);
        $arr = array();
        foreach ($studentDtls as $std) {

            $arr1['academic_year'] = $std->academic_year;
            $arr1['class_id'] = $std->class_id;
            $arr1['admission_no'] = $std->admission_no;
            $arr[] = $arr1;
        }
        // print_var($arr);


        // $this->_getFy = $studentDtls->academic_year;
        // $this->_classFee = $this->_mClassFeeMaster->getClassFeeMasterByClassId($this->_classId);
        // $this->_classFee = $this->_classFee->toArray();
        // $this->insertFyFeeDemand();
    }

    public function readFee()
    {
        $getClassFeeMasters = $this->_mClassFeeMaster::where('academic_year', $this->_fy)->get();
        $getFeeHeadTypes = $this->_mFeeHeadType::where('academic_year', $this->_fy)->get();
        $getFeeHeads = $this->_mFeeHead::where('academic_year', $this->_fy)->get();
        $getDiscountGroups = $this->_mDiscountGroup::where('academic_year', $this->_fy)->get();
        $getDiscountGroupMaps = $this->_mDiscountGroupMaps::where('academic_year', $this->_fy)->get();
        // print_var($getDiscountGroupMaps);
    }

    public function insertFyFeeDemand()
    {
        $month = [
            'january', 'february', 'march', 'april', 'may', 'june',
            'july', 'august', 'september', 'october', 'november', 'december'
        ];

        foreach ($month as $month) {
            if ($month == 'january') {
                foreach ($this->_classFee as $fee) {
                    $reqs = [
                        'fy_name' => $fee['academic_year'],
                        'month_name' => 'January',
                        'amount' => $fee['jan_fee'],
                        'student_id' => $this->_studentId,
                        'class_id' => $fee['class_id'],
                        'class_fee_master_id' => $fee['id'],
                        'fee_head_id' => $fee['fee_head_id'],
                        'discount_percent' => $fee['discount'],
                        'demand_date' => Carbon::now(),
                        'school_id' => '1',
                        'created_by' => '1',
                        'ip_address' => getClientIpAddress()
                    ];
                    $this->_mFeeDemands->store($reqs);
                }
            }

            // if ($month == 'feb_fee') {
            //     foreach ($this->_classFee as $fee) {
            //         $reqs = [
            //             'fy_name' => $fee['academic_year'],
            //             'month_name' => 'February',
            //             'amount' => $fee['feb_fee'],
            //             'student_id' => $this->_studentId,
            //             'class_id' => $fee['class_id'],
            //             'class_fee_master_id' => $fee['id'],
            //             'fee_head_id' => $fee['fee_head_id'],
            //             'discount_percent' => $fee['discount'],
            //             'demand_date' => Carbon::now(),
            //             'school_id' => '1',
            //             'created_by' => '1',
            //             'ip_address' => getClientIpAddress()
            //         ];
            //         $this->_mFeeDemands->store($reqs);
            //     }
            // }

            // if ($month == 'mar_fee') {
            //     foreach ($this->_classFee as $fee) {
            //         $reqs = [
            //             'fy_name' => $fee['academic_year'],
            //             'month_name' => 'March',
            //             'amount' => $fee['mar_fee'],
            //             'student_id' => $this->_studentId,
            //             'class_id' => $fee['class_id'],
            //             'class_fee_master_id' => $fee['id'],
            //             'fee_head_id' => $fee['fee_head_id'],
            //             'discount_percent' => $fee['discount'],
            //             'demand_date' => Carbon::now(),
            //             'school_id' => '1',
            //             'created_by' => '1',
            //             'ip_address' => getClientIpAddress()
            //         ];
            //         $this->_mFeeDemands->store($reqs);
            //     }
            // }

            // if ($month == 'apr_fee') {
            //     foreach ($this->_classFee as $fee) {
            //         $reqs = [
            //             'fy_name' => $fee['academic_year'],
            //             'month_name' => 'April',
            //             'amount' => $fee['apr_fee'],
            //             'student_id' => $this->_studentId,
            //             'class_id' => $fee['class_id'],
            //             'class_fee_master_id' => $fee['id'],
            //             'fee_head_id' => $fee['fee_head_id'],
            //             'discount_percent' => $fee['discount'],
            //             'demand_date' => Carbon::now(),
            //             'school_id' => '1',
            //             'created_by' => '1',
            //             'ip_address' => getClientIpAddress()
            //         ];
            //         $this->_mFeeDemands->store($reqs);
            //     }
            // }

            // if ($month == 'may_fee') {
            //     foreach ($this->_classFee as $fee) {
            //         $reqs = [
            //             'fy_name' => $fee['academic_year'],
            //             'month_name' => 'May',
            //             'amount' => $fee['may_fee'],
            //             'student_id' => $this->_studentId,
            //             'class_id' => $fee['class_id'],
            //             'class_fee_master_id' => $fee['id'],
            //             'fee_head_id' => $fee['fee_head_id'],
            //             'discount_percent' => $fee['discount'],
            //             'demand_date' => Carbon::now(),
            //             'school_id' => '1',
            //             'created_by' => '1',
            //             'ip_address' => getClientIpAddress()
            //         ];
            //         $this->_mFeeDemands->store($reqs);
            //     }
            // }

            // if ($month == 'jun_fee') {
            //     foreach ($this->_classFee as $fee) {
            //         $reqs = [
            //             'fy_name' => $fee['academic_year'],
            //             'month_name' => 'June',
            //             'amount' => $fee['jun_fee'],
            //             'student_id' => $this->_studentId,
            //             'class_id' => $fee['class_id'],
            //             'class_fee_master_id' => $fee['id'],
            //             'fee_head_id' => $fee['fee_head_id'],
            //             'discount_percent' => $fee['discount'],
            //             'demand_date' => Carbon::now(),
            //             'school_id' => '1',
            //             'created_by' => '1',
            //             'ip_address' => getClientIpAddress()
            //         ];
            //         $this->_mFeeDemands->store($reqs);
            //     }
            // }

            // if ($month == 'jul_fee') {
            //     foreach ($this->_classFee as $fee) {
            //         $reqs = [
            //             'fy_name' => $fee['academic_year'],
            //             'month_name' => 'July',
            //             'amount' => $fee['jul_fee'],
            //             'student_id' => $this->_studentId,
            //             'class_id' => $fee['class_id'],
            //             'class_fee_master_id' => $fee['id'],
            //             'fee_head_id' => $fee['fee_head_id'],
            //             'discount_percent' => $fee['discount'],
            //             'demand_date' => Carbon::now(),
            //             'school_id' => '1',
            //             'created_by' => '1',
            //             'ip_address' => getClientIpAddress()
            //         ];
            //         $this->_mFeeDemands->store($reqs);
            //     }
            // }

            // if ($month == 'aug_fee') {
            //     foreach ($this->_classFee as $fee) {
            //         $reqs = [
            //             'fy_name' => $fee['academic_year'],
            //             'month_name' => 'August',
            //             'amount' => $fee['aug_fee'],
            //             'student_id' => $this->_studentId,
            //             'class_id' => $fee['class_id'],
            //             'class_fee_master_id' => $fee['id'],
            //             'fee_head_id' => $fee['fee_head_id'],
            //             'discount_percent' => $fee['discount'],
            //             'demand_date' => Carbon::now(),
            //             'school_id' => '1',
            //             'created_by' => '1',
            //             'ip_address' => getClientIpAddress()
            //         ];
            //         $this->_mFeeDemands->store($reqs);
            //     }
            // }

            // if ($month == 'sep_fee') {
            //     foreach ($this->_classFee as $fee) {
            //         $reqs = [
            //             'fy_name' => $fee['academic_year'],
            //             'month_name' => 'September',
            //             'amount' => $fee['sep_fee'],
            //             'student_id' => $this->_studentId,
            //             'class_id' => $fee['class_id'],
            //             'class_fee_master_id' => $fee['id'],
            //             'fee_head_id' => $fee['fee_head_id'],
            //             'discount_percent' => $fee['discount'],
            //             'demand_date' => Carbon::now(),
            //             'school_id' => '1',
            //             'created_by' => '1',
            //             'ip_address' => getClientIpAddress()
            //         ];
            //         $this->_mFeeDemands->store($reqs);
            //     }
            // }

            // if ($month == 'oct_fee') {
            //     foreach ($this->_classFee as $fee) {
            //         $reqs = [
            //             'fy_name' => $fee['academic_year'],
            //             'month_name' => 'October',
            //             'amount' => $fee['oct_fee'],
            //             'student_id' => $this->_studentId,
            //             'class_id' => $fee['class_id'],
            //             'class_fee_master_id' => $fee['id'],
            //             'fee_head_id' => $fee['fee_head_id'],
            //             'discount_percent' => $fee['discount'],
            //             'demand_date' => Carbon::now(),
            //             'school_id' => '1',
            //             'created_by' => '1',
            //             'ip_address' => getClientIpAddress()
            //         ];
            //         $this->_mFeeDemands->store($reqs);
            //     }
            // }

            // if ($month == 'nov_fee') {
            //     foreach ($this->_classFee as $fee) {
            //         $reqs = [
            //             'fy_name' => $fee['academic_year'],
            //             'month_name' => 'November',
            //             'amount' => $fee['nov_fee'],
            //             'student_id' => $this->_studentId,
            //             'class_id' => $fee['class_id'],
            //             'class_fee_master_id' => $fee['id'],
            //             'fee_head_id' => $fee['fee_head_id'],
            //             'discount_percent' => $fee['discount'],
            //             'demand_date' => Carbon::now(),
            //             'school_id' => '1',
            //             'created_by' => '1',
            //             'ip_address' => getClientIpAddress()
            //         ];
            //         $this->_mFeeDemands->store($reqs);
            //     }
            // }

            // if ($month == 'dec_fee') {
            //     foreach ($this->_classFee as $fee) {
            //         $reqs = [
            //             'fy_name' => $fee['academic_year'],
            //             'month_name' => 'December',
            //             'amount' => $fee['dec_fee'],
            //             'student_id' => $this->_studentId,
            //             'class_id' => $fee['class_id'],
            //             'class_fee_master_id' => $fee['id'],
            //             'fee_head_id' => $fee['fee_head_id'],
            //             'discount_percent' => $fee['discount'],
            //             'demand_date' => Carbon::now(),
            //             'school_id' => '1',
            //             'created_by' => '1',
            //             'ip_address' => getClientIpAddress()
            //         ];
            //         $this->_mFeeDemands->store($reqs);
            //     }
            // }
        }
    }
    //end generate fynancial year wise data 
    */
    //get all data
    public function retrieve()
    {
        $schoolId = authUser()->school_id;
        $createdBy = authUser()->id;
        return DB::table('fee_demands as d')
            ->select(
                'd.*',
                'dg.*',
                'f.*',
                DB::raw("
                CONCAT(s.first_name,' ',s.middle_name,' ',s.last_name) as student_name,
                CASE WHEN d.status = '0' THEN 'Deactivated'  
                    WHEN d.status = '1' THEN 'Active'
                END as status,
                TO_CHAR(d.created_at::date,'dd-mm-yyyy') as date,
                TO_CHAR(d.created_at,'HH12:MI:SS AM') as time
                "),
                // 'dg.discount_group',
                's.admission_no'
            )
            ->join('students as s', 's.id', 'd.student_id')
            ->join('fee_heads as dg', 'dg.id', 'd.fee_head_id')
            ->join('class_fee_masters as f', 'f.id', '=', 'd.class_fee_master_id')
            ->where('d.school_id', $schoolId)
            ->where('d.created_by', $createdBy)
            // ->orderBy('s.admission_no')
            ->orderByDesc('d.id')
            ->get();
    }
}
