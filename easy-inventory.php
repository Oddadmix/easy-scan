<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://kand.ca
 * @since             1.0.0
 * @package           Easy_Inventory
 *
 * @wordpress-plugin
 * Plugin Name:       Easy Inventory
 * Plugin URI:        https://kand.ca
 * Description:       This plugin is used to restock items
 * Version:           1.0.0
 * Author:            Ahmed Wasfy
 * Author URI:        https://kand.ca
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       easy-inventory
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('EASY_INVENTORY_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-easy-inventory-activator.php
 */
function activate_easy_inventory()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-easy-inventory-activator.php';
	Easy_Inventory_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-easy-inventory-deactivator.php
 */
function deactivate_easy_inventory()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-easy-inventory-deactivator.php';
	Easy_Inventory_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_easy_inventory');
register_deactivation_hook(__FILE__, 'deactivate_easy_inventory');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-easy-inventory.php';


add_action('admin_menu', 'test_plugin_setup_menu');

function test_plugin_setup_menu()
{
	$appName = "Easy Inventory";

	add_menu_page($appName, 'Easy Scan', 'manage_options', 'my-menu', 'easy_scan');
	// add_submenu_page('my-menu', $appName, 'Easy Scan', 'manage_options', 'easy_scan', 'easy_scan');
	add_submenu_page('my-menu', $appName, 'Easy Order', 'manage_options', 'easy_order', 'easy_order');
}

function easy_order()
{
	$dir = plugin_dir_path(__FILE__);
	include($dir . "easy_order.php");
}

function easy_scan()
{
	$dir = plugin_dir_path(__FILE__);
	include($dir . "easy_scan.php");
}



function display_search_result()
{

	$sku = urldecode($_POST['sku_universel']);
	$product_id = wc_get_product_id_by_sku($sku);

	// get the product by id 



	$product = wc_get_product($product_id);

	$image = wp_get_attachment_image_src($product->image_id, 'single-post-thumbnail');

	list($src, $width, $height) = $image;
	$newObject = new stdClass;
	$newObject->image_src = $src;
	$newObject->id = $product->id;
	$newObject->quantity = $product->get_stock_quantity();
	$newObject->stock = $product->get_stock_quantity();
	$newObject->title = $product->name;
	// $product->get_stock_quantity();
	echo json_encode($newObject);
	//return $ajaxposts->posts; as json php
	// echo json_encode($product);

	// echo $response;
	exit;
}


add_action('wp_ajax_filter_products', 'display_search_result');


function place_order()
{
	$products = $_POST['products'];
	$order = wc_create_order();
	foreach ($products as $product) {
		$order->add_product(wc_get_product($product['id']), $product['quantity']);
	}

	$order->calculate_totals();
}

add_action('wp_ajax_place_order', 'place_order');


function update_product_quantity()
{
	$product_id = urldecode($_POST['product_id']);
	$quantity = urldecode($_POST['quantity']);
	$action = urldecode($_POST['action_type']);

	$product = wc_get_product($product_id);

	$quantity = $product->get_stock_quantity();

	if ($action == "add") {
		$quantity = $quantity + 1;
	} else if ($action == "remove") {
		$quantity = $quantity - 1;
	}



	$image = wp_get_attachment_image_src($product->image_id, 'single-post-thumbnail');

	wc_update_product_stock($product_id, $quantity);

	$product = wc_get_product($product_id);


	list($src, $width, $height) = $image;
	$newObject = new stdClass;
	$newObject->image_src = $src;
	$newObject->id = $product->id;
	$newObject->quantity = $product->get_stock_quantity();
	$newObject->title = $product->name;
	echo json_encode($newObject);

	exit;
}


add_action('wp_ajax_update_product_quantity', 'update_product_quantity');

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_easy_inventory()
{

	$plugin = new Easy_Inventory();
	$plugin->run();
}
run_easy_inventory();
