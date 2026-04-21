<?
if(!defined('ABSPATH')) exit;

$page_title = ( $load_address === 'billing' ) ? __( 'Адрес доставки', 'woocommerce' ) : __( 'Платёжный адрес', 'woocommerce' );

do_action( 'woocommerce_before_edit_account_address_form' );

if(!$load_address)
	wc_get_template('myaccount/my-address.php');
else
{
	?><form method="post"><? 
		
		do_action( "woocommerce_before_edit_address_form_{$load_address}" ); 
		
		?><div class="input_container col2 xs_flex xs_wrap"><?
		
		foreach ( $address as $key => $field )
		{
			woocommerce_form_field( $key, $field, ! empty( $_POST[ $key ] ) ? wc_clean( $_POST[ $key ] ) : $field['value'] );
		}

		do_action( "woocommerce_after_edit_address_form_{$load_address}" ); 
		
		?></div><?
		?><p><?
			?><br/><?
			?><input type="submit" class="btn button" name="save_address" value="<? esc_attr_e( 'Сохранить адрес', 'woocommerce' ); ?>" /><? 
			
			wp_nonce_field( 'woocommerce-edit_address' ); 
			
			?><input type="hidden" name="action" value="edit_address" /><?
		?></p><?
	?></form><?
}

do_action( 'woocommerce_after_edit_account_address_form' );
