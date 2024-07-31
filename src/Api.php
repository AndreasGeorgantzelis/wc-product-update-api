<?php

namespace Service;

use Automattic\WooCommerce\Client;

class Api
{
    private $woocommerce;
    public function __construct() {

        $this->woocommerce = new Client(
            'https://hedrix.cz',
            'ck_b8e4527938494c881efbb28c813c2985e3f6e49a',
            'cs_c94ecbbeb9d4045a864241b5f171785db32d4e61',
            [
                'version' => 'wc/v3',
            ]
        );
    }
    public function getApi($url) {
        try {
            $results = $this->woocommerce->get($url);
            return $results;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function postApi($url, $data) {
//        print_r( $data );
        try {
            $results = $this->woocommerce->post($url, $data);
            return $results;
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
            return ['error' => $e->getMessage()];
        }
    }
}