<?php

namespace Service;
use Service\Api;

class UpdateCategories
{


 public function __construct(private \Service\Api $api) {
 }

 public function updateProductCategories() {
     $allUpdatedProducts = [
         'update' => []
     ];
     $allProducts = $this->getProducts();
     foreach ($allProducts as $product) {
         $newCategory = new \stdClass();
         $newCategory->id = 1704;
         $product->categories[] = $newCategory;

         $updatedProduct = [
             'id' => $product->id,
             'categories' =>
                 $product->categories
             ];
         $allUpdatedProducts['update'][] = $updatedProduct;
     }
     $this->api->postApi('products/batch' ,$allUpdatedProducts);
     return $allUpdatedProducts;
 }
 public function getProducts() {
        return $this->api->getApi('products/?&after=2024-07-07T00:00:00&page=' . 2 . '&per_page=50&tag=1701');
 }
// public function getCategories() {
//       return $this->api->getApi('products/categories/1702');
// }

}

//Lucazz category id : 1702
//PhilBill category id : 1704
//Trnka category id : 1705
//Pajic category id : 1703