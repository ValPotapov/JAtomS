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

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('jquery.framework');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.tabstate');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::stylesheet('media/com_jatoms/css/admin.min.css', array('version' => 'auto'));

Factory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "showcase.cancel" || document.formvalidator.isValid(document.getElementById("item-form")))
		{
			Joomla.submitform(task, document.getElementById("item-form"));
		}
	};
');
?>
<form action="<?php echo Route::_('index.php?option=com_jatoms&view=showcase&id=' . $this->item->id); ?>"
	  method="post" name="adminForm" id="item-form" class="form-validate" enctype="multipart/form-data">
	<fieldset class="form-inline form-inline-header">
		<?php echo $this->form->renderField('title'); ?>
		<?php echo $this->form->renderField('alias'); ?>
		<?php echo $this->form->renderField('key'); ?>
	</fieldset>
	<div class="row-fluid">
		<div class="span9">
			<?php echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'general', 'class')); ?>
			<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'general', Text::_('JGLOBAL_FIELDSET_CONTENT')); ?>
			<fieldset class="form-horizontal">
				<div class="control-group">
					<p class="lead">
						<?php echo Text::_('JGLOBAL_DESCRIPTION'); ?>
					</p>
					<?php echo $this->form->getInput('description'); ?>
				</div>
			</fieldset>
			<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
			<?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>
		</div>
		<div class="span3">
			<fieldset class="well form-horizontal form-horizontal-desktop">
				<?php echo $this->form->renderFieldset('global'); ?>
			</fieldset>
		</div>
	</div>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="return" value="<?php echo Factory::getApplication()->input->getCmd('return'); ?>"/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>