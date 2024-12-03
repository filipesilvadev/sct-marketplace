<?php
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = [
    'post_type' => 'lead',
    'posts_per_page' => 30,
    'paged' => $paged
];
$brokers = new WP_Query($args);
?>
<ul class="list-posts">
    <?php
    while ($brokers->have_posts()) {
        $brokers->the_post();
        $post_ID = get_the_ID();
    ?>
        <li class="item">
            <a href="<?php the_permalink(); ?>" class="btn-text">
                <?php the_title(); ?>
            </a>
        </li>
    <?php
    }
    ?>
    <div class="pagination">
        <?php
        echo paginate_links(array(
            'total' => $brokers->max_num_pages,
            'current' => max(1, $paged),
        )); ?>
    </div>
</ul>