<?php
/**
 * Plugin Name: WC1C > extension - Support Windows-1251
 * Plugin URI: https://wc1c.info/extensions/support-windows-1251
 * Description: Getting rid of incomprehensible hieroglyphs for old versions of 1C.
 * Version: 0.7.0
 * Requires at least: 4.7
 * Requires PHP: 5.6
 * Requires WC1C: 0.8
 * WC1C tested up to: 0.11
 * Text Domain: wc1c-support-1251
 * Author: WC1C team
 * Author URI: https://wc1c.info
 * Domain Path: /assets/languages
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

	wc1c()->loader()->addNamespace('Wc1c\Extensions\Support1251', WC1C_EXTENSION_SUPPORT_1251_DIR . '/src');

	try
	{
		$extension = new Wc1c\Extensions\Support1251\Core();
	}
	catch(Exception $e)
	{
		return $extensions;
	}

	$extension->setId($extension_id);
	$extension->localization();
	$extension->loadMetaByPlugin(__FILE__, 'wc1c-support-1251');
	$extension->setMeta('name', __('Support Windows-1251', 'wc1c-support-1251'));

	$extensions[$extension_id] = $extension;

	return $extensions;
}

add_filter('wc1c_extensions_loading', 'wc1c_extension_support_1251_init', 10, 1);