<?php


class ProductImportWpsync
{

    public function __construct()
    {
        if( defined('DOING_CRON') && DOING_CRON ) {
            add_action('my_hourly_event', array(new SyncProductsJob(), 'sync_products'));
        }
    }

}
