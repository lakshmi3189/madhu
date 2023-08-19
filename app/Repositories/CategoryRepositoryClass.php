<?php

namespace App\Repositories;
use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface; //using interface 

class CategoryRepositoryClass implements CategoryRepositoryInterface{
    public function all(){
        // return Category::latest()->paginate(4);
        return Category::all();        
    }    

    public function store($data){
        Category::create($data);
    }
    
}