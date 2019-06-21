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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('jquery.framework');
HTMLHelper::_('behavior.tooltip');

HTMLHelper::stylesheet('media/com_jatoms/css/site.min.css', array('version' => 'auto'));
?>
<div id="JAtomS" class="tour" data-tour_id="<?php echo $this->tour->id; ?>">
	<div class="row-fluid">
		<div class="span8">
			<?php echo $this->loadTemplate('header'); ?>
		</div>
		<div class="span4">
			<?php echo $this->loadTemplate('info'); ?>
		</div>
	</div>
	<hr>
	<div>
		<?php echo HTMLHelper::_('bootstrap.startTabSet', 'tourTab', array('active' => 'description', 'class')); ?>

		<?php echo HTMLHelper::_('bootstrap.addTab', 'tourTab', 'description', Text::_('JGLOBAL_DESCRIPTION'));
		echo $this->loadTemplate('description');
		echo HTMLHelper::_('bootstrap.endTab'); ?>

		<?php echo HTMLHelper::_('bootstrap.addTab', 'tourTab', 'program', Text::_('COM_JATOMS_TOUR_PROGRAM'));
		echo $this->loadTemplate('program');
		echo HTMLHelper::_('bootstrap.endTab'); ?>

		<?php if (!empty($this->tour->nearest_trip->hotels)): ?>
			<?php echo HTMLHelper::_('bootstrap.addTab', 'tourTab', 'accommodations', Text::_('COM_JATOMS_TOUR_ACCOMMODATIONS'));
			echo $this->loadTemplate('accommodations');
			echo HTMLHelper::_('bootstrap.endTab'); ?>
		<?php endif; ?>

		<?php if ($this->tour->detailed_description->insurance
			|| $this->tour->detailed_description->included_services
			|| $this->tour->detailed_description->not_included_services): ?>
			<?php echo HTMLHelper::_('bootstrap.addTab', 'tourTab', 'services', Text::_('COM_JATOMS_TOUR_SERVICES'));
			echo $this->loadTemplate('services');
			echo HTMLHelper::_('bootstrap.endTab'); ?>
		<?php endif; ?>

		<?php if (!empty($this->tour->images)): ?>
			<?php echo HTMLHelper::_('bootstrap.addTab', 'tourTab', 'gallery', Text::_('COM_JATOMS_TOUR_GALLERY'));
			echo $this->loadTemplate('gallery');
			echo HTMLHelper::_('bootstrap.endTab'); ?>
		<?php endif; ?>

		<?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>
	</div>
	<?php if (!$this->tour->is_cyclic_tour && !$this->tour->is_group_tour): ?>
		<hr>
		<div>
			<?php echo $this->loadTemplate('availability'); ?>
		</div>
	<?php endif; ?>
</div>