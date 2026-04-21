<?

// Переопределяем вывод вариаций

function wc_dropdown_variation_attribute_options( $args = array() )
{
	$args = wp_parse_args( apply_filters( 'woocommerce_dropdown_variation_attribute_options_args', $args ), array(
		'options'          => false,
		'attribute'        => false,
		'product'          => false,
		'selected' 	       => false,
		'name'             => '',
		'id'               => '',
		'class'            => '',
		'show_option_none' => __( 'Choose an option', 'woocommerce' ),
	) );

	$options               = $args['options'];
	$product               = $args['product'];
	$attribute             = $args['attribute'];
	$name                  = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
	$id                    = $args['id'] ? $args['id'] : sanitize_title( $attribute );
	$class                 = $args['class'];
	$show_option_none      = $args['show_option_none'] ? true : false;
	$show_option_none_text = $args['show_option_none'] ? $args['show_option_none'] : __( 'Choose an option', 'woocommerce' );

	if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) 
	{
		$attributes = $product->get_variation_attributes();
		$options    = $attributes[ $attribute ];
	}

	$html = '<div id="attribute_' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . ' xs_attributes">';

	if ( ! empty( $options ) ) 
	{
		if ( $product && taxonomy_exists( $attribute ) ) 
		{
			$terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );

			foreach ( $terms as $term ) 
			{
				if ( in_array( $term->slug, $options ) ) 
				{
					$html .= '1<input id="attribute_'.esc_attr( $name )."_".esc_attr( $term->slug ).'" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '" data-show_option_none="' . ( $show_option_none ? 'yes' : 'no' ) . '" type="radio" value="' . esc_attr( $term->slug ) . '" ' . checked( sanitize_title( $args['selected'] ), $term->slug, false ) . '/><label for="attribute_'.esc_attr( $name )."_".esc_attr( $term->slug ).'">' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) )."</label>";
				}
			}
		}
		else 
		{
			foreach ( $options as $option ) 
			{
				$selected = sanitize_title( $args['selected'] ) === $args['selected'] ? checked( $args['selected'], sanitize_title( $option ), false ) : selected( $args['selected'], $option, false );
				$html .= '<input id="attribute_'.esc_attr( $name )."_".esc_attr( $option ).'" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '" data-show_option_none="' . ( $show_option_none ? 'yes' : 'no' ) . '" type="radio" value="' . esc_attr( $option ) . '" ' . $selected . '/><label for="attribute_'.esc_attr( $name )."_".esc_attr( $option ).'">' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ). "</label>";
			}
		}
	}

	$html .= '</div>';

	echo apply_filters( 'woocommerce_dropdown_variation_attribute_options_html', $html, $args );
}
