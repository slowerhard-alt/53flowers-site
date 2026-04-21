<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Admin\PageParts;

use Itgalaxy\Wc\Bitrix24\Integration\Includes\Bootstrap;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\CrmFields;

class DealSettings
{
    public static function render($meta)
    {
        $renderFields = new RenderFields('deal', true);
        $renderFields->startTable();

        $currentValues = $meta['deal'] ?? [];
        $dealFields = get_option(Bootstrap::DEAL_FIELDS_KEY, []);

        foreach ($dealFields as $key => $field) {
            // Not show fields
            if (in_array($key, CrmFields::$breakFields['deal'])) {
                continue;
            }

            // Not show read only fields
            if ($field['isReadOnly'] === true) {
                continue;
            }

            $title = RenderFields::resolveFieldTitle($key, $field);

            $renderFields->startFieldRow($title, $field['isRequired']);

            $currentValue = $currentValues[$key] ?? '';
            $currentValuePopulate = $currentValues[$key . '-populate'] ?? '';

            if ($key === 'TITLE') {
                if (empty($currentValue)) {
                    $currentValue = esc_html__('Order', 'wc-bitrix24-integration') . ' {order_number}';
                }

                $renderFields->inputTextField(
                    $key,
                    $title,
                    $currentValue
                );
            } elseif ($field['type'] === 'enumeration' && !empty($field['items'])) {
                $selectItems = [];

                foreach ($field['items'] as $item) {
                    $selectItems[$item['ID']] = $item['VALUE'];
                }

                $renderFields->selectField(
                    $selectItems,
                    $key,
                    $title,
                    $currentValue,
                    $currentValuePopulate,
                    $currentValues['update'] ?? []
                );
            } elseif ($field['type'] === 'char' || $field['type'] === 'boolean') {
                $renderFields->inputCheckboxField(
                    $key,
                    $title,
                    $currentValue
                );
            } elseif ($key === 'TYPE_ID') {
                $renderFields->statusField(
                    'DEAL_TYPE',
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
            } elseif ($key === 'STAGE_ID') {
                $renderFields->statusField(
                    'DEAL_STAGE',
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
                $default = CrmFields::$setFields['deal'][$key] ?? '';

                $renderFields->checkoutFieldsSelect(
                    $key,
                    $title,
                    $currentValue ?: $default,
                    $currentValues['update'] ?? []
                );
            }

            if ($key === 'TITLE') {
                $content = '<p class="description">'
                    . esc_html__('You can use the following shortcodes in this field:', 'wc-bitrix24-integration')
                    . ' {order_number}, {order_date}';

                foreach ((array) $renderFields->billing as $value => $_) {
                    $content .= ', {' . $value . '}';
                }

                foreach ((array) $renderFields->shipping as $value => $_) {
                    $content .= ', {' . $value . '}';
                }

                echo $content . '</p>';
            } elseif (in_array($key, ['ASSIGNED_BY_ID', 'RESPONSIBLE_ID'])) {
                ?>
                <p class="description">
                    <?php
                    esc_html_e(
                        'you can specify several, separated by commas, then the requests will be distributed sequentially',
                        'wc-bitrix24-integration'
                    ); ?>
                </p>
                <?php
            }

            $renderFields->endFieldRow();
        }

        $renderFields->endTable();
    }
}
