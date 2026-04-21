<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Admin\PageParts;

use Itgalaxy\Wc\Bitrix24\Integration\Includes\Bootstrap;
use Itgalaxy\Wc\Bitrix24\Integration\Includes\CrmFields;

class CompanySettings
{
    public static function render($meta)
    {
        $renderFields = new RenderFields('company');
        $renderFields->startTable();

        $currentValues = $meta['company'] ?? [];
        $companyFields = get_option(Bootstrap::COMPANY_FIELDS_KEY, []);

        foreach ($companyFields as $key => $field) {
            // Not show fields
            if (in_array($key, CrmFields::$breakFields['company'])) {
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
            } elseif ($key === 'COMPANY_TYPE') {
                $renderFields->statusField(
                    'COMPANY_TYPE',
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
            } elseif ($key === 'INDUSTRY') {
                $renderFields->statusField(
                    'INDUSTRY',
                    $key,
                    $title,
                    $currentValue
                );
            } elseif ($key === 'EMPLOYEES') {
                $renderFields->statusField(
                    'EMPLOYEES',
                    $key,
                    $title,
                    $currentValue
                );
            } elseif (in_array($key, ['COMMENTS', 'BANKING_DETAILS'])) {
                $renderFields->textareaField(
                    $key,
                    $title,
                    $currentValue
                );
            } else {
                $default = CrmFields::$setFields['company'][$key] ?? '';

                $renderFields->checkoutFieldsSelect(
                    $key,
                    $title,
                    $currentValue ?: $default
                );
            }

            $renderFields->endFieldRow();
        }

        $renderFields->endTable();
    }
}
