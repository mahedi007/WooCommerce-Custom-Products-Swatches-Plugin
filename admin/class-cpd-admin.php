<?php
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}


// custom tab to WooCommerce settings for cpd Products
function add_cpd_tab($tabs) {
    $tabs['cpd_tab'] = __('CPD Settings', 'cpd-products');
    return $tabs;
}
add_filter('woocommerce_settings_tabs_array', 'add_cpd_tab', 50);


function output_cpd_tab() {
    
    echo '<h2>' . __('CPD Settings', 'cpd-products') . '</h2>';

    woocommerce_admin_fields(get_cpd_tab_settings());
}
add_action('woocommerce_settings_tabs_cpd_tab', 'output_cpd_tab');

function save_cpd_tab_settings() {
  
  woocommerce_update_options(get_cpd_tab_settings());

  // Checking if the 'enable_cpd_feature' checkbox is checked
  $enable_cpd_feature = isset($_POST['enable_cpd_feature']) ? 'yes' : 'no';

  
  update_option('enable_cpd_feature_option', $enable_cpd_feature);

 
  $term_ids = [];

  
  foreach ($term_ids as $term_id) {
      if ($enable_cpd_feature === 'yes') {
          
          update_term_meta($term_id, 'swv_type', true);
      } else {
          
          update_term_meta($term_id, 'swv_type', false);
      }
  }
}
add_action('woocommerce_update_options_cpd_tab', 'save_cpd_tab_settings');




// Define the cpd tab settings fields
function get_cpd_tab_settings() {
    $settings = array(
        array(
            'name'     => __('Enable CPD Feature', 'cpd-products'),
            'desc_tip' => __('Check this box to enable the cpd feature.', 'cpd-products'),
            'id'       => 'enable_cpd_feature',
            'type'     => 'checkbox',
        ),
    );

    return apply_filters('woocommerce_cpd_tab_settings', $settings);
}
add_action('woocommerce_update_options_cpd_tab', 'save_cpd_tab_settings');


