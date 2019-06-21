<?php
/**
 * @package    Joomla Atom-S Component
 * @version    1.1.0
 * @author     Septdir Workshop - septdir.com
 * @copyright  Copyright (c) 2018 - 2019 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

defined('_JEXEC') or die;

JLoader::register('JAtomSHelperCache', JPATH_SITE . '/components/com_jatoms/helpers/cache.php');
JLoader::register('JAtomSHelperApi', JPATH_SITE . '/components/com_jatoms/helpers/api.php');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

class JAtomSModelShowcase extends ListModel
{
	/**
	 * Model context string.
	 *
	 * @var  string
	 *
	 * @since  1.0.0
	 */
	protected $_context = 'jatoms.showcase';

	/**
	 * An showcase.
	 *
	 * @var  array
	 *
	 * @since  1.0.0
	 */
	protected $_item = null;

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @throws  Exception
	 *
	 * @since  1.0.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = Factory::getApplication('site');

		// Set request states
		$this->setState('showcase.id', $app->input->getInt('id', 1));

		// Merge global and menu item params into new object
		$params     = $app->getParams();
		$menuParams = new Registry();
		$menu       = $app->getMenu()->getActive();
		if ($menu)
		{
			$menuParams->loadString($menu->getParams());
		}
		$mergedParams = clone $menuParams;
		$mergedParams->merge($params);

		// Set params state
		$this->setState('params', $mergedParams);

		// Set published && debug state
		if ($app->input->getInt('debug', 0))
		{
			$this->setState('filter.published', array(0, 1));
			$this->setState('debug', 1);
		}
		else
		{
			$this->setState('filter.published', 1);
		}

		// List state information
		$ordering  = empty($ordering) ? 't.date' : $ordering;
		$direction = empty($direction) ? 'asc' : $direction;

		parent::populateState($ordering, $direction);

		// Set ordering for query
		$this->setState('list.ordering', $ordering);
		$this->setState('list.direction', $direction);

		// Set limit & start for query
		$this->setState('list.limit', $params->get('tours_limit', 10, 'uint'));
		$this->setState('list.start', $app->input->get('start', 0, 'uint'));
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since  1.0.0
	 */
	protected function getStoreId($id = '')
	{
		$id .= ':' . $this->getState('showcase.id');
		$id .= ':' . serialize($this->getState('filter.published'));

		return parent::getStoreId($id);
	}

	/**
	 * Build an sql query to load tours list.
	 *
	 * @throws  Exception
	 *
	 * @return  array  Api query params.
	 *
	 * @since  1.0.0
	 */
	protected function getListQuery()
	{
		$showcase = $this->getItem();
		$query    = array(
			'showcase_key' => $showcase->key
		);

		return $query;
	}

	/**
	 * Gets an array of objects from api.
	 *
	 * @param   array    $query       Api query params.
	 * @param   integer  $limitstart  Offset.
	 * @param   integer  $limit       The number of records.
	 *
	 * @return  object[]  An array of results.
	 *
	 * @since  1.0.0
	 */
	protected function _getList($query, $limitstart = 0, $limit = 0)
	{
		$items = array();
		if (empty($query['showcase_key']))
		{
			$this->setError(Text::sprintf('COM_JATOMS_ERROR_API', Text::_('COM_JATOMS_ERROR_API_SHOWCASE_KEY')));

			return $items;
		}
		try
		{
			$api   = JAtomSHelperApi::getShowcaseData($query['showcase_key'], $limitstart, $limit);
			$items = $api->get('tour_schedules', array());
			$meta  = $api->get('meta', new stdClass());

			// Set total
			if ($total = $meta->total_count)
			{
				$totalStore = $this->getStoreId('getTotal');

				$this->cache[$totalStore] = $total;
			}
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
		}

		return $items;
	}

	/**
	 * Returns a record count for the query.
	 *
	 * @param   array  $query  Api query params.
	 *
	 * @return  integer  Number of rows for query.
	 *
	 * @since  1.0.0
	 */
	protected function _getListCount($query)
	{
		$total = 0;
		if (empty($query['showcase_key']))
		{
			$this->setError(Text::sprintf('COM_JATOMS_ERROR_API', Text::_('COM_JATOMS_ERROR_API_SHOWCASE_KEY')));

			return $total;
		}

		try
		{
			$limitstart = $this->state->get('list.start');
			$limit      = $this->state->get('list.limit');

			$api   = JAtomSHelperApi::getShowcaseData($query['showcase_key'], $limitstart, $limit);
			$meta  = $api->get('meta', new stdClass());
			$total = ($meta->total_count) ? $meta->total_count : 0;
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
		}

		return $total;
	}

	/**
	 * Method to get an array of tours data.
	 *
	 * @throws  Exception
	 *
	 * @return  mixed  Tours objects array on success, false on failure.
	 *
	 * @since  1.0.0
	 */
	public function getItems()
	{
		if ($items = parent::getItems())
		{
			$showcase   = $this->getItem();
			$imageSizes = array('original', 'medium', 'small');
			foreach ($items as &$item)
			{
				// Set images
				$item->images = array();
				if (!empty($item->tour->gallery))
				{
					foreach ($item->tour->gallery as $images)
					{
						$image = new stdClass();
						foreach ($imageSizes as $size)
						{
							if (isset($images->$size))
							{
								$src          = JAtomSHelperApi::getTourImage($item->id, $images->$size);
								$image->$size = $src;
								foreach ($imageSizes as $otherSize)
								{
									if (!isset($image->$otherSize))
									{
										$image->$otherSize = $src;
									}
								}
							}
						}
						$item->images[] = $image;
					}
				}
				$item->image = (!empty($item->images)) ? $item->images[0] : false;

				// Set link
				$item->alias = $item->tour->slug;
				$item->jslug = $item->tour->id . ':' . $item->alias;
				$item->link  = Route::_(JAtomSHelperRoute::getTourRoute($item->jslug, $showcase->slug));
				$item->order = Route::_(JAtomSHelperRoute::getBookingRoute($item->jslug, $showcase->slug));

				// Set tour type
				$item->tour->type = Text::_('COM_JATOMS_TOUR_TYPE_SIGHTSEEING');

				// Set showcase data
				$item->showcase_id    = $showcase->id;
				$item->showcase_key   = $showcase->key;
				$item->showcase_alias = $showcase->alias;
				$item->showcase_link  = $showcase->link;

				// Save route cache
				$routeCache = array(
					'id'           => $item->tour->id,
					'alias'        => $item->alias,
					'showcase_key' => $item->showcase_key,
				);
				JAtomSHelperCache::saveData('tour_route', $item->tour->id, $routeCache);
				JAtomSHelperCache::saveData('tour_route', $item->alias, $routeCache);
			}
		}

		return $items;
	}

	/**
	 * Method to get showcase data.
	 *
	 * @param   integer  $pk  The id of the showcase.
	 *
	 * @throws  Exception
	 *
	 * @return  object|boolean|Exception  Showcase object on success, false or exception on failure.
	 *
	 * @since  1.0.0
	 */
	public function getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('showcase.id');

		if ($this->_item === null)
		{
			$this->_item = array();
		}

		if (!isset($this->_item[$pk]))
		{
			try
			{
				$db    = $this->getDbo();
				$query = $db->getQuery(true)
					->select(array('s.*'))
					->from($db->quoteName('#__jatoms_showcases', 's'))
					->where('s.id = ' . (int) $pk);

				// Filter by published state
				$published = $this->getState('filter.published');
				if (is_numeric($published))
				{
					$query->where('s.state = ' . (int) $published);
				}
				elseif (is_array($published))
				{
					$published = ArrayHelper::toInteger($published);
					$published = implode(',', $published);

					$query->where('s.state IN (' . $published . ')');
				}

				$data = $db->setQuery($query)->loadObject();

				if (empty($data))
				{
					throw new Exception(Text::_('COM_JATOMS_ERROR_SHOWCASE_NOT_FOUND'), 404);
				}

				// Set images
				$data->images = new Registry($data->images);

				// Set link
				$data->slug = $data->id . ':' . $data->alias;
				$data->link = Route::_(JAtomSHelperRoute::getShowcaseRoute($data->slug));

				// Set params
				$params       = new Registry($data->params);
				$data->params = clone $this->getState('params');
				$data->params->merge($params);

				$this->_item[$pk] = $data;
			}
			catch (Exception $e)
			{
				if ($e->getCode() == 404)
				{
					throw new Exception(Text::_($e->getMessage()), 404);
				}
				else
				{
					$this->setError($e);
					$this->_item[$pk] = false;
				}
			}
		}

		return $this->_item[$pk];
	}
}