<?php
require_once('../../../wp-load.php');

// 1. Enable Taxes
update_option('woocommerce_calc_taxes', 'yes');
update_option('woocommerce_prices_include_tax', 'yes'); // As in Turkey, prices include tax
update_option('woocommerce_tax_display_shop', 'incl');
update_option('woocommerce_tax_display_cart', 'incl');

// 2. Set additional tax classes
update_option('woocommerce_tax_classes', "Ahsap Oyuncak\nIndirimli\nSifir");

// 3. Clear existing tax rates
global $wpdb;
$wpdb->query('TRUNCATE TABLE ' . $wpdb->prefix . 'woocommerce_tax_rates');
$wpdb->query('TRUNCATE TABLE ' . $wpdb->prefix . 'woocommerce_tax_rate_locations');

// Insert 10% Standard Rate (for everything else)
$wpdb->insert(
    $wpdb->prefix . 'woocommerce_tax_rates',
    array(
        'tax_rate_country'  => 'TR',
        'tax_rate_state'    => '',
        'tax_rate'          => '10.0000',
        'tax_rate_name'     => 'KDV %10',
        'tax_rate_priority' => 1,
        'tax_rate_compound' => 0,
        'tax_rate_shipping' => 1,
        'tax_rate_order'    => 1,
        'tax_rate_class'    => '' // Standard
    )
);

// Insert 20% Ahsap Oyuncak Rate
$wpdb->insert(
    $wpdb->prefix . 'woocommerce_tax_rates',
    array(
        'tax_rate_country'  => 'TR',
        'tax_rate_state'    => '',
        'tax_rate'          => '20.0000',
        'tax_rate_name'     => 'KDV %20',
        'tax_rate_priority' => 1,
        'tax_rate_compound' => 0,
        'tax_rate_shipping' => 1,
        'tax_rate_order'    => 2,
        'tax_rate_class'    => 'ahsap-oyuncak'
    )
);

echo "SUCCESS";
