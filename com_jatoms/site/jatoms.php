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

JLoader::register('JAtomSHelperRoute', JPATH_SITE . '/components/com_jatoms/helpers/route.php');

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

$controller = BaseController::getInstance('JAtomS');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();