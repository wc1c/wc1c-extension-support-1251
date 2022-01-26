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
		if(wc1c()->context()->isAdmin('plugin'))
		{
			add_filter(WC1C_PREFIX . 'configurations-update_form_load_fields', [$this, 'configurations_fields_other'], 150, 1);
		}

		if(wc1c()->context()->isReceiver())
		{
			add_filter(WC1C_PREFIX . 'schema_productscml_receiver_send_response_by_type_description', [$this, 'filter_send_response_by_type_description'], 10, 3);
			add_filter(WC1C_PREFIX . 'schema_productscml_receiver_send_response_by_type_headers', [$this, 'filter_send_response_by_type_headers'], 10, 3);
		}
	}

	/**
	 * @param $description
	 * @param $context
	 * @param $type
	 *
	 * @return string
	 */
	public function filter_send_response_by_type_description($description, $context, $type)
	{
		if(!empty($description) && $context->core()->getOptions('support_1251', 'no') === 'yes')
		{
			$description = mb_convert_encoding($description, 'cp1251', 'utf-8');
			$context->core()->log()->info('Response charset converted to Windows-1251.', ['description' => $description]);
		}

		return $description;
	}

	/**
	 * @param $headers
	 * @param $context
	 * @param $type
	 *
	 * @return string
	 */
	public function filter_send_response_by_type_headers($headers, $context, $type)
	{
		if($context->core()->getOptions('support_1251', 'no') === 'yes')
		{
			$headers['Content-Type'] = 'Content-Type: text/plain; charset=Windows-1251';
		}

		return $headers;
	}

	/**
	 * Configuration fields: other
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurations_fields_other($fields)
	{
		$this->arrayInsert($fields, 'php_post_max_size', $this->getConfigurationFields());

		return $fields;
	}

	/**
	 * Configuration fields
	 *
	 * @return array
	 */
	public function getConfigurationFields()
	{
		$fields['support_1251'] =
		[
			'title' => __('Поддержка Windows-1251', 'wc1c-support-1251'),
			'type' => 'checkbox',
			'label' => __('Отметьте чекбокс, если необходимо включить данную возможность. По умолчанию выключена.', 'wc1c-support-1251'),
			'description' => __('При включении настройки, данные из кодировки utf-8 будут конвертироваться в кодировку Windows-1251. Следует использовать для совместимости со старыми версиями 1С, которые не поддерживают современные кодировки.', 'wc1c-support-1251'),
			'default' => 'no'
		];

		return $fields;
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
			$pos = array_search($position, array_keys($array), true) + 1;

			$array = array_merge
			(
				array_slice($array, 1, $pos),
				$insert,
				array_slice($array, $pos)
			);
		}
	}
}