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
use Joomla\Utilities\ArrayHelper;

?>
<div class="alert alert-info">
	<?php echo Text::_('COM_JATOMS_TOUR_HOTEL_SELECT_DESC'); ?>
</div>
<div>
	<?php foreach ($this->tour->nearest_trip->hotels as $h => $hotel):
		if ($h > 0) echo '<hr>';
		?>
		<div class="row-fluid">
			<div class="span8">
				<div class="clearfix">
					<?php if ($hotel->stars): ?>
						<div class="text-warning pull-right">
							<?php for ($i = 0; $i < $hotel->stars; $i++): ?>
								<i class="icon-star"></i>
							<?php endfor; ?>
						</div>
					<?php endif; ?>
					<div class="lead">
						<div>
							<?php echo $hotel->name; ?>
						</div>
					</div>
				</div>
				<p>
					<strong><?php echo Text::_('JGLOBAL_DESCRIPTION'); ?></strong><br>
					<?php if (!empty(trim(strip_tags($hotel->description)))):?>
						<?php echo nl2br(strip_tags($hotel->description)); ?><br>
					<?php endif; ?>
					<?php echo $hotel->city->name . ' ' . $hotel->address; ?>
				</p>
				<?php if ($hotel->accommodations): ?>
					<p>
						<strong><?php echo Text::_('COM_JATOMS_TOUR_AVAILABLE_ROOMS'); ?></strong><br>
						<?php echo implode('<br>', ArrayHelper::getColumn($hotel->accommodations, 'name')) ?>
					</p>
				<?php endif; ?>
			</div>
			<div class="span4">
				<?php if ($hotel->image): ?>
					<p>
						<a href="<?php echo $hotel->image->original; ?>" target="_blank">
							<?php echo HTMLHelper::image($hotel->image->medium, htmlspecialchars($hotel->name)); ?>
						</a>
					</p>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>