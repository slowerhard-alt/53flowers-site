<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Includes;

class CrmFields
{
    public $fields;

    public static $breakFields = [
        'lead' => [
            'STATUS_ID',
            'SOURCE_DESCRIPTION',
            'STATUS_DESCRIPTION',
            'STATUS_SEMANTIC_ID',
            'ADDRESS_COUNTRY_CODE',
            'ADDRESS_COUNTRY',
            'ADDRESS_LOC_ADDR_ID',
            'REG_ADDRESS_LOC_ADDR_ID',
            'ORIGINATOR_ID',
            'ORIGIN_ID',
            'OPPORTUNITY',
            'CURRENCY_ID',
            'COMPANY_ID',
            'CONTACT_ID',
            'CONTACT_IDS',
            'COMMENTS',
        ],
        'deal' => [
            'CURRENCY_ID',
            'STAGE_ID',
            'CATEGORY_ID',
            'STAGE_SEMANTIC_ID',
            'IS_NEW',
            'PROBABILITY',
            'OPPORTUNITY',
            'TAX_VALUE',
            'COMPANY_ID',
            'CONTACT_ID',
            'CONTACT_IDS',
            'BEGINDATE',
            'CLOSEDATE',
            'CLOSED',
            'ADDITIONAL_INFO',
            'LOCATION_ID',
            'ORIGINATOR_ID',
            'ORIGIN_ID',
            'IS_RECURRING',
            'IS_MANUAL_OPPORTUNITY',
            'IS_RETURN_CUSTOMER',
            'IS_REPEATED_APPROACH',
            'COMMENTS',
            'ADDRESS_LOC_ADDR_ID',
            'REG_ADDRESS_LOC_ADDR_ID',
        ],
        'contact' => [
            'COMMENTS',
            'CURRENCY_ID',
            'PHOTO',
            'ADDRESS_COUNTRY_CODE',
            'ADDRESS_COUNTRY',
            'ADDRESS_COUNTRY-11',
            'ADDRESS_LOC_ADDR_ID',
            'REG_ADDRESS_LOC_ADDR_ID',
            'EXPORT',
            'SOURCE_DESCRIPTION',
            'COMPANY_ID',
            'COMPANY_IDS',
            'ORIGINATOR_ID',
            'ORIGIN_ID',
            'ORIGIN_VERSION',
            'FACE_ID',
        ],
        'company' => [
            'ADDRESS_COUNTRY_CODE',
            'ADDRESS_COUNTRY',
            'ADDRESS_LEGAL',
            'CURRENCY_ID',
            'REG_ADDRESS',
            'REG_ADDRESS_2',
            'REG_ADDRESS_CITY',
            'REG_ADDRESS_POSTAL_CODE',
            'REG_ADDRESS_REGION',
            'REG_ADDRESS_PROVINCE',
            'REG_ADDRESS_COUNTRY',
            'REG_ADDRESS_COUNTRY_CODE',
            'ADDRESS_LOC_ADDR_ID',
            'REG_ADDRESS_LOC_ADDR_ID',
            'BANKING_DETAILS',
            'LOGO',
            'IS_MY_COMPANY',
            'ORIGINATOR_ID',
            'COMMENTS',
            'CONTACT_ID',
            'ORIGIN_ID',
            'ORIGIN_VERSION',
        ],
    ];

    public static $setFields = [
        'lead' => [
            'NAME' => 'billing_first_name',
            'LAST_NAME' => 'billing_last_name',
            'COMPANY_TITLE' => 'billing_company',
            'ADDRESS' => 'billing_address_1',
            'ADDRESS_2' => 'billing_address_2',
            'ADDRESS_CITY' => 'billing_city',
            'ADDRESS_PROVINCE' => 'billing_state',
            'ADDRESS_POSTAL_CODE' => 'billing_postcode',
            'PHONE' => 'billing_phone',
            'EMAIL' => 'billing_email',
            'UTM_SOURCE' => 'utm_source',
            'UTM_MEDIUM' => 'utm_medium',
            'UTM_CAMPAIGN' => 'utm_campaign',
            'UTM_CONTENT' => 'utm_content',
            'UTM_TERM' => 'utm_term',
        ],
        'deal' => [
            'UTM_SOURCE' => 'utm_source',
            'UTM_MEDIUM' => 'utm_medium',
            'UTM_CAMPAIGN' => 'utm_campaign',
            'UTM_CONTENT' => 'utm_content',
            'UTM_TERM' => 'utm_term',
        ],
        'contact' => [
            'NAME' => 'billing_first_name',
            'LAST_NAME' => 'billing_last_name',
            'ADDRESS' => 'billing_address_1',
            'ADDRESS_2' => 'billing_address_2',
            'ADDRESS_CITY' => 'billing_city',
            'ADDRESS_PROVINCE' => 'billing_state',
            'ADDRESS_POSTAL_CODE' => 'billing_postcode',
            'PHONE' => 'billing_phone',
            'EMAIL' => 'billing_email',
            'UTM_SOURCE' => 'utm_source',
            'UTM_MEDIUM' => 'utm_medium',
            'UTM_CAMPAIGN' => 'utm_campaign',
            'UTM_CONTENT' => 'utm_content',
            'UTM_TERM' => 'utm_term',
        ],
        'company' => [
            'TITLE' => 'billing_company',
            'ADDRESS' => 'billing_address_1',
            'ADDRESS_2' => 'billing_address_2',
            'ADDRESS_CITY' => 'billing_city',
            'ADDRESS_PROVINCE' => 'billing_state',
            'ADDRESS_POSTAL_CODE' => 'billing_postcode',
            'PHONE' => 'billing_phone',
            'EMAIL' => 'billing_email',
            'UTM_SOURCE' => 'utm_source',
            'UTM_MEDIUM' => 'utm_medium',
            'UTM_CAMPAIGN' => 'utm_campaign',
            'UTM_CONTENT' => 'utm_content',
            'UTM_TERM' => 'utm_term',
        ],
    ];

    public function __construct()
    {
        $this->fields = [
            'ADDRESS' => esc_html__('Street, building', 'wc-bitrix24-integration'),
            'ADDRESS-11' => esc_html__('Street, building', 'wc-bitrix24-integration'),
            'ADDRESS_2' => esc_html__('Suite / Apartment', 'wc-bitrix24-integration'),
            'ADDRESS_2-11' => esc_html__('Suite / Apartment', 'wc-bitrix24-integration'),
            'ADDRESS_CITY' => esc_html__('City', 'wc-bitrix24-integration'),
            'ADDRESS_CITY-11' => esc_html__('City', 'wc-bitrix24-integration'),
            'ADDRESS_POSTAL_CODE' => esc_html__('Zip', 'wc-bitrix24-integration'),
            'ADDRESS_POSTAL_CODE-11' => esc_html__('Zip', 'wc-bitrix24-integration'),
            'ADDRESS_REGION' => esc_html__('Region', 'wc-bitrix24-integration'),
            'ADDRESS_REGION-11' => esc_html__('Region', 'wc-bitrix24-integration'),
            'ADDRESS_PROVINCE' => esc_html__('State / Province', 'wc-bitrix24-integration'),
            'ADDRESS_PROVINCE-11' => esc_html__('State / Province', 'wc-bitrix24-integration'),
            'ADDRESS_COUNTRY' => esc_html__('Country', 'wc-bitrix24-integration'),
            'UTM_SOURCE' => 'UTM_SOURCE',
            'UTM_MEDIUM' => 'UTM_MEDIUM',
            'UTM_CAMPAIGN' => 'UTM_CAMPAIGN',
            'UTM_CONTENT' => 'UTM_CONTENT',
            'UTM_TERM' => 'UTM_TERM',
            'TITLE' => esc_html__('Title', 'wc-bitrix24-integration'),
            'STAGE_ID' => esc_html__('Stage / Pipeline', 'wc-bitrix24-integration'),
            'RESPONSIBLE_ID' => esc_html__('Responsible user ID', 'wc-bitrix24-integration'),
            'DESCRIPTION' => esc_html__('Description', 'wc-bitrix24-integration'),
            'TAGS' => esc_html__('Tags', 'wc-bitrix24-integration'),
            'DEADLINE' => esc_html__('Deadline (use the date field type)', 'wc-bitrix24-integration'),
            'ALLOW_CHANGE_DEADLINE' => esc_html__('Responsible person can change deadline', 'wc-bitrix24-integration'),
            'TASK_CONTROL' => esc_html__('Approve task when completed', 'wc-bitrix24-integration'),
            'ALLOW_TIME_TRACKING' => esc_html__('Task planned time', 'wc-bitrix24-integration'), ];
    }

    public static function mixedContactAddressFields($fields)
    {
        $addressFields = [];

        foreach ($fields as $key => $field) {
            if (in_array($key, ['ADDRESS', 'ADDRESS_2', 'ADDRESS_CITY', 'ADDRESS_POSTAL_CODE', 'ADDRESS_REGION', 'ADDRESS_PROVINCE', 'ADDRESS_COUNTRY'])) {
                $addressFields[$key] = $field;
            }
        }

        $mixedFields = [];

        foreach ($fields as $key => $field) {
            $mixedFields[$key] = $field;

            if ($key == 'ADDRESS_COUNTRY') {
                foreach ($addressFields as $keyAddressField => $addressField) {
                    $mixedFields[$keyAddressField . '-11'] = $addressField;
                }
            }
        }

        return $mixedFields;
    }
}
