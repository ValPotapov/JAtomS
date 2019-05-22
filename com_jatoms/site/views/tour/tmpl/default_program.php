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

use Joomla\CMS\Language\Text;

?>
<div class="lead">
	<?php echo Text::_('COM_JATOMS_TOUR_PROGRAM'); ?>
</div>
<?php if (count($this->tour->days) > 1): ?>
	<div class="accordion" id="programAccordion">
		<?php foreach ($this->tour->days as $d => $day): ?>
			<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#programAccordion"
					   href="#programDay<?php echo $d; ?>">
						<?php echo Text::sprintf('COM_JATOMS_TOUR_PROGRAM_DAY', $day->day_number); ?>
					</a>
				</div>
				<div id="programDay<?php echo $d; ?>" class="accordion-body collapse">
					<div class="accordion-inner"><?php echo nl2br($day->description); ?></div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
<?php else: ?>
	<p>
		<?php echo nl2br($this->tour->days[0]->description); ?>
	</p>
<?php endif; ?>
<?php if ($this->tour->detailed_description->tour_notes): ?>
	<div class="lead"><?php echo Text::_('COM_JATOMS_TOUR_NOTES'); ?></div>
	<p>
		<?php echo nl2br($this->tour->detailed_description->tour_notes); ?>
	</p>
<?php endif; ?>

<?php if (!empty($this->tour->pick_up_points)) : ?>
	<div class="lead"><?php echo Text::_('COM_JATOMS_TOUR_PICK_UP_POINTS'); ?></div>
	<ul class="unstyled">
		<?php foreach ($this->tour->pick_up_points as $point):
			$price = str_replace(' ', '', $point->price);
			preg_match('/([0-9])*/', $price, $number);
			if ($number && $number[0] == '0')
			{
				$point->price = Text::_('COM_JATOMS_PRICE_NO_SURCHARGE');
			}?>
			<li>
				<span><?php echo $point->city->name ?>: </span>
				<strong class="text-error"><?php echo $point->price ?></strong>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>