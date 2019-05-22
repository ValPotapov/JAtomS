<?php
/**
 * @package    Joomla Atom-S Component
 * @version    1.0.0
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

class JAtomSModelTour extends ItemModel
{
	/**
	 * Model context string.
	 *
	 * @var  string
	 *
	 * @since  1.0.0
	 */
	protected $_context = 'jatoms.tour';

	/**
	 * Showcase object.
	 *
	 * @var  object
	 *
	 * @since  1.0.0
	 */
	protected $_showcase = null;

	/**
	 * Method to auto-populate the model state.
	 *
	 * @throws  Exception
	 *
	 * @since  1.0.0
	 */
	protected function populateState()
	{
		$app = Factory::getApplication('site');

		// Set request states
		$this->setState('tour.id', $app->input->getInt('id', 0));
		$this->setState('showcase.id', $app->input->getInt('showcase_id', 0));

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
	 * Method to get tour data.
	 *
	 * @param   integer  $pk  The id of the tour.
	 *
	 * @throws  Exception
	 *
	 * @return  object|boolean|Exception  Tour object on success, false or exception on failure.
	 *
	 * @since  1.0.0
	 */
	public function getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('tour.id');

		if ($this->_item === null)
		{
			$this->_item = array();
		}

		if (!isset($this->_item[$pk]))
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

				// Set images
				$data->images = array();
				if (!empty($data->gallery))
				{
					$imageSizes = array('original', 'medium', 'small');
					foreach ($data->gallery as $images)
					{
						$image = new stdClass();
						foreach ($imageSizes as $size)
						{
							if (isset($images->$size))
							{
								$src          = JAtomSHelperApi::getTourImage($data->id, $images->$size);
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
						$data->images[] = $image;
					}
				}
				$data->image = (!empty($data->images)) ? $data->images[0] : false;

				// Set link
				$data->alias = $data->slug;
				$data->jslug = $data->id . ':' . $data->alias;
				$data->link  = Route::_(JAtomSHelperRoute::getTourRoute($data->jslug, $showcase->slug));
				$data->order = JAtomSHelperApi::getTourOrderLink($data->id, $showcase->key);

				// Set params
				$data->params = $showcase->params;

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