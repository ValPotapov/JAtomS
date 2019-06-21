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

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;

?>
<div class="mod_jatoms_showcase">
	<?php if (empty($items)): ?>
		<div class="alert alert-no-items">
			<?php echo Text::_('COM_JATOMS_ERROR_TOURS_NOT_FOUND'); ?>
		</div>
	<?php else: ?>
		<div class="items">
			<?php require ModuleHelper::getLayoutPath($module->module, $layout . '_items'); ?>
		</div>
	<?php endif; ?>
</div>