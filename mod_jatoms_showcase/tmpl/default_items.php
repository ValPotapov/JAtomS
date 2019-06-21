<?php
/**
 * @package    Joomla Atom-S Showcase Module
 * @version    __DEPLOY_VERSION__
 * @author     Septdir Workshop - septdir.com
 * @copyright  Copyright (c) 2018 - 2019 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

?>
<?php foreach ($items as $item):
	$types = array();
	if ($item->tour->is_group_tour)
	{
		$types[] = Text::_('COM_JATOMS_TOUR_GROUP');
	}
	else
	{
		$types[] = Text::_('COM_JATOMS_TOUR_PREFAB');
	}
	if ($item->tour->is_reception_tour) $types[] = Text::_('COM_JATOMS_TOUR_RECEPTION');
	if ($item->tour->is_departure_tour) $types[] = Text::_('COM_JATOMS_TOUR_DEPARTURE');
	if (!empty($item->tour->type))
	{
		$types[] = $item->tour->type;
	}

	$nearestDate = Text::_('COM_JATOMS_DATE_ANY');
	$moreDates   = 0;
	if (!$item->tour->is_cyclic_tour && !$item->tour->is_group_tour)
	{
		if ($date = $item->tour->schedule[0])
		{
			$nearestDate = ($item->tour->duration_hours) ?
				HTMLHelper::_('date', $date->start, Text::_('COM_JATOMS_DATE_DMYT'))
				: HTMLHelper::_('date', $date->start, Text::_('DATE_FORMAT_LC3'));
			$moreDates   = count($item->tour->schedule) - 1;
		}
	}
	?>
	<div class="item tour well" data-tour_id="<?php echo $item->tour->id; ?>">
		<div class="lead title">
			<a href="<?php echo $item->link; ?>"><?php echo $item->tour->name; ?></a>
		</div>
		<div class="row-fluid">
			<div class="span8">
				<div class="tags">
					<?php if ($item->booking_kind && $item->booking_kind == "instant_booking"): ?>
						<span class="label label-success hasTip"
							  title="<?php echo Text::_('COM_JATOMS_TOUR_INSTANT_BOOKING_DESC'); ?>">
							<?php echo Text::_('COM_JATOMS_TOUR_INSTANT_BOOKING'); ?>
						</span>
					<?php endif; ?>
					<?php if ($item->tour->is_cyclic_tour): ?>
						<span class="label hasTip"
							  title="<?php echo Text::_('COM_JATOMS_TOUR_CYCLIC_DESC'); ?>">
							<?php echo Text::_('COM_JATOMS_TOUR_CYCLIC'); ?>
						</span>
					<?php endif; ?>
					<?php if (!empty($item->hotels_number) && $item->hotels_number > 1): ?>
						<span class="label hasTip"
							  title="<?php echo Text::_('COM_JATOMS_TOUR_HOTEL_SELECT_DESC'); ?>">
							<?php echo Text::sprintf('COM_JATOMS_TOUR_HOTEL_SELECT'); ?>
						</span>
					<?php endif; ?>
				</div>
				<dl class="dl-horizontal">
					<dt><?php echo Text::_('COM_JATOMS_DURATION'); ?></dt>
					<dd>
						<?php echo ($item->tour->duration_hours) ?
							Text::plural('COM_JATOMS_DURATION_N_HOURS', $item->tour->duration_hours)
							: Text::plural('COM_JATOMS_DURATION_N_DAYS', $item->tour->duration_days); ?>
					</dd>

					<dt>
						<?php echo ($item->tour->is_group_tour) ? Text::_('COM_JATOMS_PRICE_FROM')
							: Text::_('COM_JATOMS_PRICE_FROM_PERSON'); ?>
					</dt>
					<dd class="text-error">
						<?php echo ($item->tour->is_group_tour) ? $item->tour->group_price->price
							: $item->min_adult_main_price; ?>
					</dd>

					<?php if ($item->tour->route_cities_list): ?>
						<dt><?php echo Text::_('COM_JATOMS_TOUR_ROUTE_CITIES_LIST'); ?></dt>
						<dd><?php echo implode(', ', $item->tour->route_cities_list) ?></dd>
					<?php endif; ?>

					<?php if ($item->tour->start_city): ?>
						<dt>
							<?php echo (!$item->tour->end_city) ? Text::_('COM_JATOMS_TOUR_START_CITY')
								: Text::_('COM_JATOMS_TOUR_START_END_CITY'); ?>
						</dt>
						<dd>
							<?php echo (!$item->tour->end_city) ? $item->tour->start_city
								: Text::sprintf('COM_JATOMS_TOUR_START_END_CITY_VALUE', $item->tour->start_city, $item->tour->end_city); ?>
						</dd>
					<?php endif; ?>

					<?php if (isset($item->tour->age_limit)): ?>
						<dt>
							<?php echo Text::_('COM_JATOMS_TOUR_AGE_LIMIT'); ?>
						</dt>
						<dd class="text-success">
							<?php echo $item->tour->age_limit ?>+
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
							<?php if ($moreDates): ?>
								<span class="muted"> (<?php echo Text::sprintf('COM_JATOMS_MORE', $moreDates); ?>
									)</span>
							<?php endif; ?>
						</dd>
					<?php endif; ?>
				</dl>
			</div>
			<div class="span4">
				<?php if ($item->image): ?>
					<p>
						<a href="<?php echo $item->link; ?>">
							<?php echo HTMLHelper::image($item->image->small, $item->tour->name); ?>
						</a>
					</p>
				<?php endif; ?>
			</div>
		</div>
		<div class="row-fluid text-right">
			<a href="<?php echo $item->link; ?>" class="btn btn-primary">
				<?php echo Text::_('COM_JATOMS_TOUR_VIEW'); ?>
			</a>
		</div>
	</div>
<?php endforeach; ?>