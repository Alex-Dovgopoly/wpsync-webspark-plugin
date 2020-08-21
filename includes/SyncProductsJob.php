<?php


class SyncProductsJob
{
    const GET_PRODUCTS_API_LINK = 'https://my.api.mockaroo.com/products.json?key=89b23a40';

    public function sync_products()
    {
        $product_data = json_decode($this->getProductsJson());
        if (!$product_data)
            return;
        //$this->saveProduct($product_data[0]);
        foreach ($product_data as $product) {
            $this->saveProduct($product);
        }

    }

    private function getProductsJson()
    {
        try {
            //return file_get_contents(self::GET_PRODUCTS_API_LINK);
            return file_get_contents('E:\Work\project\temp.json');
        } catch (Throwable $ex) {
            echo 'Could not get json: '.$ex->getMessage();
            return null;
        }
    }

    private function unpublishProductBySKU($sku)
    {
        $product_id = wc_get_product_id_by_sku($sku);
        $product_post = array(
            'ID' => $product_id,
            'post_type' => 'product',
            'post_status' => 'draft'
        );
        wp_insert_post($product_post);
    }

    private function getExistingSKUs() {
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1
        );

        $wcProductsArray = get_posts($args);

        $existingSKUs = array();
        if (count($wcProductsArray)) {
            foreach ($wcProductsArray as $productPost) {
                $currentSKU = get_post_meta($productPost->ID, '_sku', true);
                array_push($existingSKUs, $currentSKU);
            }
        }
        return $existingSKUs;
    }

    private function saveIncomingProducts($productData)
    {
        $savedSKUs = array();
        foreach ($productData as $product) {
            $this->saveProduct($product);
            array_push($savedSKUs, $product->sku);
        }
        return $savedSKUs;
    }

    private function saveProduct($productToSave)
    {
        $sku = $productToSave->sku;
        $product_post = array(
            'ID' => wc_get_product_id_by_sku($sku),
            'post_title' => $productToSave->name,
            'post_content' => $productToSave->description,
            'post_type' => 'product',
            'post_status' => 'publish'
        );
        $productId = wp_insert_post($product_post);

        if ($productId) {
            update_post_meta($productId, '_price', $productToSave->price);
            update_post_meta($productId, '_sku', $productToSave->sku);
            update_post_meta($productId, '_stock', $productToSave->in_stock);
            update_post_meta($productId, "_manage_stock", "yes");
            update_post_meta($productId, '_product_image_gallery', $productToSave->picture);
        }
    }
}