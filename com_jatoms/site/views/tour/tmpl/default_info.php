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
<div>
	<div class="text-right">
		<div class="small muted">
			<?php echo ($this->tour->is_group_tour) ? Text::_('COM_JATOMS_PRICE_FROM')
				: Text::_('COM_JATOMS_PRICE_FROM_PERSON'); ?>
		</div>
		<div class="lead text-success">
			<?php echo ($this->tour->is_group_tour) ? $this->tour->nearest_trip->prices[0]->price
				: $this->tour->nearest_trip->min_adult_main_price; ?>
		</div>
	</div>
	<p>
		<?php if (!$this->tour->is_cyclic_tour && !$this->tour->is_group_tour): ?>
			<a href="#availability" class="btn btn-primary span12">
				<?php echo Text::_('COM_JATOMS_TOUR_AVAILABILITY_CHECK'); ?>
			</a>
		<?php else: ?>
			<a href="<?php echo $this->tour->order; ?>" class="btn btn-primary span12" target="_blank">
				<?php echo Text::_('COM_JATOMS_BUY'); ?>
			</a>
		<?php endif; ?>
	</p>
</div>