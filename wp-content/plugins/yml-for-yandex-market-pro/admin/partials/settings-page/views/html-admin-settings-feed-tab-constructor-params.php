<?php
/**
 * The Constructor params page tab.
 * 
 * @version    6.0.0 (20-03-2025)
 * @package    Y4YMP
 * @subpackage Y4YMP/admin/partials/settings_page/
 * 
 * @param $view_arr['tab_name']
 */
defined( 'ABSPATH' ) || exit;

$settings_constructor_params = new Y4YMP_Constructor_Params_WP_List_Table( $view_arr['feed_id'] );
$settings_constructor_params->prepare_items();
$settings_constructor_params->display();