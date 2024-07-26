<?php

namespace Service\Service\VariantProduct;

class HandleVariation
{
    public function handle($productId,$allVariations,$parentSKU,$productType,$manuColor) {
        $updatedVariation = [];
        $allUpdatedVariations = [
            'update' => []
        ];
        foreach ($allVariations as $variation) {

            $size = $this->getSize($variation);
            $variantSKU = $this->createVariantSKU($size,$parentSKU);
            $recipeID = $this->createVariationRecipeID($productType,$size,$manuColor);
            $updatedVariation = [
                'id' => $variation->id,
                'sku' => $variantSKU,
                'meta_data' => [
                    [
                        'key' => 'custom_odoo_integration_recipe_id',
                        'value' => $recipeID
                    ]
                ]
            ];
            $allUpdatedVariations['update'][] = $updatedVariation;
//            $this->putApi('products/' . $productId . '/variations/' . $variation->id . '/batch' ,$updatedVariation);
        }
        $this->postApi('products/' . $productId . '/variations/batch' ,$allUpdatedVariations);

        return $updatedVariation;
    }

    public function createVariationRecipeID($productType,$size,$manuColor):string {
        $manuColor = str_replace(" ", "-", $manuColor);
        return strtolower($productType . '_' .$size. '_' . $manuColor);
    }
    public function getProductVariations($productId) : array {
        return $this->getApi('products/'. $productId . '/variations');
    }

}