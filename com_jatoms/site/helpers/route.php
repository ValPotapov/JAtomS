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
	 * Tours links.
	 *
	 * @var  array
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected static $_tourLinks = array();

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
		$hash = md5($id . '_' . $showcase_id);
		if (!isset(self::$_tourLinks[$hash]))
		{
			$component = ComponentHelper::getComponent('com_jatoms');
			$link      = 'index.php?option=com_jatoms&view=tour';

			if (!empty($id))
			{
				$link .= '&id=' . $id;
			}

			// Set main showcase
			if ($main_showcase = $component->getParams()->get('main_showcase'))
			{
				$showcase_id = $main_showcase;
			}

			if (!empty($showcase_id))
			{
				$link .= '&showcase_id=' . $showcase_id;
			}

			// Check menu items
			$menu   = Factory::getApplication()->getMenu('site');
			$Itemid = '';
			if ($items = $menu->getItems('component_id', $component->id))
			{
				$sid = (strpos($showcase_id, ':')) ? explode(':', $showcase_id, 2)[0] : $showcase_id;
				foreach ($items as $item)
				{
					if (@$item->query['view'] == 'showcase' && @$item->query['id'] == $sid)
					{
						$Itemid = $item->id;

						break;
					}
				}
			}
			$link .= ($Itemid) ? '&Itemid=' . $Itemid : '&root=1';

			self::$_tourLinks[$hash] = $link;
		}

		return self::$_tourLinks[$hash];
	}
}