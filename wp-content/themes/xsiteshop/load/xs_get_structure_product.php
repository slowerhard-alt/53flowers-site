<?
include $_SERVER['DOCUMENT_ROOT'].'/wp-load.php';

$_id = (int)xs_format($_POST['variation_id']);

echo get_structure_for_product($_id);
