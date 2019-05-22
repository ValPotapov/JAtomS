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

use Joomla\CMS\Language\Text;

?>
<h1><?php echo $this->tour->name; ?></h1>
<p class="tags">
	<?php if ($this->tour->nearest_trip->booking_kind && $this->tour->nearest_trip->booking_kind == "instant_booking"): ?>
		<span class="label label-success hasTip"
			  title="<?php echo Text::_('COM_JATOMS_TOUR_INSTANT_BOOKING_DESC'); ?>">
			<?php echo Text::_('COM_JATOMS_TOUR_INSTANT_BOOKING'); ?>
		</span>
	<?php endif; ?>
	<?php if ($this->tour->is_cyclic_tour): ?>
		<span class="label hasTip"
			  title="<?php echo Text::_('COM_JATOMS_TOUR_CYCLIC_DESC'); ?>">
			<?php echo Text::_('COM_JATOMS_TOUR_CYCLIC'); ?>
		</span>
	<?php endif; ?>
	<?php if (!empty($this->tour->nearest_trip->hotels) && count($this->tour->nearest_trip->hotels) > 1): ?>
		<span class="label hasTip"
			  title="<?php echo Text::_('COM_JATOMS_TOUR_HOTEL_SELECT_DESC'); ?>">
			<?php echo Text::sprintf('COM_JATOMS_TOUR_HOTEL_SELECT'); ?>
		</span>
	<?php endif; ?>
</p>