<?php
/**
 * @package    Joomla Atom-S Component
 * @version    __DEPLOY_VERSION__
 * @author     Septdir Workshop - septdir.com
 * @copyright  Copyright (c) 2018 - 2019 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

?>
<?php if ($this->tour->detailed_description->included_services): ?>
	<div class="lead"><?php echo Text::_('COM_JATOMS_TOUR_INCLUDED_SERVICES'); ?></div>
	<p>
		<?php echo nl2br($this->tour->detailed_description->included_services); ?>
	</p>
<?php endif; ?>
<?php if ($this->tour->detailed_description->not_included_services): ?>
	<div class="lead"><?php echo Text::_('COM_JATOMS_TOUR_NOT_INCLUDED_SERVICES'); ?></div>
	<p>
		<?php echo nl2br($this->tour->detailed_description->not_included_services); ?>
	</p>
<?php endif; ?>
<?php if ($this->tour->detailed_description->insurance): ?>
	<div class="lead"><?php echo Text::_('COM_JATOMS_TOUR_INSURANCE'); ?></div>
	<p>
		<?php echo nl2br($this->tour->detailed_description->insurance); ?>
	</p>
<?php endif; ?>