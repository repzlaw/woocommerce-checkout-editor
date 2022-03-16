<?php
/**
 * Plugin Name:     WooCommerce Checkout Field
 * Plugin URI:      www.github/replaw
 * Description:     adding custom fields to WooCommerce
 * Author:          David
 * Author URI:      www.github/replaw
 * Text Domain:     woocommerce-checkout-field
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Dave_Check_Field
 */

// Your code starts here.

/**
 * style email and phone fields.
 * 
 * @param array $f checkout fields.
 * @return array
 */
add_filter( 'woocommerce_checkout_fields' , 'dave_checkout_fields_styling', 9999 );

function dave_checkout_fields_styling( $f ) {

	$f['billing']['billing_email']['class'][0] = 'form-row-last';
	$f['billing']['billing_phone']['class'][0] = 'form-row-first';
	
	return $f;

}

/**
 * change first name label and order comments placeholder.
 * 
 * @param array $f checkout fields.
 * @return array
 */
add_filter( 'woocommerce_checkout_fields' , 'dave_labels_placeholders', 9999 );

function dave_labels_placeholders( $f ) {

	// first name can be changed with woocommerce_default_address_fields as well
	$f['billing']['billing_first_name']['label'] = 'First Name';
	$f['order']['order_comments']['placeholder'] = 'Any notes you\'d like to add?';
	
	return $f;

}

/**
 * added extra fields.
 * 
 * @param array $checkout checkout fields.
 * @return array
 */
// add fields
add_action( 'woocommerce_after_checkout_billing_form', 'dave_select_field' );
add_action( 'woocommerce_after_order_notes', 'dave_subscribe_checkbox' );

// save fields to order meta
add_action( 'woocommerce_checkout_update_order_meta', 'dave_save_what_we_added' );

// select
function dave_select_field( $checkout ){
	
	// you can also add some custom HTML here
	
	woocommerce_form_field( 'contactmethod', array(
		'type'          => 'select', // text, textarea, select, radio, checkbox, password, about custom validation a little later
		'required'	=> true, // actually this parameter just adds "*" to the field
		'class'         => array('dave-field', 'form-row-wide'), // array only, read more about classes and styling in the previous step
		'label'         => 'Preferred contact method',
		'label_class'   => 'dave-label', // sometimes you need to customize labels, both string and arrays are supported
		'options'	=> array( // options for  or 
			''		=> 'Please select', // empty values means that field is not selected
			'By phone'	=> 'By phone', // 'value'=>'Name'
			'By email'	=> 'By email'
			)
		), $checkout->get_value( 'contactmethod' ) );

	// you can also add some custom HTML here
	
}

// checkbox
function dave_subscribe_checkbox( $checkout ) {

	woocommerce_form_field( 'subscribed', array(
		'type'	=> 'checkbox',
		'class'	=> array('dave-field form-row-wide'),
		'label'	=> ' Subscribe to our newsletter.',
		), $checkout->get_value( 'subscribed' ) );
        
}

/**
 * save fields added to post meta.
 * 
 * @param array $order_id order_id.
 * @return array
 */
// save field values
function dave_save_what_we_added( $order_id ){

	if( !empty( $_POST['contactmethod'] ) )
		update_post_meta( $order_id, 'contactmethod', sanitize_text_field( $_POST['contactmethod'] ) );
	
	
	if( !empty( $_POST['subscribed'] ) && $_POST['subscribed'] == 1 )
		update_post_meta( $order_id, 'subscribed', 1 );
    
}

/**
 * add validation message for contact method on checkout.
 */
add_action('woocommerce_checkout_process', 'dave_check_if_selected');

function dave_check_if_selected() {

	// you can add any custom validations here
	if ( empty( $_POST['contactmethod'] ) )
		wc_add_notice( 'Please select your preferred contact method.', 'error' );
    
}