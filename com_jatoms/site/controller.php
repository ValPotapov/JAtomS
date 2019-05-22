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
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

class JAtomSController extends BaseController
{
	/**
	 * The default view.
	 *
	 * @var  string
	 *
	 * @since  1.0.0
	 */
	protected $default_view = 'showcases';

	/**
	 * Typical view method for MVC based architecture.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe URL parameters and their variable types.
	 *
	 * @throws  Exception
	 *
	 * @return  BaseController  A BaseController object to support chaining.
	 *
	 * @since  1.0.0
	 */
	public function display($cachable = false, $urlparams = array())
	{
		// Duplicates protection
		if (Factory::getApplication()->getParams()->get('duplicates_protection', 1))
		{
			$view        = $this->input->get('view', $this->default_view);
			$id          = $this->input->get('id', 0);
			$showcase_id = $this->input->get('showcase_id', 0);
			$link        = false;

			if ($view == 'showcases')
			{
				$link = JAtomSHelperRoute::getShowcasesRoute();
			}

			if ($view == 'showcase')
			{
				$link = JAtomSHelperRoute::getShowcaseRoute($id);
			}

			if ($view == 'tour')
			{
				$link = JAtomSHelperRoute::getTourRoute($id, $showcase_id);
			}

			if ($link)
			{
				$uri       = Uri::getInstance();
				$root      = $uri->toString(array('scheme', 'host', 'port'));
				$canonical = Uri::getInstance(Route::_($link))->toString();
				$current   = $uri->toString(array('path', 'query', 'fragment'));

				if ($current !== $canonical)
				{
					Factory::getDocument()->addCustomTag('<link href="' . $root . $canonical . '" rel="canonical"/>');

					$redirect = Uri::getInstance(Route::_($link));
					if (!empty($uri->getVar('start')))
					{
						$redirect->setVar('start', $uri->getVar('start'));
					}
					if (!empty($uri->getVar('debug')))
					{
						$redirect->setVar('debug', $uri->getVar('debug'));
					}
					$redirect = $redirect->toString(array('path', 'query', 'fragment'));

					if (urldecode($current) != urldecode($redirect))
					{
						Factory::getApplication()->redirect($redirect, 301);
					}
				}
			}
		}

		return parent::display($cachable, $urlparams);
	}
}