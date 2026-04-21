<?
$options = array(

    array(
        "name" => "Основные настройки",
		"code" => "main",
        "group" => array(
            array(
                "name" => "Основное",
                "options" => array(
                    array(
                        "name" => "Иконка браузера",
                        "desc" => "Иконка страниц сайта (favicon). Рекомендуемый размер - 16 x 16 px. Рекомендуемый тип файла - PNG.",
                        "id" => $shortname . "_favicon",
                        "type" => "file",
                        "default" => get_bloginfo('template_directory') . "/favicon.png"
                    ),
                ),
            ),
            array(
                "name" => "Доставка",
                "options" => array(
                    array(
                        "name" => "Доставка описание",
                        "desc" => "Описание доставки в подвале.",
                        "id" => $shortname . "_delivery",
                        "type" => "textarea",
                    ),
                    array(
                        "name" => "Бесплатная",
                        "desc" => "Условия бесплатной доставки.",
                        "id" => $shortname . "_free_deli",
                        "type" => "text",
                    ),
                    array(
                        "name" => "Платная",
                        "desc" => "Условия платной доставки.",
                        "id" => $shortname . "_charge_deli",
                        "type" => "text",
                    ),
                    array(
                        "name" => "Стоимость доставки",
                        "desc" => "Дополнительная информация в подвале сайта.",
                        "id" => $shortname . "_addit_text",
                        "type" => "textarea",
                    ),
                ),
            ),


            array(
                "name" => "Контакты",
                "options" => array(
                    array(
                        "name" => "Телефон",
                        "desc" => "Контактный телефон. Если их несколько - укажите через точку с запятой. В шапке сайта будет отображаться первый номер.",
                        "id" => $shortname . "_phone",
                        "type" => "text",
                    ),
                    array(
                        "name" => "E-mail",
                        "desc" => "Адрес электронной почты. Если их несколько - укажите через точку с запятой. В шапке сайта будет отображаться первый email.",
                        "id" => $shortname . "_email",
                        "type" => "text",
                    ),
                    array(
                        "name" => "Адрес",
                        "desc" => "Почтовый адрес компании. Если их несколько - укажите через точку с запятой. В шапке сайта будет отображаться только первый адрес.",
                        "id" => $shortname . "_address",
                        "type" => "textarea",
                    ),
                    array(
                        "name" => "Режим работы",
                        "desc" => "",
                        "id" => $shortname . "_work",
                        "type" => "text",
                    ),
				),
            ),
            array(
                "name" => "Спецпредложения",
                "options" => array(
                    array(
                        "name" => "Минимальная скидка спецпредлоежний",
                        "desc" => "Размер скидки товара, от которого он будут отображаться в категории Спецпредложения.",
                        "id" => $shortname . "_price_cpecial",
                        "type" => "number",
						"min" => 0,
						"max" => 10000,
						"step" => 1,
                        "default" => 30
                    ),
                ),
            ),
            array(
                "name" => "Всплывающее сообщение",
                "options" => array(
                    array(
                        "name" => 'Показывать всплывающее сообщение при переходе на сайт',
                        "id" => $shortname . "_show_message",
                        "type" => "checkbox"
                    ),
                    array(
                        "name" => 'Текст всплывающего сообщения',
                        "id" => $shortname . "_text_message",
                        "type" => "textarea",
                    ),
                ),
            ),
            array(
                "name" => "Прочие настройки",
                "options" => array(
                    array(
                        "name" => 'Текст на кнопке в листинге простых товаров',
                        "desc" => 'Например, "Купить" или "Подробнее"',
                        "id" => $shortname . "_product_btn",
                        "type" => "text",
                        "default" => 'Купить'
                    ),
                    array(
                        "name" => 'Текст на кнопке в листинге топовых товаров',
                        "desc" => 'Например, "Открыть" или "Подробнее"',
                        "id" => $shortname . "_product_top_btn",
                        "type" => "text",
                        "default" => 'Купить'
                    ),
                    array(
                        "name" => 'Количество выводимых вариация',
                        "desc" => 'Ограничение количества выводимых вариаций в превью товара.',
                        "id" => $shortname . "_product_variation_count",
                        "type" => "number",
						"min" => 0,
						"max" => 100,
						"step" => 1,
                        "default" => 30
                    ),
                    array(
                        "name" => 'Текст перед кнопкой "Оформить заказ"',
                        "id" => $shortname . "_checkout_text",
                        "type" => "textarea",
                    ),
                    array(
                        "name" => 'Выводить кнопку "Заказать звонок" в шапке сайта?',
                        "id" => $shortname . "_recall_show",
                        "type" => "checkbox"
                    ),
                    array(
                        "name" => 'Выводить кнопку "Заказ в один клик" в карточке товара?',
                        "id" => $shortname . "_payoneclick_show",
                        "type" => "checkbox"
                    ),
                    array(
                        "name" => 'Ограничить возможность оформления заказа',
                        "id" => $shortname . "_deactive_checkout",
                        "type" => "checkbox"
                    ),
                    array(
                        "name" => 'Текст при отключенном оформлении заказа',
                        "id" => $shortname . "_deactive_checkout_text",
                        "type" => "textarea",
                    ),
                    array(
                        "name" => 'Выводить метку «Свежая поставка»',
                        "id" => $shortname . "_is_fresh_delivery",
                        "type" => "checkbox"
                    ),
                ),
            ),
        ),
    ),
	
	/*
    array(
        "name" => "Слайдер",
        "code" => "xs_slider",
        "group" => array(
            array(
                "name" => "",
				"desc" => "Отредактировать содержимое слайдов можно <a href=\"/wp-admin/edit.php?post_type=slider\">здесь</a>.",
                "options" => array(
                    array(
                        "name" => "Показывать слайдер на главной странице?",
                        "id" => $shortname . "_slider_show",
                        "type" => "checkbox"
                    ),
                    array(
                        "name" => "Автоматически запускать листание слайдов?",
                        "id" => $shortname . "_autoslider",
                        "type" => "checkbox",
                    ),
                    array(
                        "name" => "Останавливать слайдер при  наведении курсора мыши?",
                        "id" => $shortname . "_slider_pause_on_hover",
                        "type" => "checkbox",
                    ),
					array(
                        "name" => "Максимальная ширина слайдера в пикселях",
                        "desc" => "Если значение ширины равно нулю, слайдер будет автоматически растягиваться на всю ширину экрана",
                        "id" => $shortname . "_slider_width",
                        "type" => "number",
                        "min" => 0,
                        "step" => 1,
                    ),
                    array(
                        "name" => "Высота слайдера в пикселях",
                        "id" => $shortname . "_slider_height",
                        "type" => "number",
                        "min" => 1,
                        "step" => 1,
                    ),
                    array(
                        "name" => "Скорость смены слайдов",
                        "desc" => "В милисекундах",
                        "id" => $shortname . "_slider_speed",
                        "type" => "number",
                        "min" => 100,
                        "step" => 100,
                    ),
                    array(
                        "name" => "Время показа слайда",
                        "id" => $shortname . "_slider_timeout",
                        "desc" => "В милисекундах",
                        "type" => "number",
                        "min" => 1000,
                        "step" => 500,
                    ),
                    array(
                        "name" => "Эффект перехода между слайдами",
                        "id" => $shortname . "_slider_effect",
                        "type" => "select",
                        "option" => array(
                            array(
                                "name" => "Горизонтальный переход",
                                "value" => "hslide",
                            ),
                            array(
                                "name" => "Вертикальный переход",
                                "value" => "vslide",
                            ),
                            array(
                                "name" => "Появление",
                                "value" => "fase",
                            ),
                        )
                    ),
                ),
            ),
            array(
                "name" => "Навигация",
                "options" => array(
                    array(
                        "name" => "Отображать стрелки?",
                        "id" => $shortname . "_slider_show_arrow",
                        "type" => "checkbox",
                    ),
                    array(
                        "name" => "Отображать кнопки навигации?",
                        "id" => $shortname . "_slider_show_button",
                        "type" => "checkbox",
                    ),
                ),
            ),
        ),
    ),
	*/
	
    array(
        "name" => "Мессенджеры",
        "code" => "social",
        "group" => array(
            array(
                "name" => "",
                "desc" => "",
                "options" => array(
					array(
                        "name" => 'Скрыть иконки мессенджеров с сайта',
                        "id" => $shortname . "_is_hide_messenger",
                        "type" => "checkbox"
                    ),

                ),
            ),
			array(
                "name" => "Ссылки на социальные сети и мессенджеры",
                "desc" => "Прямые ссылки на страницы, например: <em>https://vk.com/xsitepro</em>",
                "options" => array(
					/*
                    array(
                        "name" => "Вконтакте",
                        "id" => $shortname . "_social_link_vk",
                        "type" => "text",
                    ),
                    array(
                        "name" => "Skype",
                        "id" => $shortname . "_social_link_skype",
                        "desc" => "Логин скайп.",
                        "type" => "text",
                    ),
                    array(
                        "name" => "Одноклассники",
                        "id" => $shortname . "_social_link_ok",
                        "type" => "text",
                    ),
                    array(
                        "name" => "Instagram",
                        "id" => $shortname . "_social_link_instagram",
                        "type" => "text",
					),
                    array(
                        "name" => "Facebook",
                        "id" => $shortname . "_social_link_facebook",
                        "type" => "text",
                    ),
                    array(
                        "name" => "Twitter",
                        "id" => $shortname . "_social_link_twitter",
                        "type" => "text",
                    ),
                    array(
                        "name" => "YouTube",
                        "id" => $shortname . "_social_link_yt",
                        "type" => "text",
                    ),
					*/
                    array(
                        "name" => "Viber",
                        "id" => $shortname . "_social_link_vb",
                        "desc" => "Номер телефона, например, <strong>79524446655</strong> (без пробелов, дефисов, скобок, начинается с 7). Работает только если у пользователя установлено приложение Viber.",
                        "type" => "text",
                    ),
                    array(
                        "name" => "WhatsApp",
                        "id" => $shortname . "_social_link_wa",
                        "desc" => "Номер телефона, например, <strong>79524446655</strong> (без пробелов, дефисов, скобок, начинается с 7). Работает только если у пользователя установлено приложение WhatsApp.",
                        "type" => "text",
                    ),
                    array(
                        "name" => "Telegram",
                        "id" => $shortname . "_social_link_teleg",
                        "desc" => "Ник или номер телефона. Работает только если у пользователя установлено приложение Telegram.",
                        "type" => "text",
                    ),
                    array(
                        "name" => "MAX",
                        "id" => $shortname . "_social_link_max",
                        "desc" => "Ссылка на поделиться в MAX.",
                        "type" => "text",
                    ),
                ),
            ),
        ),
    ),
	
	/*
    array(
        "name" => "Интергация с CRM",
        "code" => "crm",
        "group" => array(
            array(
                "name" => "",
                "desc" => "После заполнения данных настроек, на указанный аккаунт CRM будут автоматически отправляться все лиды с сайта.",
                "options" => array(
                    array(
                        "name" => "Включить интеграцию с CRM",
                        "id" => $shortname . "_crm",
                        "type" => "checkbox",
                    ),
                ),
            ),
            array(
                "name" => "Битрикс 24",
                "options" => array(
                    array(
                        "name" => "Имя домена",
                        "id" => $shortname . "_crm_bitrix_domain",
                        "desc" => "Например, xsite.bitrix24.ru.",
                        "type" => "text",
                    ),
                    array(
                        "name" => "Логин администратора",
                        "id" => $shortname . "_crm_bitrix_login",
                        "desc" => "Обычно, адрес электронной почты.",
                        "type" => "text",
                    ),
                    array(
                        "name" => "Пароль администратора",
                        "id" => $shortname . "_crm_bitrix_pass",
                        "type" => "text",
                    ),
                ),
            ),
            array(
                "name" => "SendPulse",
                "options" => array(
                    array(
                        "name" => "Client ID",
                        "id" => $shortname . "_crm_sendpulse_client_id",
                        "desc" => "Личный кабинет SendPulse -> Настройки аккаунта -> Вкладка \"API\" -> ID.",
                        "type" => "text",
                    ),
                    array(
                        "name" => "Secret ID",
                        "id" => $shortname . "_crm_sendpulse_secret_id",
                        "desc" => "Личный кабинет SendPulse -> Настройки аккаунта -> Вкладка \"API\" -> Secret.",
                        "type" => "text",
                    ),
                    array(
                        "name" => "Идентификатор адресной книги",
                        "id" => $shortname . "_crm_sendpulse_category_id",
                        "type" => "number", 
                        "desc" => "Личный кабинет SendPulse -> Адресная книга -> Выбрать книгу -> Скопировать id из url (например, в url https://login.sendpulse.com/emailservice/addressbooks/emails/id/<b>737865</b>/, идентификатор 737865).",
                        "min" => 0,
                        "max" => "",
                        "step" => 1
                    ),
                ),
            ),
        ),
    ),
	*/
	
	
    array(
        "name" => "Склад",
        "code" => "store",
        "group" => array(
            array(
               "name" => "Коэффициенты 1 группы",
                "options" => array(
                    array(
                        "name" => "Название группы",
                        "id" => $shortname . "_store_1_name",
                        "type" => "text",
						"desc" => 'По умолчанию <strong>1 группа</strong>'
                    ),
                    array(
                        "name" => "Цвет в отчёте",
                        "id" => $shortname . "_store_1_color",
                        "type" => "color",
                    ),
                    array(
                        "name" => "До 3 дней",
                        "id" => $shortname . "_store_1_0-3",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 4 до 6 дней",
                        "id" => $shortname . "_store_1_4-6",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 7 до 10 дней",
                        "id" => $shortname . "_store_1_7",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 11 дней",
                        "id" => $shortname . "_store_1_10",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                ),
            ),
            array(
               "name" => "Коэффициенты 2 группы",
                "options" => array(
                    array(
                        "name" => "Название группы",
                        "id" => $shortname . "_store_2_name",
                        "type" => "text",
						"desc" => 'По умолчанию <strong>2 группа</strong>'
                    ),
                    array(
                        "name" => "Цвет в отчёте",
                        "id" => $shortname . "_store_2_color",
                        "type" => "color",
                    ),
                    array(
                        "name" => "До 3 дней",
                        "id" => $shortname . "_store_2_0-3",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 4 до 6 дней",
                        "id" => $shortname . "_store_2_4-6",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 7 до 10 дней",
                        "id" => $shortname . "_store_2_7",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 11 дней",
                        "id" => $shortname . "_store_2_10",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                ),
            ),
            array(
               "name" => "Коэффициенты 3 группы",
                "options" => array(
                    array(
                        "name" => "Название группы",
                        "id" => $shortname . "_store_3_name",
                        "type" => "text",
						"desc" => 'По умолчанию <strong>3 группа</strong>'
                    ),
                    array(
                        "name" => "Цвет в отчёте",
                        "id" => $shortname . "_store_3_color",
                        "type" => "color",
                    ),
                    array(
                        "name" => "До 3 дней",
                        "id" => $shortname . "_store_3_0-3",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 4 до 6 дней",
                        "id" => $shortname . "_store_3_4-6",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 7 до 10 дней",
                        "id" => $shortname . "_store_3_7",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 11 дней",
                        "id" => $shortname . "_store_3_10",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                ),
            ),
            array(
               "name" => "Коэффициенты 4 группы",
                "options" => array(
                    array(
                        "name" => "Название группы",
                        "id" => $shortname . "_store_4_name",
                        "type" => "text",
						"desc" => 'По умолчанию <strong>4 группа</strong>'
                    ),
                    array(
                        "name" => "Цвет в отчёте",
                        "id" => $shortname . "_store_4_color",
                        "type" => "color",
                    ),
                    array(
                        "name" => "До 3 дней",
                        "id" => $shortname . "_store_4_0-3",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 4 до 6 дней",
                        "id" => $shortname . "_store_4_4-6",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 7 до 10 дней",
                        "id" => $shortname . "_store_4_7",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 11 дней",
                        "id" => $shortname . "_store_4_10",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                ),
            ),
            array(
               "name" => "Коэффициенты 5 группы",
                "options" => array(
                    array(
                        "name" => "Название группы",
                        "id" => $shortname . "_store_5_name",
                        "type" => "text",
						"desc" => 'По умолчанию <strong>5 группа</strong>'
                    ),
                    array(
                        "name" => "Цвет в отчёте",
                        "id" => $shortname . "_store_5_color",
                        "type" => "color",
                    ),
                    array(
                        "name" => "До 3 дней",
                        "id" => $shortname . "_store_5_0-3",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 4 до 6 дней",
                        "id" => $shortname . "_store_5_4-6",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 7 до 10 дней",
                        "id" => $shortname . "_store_5_7",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 11 дней",
                        "id" => $shortname . "_store_5_10",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                ),
            ),
            array(
               "name" => "Коэффициенты 6 группы",
                "options" => array(
                    array(
                        "name" => "Название группы",
                        "id" => $shortname . "_store_6_name",
                        "type" => "text",
						"desc" => 'По умолчанию <strong>6 группа</strong>'
                    ),
                    array(
                        "name" => "Цвет в отчёте",
                        "id" => $shortname . "_store_6_color",
                        "type" => "color",
                    ),
                    array(
                        "name" => "До 3 дней",
                        "id" => $shortname . "_store_6_0-3",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 4 до 6 дней",
                        "id" => $shortname . "_store_6_4-6",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 7 до 10 дней",
                        "id" => $shortname . "_store_6_7",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 11 дней",
                        "id" => $shortname . "_store_6_10",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                ),
            ),
            array(
               "name" => "Коэффициенты 7 группы",
                "options" => array(
                    array(
                        "name" => "Название группы",
                        "id" => $shortname . "_store_7_name",
                        "type" => "text",
						"desc" => 'По умолчанию <strong>7 группа</strong>'
                    ),
                    array(
                        "name" => "Цвет в отчёте",
                        "id" => $shortname . "_store_7_color",
                        "type" => "color",
                    ),
                    array(
                        "name" => "До 3 дней",
                        "id" => $shortname . "_store_7_0-3",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 4 до 6 дней",
                        "id" => $shortname . "_store_7_4-6",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 7 до 10 дней",
                        "id" => $shortname . "_store_7_7",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 11 дней",
                        "id" => $shortname . "_store_7_10",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                ),
            ),
            array(
               "name" => "Коэффициенты 8 группы",
                "options" => array(
                    array(
                        "name" => "Название группы",
                        "id" => $shortname . "_store_8_name",
                        "type" => "text",
						"desc" => 'По умолчанию <strong>8 группа</strong>'
                    ),
                    array(
                        "name" => "Цвет в отчёте",
                        "id" => $shortname . "_store_8_color",
                        "type" => "color",
                    ),
                    array(
                        "name" => "До 3 дней",
                        "id" => $shortname . "_store_8_0-3",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 4 до 6 дней",
                        "id" => $shortname . "_store_8_4-6",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 7 до 10 дней",
                        "id" => $shortname . "_store_8_7",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 11 дней",
                        "id" => $shortname . "_store_8_10",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                ),
            ),
            array(
               "name" => "Коэффициенты 9 группы",
                "options" => array(
                    array(
                        "name" => "Название группы",
                        "id" => $shortname . "_store_9_name",
                        "type" => "text",
						"desc" => 'По умолчанию <strong>9 группа</strong>'
                    ),
                    array(
                        "name" => "Цвет в отчёте",
                        "id" => $shortname . "_store_9_color",
                        "type" => "color",
                    ),
                    array(
                        "name" => "До 3 дней",
                        "id" => $shortname . "_store_9_0-3",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 4 до 6 дней",
                        "id" => $shortname . "_store_9_4-6",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 7 до 10 дней",
                        "id" => $shortname . "_store_9_7",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 11 дней",
                        "id" => $shortname . "_store_9_10",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                ),
            ),
            array(
               "name" => "Коэффициенты 10 группы",
                "options" => array(
                    array(
                        "name" => "Название группы",
                        "id" => $shortname . "_store_10_name",
                        "type" => "text",
						"desc" => 'По умолчанию <strong>10 группа</strong>'
                    ),
                    array(
                        "name" => "Цвет в отчёте",
                        "id" => $shortname . "_store_10_color",
                        "type" => "color",
                    ),
                    array(
                        "name" => "До 3 дней",
                        "id" => $shortname . "_store_10_0-3",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 4 до 6 дней",
                        "id" => $shortname . "_store_10_4-6",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 7 до 10 дней",
                        "id" => $shortname . "_store_10_7",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 11 дней",
                        "id" => $shortname . "_store_10_10",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                ),
            ),
            array(
               "name" => "Коэффициенты 11 группы",
                "options" => array(
                    array(
                        "name" => "Название группы",
                        "id" => $shortname . "_store_11_name",
                        "type" => "text",
						"desc" => 'По умолчанию <strong>11 группа</strong>'
                    ),
                    array(
                        "name" => "Цвет в отчёте",
                        "id" => $shortname . "_store_11_color",
                        "type" => "color",
                    ),
                    array(
                        "name" => "До 3 дней",
                        "id" => $shortname . "_store_11_0-3",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 4 до 6 дней",
                        "id" => $shortname . "_store_11_4-6",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 7 до 10 дней",
                        "id" => $shortname . "_store_11_7",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 11 дней",
                        "id" => $shortname . "_store_11_10",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                ),
            ),
            array(
               "name" => "Коэффициенты 12 группы",
                "options" => array(
                    array(
                        "name" => "Название группы",
                        "id" => $shortname . "_store_12_name",
                        "type" => "text",
						"desc" => 'По умолчанию <strong>12 группа</strong>'
                    ),
                    array(
                        "name" => "Цвет в отчёте",
                        "id" => $shortname . "_store_12_color",
                        "type" => "color",
                    ),
                    array(
                        "name" => "До 3 дней",
                        "id" => $shortname . "_store_12_0-3",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 4 до 6 дней",
                        "id" => $shortname . "_store_12_4-6",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 7 до 10 дней",
                        "id" => $shortname . "_store_12_7",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                    array(
                        "name" => "От 11 дней",
                        "id" => $shortname . "_store_12_10",
                        "type" => "number_color",
						"min" => 0,
						"step" => 0.01
                    ),
                ),
            ),
            array(
               "name" => "Без наценки",
                "options" => array(
                    array(
                        "name" => "Название группы",
                        "id" => $shortname . "_store_0_name",
                        "type" => "text",
						"desc" => 'По умолчанию <strong>Без наценки</strong>'
                    ),
                    array(
                        "name" => "Цвет в отчёте",
                        "id" => $shortname . "_store_0_color",
                        "type" => "color",
                    ),
                ),
            ),
            array(
               "name" => "Конкуренты",
                "options" => array(
                    array(
                        "name" => "Конкурент 1",
                        "id" => $shortname . "_competitor_0",
                        "type" => "text",
                    ),
                    array(
                        "name" => "Конкурент 2",
                        "id" => $shortname . "_competitor_1",
                        "type" => "text",
                    ),
                    array(
                        "name" => "Конкурент 3",
                        "id" => $shortname . "_competitor_2",
                        "type" => "text",
                    ),
                    array(
                        "name" => "Конкурент 4",
                        "id" => $shortname . "_competitor_3",
                        "type" => "text",
                    ),
                    array(
                        "name" => "Конкурент 5",
                        "id" => $shortname . "_competitor_4",
                        "type" => "text",
                    ),
                ),
            ),
            array(
               "name" => "Другие настройки",
                "options" => array(
                    array(
                        "name" => "Email для уведомлений",
                        "id" => $shortname . "_store_email",
                        "type" => "text",
						"desc" => "На этот email будут приходить предложения от менеджеров по изменению состава товаров."
                    ),
                ),
            ),
        ),
    ),
);
