<?php
get_header();
$services = get_option('csb_custom_services', []);
$post_type = get_post_type();
$service_name = $services[$post_type]['name'];
?>
<div class="st-service-archive">
    <h1>لیست <?php echo esc_html($service_name); ?></h1>
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <div class="service-item">
            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <?php foreach ($services[$post_type]['fields'] as $field) {
                $field_id = strtolower(str_replace(' ', '_', $field));
                $value = get_post_meta(get_the_ID(), $field_id, true);
                if ($value) echo "<p>{$field}: " . esc_html($value) . "</p>";
            } ?>
        </div>
    <?php endwhile; endif; ?>
</div>
<?php
get_footer();