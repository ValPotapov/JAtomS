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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$types = array();
if ($this->tour->is_group_tour)
{
	$types[] = Text::_('COM_JATOMS_TOUR_GROUP');
}
else
{
	$types[] = Text::_('COM_JATOMS_TOUR_PREFAB');
}
if ($this->tour->is_reception_tour) $types[] = Text::_('COM_JATOMS_TOUR_RECEPTION');
if ($this->tour->is_departure_tour) $types[] = Text::_('COM_JATOMS_TOUR_DEPARTURE');
if (!empty($this->tour->type))
{
	$types[] = $this->tour->type;
}

$nearestDate = Text::_('COM_JATOMS_DATE_ANY');
if (!$this->tour->is_cyclic_tour && !$this->tour->is_group_tour)
{
	if ($date = $this->tour->schedule[0])
	{
		$nearestDate = ($this->tour->duration_hours) ?
			HTMLHelper::_('date', $date->start, Text::_('COM_JATOMS_DATE_DMYT'))
			: HTMLHelper::_('date', $date->start, Text::_('DATE_FORMAT_LC3'));
	}
}
?>
<div>
	<div class="lead"><?php echo Text::_('COM_JATOMS_MAIN'); ?></div>
	<dl class="dl-horizontal">
		<dt><?php echo Text::_('COM_JATOMS_DURATION'); ?></dt>
		<dd>
			<?php echo ($this->tour->duration_hours) ?
				Text::plural('COM_JATOMS_DURATION_N_HOURS', $this->tour->duration_hours)
				: Text::plural('COM_JATOMS_DURATION_N_DAYS', $this->tour->duration_days); ?>
		</dd>

		<?php if ($this->tour->route_cities_list): ?>
			<dt><?php echo Text::_('COM_JATOMS_TOUR_ROUTE_CITIES_LIST'); ?></dt>
			<dd><?php echo implode(', ', $this->tour->route_cities_list) ?></dd>
		<?php endif; ?>

		<?php if ($this->tour->start_city): ?>
			<dt>
				<?php echo (!$this->tour->end_city) ? Text::_('COM_JATOMS_TOUR_START_CITY')
					: Text::_('COM_JATOMS_TOUR_START_END_CITY'); ?>
			</dt>
			<dd>
				<?php echo (!$this->tour->end_city) ? $this->tour->start_city
					: Text::sprintf('COM_JATOMS_TOUR_START_END_CITY_VALUE', $this->tour->start_city, $this->tour->end_city); ?>
			</dd>
		<?php endif; ?>

		<?php if (isset($this->tour->age_limit)): ?>
			<dt>
				<?php echo Text::_('COM_JATOMS_TOUR_AGE_LIMIT'); ?>
			</dt>
			<dd class="text-success">
				<?php echo $this->tour->age_limit ?>+
			</dd>
		<?php endif; ?>

		<?php if ($types): ?>
			<dt>
				<?php echo Text::_('COM_JATOMS_TOUR_TYPE'); ?>
			</dt>
			<dd>
				<?php echo implode(', ', $types); ?>
			</dd>
		<?php endif; ?>

		<?php if ($nearestDate) : ?>
			<dt>
				<?php echo Text::_('COM_JATOMS_TOUR_NEAREST_DATE'); ?>
			</dt>
			<dd>
				<?php echo $nearestDate; ?>
			</dd>
		<?php endif; ?>
	</dl>
	<?php if (!empty($this->tour->description)): ?>
		<div class="lead">
			<?php echo Text::_('JGLOBAL_DESCRIPTION'); ?>
		</div>
		<div>
			<?php echo nl2br($this->tour->description); ?>
		</div>
	<?php endif; ?>
</div>