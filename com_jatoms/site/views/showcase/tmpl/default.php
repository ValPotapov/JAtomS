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

HTMLHelper::_('jquery.framework');
HTMLHelper::_('behavior.tooltip');

HTMLHelper::stylesheet('media/com_jatoms/css/site.min.css', array('version' => 'auto'));
?>
<div id="JAtomS" class="showcase" data-showcaseid="<?php echo $this->showcase->id; ?>">
	<div class="showcase-info">
		<h1><?php echo $this->showcase->title; ?></h1>
		<div class="row-fluid">
			<div class="span4">
				<?php if ($icon = $this->showcase->images->get('icon')): ?>
					<p>
						<?php echo HTMLHelper::image($icon, $this->showcase->title); ?>
					</p>
				<?php endif; ?>
			</div>
			<div class="span8">
				<?php if ($this->showcase->description): ?>
					<div class="description">
						<?php echo $this->showcase->description; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo Text::_('COM_JATOMS_ERROR_TOURS_NOT_FOUND'); ?>
		</div>
	<?php else : ?>
		<div class="toursList">
			<h2><?php echo Text::_('COM_JATOMS_TOURS'); ?></h2>
			<div class="items">
				<?php foreach ($this->items as $item)
				{
					$this->item = &$item;
					echo $this->loadTemplate('item');
				}; ?>
			</div>
			<div class="pagination">
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
		</div>
	<?php endif; ?>
</div>