<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;

class CategoryController extends Controller
{   
    //Via repository 
    private $categoryRepository;

    public function __Construct(CategoryRepositoryInterface $categoryRepository){
        $this->categoryRepository = $categoryRepository;
    }
    public function index(){
        $categories = $this->categoryRepository->all(); //this will go to interace file and will call class file of repository. it should be bind
        return response()->json([
            'data'=>$categories,
            'message'=>'query ok'
        ]);
    }

    public function store(Request $request){
        //Container-1: Validation, Variable declaration
        $data = $request->validate([
            'title'=>'required'
        ]);

        //Container-2: Calculation

        //Container-3: Transitions
        $this->categoryRepository->store($data);
        return response()->json([
            'status'=>'ok',
            'data'=>$data
        ]);
    }
}
