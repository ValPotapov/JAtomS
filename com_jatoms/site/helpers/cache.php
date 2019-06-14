<?php
/**
 * @package    Joomla Atom-S Component
 * @version    __DEPLOY_VERSION__
 * @author     Septdir Workshop - septdir.com
 * @copyright  Copyright (c) 2018 - 2019 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\Registry\Registry;

class JAtomSHelperCache
{
	/**
	 * The request caches.
	 *
	 * @var  array
	 *
	 * @since  1.0.0
	 */
	protected static $_caches = array();

	/**
	 * The cache timeouts.
	 *
	 * @var  array
	 *
	 * @since  1.0.0
	 */
	protected static $timeouts = null;

	/**
	 * The cache timeouts default values.
	 *
	 * @var  array
	 *
	 * @since  1.0.0
	 */
	protected static $timeoutsDefault = array(
		'showcase_data' => array('count' => 5, 'type' => 'minutes'),
		'tour_data'     => array('count' => 5, 'type' => 'minutes'),
		'tour_route'    => array('count' => 1, 'type' => 'days'),
		'tour_image'    => array('count' => 1, 'type' => 'hours'),
		'tour_booking'  => array('count' => 5, 'type' => 'minutes'),
		'hotel_image'   => array('count' => 1, 'type' => 'hours'),
	);

	/**
	 * The cache paths.
	 *
	 * @var  array
	 *
	 * @since  1.0.0
	 */
	public static $paths = array(
		'showcase_data' => JPATH_CACHE . '/jatoms_showcase_data',
		'tour_data'     => JPATH_CACHE . '/jatoms_tour_data',
		'tour_route'    => JPATH_CACHE . '/jatoms_tour_route',
		'tour_image'    => JPATH_CACHE . '/jatoms_tour_image',
		'tour_booking'  => JPATH_CACHE . '/jatoms_tour_booking',
		'hotel_image'   => JPATH_CACHE . '/jatoms_hotel_image',
	);

	/**
	 * Method to get cache data.
	 *
	 * @param   string  $type       Cache type.
	 * @param   string  $key        Cache key.
	 * @param   string  $extension  Cache file extension.
	 * @param   bool    $pathOnly   Get only file path.
	 *
	 * @return  mixed|false Cache data on success, false on failure.
	 *
	 * @since  1.0.0
	 */
	public static function getData($type = null, $key = null, $extension = 'json', $pathOnly = false)
	{
		if (empty($type) || empty($key)) return false;
		$hash = md5('get_' . $type . '_' . $key);
		if (!isset(self::$_caches[$hash]))
		{
			$file    = self::$paths[$type] . '/' . $key . '.' . $extension;
			$timeout = self::getTimeout($type);

			// Get context
			$context = false;
			if (File::exists($file) && stat($file)['mtime'] >= $timeout)
			{
				$context = (!$pathOnly) ? file_get_contents($file) : str_replace(JPATH_CACHE, 'cache', $file);
			}
			self::$_caches[$hash] = $context;
		}

		return self::$_caches[$hash];
	}

	/**
	 * Method to save cache data.
	 *
	 * @param   string  $type       Cache type.
	 * @param   string  $key        Cache key.
	 * @param   mixed   $context    Cache context.
	 * @param   string  $extension  Cache file extension.
	 *
	 * @return  string|false Cache file path if success, false on failure.
	 *
	 * @since  1.0.0
	 */
	public static function saveData($type = null, $key = null, $context = null, $extension = 'json')
	{
		if (empty($type) || empty($key) || empty($context)) return false;
		$hash = md5('save_' . $type . '_' . $key);
		if (!isset(self::$_caches[$hash]))
		{
			$file = self::$paths[$type] . '/' . $key . '.' . $extension;
			if (File::exists($file))
			{
				File::delete($file);
			}

			if ($extension === 'json')
			{
				if (!empty($context) && !is_string($context) && !is_object($context) && !$context instanceof Registry)
				{
					$context = new Registry($context);
				}
				if ($context instanceof Registry)
				{
					$context = $context->toString();
				}
			}

			self::$_caches[$hash] = (!File::append($file, $context)) ? false
				: str_replace(JPATH_CACHE, 'cache', $file);
		}

		return self::$_caches[$hash];
	}

	/**
	 * Method to cache timeout.
	 *
	 * @param   string  $type  Cache type.
	 *
	 * @return  integer Timeout as unix date
	 *
	 * @since  1.0.0
	 */
	public static function getTimeout($type = null)
	{
		if ($type == null) return 0;
		if (self::$timeouts === null)
		{
			$params = ComponentHelper::getParams('com_jatoms');
			foreach (self::$timeoutsDefault as $key => $default)
			{
				$param   = $params->get($key . '_cachetimeout', $default);
				$time    = (is_object($param)) ? $param->count . ' ' . $param->type : implode(' ', $param);
				$timeout = (empty($param)) ? 0 : Factory::getDate('-' . $time)->toUnix();

				self::$timeouts[$key] = $timeout;
			}
		}

		return self::$timeouts[$type];
	}
}