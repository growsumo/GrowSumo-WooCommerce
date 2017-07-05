<?php
/*
Plugin Name: GrowSumo
Plugin URI: http://www.growsumo.com/
Description: Integrate GrowSumo tracking snippet to reward influencers driving customers to your site.
Version: 1.0.0
Author: GrowSumo
Author URI: http://www.growsumo.com/
*/

if (!defined('WP_CONTENT_URL'))
define('WP_CONTENT_URL', get_option('siteurl').'/wp-content');
if (!defined('WP_CONTENT_DIR'))
define('WP_CONTENT_DIR', ABSPATH.'wp-content');
if (!defined('WP_PLUGIN_URL'))
define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
if (!defined('WP_PLUGIN_DIR'))
define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');

// if admin populate the options page and add the admin menu
if (is_admin()) {
    add_action( 'admin_init', 'growsumo_admin_options_init' );
    add_action( 'admin_menu', 'growsumo_admin_menu' );
}


function growsumo_admin_menu() {
    // will create the admin menu if user has correct access
    add_menu_page('GrowSumo', 'GrowSumo', 'manage_options', 'growsumo', 'growsumo_options_page', plugins_url( 'growsumo/images/logo.png'));
}

function store_growsumo_company_key() {
    update_option('growsumo_company_key', '');
}

function growsumo_options_page() {
    // options page template
    include(WP_PLUGIN_DIR.'/growsumo/options.php');
}

function growsumo_admin_options_init() {
    // registers the company key as an options setting if user is admin
    register_setting('growsumo', 'growsumo_company_key');
}
function growsumojs() {
    $growsumo_company_key = get_option('growsumo_company_key');
?>
    <script type="text/javascript">
    (function() {
        if (! '<?php echo $growsumo_company_key ?>') {
            return;
        }
        var gs = document.createElement('script');
        gs.src = "https://snippet.growsumo.com/growsumo.min.js";
        gs.type = 'text/javascript';
        gs.async = 'true';
        gs.onload = gs.onreadystatechange = function() {
            var rs = this.readyState;
            if (rs && rs != 'complete' && rs != 'loaded') return;
            try {
                growsumo._initialize('<?php echo $growsumo_company_key ?>');
                if (typeof(growsumoInit) === 'function') {
                    growsumoInit();
                }
            } catch (e) {}
        };
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(gs, s);
    })();
    </script>
<?php
}

function growsumo_track_order( $order_id ){

	// get order and customer information
    $growsumo_company_key = get_option('growsumo_company_key');
	$order = new WC_Order( $order_id );
	$currency = $order->get_currency();
	$amount = $order->get_total() * 100.0;
	$order_key = $order->get_order_key();
	$customer_id = $order->get_customer_id();
	$first_name = $order->get_billing_first_name();
	$last_name = $order->get_billing_last_name();
	$name = $first_name . ' ' . $last_name;
	$email = $order->get_billing_email();
?>
	<script type='text/javascript'>
    function growsumoInit() {
        growsumo.data.name = '<?php echo $name ?>';
        growsumo.data.email = '<?php echo $email ?>';
        growsumo.data.customer_key = '<?php echo $customer_id ?>';
        growsumo.data.amount = '<?php echo $amount ?>';
        growsumo.data.currency = '<?php echo $currency ?>';
		growsumo.createSignup();
    }
	</script>
<?php
}

register_activation_hook(__FILE__, 'store_growsumo_company_key');
// attach GrowSumoJs to the snippet of each page
add_action('wp_footer', 'growsumojs');
// attach customer create function to woo commerce thankyou page
add_action('woocommerce_thankyou_order_id', 'growsumo_track_order');
// attach customer create function to wordpress user register
?>
