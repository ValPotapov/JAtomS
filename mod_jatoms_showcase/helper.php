<?php
/**
 * @package    Joomla Atom-S Showcase Module
 * @version    1.0.0
 * @author     Septdir Workshop - septdir.com
 * @copyright  Copyright (c) 2018 - 2019 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Registry\Registry;

class modJAtomSShowcaseHelper
{
	/**
	 * Tours arrays.
	 *
	 * @var  array
	 *
	 * @since  1.0.0
	 */
	protected static $_items = array();

	/**
	 * Module params.
	 *
	 * @var  array
	 *
	 * @since  1.0.0
	 */
	protected static $_params = array();

	/**
	 * Method to load languages.
	 *
	 * @since  1.0.0
	 */
	public static function loadLanguage()
	{
		$language = Factory::getLanguage();
		$language->load('com_jatoms', JPATH_SITE, $language->getTag(), true);
		$language->load('mod_jatoms_showcase', JPATH_SITE, $language->getTag(), true);
	}

	/**
	 * Method to get tours html for module.
	 *
	 * @throws  Exception
	 *
	 * @return  string Tours html.
	 *
	 * @since  1.0.0
	 */
	public static function getAjax()
	{
		// Load languages
		self::loadLanguage();

		// Get params
		if (!$params = self::getModuleParams())
		{
			throw new Exception(Text::_('MOD_JATOMS_SHOWCASE_ERROR_MODULE_NOT_FOUND'), 404);
		}

		// Get tours
		if (!$items = self::getItems($params))
		{
			throw new Exception(Text::_('COM_JATOMS_ERROR_TOURS_NOT_FOUND'), 404);
		}

		// Get html
		ob_start();
		require ModuleHelper::getLayoutPath('mod_jatoms_showcase', $params->get('layout', 'default') . '_items');
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	/**
	 * Method to get tours from model.
	 *
	 * @param   Registry  $params  Module params.
	 *
	 * @return  object[]  An array of results.
	 *
	 * @since  1.0.0
	 */
	public static function getItems($params = null)
	{
		if (empty($params)) return array();
		if (!$showcase = $params->get('showcase')) return array();

		$limit = $params->get('limit', 0);
		$hash  = $showcase . '_' . $limit;

		if (!isset(self::$_items[$hash]))
		{
			try
			{
				JLoader::register('JAtomSHelperRoute', JPATH_SITE . '/components/com_jatoms/helpers/route.php');

				BaseDatabaseModel::addIncludePath(JPATH_SITE . '/components/com_jatoms/models');
				$model = BaseDatabaseModel::getInstance('Showcase', 'JAtomSModel', array('ignore_request' => true));
				$model->setState('showcase.id', $showcase);
				$model->setState('params', Factory::getApplication()->getParams());
				$model->setState('filter.published', 1);
				$model->setState('list.limit', $limit);
				$model->setState('list.start', 0);

				$items = $model->getItems();
			}
			catch (Exception $e)
			{
				$items = array();
			}

			self::$_items[$hash] = $items;
		}

		return self::$_items[$hash];
	}

	/**
	 * Method to get module params.
	 *
	 * @param   integer  $pk  Module id.
	 *
	 * @throws  Exception
	 *
	 * @return  Registry|false Module params on success, false on failure.
	 *
	 * @since  1.0.0
	 */
	protected static function getModuleParams($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : Factory::getApplication()->input->getInt('module_id', 0);
		if (empty($pk)) return false;

		if (!isset(self::$_params[$pk]))
		{
			try
			{
				$db    = Factory::getDbo();
				$query = $db->getQuery(true)
					->select('params')
					->from('#__modules')
					->where('id =' . (int) $pk);
				$db->setQuery($query);
				$params = $db->loadResult();

				self::$_params[$pk] = (!empty($params)) ? new Registry($params) : false;
			}
			catch (Exception $e)
			{
				throw new Exception(Text::_($e->getMessage()), 500);
			}
		}

		return self::$_params[$pk];
	}
}