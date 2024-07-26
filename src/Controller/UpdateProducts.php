<?php

namespace Service\Controller;

class UpdateProducts
{
    public function __invoke() {

        $allUpdatedProducts = [
            'update' => []
        ];
        $allProducts = $this->getProducts();
        foreach ($allProducts as $product) {

            $productId = $product->id;
            if (!is_object($product)) {
                return 'didnt fetch products';
            }
            $variations = $this->getProductVariations($productId);
            $parentSKU = $this->getParentProductSku($product);
            $productType = $this->getProductType($parentSKU);
            $manuColor = $this->getColor($product);
            $this->handleVariations($productId,$variations,$parentSKU,$productType,$manuColor);
            $updatedProduct = [
                'id' => $product->id,
                'meta_data' => [[
                    'key' => 'merge_product_manufacturing_color',
                    'value' => $manuColor
                ],
                    [
                        'key' => 'merge_product_exclude_from_feed',
                        'value' => 'yes'
                    ]]
            ];
            $allUpdatedProducts['update'][] = $updatedProduct;
        }
        $this->postApi('products/batch' ,$allUpdatedProducts);
        return $allUpdatedProducts;
    }
}