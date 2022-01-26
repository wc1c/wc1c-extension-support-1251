<?php namespace Wc1c\Extensions\Support1251;

defined('ABSPATH') || exit;

use Wc1c\Abstracts\ExtensionAbstract;

/**
 * Core
 *
 * @package Wc1c\Extensions\Support1251
 */
final class Core extends ExtensionAbstract
{
	/**
	 * Init extension from WC1C
	 *
	 * @return void
	 */
	public function init()
	{
		$this->initHooks();
	}

	/**
	 * Hooks
	 */
	public function initHooks()
	{

	}

	/**
	 * Load localisation
	 */
	public function localization()
	{
		/** WP 5.x or later */
		if(function_exists('determine_locale'))
		{
			$locale = determine_locale();
		}
		else
		{
			$locale = is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale();
		}

		$locale = apply_filters('plugin_locale', $locale, 'wc1c-support-1251');

		unload_textdomain('wc1c-support-1251');
		load_textdomain('wc1c-support-1251', WP_LANG_DIR . '/plugins/wc1c-support-1251-' . $locale . '.mo');
		load_textdomain('wc1c-support-1251', WC1C_EXTENSION_SUPPORT_1251_DIR . '/languages/wc1c-support-1251-' . $locale . '.mo');
	}

	/**
	 * @param array $array
	 * @param int|string $position
	 * @param mixed $insert
	 *
	 * "@return void
	 */
	public function arrayInsert(&$array, $position, $insert)
	{
		if(is_int($position))
		{
			array_splice($array, $position, 0, $insert);
		}
		else
		{
			$pos = array_search($position, array_keys($array), true);

			$array = array_merge
			(
				array_slice($array, 1, $pos),
				$insert,
				array_slice($array, $pos)
			);
		}
	}
}