<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Includes;

class Crm
{
    public static $scope = [
        'crm',
    ];

    public static function send($sendFields, $crmFields, $currentType = 'lead', $order = null)
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY, []);
        $webhook = $settings['webhook'];
        $startLink = explode('rest', $webhook);

        $preparedFields = self::prepareFields($crmFields[$currentType], $sendFields[$currentType]);

        if (empty($sendFields['is_deal_update_request']) && empty($preparedFields)) {
            return [];
        }

        $result = [];
        $sendType = $settings['type'];

        if ($currentType == 'contact') {
            $sendType = $currentType;
        }

        /**
         * Filters the value of the resulting send type.
         *
         * @since 1.65.0
         *
         * @param string         $sendType
         * @param array          $sendFields
         * @param null|\WC_Order $order
         */
        $sendType = \apply_filters('itglx/wc/bx24/send-type', $sendType, $sendFields, $order);

        switch ($sendType) {
            case 'lead':
                if (!empty($preparedFields['ASSIGNED_BY_ID'])) {
                    $preparedFields['ASSIGNED_BY_ID'] = Helper::resolveNextResponsible(
                        $preparedFields['ASSIGNED_BY_ID']
                    );
                }

                if (!empty($sendFields['lead']['link_with_exists_contact'])) {
                    $findData = [
                        'contact' => [
                            'EMAIL' => isset($sendFields['lead']['EMAIL']) ? $sendFields['lead']['EMAIL'] : '',
                            'PHONE' => isset($sendFields['lead']['PHONE']) ? $sendFields['lead']['PHONE'] : '',
                        ],
                    ];

                    $contactID = self::findItemByField($findData, 'contact', 'PHONE');

                    if (!$contactID) {
                        $contactID = self::findItemByField($findData, 'contact', 'EMAIL');
                    }

                    // Set contact for lead
                    if ($contactID) {
                        $preparedFields['CONTACT_ID'] = $contactID;
                    }
                }

                $result = self::sendApiRequest('crm.lead.add', false, ['fields' => $preparedFields]);

                if (
                    empty($settings['do_not_create_bitrix24_notify'])
                    && isset($result[0])
                    && is_numeric($result[0])
                    && $startLink[0]
                ) {
                    $userNotify = esc_html__('New lead', 'wc-bitrix24-integration')
                        . ' [b]#'
                        . $result[0]
                        . '[/b] [url='
                        . $startLink[0] . 'crm/lead/details/' . $result[0] . '/'
                        . ']'
                        . $preparedFields['TITLE']
                        . '[/url] '
                        . esc_html__('from the site', 'wc-bitrix24-integration')
                        . ' '
                        . get_home_url();

                    self::sendApiRequest(
                        'im.notify',
                        false,
                        [
                            'to' => !empty($preparedFields['ASSIGNED_BY_ID']) ? $preparedFields['ASSIGNED_BY_ID'] : 1,
                            'message' => $userNotify,
                            'type' => 'SYSTEM',
                        ]
                    );
                }
                break;
            case 'dealcontact_alwayscreatenew':
                if (!empty($preparedFields['ASSIGNED_BY_ID'])) {
                    $preparedFields['ASSIGNED_BY_ID'] = Helper::resolveNextResponsible(
                        $preparedFields['ASSIGNED_BY_ID']
                    );
                }

                // Create contact
                if (!empty($sendFields['contact'])) {
                    $contactID = false;

                    $preparedFieldsContact = self::prepareFields($crmFields['contact'], $sendFields['contact']);

                    if ($preparedFieldsContact) {
                        if (
                            !empty($preparedFields['ASSIGNED_BY_ID'])
                            && !empty($settings['contact_responsible_by_deal'])
                        ) {
                            $preparedFieldsContact['ASSIGNED_BY_ID'] = $preparedFields['ASSIGNED_BY_ID'];
                        }

                        $result = self::sendApiRequest(
                            'crm.contact.add',
                            false,
                            ['fields' => $preparedFieldsContact]
                        );

                        if ($result) {
                            $contactID = $result[0];
                        }
                    }

                    // Set contact for deal
                    if ($contactID) {
                        $preparedFields['CONTACT_ID'] = $contactID;
                    }
                }

                if (!empty($preparedFields['CONTACT_ID'])) {
                    self::requisiteFill(
                        $preparedFields['CONTACT_ID'],
                        3, // contact
                        $sendFields['contact'],
                        !empty($sendFields['contact']['NAME']) ? $sendFields['contact']['NAME'] : '#'
                    );
                }

                if (!empty($sendFields['is_deal_update_request'])) {
                    return [
                        'CONTACT_ID' => !empty($preparedFields['CONTACT_ID']) ? $preparedFields['CONTACT_ID'] : 0,
                    ];
                }

                // Pipeline support
                $isPipelineStatus = explode(':', $preparedFields['STAGE_ID']);

                if (count($isPipelineStatus) == 2) {
                    $preparedFields['CATEGORY_ID'] = str_replace('C', '', $isPipelineStatus[0]);
                } else {
                    $isPipelineStatus = explode('||||', $preparedFields['STAGE_ID']);

                    if (count($isPipelineStatus) == 2) {
                        $preparedFields['CATEGORY_ID'] = $isPipelineStatus[0];
                        $preparedFields['STAGE_ID'] = $isPipelineStatus[1];
                    }
                }
                // Pipeline support

                $result = self::sendApiRequest('crm.deal.add', false, ['fields' => $preparedFields]);

                if (
                    empty($settings['do_not_create_bitrix24_notify'])
                    && isset($result[0])
                    && is_numeric($result[0])
                    && $startLink[0]
                ) {
                    $userNotify = esc_html__('New deal', 'wc-bitrix24-integration')
                        . ' [b]#'
                        . $result[0]
                        . '[/b] [url='
                        . $startLink[0] . 'crm/deal/details/' . $result[0] . '/'
                        . ']'
                        . $preparedFields['TITLE']
                        . '[/url] '
                        . esc_html__('from the site', 'wc-bitrix24-integration')
                        . ' '
                        . get_home_url();

                    self::sendApiRequest(
                        'im.notify',
                        false,
                        [
                            'to' => !empty($preparedFields['ASSIGNED_BY_ID']) ? $preparedFields['ASSIGNED_BY_ID'] : 1,
                            'message' => $userNotify,
                            'type' => 'SYSTEM',
                        ]
                    );
                }
                break;
            case 'deal':
            case 'dealcontact':
            case 'dealcompany':
                $dealResponsible = false;

                if (!empty($preparedFields['ASSIGNED_BY_ID'])) {
                    $dealResponsible = Helper::resolveNextResponsible($preparedFields['ASSIGNED_BY_ID']);
                    $preparedFields['ASSIGNED_BY_ID'] = $dealResponsible;
                }

                // find/create/update company
                if (in_array($sendType, ['deal', 'dealcompany']) && !empty($sendFields['company'])) {
                    $companyID = self::companyProcessing($crmFields, $sendFields, $dealResponsible);

                    // Set company for deal
                    if ($companyID) {
                        $preparedFields['COMPANY_ID'] = $companyID;

                        if ($sendType === 'dealcompany') {
                            self::requisiteFill(
                                $companyID,
                                4, // company
                                $sendFields['company'],
                                !empty($sendFields['company']['TITLE']) ? $sendFields['company']['TITLE'] : '#'
                            );
                        }
                    }
                }

                // Find or create contact
                if (!in_array($sendType, ['dealcompany']) && !empty($sendFields['contact'])) {
                    $contactID = false;

                    if (!empty($sendFields['customer_id'])) {
                        $contactID = get_user_meta($sendFields['customer_id'], '_bitrix24_contact_id', true);

                        if ($contactID) {
                            $existContact = self::sendApiRequest(
                                'crm.contact.get',
                                false,
                                [
                                    'id' => $contactID,
                                ]
                            );

                            if (!$existContact) {
                                $contactID = false;
                                update_user_meta($sendFields['customer_id'], '_bitrix24_contact_id', '');
                            }
                        }
                    }

                    if (!$contactID) {
                        $contactID = self::findItemByField($sendFields, 'contact', 'PHONE');
                    }

                    if (!$contactID) {
                        $contactID = self::findItemByField($sendFields, 'contact', 'EMAIL');
                    }

                    if ($contactID && isset($sendFields['contact_update_exists'])) {
                        $existContact = self::sendApiRequest(
                            'crm.contact.get',
                            false,
                            [
                                'id' => $contactID,
                            ]
                        );

                        if ($existContact) {
                            $fieldsUpdateContact = self::prepareFieldsToUpdate($crmFields['contact'], $sendFields['contact']);
                            // fix duplicate phone
                            if (!empty($existContact['PHONE'])
                                && !empty($fieldsUpdateContact['PHONE'])
                                && self::existEmailPhone($existContact['PHONE'], $fieldsUpdateContact['PHONE'][0]['VALUE'])
                            ) {
                                unset($fieldsUpdateContact['PHONE']);
                            }

                            // fix duplicate email
                            if (!empty($existContact['EMAIL'])
                                && !empty($fieldsUpdateContact['EMAIL'])
                                && self::existEmailPhone($existContact['EMAIL'], $fieldsUpdateContact['EMAIL'][0]['VALUE'])
                            ) {
                                unset($fieldsUpdateContact['EMAIL']);
                            }

                            self::sendApiRequest(
                                'crm.contact.update',
                                false,
                                [
                                    'id' => $contactID,
                                    'fields' => $fieldsUpdateContact,
                                ]
                            );
                        }
                    }

                    if (!$contactID) {
                        $preparedFieldsContact = self::prepareFields($crmFields['contact'], $sendFields['contact']);

                        if ($preparedFieldsContact) {
                            if (isset($companyID) && $companyID) {
                                $preparedFieldsContact['COMPANY_ID'] = $companyID;
                            }

                            if (!empty($dealResponsible) && !empty($settings['contact_responsible_by_deal'])) {
                                $preparedFieldsContact['ASSIGNED_BY_ID'] = $dealResponsible;
                            }

                            $result = self::sendApiRequest(
                                'crm.contact.add',
                                false,
                                ['fields' => $preparedFieldsContact]
                            );

                            if ($result) {
                                $contactID = $result[0];
                            }
                        }
                    }

                    // Set contact for deal
                    if ($contactID) {
                        $preparedFields['CONTACT_ID'] = $contactID;

                        // save contact to customer
                        if (
                            !empty($sendFields['customer_id'])
                            && !get_user_meta($sendFields['customer_id'], '_bitrix24_contact_id', true)
                        ) {
                            update_user_meta($sendFields['customer_id'], '_bitrix24_contact_id', $contactID);
                        }
                    }
                }

                if (!empty($preparedFields['CONTACT_ID'])) {
                    self::requisiteFill(
                        $preparedFields['CONTACT_ID'],
                        3, // contact
                        $sendFields['contact'],
                        !empty($sendFields['contact']['NAME']) ? $sendFields['contact']['NAME'] : '#'
                    );
                }

                if (!empty($sendFields['is_deal_update_request'])) {
                    return [
                        'CONTACT_ID' => !empty($preparedFields['CONTACT_ID']) ? $preparedFields['CONTACT_ID'] : 0,
                        'COMPANY_ID' => !empty($preparedFields['COMPANY_ID']) ? $preparedFields['COMPANY_ID'] : 0,
                    ];
                }

                // Pipeline support
                $isPipelineStatus = explode(':', $preparedFields['STAGE_ID']);

                if (count($isPipelineStatus) == 2) {
                    $preparedFields['CATEGORY_ID'] = str_replace('C', '', $isPipelineStatus[0]);
                } else {
                    $isPipelineStatus = explode('||||', $preparedFields['STAGE_ID']);

                    if (count($isPipelineStatus) == 2) {
                        $preparedFields['CATEGORY_ID'] = $isPipelineStatus[0];
                        $preparedFields['STAGE_ID'] = $isPipelineStatus[1];
                    }
                }
                // Pipeline support

                $result = self::sendApiRequest('crm.deal.add', false, ['fields' => $preparedFields]);

                if (
                    empty($settings['do_not_create_bitrix24_notify'])
                    && isset($result[0])
                    && is_numeric($result[0])
                    && $startLink[0]
                ) {
                    $userNotify = esc_html__('New deal', 'wc-bitrix24-integration')
                        . ' [b]#'
                        . $result[0]
                        . '[/b] [url='
                        . $startLink[0] . 'crm/deal/details/' . $result[0] . '/'
                        . ']'
                        . $preparedFields['TITLE']
                        . '[/url] '
                        . esc_html__('from the site', 'wc-bitrix24-integration')
                        . ' '
                        . get_home_url();

                    self::sendApiRequest(
                        'im.notify',
                        false,
                        [
                            'to' => !empty($preparedFields['ASSIGNED_BY_ID']) ? $preparedFields['ASSIGNED_BY_ID'] : 1,
                            'message' => $userNotify,
                            'type' => 'SYSTEM',
                        ]
                    );
                }
                break;
            case 'contact':
                $contactID = false;

                if (!empty($sendFields['customer_id'])) {
                    $contactID = get_user_meta($sendFields['customer_id'], '_bitrix24_contact_id', true);
                }

                if (!$contactID) {
                    $contactID = self::findItemByField($sendFields, 'contact', 'PHONE');
                }

                if (!$contactID) {
                    $contactID = self::findItemByField($sendFields, 'contact', 'EMAIL');
                }

                if (!$contactID) {
                    $resultContact = self::sendApiRequest('crm.contact.add', false, ['fields' => $preparedFields]);

                    if (isset($resultContact[0]) && is_numeric($resultContact[0])) {
                        // save contact to customer
                        if (
                            !empty($sendFields['customer_id'])
                            && !get_user_meta($sendFields['customer_id'], '_bitrix24_contact_id', true)
                        ) {
                            update_user_meta($sendFields['customer_id'], '_bitrix24_contact_id', $resultContact[0]);
                        }

                        self::requisiteFill(
                            $resultContact[0],
                            3, // contact
                            $sendFields['contact'],
                            !empty($sendFields['contact']['NAME']) ? $sendFields['contact']['NAME'] : '#'
                        );
                    }
                } else {
                    if (empty($sendFields['customer_send']) && !isset($sendFields['contact_update_exists'])) {
                        return $contactID;
                    }

                    $existContact = self::sendApiRequest('crm.contact.get', false, ['id' => $contactID]);

                    if ($existContact) {
                        $fieldsUpdateContact = self::prepareFieldsToUpdate($crmFields['contact'], $sendFields['contact']);
                        // fix duplicate phone
                        if (!empty($existContact['PHONE'])
                            && !empty($fieldsUpdateContact['PHONE'])
                            && self::existEmailPhone($existContact['PHONE'], $fieldsUpdateContact['PHONE'][0]['VALUE'])
                        ) {
                            unset($fieldsUpdateContact['PHONE']);
                        }

                        // fix duplicate email
                        if (!empty($existContact['EMAIL'])
                            && !empty($fieldsUpdateContact['EMAIL'])
                            && self::existEmailPhone($existContact['EMAIL'], $fieldsUpdateContact['EMAIL'][0]['VALUE'])
                        ) {
                            unset($fieldsUpdateContact['EMAIL']);
                        }

                        $result = self::sendApiRequest(
                            'crm.contact.update',
                            false,
                            [
                                'id' => $contactID,
                                'fields' => $fieldsUpdateContact,
                            ]
                        );
                    } else {
                        $result = $contactID;
                    }
                }
                break;
            default:
                // Nothing
                break;
        }

        return $result;
    }

    public static function checkConnection()
    {
        $apiResponse = self::sendApiRequest('scope', true);

        if ($apiResponse && $apiResponse != self::$scope) {
            $errorScope = false;

            foreach (self::$scope as $scope) {
                if (!in_array($scope, $apiResponse)) {
                    $errorScope = true;
                }
            }

            if ($errorScope) {
                $setting = (array) get_option(Bootstrap::OPTIONS_KEY);
                $setting['webhook'] = '';

                update_option(Bootstrap::OPTIONS_KEY, $setting);

                return 1;
            }
        }

        if (empty($apiResponse)) {
            $setting = (array) get_option(Bootstrap::OPTIONS_KEY);
            $setting['webhook'] = '';

            update_option(Bootstrap::OPTIONS_KEY, $setting);

            return 2;
        }

        return 3;
    }

    /**
     * @param array     $crmFields
     * @param array     $sendFields
     * @param false|int $dealResponsible
     *
     * @return false|int
     */
    public static function companyProcessing($crmFields, $sendFields, $dealResponsible)
    {
        $companyID = self::findItemByField($sendFields, 'company', 'PHONE');

        if (!$companyID) {
            $companyID = self::findItemByField($sendFields, 'company', 'EMAIL');
        }

        $settings = \get_option(Bootstrap::OPTIONS_KEY, []);

        // if company exists and update not enabled
        if ($companyID && empty($settings['company_update_exists'])) {
            return $companyID;
        }

        // if company not exists
        if (!$companyID) {
            $preparedFieldsCompany = self::prepareFields($crmFields['company'], $sendFields['company']);

            if (empty($preparedFieldsCompany)) {
                return false;
            }

            if (!empty($dealResponsible) && !empty($settings['company_responsible_by_deal'])) {
                $preparedFieldsCompany['ASSIGNED_BY_ID'] = $dealResponsible;
            }

            $result = self::sendApiRequest('crm.company.add', false, ['fields' => $preparedFieldsCompany]);

            return $result ? $result[0] : false;
        }

        $existCompany = self::sendApiRequest('crm.company.get', false, ['id' => $companyID]);

        if (empty($existCompany)) {
            return $companyID;
        }

        $fieldsUpdateCompany = self::prepareFieldsToUpdate($crmFields['company'], $sendFields['company']);

        // fix duplicate phone
        if (
            !empty($existCompany['PHONE'])
            && !empty($fieldsUpdateCompany['PHONE'])
            && self::existEmailPhone($existCompany['PHONE'], $fieldsUpdateCompany['PHONE'][0]['VALUE'])
        ) {
            unset($fieldsUpdateCompany['PHONE']);
        }

        // fix duplicate email
        if (
            !empty($existCompany['EMAIL'])
            && !empty($fieldsUpdateCompany['EMAIL'])
            && self::existEmailPhone($existCompany['EMAIL'], $fieldsUpdateCompany['EMAIL'][0]['VALUE'])
        ) {
            unset($fieldsUpdateCompany['EMAIL']);
        }

        self::sendApiRequest(
            'crm.company.update',
            false,
            [
                'id' => $companyID,
                'fields' => $fieldsUpdateCompany,
            ]
        );

        return $companyID;
    }

    public static function updateInformation()
    {
        self::updateFieldsList('crm.lead.fields', Bootstrap::LEAD_FIELDS_KEY);

        self::updateFieldsList('crm.deal.fields', Bootstrap::DEAL_FIELDS_KEY);
        self::updateFieldsList('crm.dealcategory.list', Bootstrap::DEAL_CATEGORY_LIST_KEY);

        self::updateFieldsList('crm.contact.fields', Bootstrap::CONTACT_FIELDS_KEY);
        self::updateFieldsList('crm.company.fields', Bootstrap::COMPANY_FIELDS_KEY);

        self::updateFieldsList('crm.status.list', Bootstrap::STATUS_LIST_KEY);
    }

    public static function updateFieldsList($method, $optionKey)
    {
        $apiResponse = self::sendApiRequest($method, true);

        if ($apiResponse) {
            update_option($optionKey, $apiResponse);
        }
    }

    public static function findItemByField($sendFields, $type, $field)
    {
        if (!empty($sendFields[$type][$field])) {
            $findParams = [
                'type' => $field,
                'entity_type' => $type,
                'values' => [$sendFields[$type][$field]],
            ];

            // https://dev.1c-bitrix.ru/rest_help/crm/auxiliary/duplicates/crm_duplicate_findbycomm.php
            $findItem = self::sendApiRequest('crm.duplicate.findbycomm', false, $findParams);

            if (!empty($findItem)) {
                $ids = current($findItem);

                return $ids[0];
            }
        }

        return false;
    }

    public static function existEmailPhone($values, $currentValue)
    {
        foreach ($values as $field) {
            if ($field['VALUE'] === $currentValue) {
                return true;
            }
        }

        return false;
    }

    public static function prepareFields($crmFields, $sendFields)
    {
        foreach ($crmFields as $key => $field) {
            if (empty($sendFields[$key])) {
                if (isset($sendFields[$key])) {
                    unset($sendFields[$key]);
                }

                continue;
            }

            if (in_array($key, ['PHONE', 'EMAIL', 'WEB'])) {
                $sendFields[$key] = [
                    [
                        'VALUE' => $sendFields[$key],
                        'VALUE_TYPE' => 'WORK',
                    ],
                ];
            }

            // Prepare and populate value to list field
            if ($field['type'] === 'enumeration' && !empty($field['items'])) {
                $explodedField = !is_array($sendFields[$key]) ? explode(',', $sendFields[$key]) : $sendFields[$key];
                $resolveValues = self::resolveSelectMultiSelectValues($field, $explodedField);

                if (!empty($resolveValues)) {
                    $sendFields[$key] = $field['isMultiple'] ? $resolveValues : current($resolveValues);
                } else {
                    unset($sendFields[$key]);
                }

                continue;
            }

            if (in_array($field['type'], ['string', 'url'], true) && $field['isMultiple']) {
                $sendFields[$key] = !is_array($sendFields[$key]) ? [$sendFields[$key]] : $sendFields[$key];
            }
        }

        if (!empty($sendFields['COMMENTS'])) {
            $sendFields['COMMENTS'] = str_replace("\n", '<br>', $sendFields['COMMENTS']);
        }

        return $sendFields;
    }

    public static function prepareFieldsToUpdate($crmFields, $sendFields)
    {
        foreach ($crmFields as $key => $field) {
            if (!isset($sendFields[$key])) {
                continue;
            }

            if ($sendFields[$key] === '') {
                unset($sendFields[$key]);

                continue;
            }

            if (in_array($key, ['PHONE', 'EMAIL', 'WEB']) && !empty($sendFields[$key])) {
                $sendFields[$key] = [
                    [
                        'VALUE' => $sendFields[$key],
                        'VALUE_TYPE' => 'WORK',
                    ],
                ];
            }

            // Prepare and populate value to list field
            if ($field['type'] === 'enumeration'
                && !empty($sendFields[$key])
                && !empty($field['items'])
            ) {
                $explodedField = !is_array($sendFields[$key]) ? explode(',', $sendFields[$key]) : $sendFields[$key];
                $resolveValues = self::resolveSelectMultiSelectValues($field, $explodedField);

                if (empty($resolveValues)) {
                    unset($sendFields[$key]);
                } else {
                    $sendFields[$key] = $field['isMultiple'] ? $resolveValues : current($resolveValues);
                }
            }

            if (in_array($field['type'], ['string', 'url'], true) && $field['isMultiple']) {
                $sendFields[$key] = !is_array($sendFields[$key]) ? [$sendFields[$key]] : $sendFields[$key];
            }
        }

        if (!empty($sendFields['COMMENTS'])) {
            $sendFields['COMMENTS'] = str_replace(
                "\n",
                '<br>',
                $sendFields['COMMENTS']
            );
        }

        return $sendFields;
    }

    /**
     * @param int    $entityID
     * @param string $entityType
     * @param string $message
     *
     * @see https://dev.1c-bitrix.ru/rest_help/crm/stream/livefeedmessage_add.php
     *
     * @return void
     */
    public static function sendTimelineComment($entityID, $entityType, $message)
    {
        self::sendApiRequest(
            'crm.timeline.comment.add',
            false,
            [
                'fields' => [
                    'COMMENT' => $message,
                    'ENTITY_TYPE' => $entityType,
                    'ENTITY_ID' => $entityID,
                ],
            ]
        );
    }

    public static function sendApiRequest($method, $showError = false, $fields = [], $ignoreLog = false)
    {
        // prevent too frequent requests
        usleep(200000);

        $settings = \get_option(Bootstrap::OPTIONS_KEY, []);

        $webhook = $settings['webhook'];

        /**
         * Filters the dataset that will be sent in the request body to API.
         *
         * @since 1.61.0
         *
         * @param array $fields Current array of send fields.
         */
        $fields = \apply_filters('itglx/wc/bx24/body-' . $method, $fields);

        if (!$ignoreLog) {
            Helper::log($method, self::clearFileDataBeforeLog($fields));
        }

        try {
            $response = wp_remote_post(
                $webhook . $method,
                [
                    'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36',
                    'body' => $fields,
                    'timeout' => 20,
                ]
            );

            if (is_wp_error($response)) {
                throw new \Exception(
                    $response->get_error_message(),
                    (int) $response->get_error_code()
                );
            }

            $body = $response['body'];

            if (!empty($body)) {
                $result = json_decode(str_replace('\'', '"', $body), true);

                if (!$ignoreLog) {
                    Helper::log('bitrix decode response', $result);
                }

                if (isset($result['result'])) {
                    return (array) $result['result'];
                }

                if (!empty($result['error'])) {
                    if ($showError) {
                        throw new \Exception(
                            isset($result['error_message'])
                                ? esc_html($result['error_message'])
                                : esc_html($result['error_description']),
                            (int) $result['error']
                        );
                    }
                }
            }

            Helper::log('bitrix empty response', $response, 'warning');
        } catch (\Exception $error) {
            Helper::log($error->getCode() . ': ' . $error->getMessage(), [], 'error');

            if ($showError) {
                printf(
                    '<div data-ui-component="wcbitrix24notice" class="error notice notice-error">'
                    . '<p><strong>Error (%s)</strong>: %s</p></div>',
                    esc_html($error->getCode()),
                    esc_html($error->getMessage())
                );
            }
        }

        return [];
    }

    /**
     * @param int    $entityId
     * @param int    $entityType
     * @param array  $entityData
     * @param string $name
     *
     * @see https://dev.1c-bitrix.ru/rest_help/crm/requisite/methods/crm_requisite_add.php
     *
     * @return void
     */
    private static function requisiteFill($entityId, $entityType, $entityData, $name)
    {
        $result = self::sendApiRequest(
            'crm.requisite.list',
            false,
            [
                'filter' => [
                    'ENTITY_TYPE_ID' => (int) $entityType,
                    'ENTITY_ID' => (int) $entityId,
                ],
            ]
        );

        // if not filled, make filling
        if (!empty($result)) {
            // 11, shipping address - https://dev.1c-bitrix.ru/rest_help/crm/auxiliary/enum/crm_enum_addresstype.php
            if (!empty($entityData['ADDRESS-11']) || !empty($entityData['ADDRESS_2-11' || !empty($entityData['ADDRESS_CITY-11'])])) {
                $address = self::sendApiRequest(
                    'crm.address.list',
                    false,
                    [
                        'filter' => [
                            'TYPE_ID' => 11,
                            'ENTITY_TYPE_ID' => 8,
                            'ENTITY_ID' => (int) $result[0]['ID'],
                        ],
                    ]
                );

                if (empty($address)) {
                    self::sendApiRequest(
                        'crm.address.add',
                        false,
                        [
                            'fields' => [
                                'TYPE_ID' => 11, // 11, shipping address - https://dev.1c-bitrix.ru/rest_help/crm/auxiliary/enum/crm_enum_addresstype.php
                                'ENTITY_TYPE_ID' => 8, // requisite
                                'ENTITY_ID' => (int) $result[0]['ID'], // entity_id = requisite_id
                                'ADDRESS_1' => isset($entityData['ADDRESS-11']) ? $entityData['ADDRESS-11'] : '',
                                'ADDRESS_2' => isset($entityData['ADDRESS_2-11']) ? $entityData['ADDRESS_2-11'] : '',
                                'CITY' => isset($entityData['ADDRESS_CITY-11']) ? $entityData['ADDRESS_CITY-11'] : '',
                                'POSTAL_CODE' => isset($entityData['ADDRESS_POSTAL_CODE-11']) ? $entityData['ADDRESS_POSTAL_CODE-11'] : '',
                                'REGION' => isset($entityData['ADDRESS_REGION-11']) ? $entityData['ADDRESS_REGION-11'] : '',
                                'PROVINCE' => isset($entityData['ADDRESS_PROVINCE-11']) ? $entityData['ADDRESS_PROVINCE-11'] : '',
                                'COUNTRY' => isset($entityData['ADDRESS_COUNTRY-11']) ? $entityData['ADDRESS_COUNTRY-11'] : '',
                            ],
                        ]
                    );
                }
            }

            return;
        }

        // create a set of requisite
        $requisite = self::sendApiRequest(
            'crm.requisite.add',
            false,
            [
                'fields' => [
                    'ACTIVE' => 'Y',
                    'ENTITY_TYPE_ID' => (int) $entityType,
                    'ENTITY_ID' => (int) $entityId,
                    'NAME' => $name,
                    'PRESET_ID' => 1,
                ],
            ]
        );

        // create the given addresses for the created requisite
        self::sendApiRequest(
            'crm.address.add',
            false,
            [
                'fields' => [
                    'TYPE_ID' => 1,
                    'ENTITY_TYPE_ID' => 8, // requisite
                    'ENTITY_ID' => $requisite[0], // entity_id = requisite_id
                    'ADDRESS_1' => isset($entityData['ADDRESS']) ? $entityData['ADDRESS'] : '',
                    'ADDRESS_2' => isset($entityData['ADDRESS_2']) ? $entityData['ADDRESS_2'] : '',
                    'CITY' => isset($entityData['ADDRESS_CITY']) ? $entityData['ADDRESS_CITY'] : '',
                    'POSTAL_CODE' => isset($entityData['ADDRESS_POSTAL_CODE']) ? $entityData['ADDRESS_POSTAL_CODE'] : '',
                    'REGION' => isset($entityData['ADDRESS_REGION']) ? $entityData['ADDRESS_REGION'] : '',
                    'PROVINCE' => isset($entityData['ADDRESS_PROVINCE']) ? $entityData['ADDRESS_PROVINCE'] : '',
                    'COUNTRY' => isset($entityData['ADDRESS_COUNTRY']) ? $entityData['ADDRESS_COUNTRY'] : '',
                ],
            ]
        );

        // 11, shipping address - https://dev.1c-bitrix.ru/rest_help/crm/auxiliary/enum/crm_enum_addresstype.php
        if (!empty($entityData['ADDRESS-11']) || !empty($entityData['ADDRESS_2-11' || !empty($entityData['ADDRESS_CITY-11'])])) {
            self::sendApiRequest(
                'crm.address.add',
                false,
                [
                    'fields' => [
                        'TYPE_ID' => 11, // 11, shipping address - https://dev.1c-bitrix.ru/rest_help/crm/auxiliary/enum/crm_enum_addresstype.php
                        'ENTITY_TYPE_ID' => 8, // requisite
                        'ENTITY_ID' => $requisite[0], // entity_id = requisite_id
                        'ADDRESS_1' => isset($entityData['ADDRESS-11']) ? $entityData['ADDRESS-11'] : '',
                        'ADDRESS_2' => isset($entityData['ADDRESS_2-11']) ? $entityData['ADDRESS_2-11'] : '',
                        'CITY' => isset($entityData['ADDRESS_CITY-11']) ? $entityData['ADDRESS_CITY-11'] : '',
                        'POSTAL_CODE' => isset($entityData['ADDRESS_POSTAL_CODE-11']) ? $entityData['ADDRESS_POSTAL_CODE-11'] : '',
                        'REGION' => isset($entityData['ADDRESS_REGION-11']) ? $entityData['ADDRESS_REGION-11'] : '',
                        'PROVINCE' => isset($entityData['ADDRESS_PROVINCE-11']) ? $entityData['ADDRESS_PROVINCE-11'] : '',
                        'COUNTRY' => isset($entityData['ADDRESS_COUNTRY-11']) ? $entityData['ADDRESS_COUNTRY-11'] : '',
                    ],
                ]
            );
        }
    }

    private static function resolveSelectMultiSelectValues($field, $explodedField)
    {
        $resolveValues = [];
        $explodedField = array_map('trim', $explodedField);

        $ids = \array_column($field['items'], 'ID');
        $values = \array_column($field['items'], 'VALUE');

        foreach ($explodedField as $explodeValue) {
            if (array_search($explodeValue, $ids) !== false) {
                $resolveValues[] = $explodeValue;
            } elseif (array_search($explodeValue, $values) !== false) {
                $resolveValues[] = $ids[array_search($explodeValue, $values)];
            }
        }

        return $resolveValues;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private static function clearFileDataBeforeLog(array $data): array
    {
        if (!isset($data['fields'])) {
            return $data;
        }

        if (!empty($data['fields']['FILES'])) {
            $data['fields']['FILES'] = 'file data';
        }

        foreach ($data['fields'] as $key => $field) {
            if (!is_array($field)) {
                continue;
            }

            if (isset($field['fileData'])) {
                $field['fileData'][1] = 'content';

                $data['fields'][$key] = $field;

                continue;
            }

            foreach ($field as $subkey => $value) {
                if (!is_array($value) || !isset($value['fileData'])) {
                    continue;
                }

                $value['fileData'][1] = 'content';

                $data['fields'][$key][$subkey] = $value;
            }
        }

        return $data;
    }
}
