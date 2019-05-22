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

use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

class JFormFieldTimeout extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 *
	 * @since  1.0.0
	 */
	protected $type = 'timeout';

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form
	 *                                      field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as an array container for the
	 *                                      field.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since  1.0.0
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		if ($return = parent::setup($element, $value, $group))
		{
			if (!empty($this->value) && is_string($this->value))
			{
				list($count, $type) = explode(' ', $this->value, 2);
				$this->value = array(
					'count' => $count,
					'type'  => $type
				);
			}
		}

		return $return;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since  1.0.0
	 */
	public function getInput()
	{
		$count  = (is_array($this->value) && !empty($this->value['count'])) ? $this->value['count'] : 1;
		$number = '<input type="number" name="' . $this->name . '[count]" class="input-mini" value="' . $count . '">';

		$type = (is_array($this->value) && !empty($this->value['type'])) ? $this->value['type'] : 'hours';

		$options = array(
			HTMLHelper::_('select.option', 'seconds', Text::_('COM_JATOMS_TIMEOUT_SECONDS')),
			HTMLHelper::_('select.option', 'minutes', Text::_('COM_JATOMS_TIMEOUT_MINUTES')),
			HTMLHelper::_('select.option', 'hours', Text::_('COM_JATOMS_TIMEOUT_HOURS')),
			HTMLHelper::_('select.option', 'days', Text::_('COM_JATOMS_TIMEOUT_DAYS')),
			HTMLHelper::_('select.option', 'weeks', Text::_('COM_JATOMS_TIMEOUT_WEEKS')),
			HTMLHelper::_('select.option', 'months', Text::_('COM_JATOMS_TIMEOUT_MONTHS')),
			HTMLHelper::_('select.option', 'years', Text::_('COM_JATOMS_TIMEOUT_YEARS')),
		);
		$select  = HTMLHelper::_('select.genericlist', $options, $this->name . '[type]',
			array('class' => 'input-small'), 'value', 'text', $type);

		return $number . ' ' . $select;
	}
}