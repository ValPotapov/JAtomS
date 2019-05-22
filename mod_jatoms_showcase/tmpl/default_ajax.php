<?php
/**
 * @package    Joomla Atom-S Showcase Module
 * @version    1.0.0
 * @author     Septdir Workshop - septdir.com
 * @copyright  Copyright (c) 2018 - 2019 Septdir Workshop. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://www.septdir.com/
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('script', 'media/mod_jatoms_showcase/js/ajax.min.js', array('version' => 'auto'));

$url = Route::_('index.php?option=com_ajax&module=jatoms_showcase&format=json');
?>
<div class="mod_jatoms_showcase" mod_jatoms_showcase="container" data-module_id="<?php echo $module->id; ?>"
	 data-url="<?php echo $url; ?>">
	<div mod_jatoms_showcase="loading" class="center">
		<?php echo HTMLHelper::image('media/jui/images/ajax-loader.gif', ''); ?>
	</div>
	<div mod_jatoms_showcase="error" class="alert alert-danger" style="display: none"></div>
	<div class="items" mod_jatoms_showcase="items" style="display: none"></div>
</div>