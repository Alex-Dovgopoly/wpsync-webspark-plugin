<?php
/*
Plugin Name: WPsync WebSPARK
Version: 1.0
Author: Alex Dovgopoly
Description: Тестовое задание на должность разработчика WordPress
*/





require_once plugin_dir_path(__FILE__) . 'includes/SyncProductsJob.php';
require_once plugin_dir_path(__FILE__) . 'includes/ProductImportWpsync.php';
require_once( wp_normalize_path(ABSPATH).'wp-load.php');

new ProductImportWpsync();

register_activation_hook(__FILE__, 'activationProductImport');
function activationProductImport() {

    wp_clear_scheduled_hook( 'my_hourly_event' );
    // Проверим нет ли уже задачи с таким же хуком

    // добавим новую cron задачу
    wp_schedule_event( time(), 'hourly', 'my_hourly_event');
}

register_deactivation_hook( __FILE__, 'deactivationProductImport');
function deactivationProductImport() {
    wp_clear_scheduled_hook('my_hourly_event');
}
