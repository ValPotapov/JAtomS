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

$description = $this->tour->name . ' &mdash; ';
$description .= ($this->tour->duration_hours) ?
	Text::plural('COM_JATOMS_DURATION_N_HOURS', $this->tour->duration_hours)
	: Text::plural('COM_JATOMS_DURATION_N_DAYS', $this->tour->duration_days);

$discounts = array();
if (!empty($this->tour->nearest_trip->tour_price_discounts))
{
	foreach ($this->tour->nearest_trip->tour_price_discounts as $discount)
	{
		$discounts[] = $discount->type;
	}
}
if (!empty($this->tour->nearest_trip->hotels) && !$this->tour->is_group_tour)
{
	foreach ($this->tour->nearest_trip->hotels as $h => $hotel)
	{
		$break = false;
		foreach ($hotel->accommodations as $a => $accommodation)
		{
			if (!empty($accommodation->prices->adult_extra_bed_price))
			{
				$break = true;
				break;
			}
		}
		if ($break)
		{
			$discounts[] = Text::_('COM_JATOMS_DISCOUNTS_CHILDREN');
			break;
		}
	}
}
?>
<div id="availability">
	<div class="alert alert-success">
		<p>
			<?php echo Text::_('COM_JATOMS_NO_COMMISSIONS'); ?>
		</p>
		<p><?php echo Text::_('COM_JATOMS_DISCOUNTS') . ' ' . implode(',', $discounts); ?></p>
		<p>
			<?php echo Text::_('COM_JATOMS_CONVENIENT_PAYMENT_METHODS'); ?>
		</p>
	</div>
	<div class="lead"><?php echo Text::_('COM_JATOMS_TOUR_AVAILABILITY'); ?></div>
	<p><?php echo $description; ?></p>
	<table class="table table-bordered">
		<thead>
		<tr>
			<th class="center">
				<?php echo Text::_('COM_JATOMS_DATE_START'); ?>
			</th>
			<th class="center">
				<?php echo Text::_('COM_JATOMS_DATE_END'); ?>
			</th>
			<th class="center">
				<?php echo Text::_('COM_JATOMS_TOUR_SEATS_LEFT'); ?>
			</th>
			<th class="center" colspan="2">
				<?php echo Text::_('COM_JATOMS_PRICE_AND_BUY'); ?>
			</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($this->tour->schedule as $s => $schedule): ?>
			<tr>
				<td class="center">
					<?php echo HTMLHelper::_('date', $schedule->start, Text::_('DATE_FORMAT_LC3')); ?>
					<?php if ($this->tour->duration_hours): ?>
						<div>
							<?php echo HTMLHelper::_('date', $schedule->start, Text::_('COM_JATOMS_DATE_AT_TIME')); ?>
						</div>
					<?php endif; ?>
				</td>
				<td class="center">
					<div>
						<?php echo HTMLHelper::_('date', $schedule->end, Text::_('DATE_FORMAT_LC3')); ?>
					</div>
					<?php if ($this->tour->duration_hours): ?>
						<div>
							<?php echo HTMLHelper::_('date', $schedule->end, Text::_('COM_JATOMS_DATE_AT_TIME')); ?>
						</div>
					<?php endif; ?>
				</td>
				<td class="center">
					<?php echo $schedule->seats_left; ?>
				</td>
				<td class="center">
					<div class="lead text-success">
						<?php echo $schedule->min_price; ?>
					</div>
					<div>
						<?php if ($schedule->instant_booking): ?>
							<span class="label label-success hasTip"
								  title="<?php echo Text::_('COM_JATOMS_TOUR_INSTANT_BOOKING_DESC'); ?>">
								<?php echo Text::_('COM_JATOMS_TOUR_INSTANT_BOOKING'); ?>
							</span>
						<?php endif; ?>
					</div>
				</td>
				<td class="center">
					<a href="<?php echo $schedule->order; ?>" rel="nofollow" class="btn btn-primary">
						<?php echo Text::_('COM_JATOMS_BUY'); ?>
					</a>
					<div class="center muted small">
						<?php echo Text::_('COM_JATOMS_BUY_OR_BOOK'); ?>
					</div>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>