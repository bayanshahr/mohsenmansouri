<?php
/*
Plugin Name: CopyRight Footer Text
Description: Automatically appends a custom copyright text to the end of each post.
Version: 1.1
Author:Mohsen Mansouri
websit:  https://drmohsenmansouri.ir
*/

if (!defined('ABSPATH')) {
    exit; // جلوگیری از دسترسی مستقیم
}

// افزودن متن به انتهای هر نوشته
function crft_append_footer_text($content) {
    if (is_single() && is_main_query()) {
        $footer_text = get_option('crft_footer_text', ''); // دریافت متن از تنظیمات
        if (!empty($footer_text)) {
            $content .= '<div class="crft-footer-text">' . wp_kses_post($footer_text) . '</div>';
        }
    }
    return $content;
}
add_filter('the_content', 'crft_append_footer_text');

// افزودن صفحه تنظیمات به پیشخوان وردپرس
function crft_register_settings_menu() {
    add_options_page(
        'کپی‌رایت نوشته', // عنوان صفحه
        'کپی‌رایت نوشته', // نام منو
        'manage_options',
        'crft-settings',
        'crft_settings_page_callback'
    );
}
add_action('admin_menu', 'crft_register_settings_menu');

// صفحه تنظیمات افزونه
function crft_settings_page_callback() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['crft_footer_text'])) {
        check_admin_referer('crft_save_settings');
        update_option('crft_footer_text', wp_kses_post($_POST['crft_footer_text']));
        echo '<div class="updated"><p>تنظیمات ذخیره شد.</p></div>';
    }

    $footer_text = get_option('crft_footer_text', '');
    ?>
    <div class="wrap">
        <h1>تنظیمات کپی‌رایت نوشته</h1>
        <form method="post">
            <?php wp_nonce_field('crft_save_settings'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="crft_footer_text">متن کپی‌رایت:</label></th>
                    <td>
                        <?php
                        wp_editor(
                            $footer_text, // محتوای پیش‌فرض
                            'crft_footer_text', // شناسه
                            [
                                'textarea_name' => 'crft_footer_text', // نام فیلد
                                'textarea_rows' => 8,
                                'media_buttons' => true, // دکمه‌های افزودن رسانه
                            ]
                        );
                        ?>
                    </td>
                </tr>
            </table>
            <?php submit_button('ذخیره تنظیمات'); ?>
        </form>
    </div>
    <?php
}
