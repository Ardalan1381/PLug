<?php
// ثبت سرویس‌ها به صورت پویا
add_action('init', 'csb_register_custom_services');
function csb_register_custom_services() {
    $services = get_option('csb_custom_services', []);
    foreach ($services as $slug => $service) {
        $labels = [
            'name' => $service['name'],
            'singular_name' => $service['name'],
            'add_new' => "Add New {$service['name']}",
            'add_new_item' => "Add New {$service['name']}",
            'edit_item' => "Edit {$service['name']}",
            'menu_name' => $service['name'],
        ];

        register_post_type($slug, [
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'author'],
            'rewrite' => ['slug' => str_replace('st_', '', $slug)],
            'menu_icon' => 'dashicons-plus-alt',
            'show_in_menu' => true,
        ]);
    }
}

// اتصال به Traveler
add_filter('st_register_post_type', 'csb_add_services_to_traveler');
function csb_add_services_to_traveler($post_types) {
    $services = get_option('csb_custom_services', []);
    foreach ($services as $slug => $service) {
        $post_types[$slug] = [
            'title' => $service['name'],
            'font_icon' => $service['icon'],
            'order' => 10,
        ];
    }
    return $post_types;
}

// متاباکس‌ها
add_action('admin_init', 'csb_add_service_metaboxes');
function csb_add_service_metaboxes() {
    if (!class_exists('STAdmin')) return;

    $services = get_option('csb_custom_services', []);
    foreach ($services as $slug => $service) {
        $fields = array_map(function($field) {
            return [
                'label' => $field,
                'id' => strtolower(str_replace(' ', '_', $field)),
                'type' => 'text',
                'std' => '',
            ];
        }, $service['fields']);

        STAdmin::inst()->metabox_init([
            'id' => "{$slug}_metabox",
            'title' => "{$service['name']} Settings",
            'pages' => [$slug],
            'context' => 'normal',
            'priority' => 'high',
            'fields' => $fields
        ]);
    }
}