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
use Joomla\CMS\Helper\RouteHelper;

class JAtomSHelperRoute extends RouteHelper
{
	/**
	 * Showcase menu items.
	 *
	 * @var  array
	 *
	 * @since  1.1.0
	 */
	protected static $_showcaseItemid = array();

	/**
	 * Fetches showcases route.
	 *
	 * @return  string  Showcases view link.
	 *
	 * @since  1.0.0
	 */
	public static function getShowcasesRoute()
	{
		return 'index.php?option=com_jatoms&view=showcases';
	}

	/**
	 * Fetches showcase route.
	 *
	 * @param   int  $id  The id of the showcase.
	 *
	 * @return  string  Showcase view link.
	 *
	 * @since  1.0.0
	 */
	public static function getShowcaseRoute($id = null)
	{
		$link = 'index.php?option=com_jatoms&view=showcase&root=1';
		if (!empty($id))
		{
			$link .= '&id=' . $id;
		}

		return $link;
	}

	/**
	 * Fetches tour route.
	 *
	 * @param   int  $id           The id of the tour.
	 * @param   int  $showcase_id  The id of the showcase.
	 *
	 * @throws  Exception
	 *
	 * @return  string  Tour view link.
	 *
	 * @since  1.0.0
	 */
	public static function getTourRoute($id = null, $showcase_id = null)
	{
		$link = 'index.php?option=com_jatoms&view=tour';

		if (!empty($id))
		{
			$link .= '&id=' . $id;
		}

		// Set main showcase
		if ($main_showcase = ComponentHelper::getParams('com_jatoms')->get('main_showcase'))
		{
			$showcase_id = $main_showcase;
		}

		if (!empty($showcase_id))
		{
			$link .= '&showcase_id=' . $showcase_id;
		}

		// Check menu items
		$link .= ($Itemid = self::getShowcaseItemid($showcase_id)) ? '&Itemid=' . $Itemid : '&root=1';

		return $link;
	}

	/**
	 * Fetches booking route.
	 *
	 * @param   int  $tour_id      The id of the tour.
	 * @param   int  $showcase_id  The id of the showcase.
	 * @param   int  $trip         The id of the schedule.
	 *
	 * @throws Exception
	 * @return  string  Booking view link.
	 *
	 * @since  1.1.0
	 */
	public static function getBookingRoute($tour_id = null, $showcase_id = null, $trip = null)
	{
		$link = 'index.php?option=com_jatoms&view=booking&key=1';

		if (!empty($tour_id))
		{
			$link .= '&tour_id=' . $tour_id;
		}

		// Set main showcase
		if ($main_showcase = ComponentHelper::getParams('com_jatoms')->get('main_showcase'))
		{
			$showcase_id = $main_showcase;
		}

		if (!empty($showcase_id))
		{
			$link .= '&showcase_id=' . $showcase_id;
		}

		if (!empty($trip))
		{
			$link .= '&trip=' . $trip;
		}

		$link .= ($Itemid = self::getShowcaseItemid($showcase_id)) ? '&Itemid=' . $Itemid : '&root=1';

		return $link;
	}

	/**
	 * Method to get showcase menu item id.
	 *
	 * @param   int  $showcase_id  The id of the showcase.
	 *
	 * @throws  Exception
	 *
	 * @return  string  Tour view link.
	 *
	 * @since  1.0.0
	 */
	protected static function getShowcaseItemid($showcase_id = null)
	{
		$showcase_id = (strpos($showcase_id, ':')) ? explode(':', $showcase_id, 2)[0] : $showcase_id;
		if (empty($showcase_id))
		{
			return false;
		}
		if (!isset(self::$_showcaseItemid[$showcase_id]))
		{
			$menu   = Factory::getApplication()->getMenu('site');
			$Itemid = false;
			if ($items = $menu->getItems('component_id', ComponentHelper::getComponent('com_jatoms')->id))
			{
				foreach ($items as $item)
				{
					if (@$item->query['view'] == 'showcase' && @$item->query['id'] == $showcase_id)
					{
						$Itemid = $item->id;

						break;
					}
				}
			}
			self::$_showcaseItemid[$showcase_id] = $Itemid;
		}

		return self::$_showcaseItemid[$showcase_id];
	}
}