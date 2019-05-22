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

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('list');

class JFormFieldShowcases extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 *
	 * @since  1.0.0
	 */
	protected $type = 'showcases';

	/**
	 * Field options array.
	 *
	 * @var  array
	 *
	 * @since  1.0.0
	 */
	protected $_options = null;

	/**
	 * Method to get the field options.
	 *
	 * @throws  Exception
	 *
	 * @return  array  The field option objects.
	 *
	 * @since  1.0.0
	 */
	protected function getOptions()
	{
		if ($this->_options === null)
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true)
				->select(array('s.id', 's.title'))
				->from($db->quoteName('#__jatoms_showcases', 's'))
				->order($db->escape('s.ordering') . ' ' . $db->escape('asc'))
				->group(array('s.id'));
			$items = $db->setQuery($query)->loadObjectList('id');

			// Check admin type view
			$app       = Factory::getApplication();
			$component = $app->input->get('option', 'com_jatoms');
			$view      = $app->input->get('view', 'showcase');
			$id        = $app->input->getInt('id', 0);
			$sameView  = ($app->isClient('administrator') && $component == 'com_jatoms' && $view == 'showcase');

			// Prepare options
			$options = parent::getOptions();
			foreach ($items as $i => $item)
			{
				$option          = new stdClass();
				$option->value   = $item->id;
				$option->text    = (!empty($item->title)) ? $item->title : $item->element;
				$option->disable = ($sameView && $item->id == $id);

				$options[] = $option;
			}

			$this->_options = $options;
		}

		return $this->_options;
	}
}