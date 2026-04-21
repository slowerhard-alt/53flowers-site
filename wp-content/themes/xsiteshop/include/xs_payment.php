<?
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	function init_your_gateway_class() {
		if ( ! class_exists( 'WC_xs_my_payment' ) ) {
			class WC_xs_my_payment extends WC_Payment_Gateway {
				
					public function __construct() {
						$this->id                 = 'rschot'; // Id метода доставки. Должен быть уникальным.
						$this->method_title       = __( 'Оплата переводом на карту' );
						$this->method_description = __( 'Оплата переводом на карту' );
	 
						$this->enabled            = "yes"; // Принудительное включение метода доставки
						$this->title              = $this->get_option( 'title' );
						$this->description        = $this->get_option( 'description' );
	 
						$this->init();
					}
	 
					function init() {
						$this->init_form_fields(); //Это часть API настроек. Переопределите этот метод, чтобы добавить свои собственные настройки
						$this->init_settings(); // Это часть API настроек. Загружает настройки, которые вы ранее инициировали.
	 
						// Сохранение настроек
						
						add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
					}
					
					function init_form_fields() {
						$this->form_fields = array(
							'enabled' => array(
								'title' => __( 'Включить/Выключить', 'woocommerce' ),
								'type' => 'checkbox',
								'label' => __( 'Включить оплату переводом на карту', 'woocommerce' ),
								'default' => 'yes'
							),
							'title' => array(
								'title' => __( 'Заголовок', 'woocommerce' ),
								'type' => 'text',
								'description' => __( 'Это Заголовок который видит пользователь.', 'woocommerce' ),
								'default' => __( 'Оплата переводом на карту', 'woocommerce' ),
								'desc_tip'      => true,
							),
							'description' => array(
								'title' => __( 'Сообщение', 'woocommerce' ),
								'type' => 'textarea',
								'default' => ''
							)
						);
					}
					
					function process_payment( $order_id ) {
						global $woocommerce;
						$order = new WC_Order( $order_id );
					 
						// Отметка (мы ожидаем чек)
						$order->update_status('wc-processing', __('В обработке'));
					 
						// Уменьшение уровня запасов
						//$order->reduce_order_stock();
					 
						// Очистка корзины
						$woocommerce->cart->empty_cart();
					 
						// Редирект на страницу благодарности(успешной орплаты)
						return array(
							'result' => 'success',
							'redirect' => $this->get_return_url( $order )
						);
					}
	 
			}
		}
		if ( ! class_exists( 'WC_xs_my_payment_1' ) ) {
			class WC_xs_my_payment_1 extends WC_Payment_Gateway {
				
					public function __construct() {
						$this->id                 = 'xs_card'; // Id метода доставки. Должен быть уникальным.
						$this->method_title       = __( 'Оплата картой на сайте' );
						$this->method_description = __( 'Оплата картой на сайте' );
	 
						$this->enabled            = "yes"; // Принудительное включение метода доставки
						$this->title              = $this->get_option( 'title' );
						$this->description        = $this->get_option( 'description' );
	 
						$this->init();
					}
	 
					function init() {
						$this->init_form_fields(); //Это часть API настроек. Переопределите этот метод, чтобы добавить свои собственные настройки
						$this->init_settings(); // Это часть API настроек. Загружает настройки, которые вы ранее инициировали.
	 
						// Сохранение настроек
						
						add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
					}
					
					function init_form_fields() {
						$this->form_fields = array(
							'enabled' => array(
								'title' => __( 'Включить/Выключить', 'woocommerce' ),
								'type' => 'checkbox',
								'label' => __( 'Включить оплату картой на сайте', 'woocommerce' ),
								'default' => 'yes'
							),
							'title' => array(
								'title' => __( 'Заголовок', 'woocommerce' ),
								'type' => 'text',
								'description' => __( 'Это Заголовок который видит пользователь.', 'woocommerce' ),
								'default' => __( 'Оплата картой на сайте', 'woocommerce' ),
								'desc_tip'      => true,
							),
							'description' => array(
								'title' => __( 'Сообщение', 'woocommerce' ),
								'type' => 'textarea',
								'default' => ''
							)
						);
					}
					
					function process_payment( $order_id ) {
						global $woocommerce;
						$order = new WC_Order( $order_id );
					 
						// Отметка (мы ожидаем чек)
						$order->update_status('wc-processing', __('В обработке'));
					 
						// Уменьшение уровня запасов
						//$order->reduce_order_stock();
					 
						// Очистка корзины
						$woocommerce->cart->empty_cart();
					 
						// Редирект на страницу благодарности(успешной орплаты)
						return array(
							'result' => 'success',
							'redirect' => $this->get_return_url( $order )
						);
					}
	 
			}
		}
		if ( ! class_exists( 'WC_xs_my_payment_2' ) ) {
			class WC_xs_my_payment_2 extends WC_Payment_Gateway {
				
					public function __construct() {
						$this->id                 = 'xs_rschot'; // Id метода доставки. Должен быть уникальным.
						$this->method_title       = __( 'Оплата на расчётный счёт организации' );
						$this->method_description = __( 'Оплата на расчётный счёт организации' );
	 
						$this->enabled            = "yes"; // Принудительное включение метода доставки
						$this->title              = $this->get_option( 'title' );
						$this->description        = $this->get_option( 'description' );
	 
						$this->init();
					}
	 
					function init() {
						$this->init_form_fields(); //Это часть API настроек. Переопределите этот метод, чтобы добавить свои собственные настройки
						$this->init_settings(); // Это часть API настроек. Загружает настройки, которые вы ранее инициировали.
	 
						// Сохранение настроек
						
						add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
					}
					
					function init_form_fields() {
						$this->form_fields = array(
							'enabled' => array(
								'title' => __( 'Включить/Выключить', 'woocommerce' ),
								'type' => 'checkbox',
								'label' => __( 'Включить оплату на расчётный счёт организации', 'woocommerce' ),
								'default' => 'yes'
							),
							'title' => array(
								'title' => __( 'Заголовок', 'woocommerce' ),
								'type' => 'text',
								'description' => __( 'Это Заголовок который видит пользователь.', 'woocommerce' ),
								'default' => __( 'Оплата на расчётный счёт организации', 'woocommerce' ),
								'desc_tip'      => true,
							),
							'description' => array(
								'title' => __( 'Сообщение', 'woocommerce' ),
								'type' => 'textarea',
								'default' => ''
							)
						);
					}
					
					function process_payment( $order_id ) {
						global $woocommerce;
						$order = new WC_Order( $order_id );
					 
						// Отметка (мы ожидаем чек)
						$order->update_status('wc-processing', __('В обработке'));
					 
						// Уменьшение уровня запасов
						//$order->reduce_order_stock();
					 
						// Очистка корзины
						$woocommerce->cart->empty_cart();
					 
						// Редирект на страницу благодарности(успешной орплаты)
						return array(
							'result' => 'success',
							'redirect' => $this->get_return_url( $order )
						);
					}
	 
			}
		}
		if ( ! class_exists( 'WC_xs_my_payment_3' ) ) {
			class WC_xs_my_payment_3 extends WC_Payment_Gateway {
				
					public function __construct() {
						$this->id                 = 'xs_paypal'; // Id метода доставки. Должен быть уникальным.
						$this->method_title       = __( 'Оплата через PayPal' );
						$this->method_description = __( 'Оплата через PayPal' );
	 
						$this->enabled            = "yes"; // Принудительное включение метода доставки
						$this->title              = $this->get_option( 'title' );
						$this->description        = $this->get_option( 'description' );
	 
						$this->init();
					}
	 
					function init() {
						$this->init_form_fields(); //Это часть API настроек. Переопределите этот метод, чтобы добавить свои собственные настройки
						$this->init_settings(); // Это часть API настроек. Загружает настройки, которые вы ранее инициировали.
	 
						// Сохранение настроек
						
						add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
					}
					
					function init_form_fields() {
						$this->form_fields = array(
							'enabled' => array(
								'title' => __( 'Включить/Выключить', 'woocommerce' ),
								'type' => 'checkbox',
								'label' => __( 'Включить оплату через PayPal', 'woocommerce' ),
								'default' => 'yes'
							),
							'title' => array(
								'title' => __( 'Заголовок', 'woocommerce' ),
								'type' => 'text',
								'description' => __( 'Это Заголовок который видит пользователь.', 'woocommerce' ),
								'default' => __( 'Оплата через PayPal', 'woocommerce' ),
								'desc_tip'      => true,
							),
							'description' => array(
								'title' => __( 'Сообщение', 'woocommerce' ),
								'type' => 'textarea',
								'default' => ''
							)
						);
					}
					
					function process_payment( $order_id ) {
						global $woocommerce;
						$order = new WC_Order( $order_id );
					 
						// Отметка (мы ожидаем чек)
						$order->update_status('wc-processing', __('В обработке'));
					 
						// Уменьшение уровня запасов
						//$order->reduce_order_stock();
					 
						// Очистка корзины
						$woocommerce->cart->empty_cart();
					 
						// Редирект на страницу благодарности(успешной орплаты)
						return array(
							'result' => 'success',
							'redirect' => $this->get_return_url( $order )
						);
					}
	 
			}
		}
		if ( ! class_exists( 'WC_xs_my_payment_4' ) ) {
			class WC_xs_my_payment_4 extends WC_Payment_Gateway {
				
					public function __construct() {
						$this->id                 = 'xs_after_pay'; // Id метода доставки. Должен быть уникальным.
						$this->method_title       = __( 'Оплата после согласования заказа' );
						$this->method_description = __( 'Оплата после согласования заказа' );
	 
						$this->enabled            = "yes"; // Принудительное включение метода доставки
						$this->title              = $this->get_option( 'title' );
						$this->description        = $this->get_option( 'description' );
	 
						$this->init();
					}
	 
					function init() {
						$this->init_form_fields(); //Это часть API настроек. Переопределите этот метод, чтобы добавить свои собственные настройки
						$this->init_settings(); // Это часть API настроек. Загружает настройки, которые вы ранее инициировали.
	 
						// Сохранение настроек
						
						add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
					}
					
					function init_form_fields() {
						$this->form_fields = array(
							'enabled' => array(
								'title' => __( 'Включить/Выключить', 'woocommerce' ),
								'type' => 'checkbox',
								'label' => __( 'Включить оплату после согласования заказа', 'woocommerce' ),
								'default' => 'yes'
							),
							'title' => array(
								'title' => __( 'Заголовок', 'woocommerce' ),
								'type' => 'text',
								'description' => __( 'Это Заголовок который видит пользователь.', 'woocommerce' ),
								'default' => __( 'Оплата после согласования заказа', 'woocommerce' ),
								'desc_tip'      => true,
							),
							'description' => array(
								'title' => __( 'Сообщение', 'woocommerce' ),
								'type' => 'textarea',
								'default' => ''
							)
						);
					}
					
					function process_payment( $order_id ) {
						global $woocommerce;
						$order = new WC_Order( $order_id );
					 
						// Отметка (мы ожидаем чек)
						$order->update_status('wc-processing', __('В обработке'));
					 
						// Уменьшение уровня запасов
						//$order->reduce_order_stock();
					 
						// Очистка корзины
						$woocommerce->cart->empty_cart();
					 
						// Редирект на страницу благодарности(успешной орплаты)
						return array(
							'result' => 'success',
							'redirect' => $this->get_return_url( $order )
						);
					}
	 
			} 
		}
	}
	add_action( 'wp_loaded', 'init_your_gateway_class' );
	
	function add_your_gateway_class( $methods ) {
		$methods[] = 'WC_xs_my_payment'; 
		$methods[] = 'WC_xs_my_payment_1'; 
		$methods[] = 'WC_xs_my_payment_2'; 
		$methods[] = 'WC_xs_my_payment_3'; 
		$methods[] = 'WC_xs_my_payment_4'; 
		return $methods;
	}
 
	add_filter( 'woocommerce_payment_gateways', 'add_your_gateway_class' );
}