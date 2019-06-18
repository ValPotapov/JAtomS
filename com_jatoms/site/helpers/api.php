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

JLoader::register('JAtomSHelperCache', JPATH_SITE . '/components/com_jatoms/helpers/cache.php');

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

class JAtomSHelperApi
{
	/**
	 * Atom-S api host.
	 *
	 * @var  string
	 *
	 * @since  1.0.0
	 */
	protected static $host = 'atom-s.com';

	/**
	 * Atom-S api version.
	 *
	 * @var  string
	 *
	 * @since  1.0.0
	 */
	protected static $version = 'v2';

	/**
	 * Atom-S api locale.
	 *
	 * @var  string
	 *
	 * @since  1.0.0
	 */
	protected static $locale = null;

	/**
	 * Showcase data.
	 *
	 * @var  array
	 *
	 * @since  1.0.0
	 */
	protected static $_showcase = array();

	/**
	 * Tour data.
	 *
	 * @var  array
	 *
	 * @since  1.0.0
	 */
	protected static $_tour = array();

	/**
	 * Tour booking data.
	 *
	 * @var  array
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected static $_tourBooking = array();

	/**
	 * Tour images.
	 *
	 * @var  array
	 *
	 * @since  1.0.0
	 */
	protected static $_tourImage = array();

	/**
	 * Hotel images.
	 *
	 * @var  array
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected static $_hotelImage = array();

	/**
	 * Method to get showcase data.
	 *
	 * @param   string   $showcase_key  Showcase Api key
	 * @param   integer  $limitstart    Tours offset.
	 * @param   integer  $limit         Number of tours.
	 *
	 * @throws  Exception
	 *
	 * @return  Registry|false Showcase data as Registry object on success, false on failure.
	 *
	 * @since  1.0.0
	 */
	public static function getShowcaseData($showcase_key = null, $limitstart = 0, $limit = 0)
	{
		if (empty($showcase_key))
		{
			throw new Exception(Text::sprintf('COM_JATOMS_ERROR_API',
				Text::_('COM_JATOMS_ERROR_API_SHOWCASE_KEY')));
		}

		$locale = self::getLocale();
		$key    = $showcase_key . '_' . $limitstart . '_' . $limit . '_' . $locale;
		$hash   = md5($key);
		if (!isset(self::$_showcase[$hash]))
		{
			if (!$context = JAtomSHelperCache::getData('showcase_data', $key))
			{
				// Prepare url
				$url = 'https://' . self::$host . '/api/' . self::$version . '/' . $showcase_key . '/search.json';

				$query             = array();
				$query['locale']   = $locale;
				$query['per_page'] = ($limit >= 0) ? $limit : 1;
				$query['page']     = 1;
				if (!empty($limitstart) && !empty($limit))
				{
					$query['page'] = ($page = ($limitstart / $limit) + 1) ? $page : 1;
				}

				$url .= '?' . http_build_query($query);

				// Try download
				if (!$context = @file_get_contents($url))
				{
					throw new Exception(Text::sprintf('COM_JATOMS_ERROR_API',
						Text::_('COM_JATOMS_ERROR_API_URL')));
				}

				// Save cache
				JAtomSHelperCache::saveData('showcase_data', $key, $context);
			}

			self::$_showcase[$hash] = new Registry($context);
		}

		return self::$_showcase[$hash];
	}

	/**
	 * Method to get tour data.
	 *
	 * @param   integer  $pk            The id of the tour.
	 * @param   string   $showcase_key  Showcase Api key.
	 *
	 * @throws  Exception
	 *
	 * @return  Registry|false Tour data as Registry object on success, false on failure.
	 *
	 * @since  1.0.0
	 */
	public static function getTourData($pk = null, $showcase_key = null)
	{
		if (empty($showcase_key))
		{
			throw new Exception(Text::sprintf('COM_JATOMS_ERROR_API',
				Text::_('COM_JATOMS_ERROR_API_SHOWCASE_KEY')));
		}
		if (empty($pk))
		{
			throw new Exception(Text::sprintf('COM_JATOMS_ERROR_API',
				Text::_('COM_JATOMS_ERROR_API_EMPTY_TOUR_ID')));
		}

		$locale = self::getLocale();
		$key    = $pk . '_' . $locale;
		$hash   = md5($key);
		if (!isset(self::$_tour[$hash]))
		{
			if (!$context = JAtomSHelperCache::getData('tour_data', $key))
			{
				// Prepare url
				$url = 'https://' . self::$host . '/api/' . self::$version . '/' . $showcase_key . '/search/tour/'
					. $pk . '.json';

				$query           = array();
				$query['locale'] = $locale;

				$url .= '?' . http_build_query($query);

				// Try download
				if (!$context = @file_get_contents($url))
				{
					throw new Exception(Text::sprintf('COM_JATOMS_ERROR_API',
						Text::_('COM_JATOMS_ERROR_API_URL')));
				}

				// Save cache
				$registry   = new Registry($context);
				$tour       = $registry->get('tour');
				$id         = $tour->id;
				$alias      = $tour->slug;
				$routeCache = array(
					'id'           => $id,
					'alias'        => $alias,
					'showcase_key' => $showcase_key,
				);
				JAtomSHelperCache::saveData('tour_data', $id . '_' . $locale, $context);
				JAtomSHelperCache::saveData('tour_data', $alias . '_' . $locale, $context);
				JAtomSHelperCache::saveData('tour_route', $id, $routeCache);
				JAtomSHelperCache::saveData('tour_route', $alias, $routeCache);

				// Set value
				self::$_tour[md5($id . '_' . $locale)]    = $registry;
				self::$_tour[md5($alias . '_' . $locale)] = $registry;
			}
			else
			{
				$registry = new Registry($context);
				$tour     = $registry->get('tour');
				$id       = $tour->id;
				$alias    = $tour->slug;

				// Set value
				self::$_tour[md5($id . '_' . $locale)]    = $registry;
				self::$_tour[md5($alias . '_' . $locale)] = $registry;
			}
		}

		return self::$_tour[$hash];
	}

	/**
	 * Method to get tour image.
	 *
	 * @param   integer  $pk   The id of the tour.
	 * @param   string   $src  Image src.
	 *
	 * @throws  Exception
	 *
	 * @return  string|false Tour image src on success, false on failure.
	 *
	 * @since  1.0.0
	 */
	public static function getTourImage($pk = null, $src = null)
	{
		if (empty($pk) || empty($src)) return false;

		// Clean src
		$src  = preg_replace('/\?[0-9]*$/', '', $src);
		$hash = md5($src);
		if (!isset(self::$_tourImage[$hash]))
		{
			$filename  = basename(urldecode($src));
			$extension = File::getExt($filename);
			$name      = md5(File::stripExt($filename));
			$key       = $pk . '_' . $name;

			// Get cache image
			if (!$image = JAtomSHelperCache::getData('tour_image', $key, $extension, true))
			{
				if ($context = @file_get_contents($src))
				{
					$image = JAtomSHelperCache::saveData('tour_image', $key, $context, $extension);
				}
			}

			self::$_tourImage[$hash] = $image;
		}

		return self::$_tourImage[$hash];
	}

	/**
	 * Method to get tour booking.
	 *
	 * @param   integer  $tour_id       The id of the tour.
	 * @param   string   $showcase_key  Showcase Api key.
	 * @param   integer  $trip          The id of the schedule.
	 * @param   bool     $group         Is group tour.
	 *
	 * @throws Exception
	 * @return  Registry|false Booking data as Registry object on success, false on failure.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public static function getTourBooking($tour_id = null, $showcase_key = null, $trip = null, $group = false)
	{
		if (empty($showcase_key))
		{
			throw new Exception(Text::sprintf('COM_JATOMS_ERROR_API',
				Text::_('COM_JATOMS_ERROR_API_SHOWCASE_KEY')));
		}
		if (empty($tour_id))
		{
			throw new Exception(Text::sprintf('COM_JATOMS_ERROR_API',
				Text::_('COM_JATOMS_ERROR_API_EMPTY_TOUR_ID')));
		}

		$type   = ($group) ? 'request_constructor' : 'package_constructor';
		$locale = self::getLocale();
		$key    = $tour_id . '_' . $trip . '_' . $type . '_' . $locale;
		$hash   = md5($key);
		if (!isset(self::$_tourBooking[$hash]))
		{
			if (!$context = JAtomSHelperCache::getData('tour_booking', $key))
			{
				$url = 'https://' . self::$host . '/api/' . self::$version . '/' . $showcase_key . '/search/tour/'
					. $tour_id . '/' . $type;

				$query           = array();
				$query['locale'] = $locale;
				if (!empty($trip))
				{
					$query['trip'] = $trip;
				}

				$url .= '?' . http_build_query($query);

				$headers = @get_headers($url);
				$context = array(
					'tour_id'      => $tour_id,
					'showcase_key' => $showcase_key,
					'trip'         => $trip,
					'iframe'       => $url,
					'status'       => (empty($headers) || $headers[0] !== 'HTTP/1.1 200 OK') ? 'error' : 'success'
				);
				JAtomSHelperCache::saveData('tour_booking', $key, $context);

				self::$_tourBooking[$hash] = new Registry($context);
			}
			else
			{
				$registry = new Registry($context);

				self::$_tourBooking[$hash] = $registry;
			}
		}

		return self::$_tourBooking[$hash];
	}

	/**
	 * Method to get hotel image.
	 *
	 * @param   integer  $pk   The id of the hotel.
	 * @param   string   $src  Image src.
	 *
	 * @throws  Exception
	 *
	 * @return  string|false Tour image src on success, false on failure.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public static function getHotelImage($pk = null, $src = null)
	{
		if (empty($pk) || empty($src)) return false;

		// Clean src
		$src  = preg_replace('/\?[0-9]*$/', '', $src);
		$hash = md5($src);
		if (!isset(self::$_hotelImage[$hash]))
		{
			$filename  = basename(urldecode($src));
			$extension = File::getExt($filename);
			$name      = md5(File::stripExt($filename));
			$key       = $pk . '_' . $name;

			// Get cache image
			if (!$image = JAtomSHelperCache::getData('hotel_image', $key, $extension, true))
			{
				if ($context = @file_get_contents($src))
				{
					$image = JAtomSHelperCache::saveData('hotel_image', $key, $context, $extension);
				}
			}

			self::$_hotelImage[$hash] = $image;
		}

		return self::$_hotelImage[$hash];
	}

	protected static function getLocale()
	{
		if (self::$locale === null)
		{
			$locale = explode('-', Factory::getLanguage()->getTag(), 2)[0];
			if ($locale !== 'ru')
			{
				$locale = 'en';
			}

			self::$locale = $locale;
		}

		return self::$locale;
	}
}