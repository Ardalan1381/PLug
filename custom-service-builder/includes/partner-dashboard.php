<?php
// نقش Partner
function csb_setup_partner_role() {
    if (!get_role('partner')) {
        add_role('partner', __('Partner', 'traveler'), ['read' => true]);
    }
}

// محدود کردن داشبورد Partner
add_filter('st_partner_dashboard_data', 'csb_partner_dashboard_data', 10, 2);
function csb_partner_dashboard_data($data, $user_id) {
    $services = get_option('csb_custom_services', []);
    foreach ($services as $slug => $service) {
        $args = [
            'post_type' => $slug,
            'author' => $user_id,
            'posts_per_page' => -1,
        ];
        $query = new WP_Query($args);
        $data['services'][$slug] = [
            'title' => $service['name'],
            'items' => $query->posts,
            'bookings' => csb_get_partner_bookings($slug, $user_id),
        ];
    }
    return $data;
}

function csb_get_partner_bookings($post_type, $user_id) {
    $args = [
        'post_type' => 'st_order',
        'meta_query' => [
            ['key' => 'st_booking_post_type', 'value' => $post_type],
            ['key' => 'st_partner', 'value' => $user_id],
        ],
        'posts_per_page' => -1,
    ];
    $query = new WP_Query($args);
    return $query->posts;
}

add_action('st_save_order', 'csb_assign_order_to_partner');
function csb_assign_order_to_partner($order_id) {
    $post_id = get_post_meta($order_id, 'st_booking_id', true);
    $post_type = get_post_meta($order_id, 'st_booking_post_type', true);
    $services = get_option('csb_custom_services', []);
    if (isset($services[$post_type])) {
        $partner_id = get_post_field('post_author', $post_id);
        update_post_meta($order_id, 'st_partner', $partner_id);
    }
}