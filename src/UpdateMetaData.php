<?php

namespace Service;

use Automattic\WooCommerce\Client;

class UpdateMetaData {
    public function __construct(private \Service\Api $api) {


    }
    public function updateProducts() {

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
         $this->api->postApi('products/batch' ,$allUpdatedProducts);
        return $allUpdatedProducts;
    }
    public function getProductVariations($productId) : array {
       return $this->api->getApi('products/'. $productId . '/variations');
    }

    public function handleVariations($productId,$allVariations,$parentSKU,$productType,$manuColor) {
        $updatedVariation = [];
        $allUpdatedVariations = [
            'update' => []
        ];
        foreach ($allVariations as $variation) {

            $size = $this->getSize($variation);
            $variantSKU = $this->createVariantSKU($size,$parentSKU);
            $recipeID = $this->createRecipeID($productType,$size,$manuColor);
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
        $this->api->postApi('products/' . $productId . '/variations/batch' ,$allUpdatedVariations);

        return $updatedVariation;
    }

    public function createVariantSKU($size,$parentSKU):string {
        return $parentSKU . '-' .$size;
    }
    public function createRecipeID($productType,$size,$manuColor):string {
        $manuColor = str_replace(" ", "-", $manuColor);
        return strtolower($productType . '_' .$size. '_' . $manuColor);
    }


    public function getSize($variation) {

        $attributes = $variation->attributes;
        foreach ($attributes as $attribute) {
            if ($attribute->attribute_slug === 'size-clothing') {
                return $attribute->option;
            }
        } return 'error';
    }

    public function getParentProductSku($product) {
        if ($product->sku ?? false) {
            return $product->sku;
        } else {
            error_log("Invalid product data or missing SKU");
        }
    }

    public function getProductType($parentSKU) : string {
        if (!is_string($parentSKU) || trim($parentSKU) === '') {
            error_log("Invalid SKU");
            return 'invalid SKU';
        }

        $parentSKU = trim($parentSKU);

        if (str_contains($parentSKU, 'RTSHR')) {
            return 't-shirt';
        } elseif (str_contains($parentSKU, 'SSHRT')) {
            return 'sweatshorts';
        } elseif (str_contains($parentSKU, 'OTSHR')) {
            return 'oversized-t-shirt';
        } else {
            error_log("Unrecognized SKU pattern: $parentSKU");
            return 'something went wrong';
        }
    }


    public function getColor($product): string {

        $colorAttribute = $this->getColorAttribute($product);

        if (is_object($colorAttribute) && property_exists($colorAttribute, 'options_slugs') && is_array($colorAttribute->options_slugs) && count($colorAttribute->options_slugs) > 0) {
            $colorSlug = $colorAttribute->options_slugs[0];
            switch ($colorSlug) {
                case 'color-black':
                    return 'BLACK';
                case 'color-white':
                    return 'WHITE';
                case 'color-blue':
                    return 'BLUE';
                case 'color-pink':
                    return 'PINK';
                case 'color-ecru':
                case 'color-beige':
                    return 'ECRU';
                case 'color-green':
                    return 'GREEN';
                case 'color-gray':
                    return 'GREY';
                case 'color-burgundy':
                    return 'BURGUNDY';
                case 'color-khaki':
                    return 'KHAKI ARMY';
                case 'color-iron-gray':
                    return 'IRON GREY';
                case 'color-purple':
                    return 'PURPLE';
                case 'color-mint-green':
                    return 'LIGHT GREEN';
                case 'color-light-blue':
                    return 'SKY BLUE';
                case 'color-veraman':
                    return 'VERAMAN';
                case 'color-orange':
                    return 'ORANGE';
                case 'color-blue-electric':
                    return 'BLUE ELECTRIC';
                default:
                    error_log("Unmapped color slug: $colorSlug");
                    return 'color not mapped yet';
            }
        } else {
            error_log("Invalid color attribute for product: " . json_encode($product));
            return 'color not mapped yet';
        }
    }


    public function getColorAttribute($product) {

        $attributes = $product->attributes;
        foreach ($attributes as $attribute) {
            if ($attribute->taxonomy_slug === 'pa_color') {
                return $attribute;
            }
        }
        return 'something went wrong';
    }

    public function getProductIds() : array {
        $productIds = [];
        for ($page = 1; $page <=11; $page++) {
            $allProducts = $this->getProducts($page);
            if (is_array($allProducts)) {
                foreach ($allProducts as $product) {
                    if (is_object($product) && property_exists($product, 'id')) {
                        $productIds[] = $product->id;
                    } else {
                        error_log("Invalid product data encountered on page $page.");
                    }
                }
            } else {
                error_log("Invalid response for products on page $page.");
            }
        }
        return $productIds;
    }

    public function getProducts() {
        return $this->api->getApi('products/?&after=2024-07-07T00:00:00&page=' . 10 . '&per_page=20&tag=1700');
    }



}
