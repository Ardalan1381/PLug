<?php
// فعال‌سازی رزرو
add_filter('st_service_booking', 'csb_enable_service_booking');
function csb_enable_service_booking($services) {
    $custom_services = get_option('csb_custom_services', []);
    return array_merge($services, array_keys($custom_services));
}

// تنظیم قیمت
add_filter('st_get_post_price', 'csb_service_price', 10, 2);
function csb_service_price($price, $post_id) {
    $services = get_option('csb_custom_services', []);
    $post_type = get_post_type($post_id);
    if (isset($services[$post_type])) {
        $fee = get_post_meta($post_id, 'fee', true);
        return $fee ? floatval($fee) : $price;
    }
    return $price;
}

add_filter('st_cart_item_price', 'csb_cart_item_price', 10, 3);
function csb_cart_item_price($price, $post_id, $item_data) {
    $services = get_option('csb_custom_services', []);
    $post_type = get_post_type($post_id);
    if (isset($services[$post_type])) {
        $fee = get_post_meta($post_id, 'fee', true);
        return $fee ? floatval($fee) : $price;
    }
    return $price;
}

// پشتیبانی از قالب‌ها
add_action('template_include', 'csb_template_override');
function csb_template_override($template) {
    $services = get_option('csb_custom_services', []);
    $post_type = get_post_type();

    if (isset($services[$post_type])) {
        if (is_single()) {
            $new_template = locate_template(["single-{$post_type}.php"]);
            return $new_template ?: plugin_dir_path(__FILE__) . '../templates/single-service.php';
        } elseif (is_archive()) {
            $new_template = locate_template(["archive-{$post_type}.php"]);
            return $new_template ?: plugin_dir_path(__FILE__) . '../templates/archive-service.php';
        }
    }
    return $template;
}