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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Menu\MenuItem;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;

class JAtomSViewBooking extends HtmlView
{
	/**
	 * Model state variables.
	 *
	 * @var  Joomla\CMS\Object\CMSObject
	 *
	 * @since  1.1.0
	 */
	protected $state;

	/**
	 * Application params.
	 *
	 * @var  Registry;
	 *
	 * @since  1.1.0
	 */
	public $params;

	/**
	 * Booking object.
	 *
	 * @var  object|false
	 *
	 * @since  1.1.0
	 */
	protected $booking;

	/**
	 * Tour object.
	 *
	 * @var  object|false
	 *
	 * @since  1.1.0
	 */
	protected $tour;

	/**
	 * Showcase object.
	 *
	 * @var  object|false
	 *
	 * @since  1.1.0
	 */
	protected $showcase;

	/**
	 * Active menu item.
	 *
	 * @var  MenuItem
	 *
	 * @since  1.1.0
	 */
	protected $menu;

	/**
	 * Page class suffix from params.
	 *
	 * @var  string
	 *
	 * @since  1.1.0
	 */
	public $pageclass_sfx;

	/**
	 * Display the view.
	 *
	 * @param   string  $tpl  The name of the template file to parse.
	 *
	 * @throws  Exception
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @since  1.1.0
	 */
	public function display($tpl = null)
	{
		$this->state    = $this->get('State');
		$this->params   = $this->state->get('params');
		$this->booking  = $this->get('Item');
		$this->tour     = $this->booking->tour;
		$this->showcase = $this->tour->showcase;
		$this->menu     = Factory::getApplication()->getMenu()->getActive();

		// Check for errors
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode('\n', $errors), 500);
		}

		// Create a shortcut for item
		$booking = $this->booking;

		// Check to see which parameters should take priority
		$temp = clone $this->params;
		$menu = $this->menu;
		if ($menu
			&& $menu->query['option'] == 'com_jatoms'
			&& (int) @$menu->query['tour_id'] === (int) $this->tour->id)
		{
			if (isset($menu->query['layout']))
			{
				$this->setLayout($menu->query['layout']);
			}
			elseif ($layout = $booking->params->get('booking_layout'))
			{
				$this->setLayout($layout);
			}

			$booking->params->merge($temp);
		}
		else
		{
			$temp->merge($booking->params);
			$booking->params = $temp;

			if ($layout = $booking->params->get('booking_layout'))
			{
				$this->setLayout($layout);
			}
		}
		$this->params = $booking->params;

		// Escape strings for html output
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

		// Prepare the document
		$this->_prepareDocument();

		return parent::display($tpl);
	}

	/**
	 * Prepare the document.
	 *
	 * @throws  Exception
	 *
	 * @since  1.1.0
	 */
	protected function _prepareDocument()
	{
		$app     = Factory::getApplication();
		$booking = $this->booking;
		$tour    = $this->tour;
		$menu    = $this->menu;
		$current = ($menu
			&& $menu->query['option'] === 'com_jatoms'
			&& $menu->query['view'] === 'booking'
			&& (int) @$menu->query['tour_id'] === (int) $tour->id);

		// Add booking pathway item if no current menu
		if ($menu && !$current)
		{
			$paths = array(array('title' => Text::_('COM_JATOMS_BOOKING'), 'link' => ''));

			// Add tour pathway item if no current menu
			if ($menu->query['option'] !== 'com_jatoms'
				|| $menu->query['view'] !== 'tour'
				|| (int) @$menu->query['id'] !== (int) $tour->id)
			{

				$paths[] = array('title' => $tour->name,
				                 'link'  => $tour->link);

				// Add showcase pathway item if no current menu
				$showcase = $this->showcase;
				if ($menu->query['option'] !== 'com_jatoms'
					|| $menu->query['view'] !== 'showcase'
					|| (int) @$menu->query['id'] !== (int) $showcase->id)
				{
					$paths[] = array('title' => $showcase->title, 'link' => $showcase->link);

					// Add showcases pathway item if no current menu
					if ($menu->query['option'] !== 'com_jatoms'
						|| $menu->query['view'] !== 'showcases')
					{
						$paths[] = array('title' => Text::_('COM_JATOMS_SHOWCASES'),
						                 'link'  => Route::_(JAtomSHelperRoute::getShowcasesRoute()));
					}
				}

			}

			// Add pathway items
			$pathway = $app->getPathway();
			foreach (array_reverse($paths) as $path)
			{
				$pathway->addItem($path['title'], $path['link']);
			}
		}

		// Set meta title
		$title    = (!$current) ? $booking->title : $this->params->get('page_title');
		$sitename = $app->get('sitename');

		if ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = Text::sprintf('JPAGETITLE', $sitename, $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = Text::sprintf('JPAGETITLE', $title, $sitename);
		}
		$this->document->setTitle($title);

		// Set meta robots
		$this->document->setMetadata('robots', 'noindex');
	}
}