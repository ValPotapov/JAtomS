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
JLoader::register('JAtomSHelperApi', JPATH_SITE . '/components/com_jatoms/helpers/api.php');

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Factory;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\Registry\Registry;

class JAtomSRouter extends RouterView
{
	/**
	 * Router segments.
	 *
	 * @var  array
	 *
	 * @since  1.0.0
	 */
	protected $_segments = array();

	/**
	 * Router ids.
	 *
	 * @var  array
	 *
	 * @since  1.0.0
	 */
	protected $_ids = array();

	/**
	 * Showcases keys.
	 *
	 * @var  array
	 *
	 * @since  1.0.0
	 */
	protected $_showcases = array();

	/**
	 * Router constructor.
	 *
	 * @param   CMSApplication  $app   The application object.
	 * @param   AbstractMenu    $menu  The menu object to work with.
	 *
	 * @since  1.0.0
	 */
	public function __construct($app = null, $menu = null)
	{
		// Showcases route
		$showcases = new RouterViewConfiguration('showcases');
		$showcases->setKey('root');
		$this->registerView($showcases);

		// Showcase route
		$showcase = new RouterViewConfiguration('showcase');
		$showcase->setKey('id')->setParent($showcases, 'root');
		$this->registerView($showcase);

		// Tour route
		$tour = new RouterViewConfiguration('tour');
		$tour->setKey('id')->setParent($showcase, 'showcase_id');
		$this->registerView($tour);

		parent::__construct($app, $menu);

		$this->attachRule(new MenuRules($this));
		$this->attachRule(new StandardRules($this));
		$this->attachRule(new NomenuRules($this));
	}

	/**
	 * Method to get the segment(s) for showcases.
	 *
	 * @param   string  $id     ID of the item to retrieve the segments.
	 * @param   array   $query  The request that is built right now.
	 *
	 * @return  array|string  The segments of this item.
	 *
	 * @since  1.0.0
	 */
	public function getShowcasesSegment($id, $query)
	{
		return array(1 => 'showcases');
	}

	/**
	 * Method to get the segment(s) for showcase.
	 *
	 * @param   string  $id     ID of the item to retrieve the segments.
	 * @param   array   $query  The request that is built right now.
	 *
	 * @return  array  The segments of this item.
	 *
	 * @since  1.0.0
	 */
	public function getShowcaseSegment($id, $query)
	{
		if (!strpos($id, ':'))
		{
			$hash = md5('showcase_' . $id);
			if (!isset($this->_segments[$hash]))
			{
				$db      = Factory::getDbo();
				$dbquery = $db->getQuery(true)
					->select(array('alias', $db->quoteName('key')))
					->from('#__jatoms_showcases')
					->where('id = ' . (int) $id);
				$db->setQuery($dbquery);
				$showcase = $db->loadObject();

				$this->_showcases[$id]  = $showcase->key;
				$this->_segments[$hash] = $showcase->alias;
			}

			$id .= ':' . $this->_segments[$hash];
		}

		list($void, $segment) = explode(':', $id, 2);

		return array($void => $segment);
	}

	/**
	 * Method to get the segment(s) for tour.
	 *
	 * @param   string  $id     ID of the item to retrieve the segments.
	 * @param   array   $query  The request that is built right now.
	 *
	 * @throws  Exception
	 *
	 * @return  array  The segments of this item.
	 *
	 * @since  1.0.0
	 */
	public function getTourSegment($id, $query)
	{
		if (!strpos($id, ':'))
		{
			$hash = md5('tour_' . $id);
			if (!isset($this->_segments[$hash]))
			{
				$alias = false;

				// Try get cache
				if ($cache = JAtomSHelperCache::getData('tour_route', $id))
				{
					$registry = new Registry($cache);
					$alias    = $registry->get('alias', false);
				}
				else
				{
					// Get showcase id
					$showcase_id = (!empty($query['showcase_id'])) ? $query['showcase_id'] : 0;
					if (strpos($showcase_id, ':'))
					{
						$showcase_id = explode(':', $showcase_id, 2)[0];
					}

					if ($showcase_id)
					{
						// Get showcase key
						if (!isset($this->_showcases[$showcase_id]))
						{
							$db      = Factory::getDbo();
							$dbquery = $db->getQuery(true)
								->select($db->quoteName('key'))
								->from('#__jatoms_showcases')
								->where('id = ' . (int) $showcase_id);
							$db->setQuery($dbquery);

							$this->_showcases[$showcase_id] = $db->loadResult();
						}

						// Get tour data
						if ($showcase_key = $this->_showcases[$showcase_id])
						{
							try
							{
								$api   = JAtomSHelperApi::getTourData($id, $showcase_key);
								$tour  = $api->get('tour');
								$alias = $tour->slug;
							}
							catch (Exception $e)
							{
								$alias = false;
							}
						}
					}
				}

				$this->_segments[$hash] = $alias;
			}

			$id .= ':' . $this->_segments[$hash];
		}

		list($void, $segment) = explode(':', $id, 2);

		return array($void => $segment);
	}

	/**
	 * Method to get the id for showcases.
	 *
	 * @param   string  $segment  Segment to retrieve the ID.
	 * @param   array   $query    The request that is parsed right now.
	 *
	 * @return  integer|false  The id of this item or false.
	 *
	 * @since  1.0.0
	 */
	public function getShowcasesId($segment, $query)
	{
		return (@$query['view'] == 'showcases' || $segment == 'showcases') ? 1 : false;
	}

	/**
	 * Method to get the id for showcase.
	 *
	 * @param   string  $segment  Segment to retrieve the id.
	 * @param   array   $query    The request that is parsed right now.
	 *
	 * @return  integer|false  The id of this item or false.
	 *
	 * @since  1.0.0
	 */
	public function getShowcaseId($segment, $query)
	{
		if (!empty($segment))
		{
			$hash = md5('showcase_' . $segment);
			if (!isset($this->_ids[$hash]))
			{
				$db      = Factory::getDbo();
				$dbquery = $db->getQuery(true)
					->select(array('id', $db->quoteName('key')))
					->from('#__jatoms_showcases')
					->where($db->quoteName('alias') . ' = ' . $db->quote($segment));
				$db->setQuery($dbquery);

				$id = false;
				if ($showcase = $db->loadObject())
				{
					$id = (int) $showcase->id;

					$this->_showcases[$id] = $showcase->key;
				}

				$this->_ids[$hash] = $id;
			}

			return $this->_ids[$hash];
		}

		return false;
	}

	/**
	 * Method to get the id for tour.
	 *
	 * @param   string  $segment  Segment to retrieve the id.
	 * @param   array   $query    The request that is parsed right now.
	 *
	 * @return  integer|false  The id of this item or false.
	 *
	 * @since  1.0.0
	 */
	public function getTourId($segment, $query)
	{
		if (!empty($segment))
		{
			$hash = md5('tour_' . $segment);
			if (!isset($this->_ids[$hash]))
			{
				$id = false;

				// Try get cache
				if ($cache = JAtomSHelperCache::getData('tour_route', $segment))
				{
					$registry = new Registry($cache);
					$id       = $registry->get('id', false);
				}
				else
				{
					// Get showcase id
					$showcase_id = (@$query['view'] == 'showcase' && !empty(@$query['id'])) ? $query['id'] : 0;
					if (strpos($showcase_id, ':'))
					{
						$showcase_id = explode(':', $showcase_id, 2)[0];
					}

					if ($showcase_id)
					{
						// Get showcase key
						if (!isset($this->_showcases[$showcase_id]))
						{
							$db      = Factory::getDbo();
							$dbquery = $db->getQuery(true)
								->select($db->quoteName('key'))
								->from('#__jatoms_showcases')
								->where('id = ' . (int) $showcase_id);
							$db->setQuery($dbquery);

							$this->_showcases[$showcase_id] = $db->loadResult();
						}

						// Get tour data
						if ($showcase_key = $this->_showcases[$showcase_id])
						{
							try
							{
								$api  = JAtomSHelperApi::getTourData($segment, $showcase_key);
								$tour = $api->get('tour');
								$id   = $tour->id;
							}
							catch (Exception $e)
							{
								$id = false;
							}
						}
					}
				}

				$this->_ids[$hash] = $id;
			}

			return $this->_ids[$hash];
		}

		return false;
	}
}

/**
 * JAtomS router functions.
 *
 * @param   array &$query  An array of URL arguments.
 *
 * @throws  Exception
 *
 * @return  array  The URL arguments to use to assemble the subsequent URL.
 *
 * @since  1.0.0
 */
function JAtomSBuildRoute(&$query)
{
	$app    = Factory::getApplication();
	$router = new JAtomSRouter($app, $app->getMenu());

	return $router->build($query);
}

/**
 * Parse the segments of a URL.
 *
 * @param   array  $segments  The segments of the URL to parse.
 *
 * @throws  Exception
 *
 * @return  array  The URL attributes to be used by the application.
 *
 * @since  1.0.0
 */
function JAtomSParseRoute($segments)
{
	$app    = Factory::getApplication();
	$router = new JAtomSRouter($app, $app->getMenu());

	return $router->parse($segments);
}