<?php
namespace App\Services\Product;

use App\Models\Product;
use App\Models\VarietyBox;
use App\Models\VarietyBoxProduct;
use App\Traits\Common;
use App\Traits\FileUpload;

/**
 *
 */
class VarietyBoxService
{
    use Common, FileUpload;

    public function createVarietyBox($request)
    {
            $varietyBox = new Product();
            $varietyBox->name = $request->name;
            $varietyBox->category_id = $request->category_id;
            $varietyBox->individual_price = $request->individual_price;
            $varietyBox->group_price = $request->group_price;
            $varietyBox->quantity_instock = $request->quantity_instock;
            $varietyBox->sku = $this->randomalAphanumericString(8);
            $varietyBox->featured_image = isset($request->image) ? $this->uploadFileTocloudinary($request->image) : null;
            $varietyBox->description = isset($request->description) ? $request->description : null;
            $varietyBox->type = "variety_box";
            $varietyBox->variety_box_size = $request->variety_box_size;
            $varietyBox->save();

            // if(isset($request->products) && $varietyBox){
            //     $this->addProductToVarietyBox($request->products, $varietyBox);
            // }

            return $varietyBox;
    }



     public function addProductToVarietyBox($request)
    {
        if(isset($request->products)){
            foreach ($request->products as $product) {
            $varietyBox = new VarietyBoxProduct();
            $varietyBox->variety_box_id = $request->variety_box_id;
            $varietyBox->product_id = $product['product_id'];
            $varietyBox->quantity = $product['quantity'];
            $varietyBox->save();
        }
    }
    }
            

    public function updateVarietyBox($request)
    {
        
            $varietyBox = Product::where('id', $request->varietyBoxId)->where('type', 'variety_box')->first();
            if($varietyBox){
            
            $varietyBox->name = $request->name;
            $varietyBox->category_id = $request->category_id;
            $varietyBox->individual_price = $request->individual_price;
            $varietyBox->group_price = $request->group_price;
            $varietyBox->product_size = $request->product_size;
            $varietyBox->quantity_instock = $request->quantity_instock;
            $varietyBox->featured_image = isset($request->image) ? $this->uploadFileTocloudinary($request->image) :  $varietyBox->featured_image;
            $varietyBox->description = isset($request->description) ? $request->description : $varietyBox->description;
            $varietyBox->variety_box_size = $request->variety_box_size;
            $varietyBox->save();

            if(isset($request->products) && $varietyBox){
                $this->addProductToVarietyBox($request->products, $varietyBox);
            }

            return $varietyBox;
        }

        return null;

    }

    public function listVarietyBoxes()
    {
        
           return Product::where('type', 'variety_box')->with(['category', 'varietyProducts'])->orderBy('id', 'desc')->get();

            
    }

    public function showVarietyBox($varietyBoxId)
    {

           return product::where('id', $varietyBoxId)->where('type', 'variety_box')->with(['category','varietyProducts'])->first();

    }

  
}
