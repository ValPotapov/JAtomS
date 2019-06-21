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

?>
<div id="JAtomS" class="booking" data-tour_id="<?php echo $this->tour->id; ?>">
	<h1><?php echo $this->booking->title; ?></h1>
	<iframe src="<?php echo $this->booking->iframe; ?>" frameborder="0" class="span12" style="height: 100vh"></iframe>
</div>