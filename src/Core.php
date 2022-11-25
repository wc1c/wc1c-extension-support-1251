<?php namespace Wc1c\Extensions\Support1251;

defined('ABSPATH') || exit;

use Wc1c\Configuration;
use Wc1c\Exceptions\Exception;
use Wc1c\Extensions\Abstracts\ExtensionAbstract;

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
		if(isset($_GET['configuration_id']) && wc1c()->context()->isAdmin('plugin'))
		{
			$configuration_id = (int)$_GET['configuration_id'];

			try
			{
				$configuration = new Configuration($configuration_id);
			}
			catch(Exception $e)
			{
				return;
			}

			if($configuration->getSchema() !== 'productscml' && $configuration->getSchema() !== 'pqcml' && $configuration->getSchema() !== 'productscleanercml')
			{
				return;
			}

			add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurations_fields_other'], 150, 1);
		}

		if(wc1c()->context()->isReceiver())
		{
			add_filter('wc1c_schema_productscml_receiver_send_response_by_type_description', [$this, 'filterSendResponseByTypeDescription'], 10, 3);
			add_filter('wc1c_schema_productscml_receiver_send_response_by_type_headers', [$this, 'filterSendResponseByTypeHeaders'], 10, 3);

			add_filter('wc1c_schema_productscleanercml_receiver_send_response_by_type_description', [$this, 'filterSendResponseByTypeDescription'], 10, 3);
			add_filter('wc1c_schema_productscleanercml_receiver_send_response_by_type_headers', [$this, 'filterSendResponseByTypeHeaders'], 10, 3);

			add_filter('wc1c_schema_pqcml_receiver_send_response_by_type_description', [$this, 'filterSendResponseByTypeDescription'], 10, 3);
			add_filter('wc1c_schema_pqcml_receiver_send_response_by_type_headers', [$this, 'filterSendResponseByTypeHeaders'], 10, 3);
		}
	}

	/**
	 * @param $description
	 * @param $context
	 * @param $type
	 *
	 * @return string
	 */
	public function filterSendResponseByTypeDescription($description, $context, $type): string
	{
		if(!empty($description) && $context->core()->getOptions('support_1251', 'no') === 'yes')
		{
			$description = mb_convert_encoding($description, 'cp1251', 'utf-8');
			$context->core()->log()->info(__('Response charset converted to Windows-1251.', 'wc1c-support-1251'));
		}

		return $description;
	}

	/**
	 * @param $headers
	 * @param $context
	 * @param $type
	 *
	 * @return array
	 */
	public function filterSendResponseByTypeHeaders($headers, $context, $type): array
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
	public function configurations_fields_other($fields): array
	{
		$this->arrayInsert($fields, 'php_post_max_size', $this->getConfigurationFields());

		return $fields;
	}

	/**
	 * Configuration fields
	 *
	 * @return array
	 */
	public function getConfigurationFields(): array
	{
		$fields['support_1251'] =
		[
			'title' => __('Support Windows-1251', 'wc1c-support-1251'),
			'type' => 'checkbox',
			'label' => __('Check the checkbox to enable support. Disabled by default.', 'wc1c-support-1251'),
			'description' => __('Data from utf-8 encoding will be converted to Windows-1251 encoding. Should be used for compatibility with older versions of 1C that do not support modern encodings.', 'wc1c-support-1251'),
			'default' => 'no'
		];

		return $fields;
	}

	/**
	 * Load localisation
	 */
	public function localization()
	{
		$locale = determine_locale();

		if(has_filter('plugin_locale'))
		{
			$locale = apply_filters('plugin_locale', $locale, 'wc1c-support-1251');
		}

		load_textdomain('wc1c-support-1251', WP_LANG_DIR . '/plugins/wc1c-support-1251-' . $locale . '.mo');
		load_textdomain('wc1c-support-1251', WC1C_EXTENSION_SUPPORT_1251_DIR . '/assets/languages/wc1c-support-1251-' . $locale . '.mo');
	}

	/**
	 * @param array $array
	 * @param int|string $position
	 * @param mixed $insert
	 *
	 * "@return void
	 */
	public function arrayInsert(array &$array, $position, $insert)
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
				array_slice($array, 0, $pos),
				$insert,
				array_slice($array, $pos)
			);
		}
	}
}