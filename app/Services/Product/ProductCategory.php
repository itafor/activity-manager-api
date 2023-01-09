<?php
namespace App\Services\Product;

use App\Models\Category;
use App\Traits\Common;
use App\Traits\FileUpload;

/**
 *
 */
class ProductCategory
{
    use Common, FileUpload;

    public function createCategory($request)
    {
            $category = new Category();
            $category->name =$request->name;
            $category->image = isset($request->image) ? $this->uploadFileTocloudinary($request->image) : null;
            $category->description = isset($request->description) ? $request->description : null;
            $category->save();

            return $category;
    }

    public function updateCategory($request)
    {
        
            $category = Category::where('id', $request->category_id)->first();
            $category->name = $request->name;
            $category->image = isset($request->image) ? $this->uploadFileTocloudinary($request->image) : $category->image;
            $category->description = isset($request->description) ? $request->description : $category->description;
            $category->save();

            return $category;

    }

    public function listCategories()
    {
        
           return Category::orderBy('id', 'desc')->get();

            
    }

    public function showCategory($categoryId)
    {
            return Category::where('id', $categoryId)->first();
    }

  
}
