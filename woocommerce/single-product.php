<?php
/**
 * Custom WooCommerce Single Product Template (Emdief Home)
 *
 * @package Mis360-Furniture
 */


if (!defined('ABSPATH')) {
    exit;
}

get_header('shop');
mis360_breadcrumbs();
?>

<div class="emdief-single-product-page">
    <div class="emdief-container">
        <?php
        while (have_posts()):
            the_post();
            wc_get_template_part('content', 'single-product');
        endwhile;
        ?>
    </div>
</div>

<?php
get_footer('shop');
