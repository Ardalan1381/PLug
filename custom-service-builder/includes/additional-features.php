<?php
// تقویم رزرو
add_action('wp_ajax_csb_get_booking_slots', 'csb_get_booking_slots');
add_action('wp_ajax_nopriv_csb_get_booking_slots', 'csb_get_booking_slots');
function csb_get_booking_slots() {
    $post_id = intval($_GET['post_id']);
    $orders = get_posts([
        'post_type' => 'st_order',
        'meta_key' => 'st_booking_id',
        'meta_value' => $post_id,
        'posts_per_page' => -1,
    ]);

    $events = [];
    foreach ($orders as $order) {
        $booking_time = get_post_meta($order->ID, 'check_in', true);
        if ($booking_time) {
            $events[] = [
                'title' => 'رزرو شده',
                'start' => $booking_time,
            ];
        }
    }
    wp_send_json($events);
}

// فیلتر سرویس‌ها
add_shortcode('csb_service_filter', 'csb_service_filter');
function csb_service_filter($atts) {
    $atts = shortcode_atts(['service' => ''], $atts);
    $service_slug = $atts['service'];
    $services = get_option('csb_custom_services', []);

    if (!$service_slug || !isset($services[$service_slug])) {
        return 'سرویس مشخص نشده یا وجود ندارد!';
    }

    $service = $services[$service_slug];
    ob_start();
    ?>
    <div class="csb-filter">
        <h2>جستجوی <?php echo esc_html($service['name']); ?></h2>
        <form method="get">
            <?php foreach ($service['fields'] as $field) : ?>
                <label><?php echo esc_html($field); ?>:</label>
                <input type="text" name="<?php echo strtolower(str_replace(' ', '_', $field)); ?>">
            <?php endforeach; ?>
            <button type="submit">جستجو</button>
        </form>
        <?php
        $args = ['post_type' => $service_slug, 'posts_per_page' => -1];
        $meta_query = [];
        foreach ($service['fields'] as $field) {
            $key = strtolower(str_replace(' ', '_', $field));
            if (!empty($_GET[$key])) {
                $meta_query[] = ['key' => $key, 'value' => sanitize_text_field($_GET[$key]), 'compare' => 'LIKE'];
            }
        }
        if ($meta_query) $args['meta_query'] = $meta_query;

        $query = new WP_Query($args);
        if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post(); ?>
            <div class="service-item">
                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <?php foreach ($service['fields'] as $field) {
                    $value = get_post_meta(get_the_ID(), strtolower(str_replace(' ', '_', $field)), true);
                    if ($value) echo "<p>{$field}: " . esc_html($value) . "</p>";
                } ?>
            </div>
        <?php endwhile; endif; wp_reset_postdata(); ?>
    </div>
    <?php
    return ob_get_clean();
}

// اعلان پیامکی (نیاز به API پیامک داره)
add_action('st_save_order', 'csb_send_sms_notification');
function csb_send_sms_notification($order_id) {
    $post_id = get_post_meta($order_id, 'st_booking_id', true);
    $post_type = get_post_meta($order_id, 'st_booking_post_type', true);
    $services = get_option('csb_custom_services', []);

    if (isset($services[$post_type])) {
        $partner_id = get_post_meta($order_id, 'st_partner', true);
        $partner = get_userdata($partner_id);
        $phone = get_user_meta($partner_id, 'phone', true); // فرض بر وجود فیلد شماره تلفن

        if ($phone) {
            $message = "رزرو جدید برای " . get_the_title($post_id) . " در تاریخ " . get_post_meta($order_id, 'check_in', true);
            // اینجا باید API پیامک (مثل کاوه‌نگار) رو فراخوانی کنید
            // csb_send_sms($phone, $message);
        }
    }
}