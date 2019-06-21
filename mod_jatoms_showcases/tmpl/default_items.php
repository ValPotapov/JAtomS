<?php
/**
 * @package    Joomla Atom-S Showcases Module
 * @version    1.1.0
 * @author     Septdir Workshop - septdir.com
 * @copyright  Copyright (c) 2018 - 2019 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

?>
<?php foreach ($items as $item) : ?>
	<div class="showcase-<?php echo $item->id; ?> well">
		<div class="lead"><a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a></div>
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