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
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class JAtomSViewShowcase extends HtmlView
{
	/**
	 * Model state variables.
	 *
	 * @var  Joomla\CMS\Object\CMSObject
	 *
	 * @since  1.0.0
	 */
	protected $state;

	/**
	 * Form object.
	 *
	 * @var  Form
	 *
	 * @since  1.0.0
	 */
	protected $form;

	/**
	 * Showcase object.
	 *
	 * @var  object
	 *
	 * @since  1.0.0
	 */
	protected $item;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse.
	 *
	 * @throws  Exception
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @since  1.0.0
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->form  = $this->get('Form');
		$this->item  = $this->get('Item');

		// Check for errors
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode('\n', $errors), 500);
		}

		// Add title and toolbar
		$this->addToolbar();

		return parent::display($tpl);
	}

	/**
	 * Add title and toolbar.
	 *
	 * @throws  Exception
	 *
	 * @since  1.0.0
	 */
	protected function addToolbar()
	{
		$isNew = ($this->item->id == 0);
		$canDo = JAtomSHelper::getActions('com_jatoms', 'showcase', $this->item->id);

		// Disable menu
		Factory::getApplication()->input->set('hidemainmenu', true);

		// Set page title
		$title = ($isNew) ? Text::_('COM_JATOMS_SHOWCASE_ADD') : Text::_('COM_JATOMS_SHOWCASE_EDIT');
		ToolbarHelper::title(Text::_('COM_JATOMS') . ': ' . $title, 'compass');

		// Add apply & save buttons
		if ($canDo->get('core.edit'))
		{
			ToolbarHelper::apply('showcase.apply');
			ToolbarHelper::save('showcase.save');
		}

		// Add save new button
		if ($canDo->get('core.create'))
		{
			ToolbarHelper::save2new('showcase.save2new');
		}

		// Add cancel button
		ToolbarHelper::cancel('showcase.cancel', 'JTOOLBAR_CLOSE');
	}
}