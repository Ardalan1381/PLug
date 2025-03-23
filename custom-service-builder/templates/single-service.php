<?php
get_header();
while (have_posts()) : the_post();
    $services = get_option('csb_custom_services', []);
    $post_type = get_post_type();
    $service = $services[$post_type];
?>
<div class="st-service-page">
    <h1><?php the_title(); ?></h1>
    <div class="service-info">
        <?php foreach ($service['fields'] as $field) {
            $field_id = strtolower(str_replace(' ', '_', $field));
            $value = get_post_meta(get_the_ID(), $field_id, true);
            if ($value) echo "<p><strong>{$field}:</strong> " . esc_html($value) . "</p>";
        } ?>
    </div>
    <div id="csb-calendar" data-post-id="<?php echo get_the_ID(); ?>"></div>
    <div class="booking-form">
        <?php echo do_shortcode('[st_booking_form post_id="' . get_the_ID() . '"]'); ?>
    </div>
</div>
<?php
endwhile;
get_footer();