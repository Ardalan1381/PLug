<?php
// تنظیمات ادمین برای مدیریت سرویس‌ها
add_action('admin_menu', 'csb_admin_menu');
function csb_admin_menu() {
    add_menu_page(
        'Custom Services',
        'Custom Services',
        'manage_options',
        'csb-settings',
        'csb_settings_page',
        'dashicons-plus-alt',
        21
    );
}

function csb_settings_page() {
    if (isset($_POST['csb_add_service'])) {
        $service_name = sanitize_text_field($_POST['service_name']);
        $service_slug = sanitize_key($_POST['service_slug']);
        $fields = !empty($_POST['fields']) ? array_map('sanitize_text_field', explode("\n", trim($_POST['fields']))) : [];

        if (!preg_match('/^st_/', $service_slug)) {
            $service_slug = 'st_' . $service_slug;
        }

        $services = get_option('csb_custom_services', []);
        $services[$service_slug] = [
            'name' => $service_name,
            'fields' => $fields,
            'icon' => 'fa fa-plus' // آیکون پیش‌فرض
        ];
        update_option('csb_custom_services', $services);

        echo '<div class="updated"><p>سرویس اضافه شد!</p></div>';
    }

    $services = get_option('csb_custom_services', []);
    ?>
    <div class="wrap">
        <h1>مدیریت سرویس‌های سفارشی</h1>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th><label>نام سرویس</label></th>
                    <td><input type="text" name="service_name" required placeholder="مثال: Doctors"></td>
                </tr>
                <tr>
                    <th><label>شناسه سرویس</label></th>
                    <td><input type="text" name="service_slug" required placeholder="مثال: st_doctors"></td>
                </tr>
                <tr>
                    <th><label>فیلدهای سفارشی</label></th>
                    <td><textarea name="fields" rows="5" placeholder="هر خط یک فیلد: Fee, Specialty, City"></textarea></td>
                </tr>
            </table>
            <input type="submit" name="csb_add_service" class="button-primary" value="اضافه کردن">
        </form>

        <h2>سرویس‌های موجود</h2>
        <table class="wp-list-table widefat">
            <thead><tr><th>نام</th><th>شناسه</th><th>فیلدها</th></tr></thead>
            <tbody>
            <?php foreach ($services as $slug => $service) : ?>
                <tr>
                    <td><?php echo esc_html($service['name']); ?></td>
                    <td><?php echo esc_html($slug); ?></td>
                    <td><?php echo esc_html(implode(', ', $service['fields'])); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}