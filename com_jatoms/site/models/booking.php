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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

class JAtomSModelBooking extends ItemModel
{
	/**
	 * Model context string.
	 *
	 * @var  string
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected $_context = 'jatoms.booking';

	/**
	 * Tour object.
	 *
	 * @var  object
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected $_tour = null;

	/**
	 * Showcase object.
	 *
	 * @var  object
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected $_showcase = null;

	/**
	 * Method to auto-populate the model state.
	 *
	 * @throws  Exception
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected function populateState()
	{
		$app = Factory::getApplication('site');

		// Set request states
		$this->setState('tour.id', $app->input->getInt('tour_id', 0));
		$this->setState('showcase.id', $app->input->getInt('showcase_id', 0));
		$this->setState('trip', $app->input->getInt('trip', 0));

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
	}

	/**
	 * Method to get booking data.
	 *
	 * @param   integer  $pk  The id of the schedule.
	 *
	 * @throws  Exception
	 *
	 * @return  object|boolean|Exception  Booking object on success, false or exception on failure.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('trip');

		if ($this->_item === null)
		{
			$this->_item = array();
		}

		if (!isset($this->_item[$pk]))
		{
			try
			{
				$tour     = $this->getTour();
				$showcase = $this->getShowcase();
				$api      = JAtomSHelperApi::getTourBooking($tour->id, $showcase->key, $pk, $tour->is_group_tour);
				$data     = ($api->get('status', 'error') !== 'error') ? $api->toObject() : false;

				if (empty($data))
				{
					throw new Exception(Text::_('COM_JATOMS_ERROR_BOOKING_NOT_FOUND'), 404);
				}

				// Set link
				$data->link  = Route::_(JAtomSHelperRoute::getBookingRoute($tour->jslug, $showcase->slug, $pk));
				$data->title = Text::sprintf('COM_JATOMS_BOOKING_TITLE', $tour->name);

				// Set tour
				$data->tour = $tour;

				// Set params
				$data->params = $tour->params;

				// Set showcase
				$data->showcase     = $showcase;
				$data->showcase_key = $showcase->key;

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

	/**
	 * Method to get tour data.
	 *
	 * @param   integer  $pk  The id of the tour.
	 *
	 * @throws  Exception
	 *
	 * @return  object|boolean|Exception  Tour object on success, false or exception on failure.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getTour($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('tour.id');

		if ($this->_tour === null)
		{
			$this->_tour = array();
		}

		if (!isset($this->_tour[$pk]))
		{
			try
			{
				$showcase = $this->getShowcase();
				$api      = JAtomSHelperApi::getTourData($pk, $showcase->key);
				$data     = $api->get('tour');

				if (empty($data))
				{
					throw new Exception(Text::_('COM_JATOMS_ERROR_TOUR_NOT_FOUND'), 404);
				}


				// Set link
				$data->alias = $data->slug;
				$data->jslug = $data->id . ':' . $data->alias;
				$data->link  = Route::_(JAtomSHelperRoute::getTourRoute($data->jslug, $showcase->slug));

				// Set params
				$data->params = $showcase->params;

				// Set showcase
				$data->showcase     = $showcase;
				$data->showcase_key = $showcase->key;

				$this->_tour[$pk] = $data;
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
					$this->_tour[$pk] = false;
				}
			}
		}

		return $this->_tour[$pk];
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
	 * @since  __DEPLOY_VERSION__
	 */
	public function getShowcase($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('showcase.id');

		if ($this->_showcase === null)
		{
			$this->_showcase = array();
		}

		if (!isset($this->_showcase[$pk]))
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

				$this->_showcase[$pk] = $data;
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
					$this->_showcase[$pk] = false;
				}
			}
		}

		return $this->_showcase[$pk];
	}
}