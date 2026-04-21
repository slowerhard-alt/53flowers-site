<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Admin\PageParts;

use Itgalaxy\Wc\Bitrix24\Integration\Includes\Bootstrap;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\CrmFields;

class ContactSettings
{
    public static function render($meta)
    {
        $renderFields = new RenderFields('contact');
        $renderFields->startTable();

        $currentValues = $meta['contact'] ?? [];
        $contactFields = CrmFields::mixedContactAddressFields(get_option(Bootstrap::CONTACT_FIELDS_KEY, []));

        foreach ($contactFields as $key => $field) {
            // Not show fields
            if (in_array($key, CrmFields::$breakFields['contact'])) {
                continue;
            }

            // Not show read only fields
            if ($field['isReadOnly'] === true) {
                continue;
            }

            $title = RenderFields::resolveFieldTitle($key, $field);

            if ($key === 'ADDRESS') {
                echo '<tr><td colspan="2"><hr>' . esc_html__('Main address', 'wc-bitrix24-integration') . '</td></tr>';
            } elseif ($key === 'ADDRESS-11') {
                echo '<tr><td colspan="2"><hr>' . esc_html__('Delivery address', 'wc-bitrix24-integration') . '</td></tr>';
            }

            $renderFields->startFieldRow($title, $field['isRequired']);

            $currentValue = $currentValues[$key] ?? '';
            $currentValuePopulate = $currentValues[$key . '-populate'] ?? '';

            if ($field['type'] === 'enumeration' && !empty($field['items'])) {
                $selectItems = [];

                foreach ($field['items'] as $item) {
                    $selectItems[$item['ID']] = $item['VALUE'];
                }

                $renderFields->selectField(
                    $selectItems,
                    $key,
                    $title,
                    $currentValue,
                    $currentValuePopulate
                );
            } elseif ($field['type'] === 'char' || $field['type'] === 'boolean') {
                $renderFields->inputCheckboxField(
                    $key,
                    $title,
                    $currentValue
                );
            } elseif ($key === 'TYPE_ID') {
                $renderFields->statusField(
                    'CONTACT_TYPE',
                    $key,
                    $title,
                    $currentValue
                );
            } elseif ($key === 'SOURCE_ID') {
                $renderFields->statusField(
                    'SOURCE',
                    $key,
                    $title,
                    $currentValue
                );
            } elseif (in_array($key, ['COMMENTS', 'SOURCE_DESCRIPTION', 'STATUS_DESCRIPTION'])) {
                $renderFields->textareaField(
                    $key,
                    $title,
                    $currentValue
                );
            } else {
                $default = CrmFields::$setFields['contact'][$key] ?? '';

                $renderFields->checkoutFieldsSelect(
                    $key,
                    $title,
                    $currentValue ?: $default
                );
            }

            $renderFields->endFieldRow();

            if ($key === 'ADDRESS_PROVINCE') {
                echo '<tr><td colspan="2">' . esc_html__('Main address', 'wc-bitrix24-integration') . '<hr></td></tr>';
            } elseif ($key === 'ADDRESS_PROVINCE-11') {
                echo '<tr><td colspan="2">' . esc_html__('Delivery address', 'wc-bitrix24-integration') . '<hr></td></tr>';
            }
        }

        $renderFields->endTable();
    }
}
