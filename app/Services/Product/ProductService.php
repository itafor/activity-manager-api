<?php
namespace App\Services\Product;

use App\Models\Favorite;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\RelatedProduct;
use App\Traits\Common;
use App\Traits\FileUpload;

/**
 *
 */
class ProductService
{
    use Common, FileUpload;

    public function createProduct($request)
    {
            $product = new Product();
            $product->name = $request->name;
            $product->category_id = $request->category_id;
            $product->individual_price = $request->individual_price;
            $product->group_price = $request->group_price;
            $product->product_size = $request->product_size;
            $product->quantity_instock = $request->quantity_instock;
            $product->sku = $this->randomalAphanumericString(8);
            $product->featured_image = isset($request->image) ?  $this->uploadFileTocloudinary($request->image):null;
            $product->description = isset($request->description) ? $request->description : null;
            $product->type = "product";
            $product->save();


             if(isset($request->related_productIds) && $product){
            	$this->addRelatedProduct($request->related_productIds, $product);
            }

             if(isset($request->more_product_images) && $product){
            	$this->addMoreProductImages($request->more_product_images, $product);
            }

            return $product;
    }


    public function addProductInventory($request, $product)
    {
            $inventory = new Inventory();
            $inventory->product_id = $product->id;
            $inventory->quantity = $request->quantity_instock;
            $inventory->save();
            return $inventory;
    }

    public function addRelatedProduct($request)
    {
    	if(isset($request->related_productIds)){
    		foreach ($request->related_productIds as $relatedproduct) {
    		$related_product = new RelatedProduct();
            $related_product->product_id = $request->product_id;
            $related_product->related_product_id = $relatedproduct['related_product_id'];
            $related_product->save();
    	}
    }
            
    }

       public function addMoreProductImages($more_product_images, $product)
    {
    	if(isset($more_product_images)){
    		foreach ($more_product_images as $image) {
    		$pimage = new ProductImage();
            $pimage->product_id = $product->id;
            $pimage->image_url =  $this->uploadFileTocloudinary($image);
            $pimage->save();
    	}
    }
            
    }

    public function updateProduct($request)
    {
        
            $product = Product::where('id', $request->productId)->where('type', 'product')->first();
            if($product){
            
            $product->name = $request->name;
            $product->category_id = $request->category_id;
            $product->individual_price = $request->individual_price;
            $product->group_price = $request->group_price;
            $product->product_size = $request->product_size;
            $product->quantity_instock = $request->quantity_instock;
            $product->featured_image = isset($request->image) ? $this->uploadFileTocloudinary($request->image) :  $product->featured_image;
            $product->description = isset($request->description) ? $request->description : $product->description;
            $product->save();

             if(isset($request->related_productIds) && $product){
                $this->addRelatedProduct($request->related_productIds, $product);
            }

             if(isset($request->more_product_images) && $product){
                $this->addMoreProductImages($request->more_product_images, $product);
            }

            return $product;
        }

    }

    public function listProducts()
    {
        
           return Product::where('type', 'product')->with(['category', 'images', 'relatedProducts'])->orderBy('id', 'desc')->get();

            
    }

    public function listProductsByCategory($categoryId)
    {
        
           return Product::where('category_id', $categoryId)->where('type', 'product')->with(['category', 'images', 'relatedProducts'])->orderBy('id', 'desc')->get();

            
    }

    public function showProduct($productId)
    {
            return Product::where('id', $productId)->where('type', 'product')->with(['category', 'images', 'relatedProducts'])->first();
    }

     public function addProductTofavourites($productId)
    {
            $product = Product::where('id', $productId)->first();
            if($product){
                $favorite = new Favorite();
                $favorite->user_id = auth()->user()->id;
                $favorite->product_id= $productId;
                $favorite->save();
                return $product;
            }
            return $product;
    }

   public function myFavouritesProducts()
    {
        
           return Favorite::where('user_id', auth()->user()->id)->with(['product'])->orderBy('id', 'desc')->get();
            
    }

     public function searchProducts($request)
    {
        $courses = Product::join('categories', 'categories.id', '=', 'products.category_id')
                   ->where('products.name', 'LIKE', '%' . $request->search_item . '%')
                   ->orWhere('categories.name', 'LIKE', '%' . $request->search_item . '%')
                   ->select('products.*')->with(['category', 'images', 'relatedProducts'])->get();

        return $courses;
    }
   
}
