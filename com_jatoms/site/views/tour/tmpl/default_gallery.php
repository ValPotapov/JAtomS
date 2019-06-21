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

?>
<div>
	<?php foreach ($this->tour->images as $image): ?>
		<p>
			<a href="<?php echo $image->original; ?>" target="_blank">
				<?php echo HTMLHelper::image($image->medium, htmlspecialchars($this->tour->name)); ?>
			</a>
		</p>
	<?php endforeach; ?>
</div>