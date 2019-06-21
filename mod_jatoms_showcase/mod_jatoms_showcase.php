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

require_once __DIR__ . '/helper.php';
$helper = new modJAtomSShowcaseHelper();

$helper::loadLanguage();

$layout = $params->get('layout', 'default');
if (!$params->get('ajax'))
{
	$items = $helper::getItems($params);
}
else
{
	$layout .= '_ajax';
}

require ModuleHelper::getLayoutPath($module->module, $layout);