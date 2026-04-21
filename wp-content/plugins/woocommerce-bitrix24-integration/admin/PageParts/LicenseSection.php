<?php

namespace Itgalaxy\Wc\Bitrix24\Integration\Admin\PageParts;

use Itgalaxy\Wc\Bitrix24\Integration\Includes\Bootstrap;

class LicenseSection
{
    public static function render()
    {
        $code = get_site_option(Bootstrap::PURCHASE_CODE_OPTIONS_KEY, '');

        if (!\wp_doing_ajax()) {
            echo '<hr>';
        }
        ?>
        <div class="postbox" data-ui-component="itglx-license-block">
            <h3 class="hndle border-bottom text-uppercase">
                <?php esc_html_e('License verification', 'wc-bitrix24-integration'); ?>
                <?php if ($code) { ?>
                    - <small style="color: green;">
                        <?php esc_html_e('verified', 'wc-bitrix24-integration'); ?>
                    </small>
                <?php } else { ?>
                    - <small style="color: red;">
                        <?php esc_html_e('please verify your license key', 'wc-bitrix24-integration'); ?>
                    </small>
                <?php } ?>
            </h3>
            <div class="inside">
                <form method="post" action="#" id="wcbx24-license-verify">
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="purchase-code">
                                    <?php esc_html_e('License key', 'wc-bitrix24-integration'); ?>
                                </label>
                            </th>
                            <td>
                                <input type="text"
                                       aria-required="true"
                                       required
                                       value="<?php echo !empty($code) ? esc_attr($code) : ''; ?>"
                                       id="purchase-code"
                                       name="purchase-code"
                                       class="large-text">
                                <small>
                                    <a href="https://wordpress-plugins.ru/liczenziya-podderzhka/" target="_blank">
                                        <?php esc_html_e('Where Is My License Key?', 'wc-bitrix24-integration'); ?>
                                    </a>
                                </small>
                            </td>
                        </tr>
                    </table>
                    <p>
                        <button type="submit" class="button button-primary" name="verify">
                            <?php esc_html_e('Verify', 'wc-bitrix24-integration'); ?>
                        </button>
                        <?php if ($code) { ?>
                            <button type="submit" class="button button-primary" name="unverify">
                                <?php esc_html_e('Unverify', 'wc-bitrix24-integration'); ?>
                            </button>
                        <?php } ?>
                    </p>
                </form>
            </div>
        </div>
        <?php
    }
}
