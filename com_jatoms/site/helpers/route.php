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

use Joomla\CMS\Helper\RouteHelper;

class JAtomSHelperRoute extends RouteHelper
{
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
		if (!empty($showcase_id))
		{
			$link .= '&showcase_id=' . $showcase_id;
		}

		return $link;
	}
}