<?php
/**
 * Plugin Name: WC1C > extension - Support Windows-1251
 * Plugin URI: https://wc1c.info/extensions/support-windows-1251
 * Description: Getting rid of incomprehensible hieroglyphs for old versions of 1C.
 * Version: 0.1.0
 * Requires at least: 4.7
 * Requires PHP: 5.6
 * WC requires at least: 3.5
 * WC tested up to: 6.1
 * Requires WC1C: 0.7
 * WC1C tested up to: 0.7
 * Text Domain: wc1c-support-1251
 * Author: WC1C team
 * Author URI: https://wc1c.info
 * Domain Path: /languages
 * Copyright: WC1C team © 2018-2022
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package Wc1c\Extensions
 */
defined('ABSPATH') || exit;

function wc1c_extension_support_1251_init($extensions)
{
	$extension_id = 'support-1251';

	if(isset($extensions[$extension_id]))
	{
		return $extensions;
	}

	define('WC1C_EXTENSION_SUPPORT_1251_DIR', __DIR__);
	define('WC1C_EXTENSION_SUPPORT_1251_PLUGIN_URL', plugin_dir_url(__FILE__));

	wc1c()->loader()->addNamespace('Wc1c\Extensions\Support1251', __DIR__ . '/src');

	try
	{
		$extension = new Wc1c\Extensions\Support1251\Core();
	}
	catch(Exception $e)
	{
		return $extensions;
	}

	$extension->localization();

	$default_headers =
	[
		'Name' => 'Plugin Name',
		'PluginURI' => 'Plugin URI',
		'Version' => 'Version',
		'Description' => 'Description',
		'Author' => 'Author',
		'AuthorURI' => 'Author URI',
		'TextDomain' => 'Text Domain',
		'DomainPath' => 'Domain Path',
		'Network' => 'Network',
		'RequiresWP' => 'Requires at least',
		'RequiresPHP' => 'Requires PHP',
		'RequiresWC' => 'WC requires at least',
		'TestedWC' => 'WC tested up to',
		'RequiresWC1C' => 'Requires WC1C',
		'TestedWC1C' => 'WC1C tested up to',
	];

	$plugin_data = get_file_data(__FILE__, $default_headers, 'plugin');

	$extension->setId($extension_id);

	$extension->setMeta('version', $plugin_data['Version']);
	$extension->setMeta('version_php_min', $plugin_data['RequiresPHP']);
	$extension->setMeta('version_wp_min', $plugin_data['RequiresWP']);
	$extension->setMeta('version_wc_min', $plugin_data['RequiresWC']);
	$extension->setMeta('version_wc_max', $plugin_data['TestedWC']);
	$extension->setMeta('version_wc1c_min', $plugin_data['RequiresWC1C']);
	$extension->setMeta('version_wc1c_max', $plugin_data['TestedWC1C']);
	$extension->setMeta('author', __($plugin_data['Author'], 'wc1c-support-1251'));
	$extension->setMeta('name', __($plugin_data['Name'], 'wc1c-support-1251'));
	$extension->setMeta('description', __($plugin_data['Description'], 'wc1c-support-1251'));

	$extensions[$extension_id] = $extension;

	return $extensions;
}

add_filter('wc1c_extensions_loading', 'wc1c_extension_support_1251_init', 10, 1);