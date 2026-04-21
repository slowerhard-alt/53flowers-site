<?php
/**
 * Plugin Name: 53flowers Schema Markup
 * Description: Schema.org разметка для 53flowers.com — LocalBusiness, Product, CollectionPage, FAQ, BreadcrumbList
 * Version: 1.0.0
 * Author: territoriya-yd
 */

defined('ABSPATH') || exit;

// =============================================================================
// ОБЩИЕ ДАННЫЕ КОМПАНИИ
// =============================================================================

function flowers53_company_data(): array {
	return [
		'name'          => 'Цветы.ру',
		'url'           => 'https://53flowers.com',
		'id'            => 'https://53flowers.com/#organization',
		'telephone'     => '+7-800-301-04-16',
		'email'         => 'director@53flowers.com',
		'logo'          => 'https://53flowers.com/wp-content/uploads/2021/08/logo.jpg',
		'street'        => 'ул. Большая Санкт-Петербургская, 32',
		'locality'      => 'Великий Новгород',
		'region'        => 'Новгородская область',
		'postal'        => '173000',
		'country'       => 'RU',
		'lat'           => '58.5225',
		'lng'           => '31.2718',
		'opens'         => '08:00',
		'closes'        => '22:00',
		'price_range'   => '₽₽',
		'same_as'       => [
			'https://vk.com/53flowers',
		],
		'rating_value'  => '4.9',
		'rating_count'  => '780',
	];
}

// =============================================================================
// ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ
// =============================================================================

function flowers53_output_schema( array $data ): void {
	if ( ( $data['@type'] ?? '' ) === 'BreadcrumbList' && ! empty( $data['itemListElement'] ) ) {
		$filtered = [];
		$pos = 1;
		foreach ( $data['itemListElement'] as $item ) {
			if ( ! empty( $item['name'] ) ) {
				$item['position'] = $pos++;
				$filtered[] = $item;
			}
		}
		if ( count( $filtered ) < 2 ) return;
		$data['itemListElement'] = $filtered;
	}
	echo '<script type="application/ld+json">'
		. wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
		. '</script>' . "\n";
}

function flowers53_term_url( $term ): string {
	$url = get_term_link( $term );
	if ( is_wp_error( $url ) ) return '';
	return $url;
}

function flowers53_organization_node(): array {
	$c = flowers53_company_data();
	return [
		'@context' => 'https://schema.org',
		'@type'   => 'Organization',
		'@id'     => $c['id'],
		'name'    => $c['name'],
		'url'     => $c['url'],
		'logo'    => [ '@type' => 'ImageObject', 'url' => $c['logo'] ],
		'telephone' => $c['telephone'],
		'email'   => $c['email'],
		'address' => [
			'@type'           => 'PostalAddress',
			'streetAddress'   => $c['street'],
			'addressLocality' => $c['locality'],
			'addressRegion'   => $c['region'],
			'postalCode'      => $c['postal'],
			'addressCountry'  => $c['country'],
		],
		'sameAs'  => $c['same_as'],
	];
}

function flowers53_opening_hours(): array {
	$c = flowers53_company_data();
	return [ [
		'@type'      => 'OpeningHoursSpecification',
		'dayOfWeek'  => [ 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday' ],
		'opens'      => $c['opens'],
		'closes'     => $c['closes'],
	] ];
}

function flowers53_shipping_details(): array {
	return [
		'@type'               => 'OfferShippingDetails',
		'shippingRate'        => [ '@type' => 'MonetaryAmount', 'value' => '0', 'currency' => 'RUB' ],
		'shippingDestination' => [ '@type' => 'DefinedRegion', 'addressCountry' => 'RU', 'addressRegion' => 'Новгородская область' ],
		'deliveryTime'        => [
			'@type'        => 'ShippingDeliveryTime',
			'handlingTime' => [ '@type' => 'QuantitativeValue', 'minValue' => 0, 'maxValue' => 1, 'unitCode' => 'HUR' ],
			'transitTime'  => [ '@type' => 'QuantitativeValue', 'minValue' => 1, 'maxValue' => 2, 'unitCode' => 'HUR' ],
		],
	];
}

function flowers53_return_policy(): array {
	return [
		'@type'                => 'MerchantReturnPolicy',
		'applicableCountry'    => 'RU',
		'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
		'merchantReturnDays'   => 1,
		'returnMethod'         => 'https://schema.org/ReturnByMail',
		'returnFees'           => 'https://schema.org/FreeReturn',
	];
}

function flowers53_seller(): array {
	$c = flowers53_company_data();
	return [
		'@type'     => 'LocalBusiness',
		'name'      => $c['name'],
		'telephone' => $c['telephone'],
		'url'       => $c['url'],
		'address'   => [
			'@type'           => 'PostalAddress',
			'streetAddress'   => $c['street'],
			'addressLocality' => $c['locality'],
			'addressRegion'   => $c['region'],
			'postalCode'      => $c['postal'],
			'addressCountry'  => $c['country'],
		],
	];
}

// =============================================================================
// 1. ГЛАВНАЯ — Florist + Organization + WebSite
// =============================================================================

add_action( 'wp_head', function () {
	if ( ! is_front_page() ) return;
	$c = flowers53_company_data();

	// Florist (LocalBusiness)
	flowers53_output_schema( [
		'@context'                  => 'https://schema.org',
		'@type'                     => 'Florist',
		'@id'                       => $c['url'] . '#store',
		'name'                      => $c['name'] . ' — доставка цветов в Великом Новгороде',
		'description'               => 'Доставка цветов Великий Новгород — Всегда свежие цветы, высокий уровень сервиса. Гарантия качества! Доставим в течение часа.',
		'url'                       => $c['url'],
		'image'                     => $c['logo'],
		'telephone'                 => $c['telephone'],
		'email'                     => $c['email'],
		'address'                   => [
			'@type'           => 'PostalAddress',
			'streetAddress'   => $c['street'],
			'addressLocality' => $c['locality'],
			'addressRegion'   => $c['region'],
			'postalCode'      => $c['postal'],
			'addressCountry'  => $c['country'],
		],
		'geo'                       => [
			'@type'     => 'GeoCoordinates',
			'latitude'  => $c['lat'],
			'longitude' => $c['lng'],
		],
		'openingHoursSpecification' => flowers53_opening_hours(),
		'priceRange'                => $c['price_range'],
		'paymentAccepted'           => [ 'Cash', 'CreditCard', 'SBP' ],
		'currenciesAccepted'        => 'RUB',
		'areaServed'                => [ '@type' => 'City', 'name' => 'Великий Новгород' ],
		'aggregateRating'           => [
			'@type'       => 'AggregateRating',
			'ratingValue' => $c['rating_value'],
			'ratingCount' => $c['rating_count'],
			'bestRating'  => '5',
			'worstRating' => '1',
		],
		'sameAs'                    => $c['same_as'],
	] );

	// Organization
	flowers53_output_schema( flowers53_organization_node() );

	// WebSite + SearchAction
	flowers53_output_schema( [
		'@context'        => 'https://schema.org',
		'@type'           => 'WebSite',
		'@id'             => $c['url'] . '/#website',
		'name'            => $c['name'] . ' — доставка цветов в Великом Новгороде',
		'url'             => $c['url'],
		'potentialAction' => [
			'@type'       => 'SearchAction',
			'target'      => $c['url'] . '/?s={search_term_string}',
			'query-input' => 'required name=search_term_string',
		],
	] );
}, 5 );

// =============================================================================
// 2. СТРАНИЦА КАТАЛОГА (shop) — WebPage + FAQ + BreadcrumbList
// =============================================================================

add_action( 'wp_head', function () {
	if ( ! is_shop() ) return;
	$c = flowers53_company_data();

	flowers53_output_schema( [
		'@context'    => 'https://schema.org',
		'@type'       => 'WebPage',
		'@id'         => $c['url'] . '/catalog/#webpage',
		'url'         => $c['url'] . '/catalog/',
		'name'        => 'Каталог букетов | Доставка цветов Великий Новгород',
		'description' => 'Каталог свежих цветов и букетов с бесплатной доставкой в Великом Новгороде. Розы, тюльпаны, композиции в коробках и корзинках.',
		'publisher'   => [ '@id' => $c['id'] ],
		'inLanguage'  => 'ru-RU',
	] );

	flowers53_output_schema( [
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => [
			[ '@type' => 'ListItem', 'position' => 1, 'name' => 'Главная', 'item' => $c['url'] ],
			[ '@type' => 'ListItem', 'position' => 2, 'name' => 'Каталог' ],
		],
	] );

	flowers53_output_schema( [
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => [
			[
				'@type'          => 'Question',
				'name'           => 'Как быстро доставят цветы в Великом Новгороде?',
				'acceptedAnswer' => [ '@type' => 'Answer', 'text' => 'Доставляем в течение 1-2 часов. Работаем ежедневно с 8:00 до 22:00.' ],
			],
			[
				'@type'          => 'Question',
				'name'           => 'Сколько стоит доставка цветов?',
				'acceptedAnswer' => [ '@type' => 'Answer', 'text' => 'Доставка по Великому Новгороду бесплатная. Без скрытых платежей.' ],
			],
			[
				'@type'          => 'Question',
				'name'           => 'Можно ли заказать цветы на конкретное время?',
				'acceptedAnswer' => [ '@type' => 'Answer', 'text' => 'Да, выбирайте удобный 2-часовой интервал доставки при оформлении заказа.' ],
			],
			[
				'@type'          => 'Question',
				'name'           => 'Что если цветы не понравятся?',
				'acceptedAnswer' => [ '@type' => 'Answer', 'text' => 'Гарантируем качество и свежесть. Заменим букет или вернём деньги.' ],
			],
		],
	] );
}, 5 );

// =============================================================================
// 3. КАТЕГОРИИ — BreadcrumbList + CollectionPage + ItemList
// =============================================================================

add_action( 'wp_head', function () {
	if ( ! is_product_category() ) return;
	$c    = flowers53_company_data();
	$term = get_queried_object();
	if ( ! $term || is_wp_error( $term ) ) return;

	$bc_items = [
		[ '@type' => 'ListItem', 'position' => 1, 'name' => 'Главная', 'item' => $c['url'] . '/' ],
	];
	$ancestors = array_reverse( get_ancestors( $term->term_id, 'product_cat', 'taxonomy' ) );
	$pos = 2;
	foreach ( $ancestors as $ancestor_id ) {
		$ancestor = get_term( $ancestor_id, 'product_cat' );
		if ( $ancestor && ! is_wp_error( $ancestor ) ) {
			$bc_items[] = [
				'@type'    => 'ListItem',
				'position' => $pos++,
				'name'     => $ancestor->name,
				'item'     => flowers53_term_url( $ancestor ),
			];
		}
	}
	$bc_items[] = [ '@type' => 'ListItem', 'position' => $pos, 'name' => $term->name ];

	flowers53_output_schema( [
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $bc_items,
	] );
}, 4 );

add_action( 'wp_head', function () {
	if ( ! is_product_category() ) return;
	$c    = flowers53_company_data();
	$term = get_queried_object();
	if ( ! $term || is_wp_error( $term ) ) return;

	$products = wc_get_products( [
		'category' => [ $term->slug ],
		'limit'    => 20,
		'orderby'  => 'popularity',
		'order'    => 'DESC',
		'status'   => 'publish',
	] );

	if ( empty( $products ) ) return;

	$items = [];
	foreach ( $products as $i => $product ) {
		$image_id  = $product->get_image_id();
		$image_url = $image_id ? wp_get_attachment_url( $image_id ) : '';
		$price     = $product->get_price();

		$item = [
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'item'     => [
				'@type'       => 'Product',
				'name'        => $product->get_name(),
				'url'         => $product->get_permalink(),
				'description' => wp_strip_all_tags( $product->get_short_description() ?: $product->get_description() ),
				'brand'       => [ '@type' => 'Brand', 'name' => $c['name'] ],
				'offers'      => [
					'@type'         => 'Offer',
					'priceCurrency' => 'RUB',
					'price'         => $price ?: '0',
					'availability'  => $product->is_in_stock()
						? 'https://schema.org/InStock'
						: 'https://schema.org/OutOfStock',
					'url'           => $product->get_permalink(),
					'seller'        => [ '@type' => 'Organization', 'name' => $c['name'] ],
				],
			],
		];
		if ( $image_url ) $item['item']['image'] = $image_url;
		$items[] = $item;
	}

	$category_url = flowers53_term_url( $term );

	flowers53_output_schema( [
		'@context' => 'https://schema.org',
		'@graph'   => [
			[
				'@type'       => 'CollectionPage',
				'@id'         => $category_url . '#webpage',
				'url'         => $category_url,
				'name'        => $term->name,
				'description' => $term->description ?: $term->name . ' — купить с доставкой в Великом Новгороде',
				'inLanguage'  => 'ru-RU',
				'isPartOf'    => [ '@id' => $c['url'] . '/#website' ],
				'publisher'   => [ '@id' => $c['id'] ],
				'mainEntity'  => [ '@id' => $category_url . '#itemlist' ],
			],
			[
				'@type'           => 'ItemList',
				'@id'             => $category_url . '#itemlist',
				'name'            => $term->name,
				'numberOfItems'   => count( $items ),
				'itemListOrder'   => 'https://schema.org/ItemListOrderDescending',
				'itemListElement' => $items,
			],
		],
	] );
}, 5 );

// =============================================================================
// 4a. ТОВАРЫ — BreadcrumbList
// =============================================================================

add_action( 'wp_head', function () {
	if ( ! is_singular( 'product' ) ) return;
	global $post;
	$c = flowers53_company_data();

	$items = [
		[ '@type' => 'ListItem', 'position' => 1, 'name' => 'Главная', 'item' => $c['url'] . '/' ],
	];

	$primary_term_id = get_post_meta( $post->ID, '_yoast_wpseo_primary_product_cat', true );
	$term = $primary_term_id ? get_term( (int) $primary_term_id, 'product_cat' ) : null;
	if ( ! $term ) {
		$terms = get_the_terms( $post->ID, 'product_cat' );
		$term  = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0] : null;
	}

	if ( $term ) {
		$ancestors = array_reverse( get_ancestors( $term->term_id, 'product_cat', 'taxonomy' ) );
		$pos = 2;
		foreach ( $ancestors as $ancestor_id ) {
			$ancestor = get_term( $ancestor_id, 'product_cat' );
			if ( $ancestor && ! is_wp_error( $ancestor ) ) {
				$items[] = [
					'@type'    => 'ListItem',
					'position' => $pos++,
					'name'     => $ancestor->name,
					'item'     => flowers53_term_url( $ancestor ),
				];
			}
		}
		$items[] = [
			'@type'    => 'ListItem',
			'position' => $pos++,
			'name'     => $term->name,
			'item'     => flowers53_term_url( $term ),
		];
	}

	flowers53_output_schema( [
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $items,
	] );
}, 5 );

// =============================================================================
// 4b. ТОВАРЫ — полная Product schema через wp_head
// =============================================================================

add_action( 'wp_head', function () {
	if ( ! is_singular( 'product' ) ) return;
	global $post;
	$c = flowers53_company_data();

	$product = wc_get_product( $post->ID );
	if ( ! $product ) return;

	$image_id  = $product->get_image_id();
	$image_url = $image_id ? wp_get_attachment_url( $image_id ) : $c['logo'];
	$price     = $product->get_price();
	$desc      = wp_strip_all_tags( $product->get_short_description() ?: $product->get_description() );

	$schema = [
		'@context'    => 'https://schema.org',
		'@type'       => 'Product',
		'name'        => $product->get_name(),
		'url'         => $product->get_permalink(),
		'description' => $desc ?: $product->get_name(),
		'image'       => $image_url,
		'brand'       => [ '@type' => 'Brand', 'name' => $c['name'] ],
		'offers'      => [
			'@type'                     => 'Offer',
			'priceCurrency'             => 'RUB',
			'price'                     => $price ?: '0',
			'availability'              => $product->is_in_stock()
				? 'https://schema.org/InStock'
				: 'https://schema.org/OutOfStock',
			'url'                       => $product->get_permalink(),
			'seller'                    => flowers53_seller(),
			'shippingDetails'           => flowers53_shipping_details(),
			'hasMerchantReturnPolicy'   => flowers53_return_policy(),
		],
	];

	$sku = $product->get_sku();
	if ( $sku ) $schema['sku'] = $sku;

	// Для вариативных товаров — AggregateOffer
	if ( $product->is_type( 'variable' ) ) {
		$prices = $product->get_variation_prices( true );
		if ( ! empty( $prices['price'] ) ) {
			$schema['offers'] = [
				'@type'                     => 'AggregateOffer',
				'priceCurrency'             => 'RUB',
				'lowPrice'                  => min( $prices['price'] ),
				'highPrice'                 => max( $prices['price'] ),
				'offerCount'                => count( $prices['price'] ),
				'availability'              => $product->is_in_stock()
					? 'https://schema.org/InStock'
					: 'https://schema.org/OutOfStock',
				'url'                       => $product->get_permalink(),
				'seller'                    => flowers53_seller(),
				'shippingDetails'           => flowers53_shipping_details(),
				'hasMerchantReturnPolicy'   => flowers53_return_policy(),
			];
		}
	}

	flowers53_output_schema( $schema );
}, 6 );

// Отключаем WooCommerce Product structured data (мы генерируем свою)
add_filter( 'woocommerce_structured_data_product', '__return_empty_array', PHP_INT_MAX );

// =============================================================================
// 5. СТРАНИЦА КОНТАКТОВ
// =============================================================================

add_action( 'wp_head', function () {
	if ( ! is_page( 'o-nas-i-kontakty' ) ) return;
	$c = flowers53_company_data();

	flowers53_output_schema( [
		'@context'                  => 'https://schema.org',
		'@type'                     => 'LocalBusiness',
		'@id'                       => $c['url'] . '#store',
		'name'                      => $c['name'],
		'description'               => 'Доставка цветов и букетов в Великом Новгороде. Свежие цветы, быстрая доставка за 1 час.',
		'url'                       => $c['url'],
		'telephone'                 => $c['telephone'],
		'email'                     => $c['email'],
		'address'                   => [
			'@type'           => 'PostalAddress',
			'streetAddress'   => $c['street'],
			'addressLocality' => $c['locality'],
			'addressRegion'   => $c['region'],
			'postalCode'      => $c['postal'],
			'addressCountry'  => $c['country'],
		],
		'geo'                       => [
			'@type'     => 'GeoCoordinates',
			'latitude'  => $c['lat'],
			'longitude' => $c['lng'],
		],
		'openingHoursSpecification' => flowers53_opening_hours(),
		'priceRange'                => $c['price_range'],
		'paymentAccepted'           => [ 'Cash', 'CreditCard', 'SBP' ],
		'currenciesAccepted'        => 'RUB',
		'sameAs'                    => $c['same_as'],
	] );
}, 5 );

// =============================================================================
// 6. СТАТЬИ (news) — Article + BreadcrumbList
// =============================================================================

add_action( 'wp_head', function () {
	if ( ! is_singular( 'post' ) ) return;
	$post = get_post();
	if ( ! $post ) return;
	$c = flowers53_company_data();
	$thumb = get_the_post_thumbnail_url( $post->ID, 'full' );

	$raw   = $post->post_content;
	$clean = preg_replace( '/\[\/?[a-z_]+[^\]]*\]/', '', $raw );
	$clean = wp_strip_all_tags( $clean );
	$clean = preg_replace( '/\s+/', ' ', trim( $clean ) );
	$desc  = mb_substr( $clean, 0, 200 );
	if ( ! $desc ) $desc = get_post_meta( $post->ID, '_yoast_wpseo_metadesc', true );

	$cats     = get_the_category( $post->ID );
	$cat_name = ! empty( $cats ) ? $cats[0]->name : 'Новости';
	$cat_url  = ! empty( $cats ) ? get_category_link( $cats[0]->term_id ) : $c['url'] . '/news/';

	$article = [
		'@context'         => 'https://schema.org',
		'@type'            => 'Article',
		'headline'         => get_the_title( $post ),
		'description'      => $desc,
		'datePublished'    => get_the_date( 'c', $post ),
		'dateModified'     => get_the_modified_date( 'c', $post ),
		'mainEntityOfPage' => [ '@type' => 'WebPage', '@id' => get_permalink( $post ) ],
		'author'           => [ '@type' => 'Organization', 'name' => $c['name'], 'url' => $c['url'] ],
		'publisher'        => [ '@type' => 'Organization', 'name' => $c['name'], 'logo' => [ '@type' => 'ImageObject', 'url' => $c['logo'] ] ],
	];
	if ( $thumb ) $article['image'] = $thumb;

	flowers53_output_schema( $article );
	flowers53_output_schema( [
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => [
			[ '@type' => 'ListItem', 'position' => 1, 'name' => 'Главная', 'item' => $c['url'] . '/' ],
			[ '@type' => 'ListItem', 'position' => 2, 'name' => $cat_name, 'item' => $cat_url ],
			[ '@type' => 'ListItem', 'position' => 3, 'name' => get_the_title( $post ) ],
		],
	] );
}, 5 );

// =============================================================================
// 7. ОБЫЧНЫЕ СТРАНИЦЫ — BreadcrumbList
// =============================================================================

add_action( 'wp_head', function () {
	if ( ! is_singular( 'page' ) || is_front_page() ) return;
	$post = get_post();
	if ( ! $post ) return;
	$c = flowers53_company_data();

	$items = [
		[ '@type' => 'ListItem', 'position' => 1, 'name' => 'Главная', 'item' => $c['url'] . '/' ],
	];
	if ( $post->post_parent ) {
		$parent = get_post( $post->post_parent );
		if ( $parent ) {
			$items[] = [ '@type' => 'ListItem', 'position' => 2, 'name' => get_the_title( $parent ), 'item' => get_permalink( $parent ) ];
			$items[] = [ '@type' => 'ListItem', 'position' => 3, 'name' => get_the_title( $post ) ];
		}
	} else {
		$items[] = [ '@type' => 'ListItem', 'position' => 2, 'name' => get_the_title( $post ) ];
	}

	flowers53_output_schema( [
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $items,
	] );
}, 5 );

// =============================================================================
// ОТКЛЮЧАЕМ СХЕМЫ ОТ YOAST И WOOCOMMERCE BREADCRUMBS
// =============================================================================

add_filter( 'wpseo_json_ld_output', '__return_false' );
add_filter( 'wpseo_schema_graph', '__return_empty_array', PHP_INT_MAX );
add_filter( 'wpseo_schema_graph_pieces', '__return_empty_array', PHP_INT_MAX );

// BreadcrumbList от WooCommerce
add_action( 'init', function () {
	if ( function_exists( 'WC' ) && WC()->structured_data ) {
		remove_action( 'woocommerce_breadcrumb', [ WC()->structured_data, 'generate_breadcrumblist_data' ], 10 );
	}
}, 20 );
add_filter( 'woocommerce_structured_data_breadcrumblist', '__return_empty_array' );
add_filter( 'woocommerce_structured_data_context', function( $types ) {
	unset( $types['breadcrumblist'] );
	return $types;
} );
add_filter( 'woocommerce_get_structured_data', function( $data ) {
	foreach ( $data as $key => $value ) {
		if ( isset( $value['@type'] ) && $value['@type'] === 'BreadcrumbList' ) {
			unset( $data[ $key ] );
		}
	}
	return $data;
} );
