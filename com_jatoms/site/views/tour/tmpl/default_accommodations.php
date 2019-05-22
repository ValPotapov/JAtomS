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
<div class="alert alert-info">
	<?php echo Text::_('COM_JATOMS_TOUR_HOTEL_SELECT_DESC'); ?>
</div>
<div>
	<?php foreach ($this->tour->nearest_trip->hotels as $h => $hotel):
		if ($h > 0) echo '<hr>';
		?>
		<div>
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
					<div class="small muted">
						<?php echo $hotel->city->name; ?>
					</div>
				</div>
			</div>
			<p>
				<?php echo nl2br($hotel->description); ?>
			</p>
		</div>
	<?php endforeach; ?>
</div>