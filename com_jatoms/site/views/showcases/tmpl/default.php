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

HTMLHelper::stylesheet('media/com_jatoms/css/site.min.css', array('version' => 'auto'));
?>
<div id="JAtomS" class="showcases">
	<div class="page-header">
		<h1>
			<?php echo ($this->params->get('show_page_heading') && $this->params->get('page_heading'))
				? $this->params->get('page_heading') : Text::_('COM_JATOMS_SHOWCASES'); ?>
		</h1>
	</div>
	<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo Text::_('COM_JATOMS_ERROR_SHOWCASES_NOT_FOUND'); ?>
		</div>
	<?php else : ?>
		<div class="showcasesList">
			<div class="items">
				<?php foreach ($this->items as $item) : ?>
					<div class="showcase-<?php echo $item->id; ?> well">
						<h2><a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a></h2>
						<div class="row-fluid">
							<div class="span8">
								<?php if ($item->description): ?>
									<div class="description">
										<?php echo $item->description; ?>
									</div>
								<?php endif; ?>
							</div>
							<div class="span4">
								<?php if ($icon = $item->images->get('icon')): ?>
									<p>
										<?php echo HTMLHelper::image($icon, $item->title); ?>
									</p>
								<?php endif; ?>
							</div>
						</div>
						<div class="text-right">
							<a href="<?php echo $item->link; ?>" class="btn btn-primary">
								<?php echo Text::_('COM_JATOMS_TOURS_LIST'); ?>
							</a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="pagination">
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
		</div>
	<?php endif; ?>
</div>