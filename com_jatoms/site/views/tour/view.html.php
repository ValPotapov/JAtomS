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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Menu\MenuItem;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

class JAtomSViewTour extends HtmlView
{
	/**
	 * Model state variables.
	 *
	 * @var  Joomla\CMS\Object\CMSObject
	 *
	 * @since  1.0.0
	 */
	protected $state;

	/**
	 * Application params.
	 *
	 * @var  Registry;
	 *
	 * @since  1.0.0
	 */
	public $params;

	/**
	 * Tour object.
	 *
	 * @var  object|false
	 *
	 * @since  1.0.0
	 */
	protected $tour;

	/**
	 * Showcase object.
	 *
	 * @var  object|false
	 *
	 * @since  1.0.0
	 */
	protected $showcase;

	/**
	 * Active menu item.
	 *
	 * @var  MenuItem
	 *
	 * @since  1.0.0
	 */
	protected $menu;

	/**
	 * Page class suffix from params.
	 *
	 * @var  string
	 *
	 * @since  1.0.0
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
	 * @since  1.0.0
	 */
	public function display($tpl = null)
	{
		$this->state    = $this->get('State');
		$this->params   = $this->state->get('params');
		$this->tour     = $this->get('Item');
		$this->showcase = $this->tour->showcase;
		$this->menu     = Factory::getApplication()->getMenu()->getActive();

		// Check for errors
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode('\n', $errors), 500);
		}

		// Create a shortcut for item
		$tour = $this->tour;

		// Check to see which parameters should take priority
		$temp = clone $this->params;
		$menu = $this->menu;
		if ($menu
			&& $menu->query['option'] == 'com_jatoms'
			&& $menu->query['view'] == 'tour'
			&& @$menu->query['id'] == $tour->id)
		{
			if (isset($menu->query['layout']))
			{
				$this->setLayout($menu->query['layout']);
			}
			elseif ($layout = $tour->params->get('tour_layout'))
			{
				$this->setLayout($layout);
			}

			$tour->params->merge($temp);
		}
		else
		{
			$temp->merge($tour->params);
			$tour->params = $temp;

			if ($layout = $tour->params->get('tour_layout'))
			{
				$this->setLayout($layout);
			}
		}
		$this->params = $tour->params;


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
	 * @since  1.0.0
	 */
	protected function _prepareDocument()
	{
		$app     = Factory::getApplication();
		$root    = Uri::getInstance()->toString(array('scheme', 'host', 'port'));
		$tour    = $this->tour;
		$menu    = $this->menu;
		$current = ($menu
			&& $menu->query['option'] === 'com_jatoms'
			&& $menu->query['view'] === 'tour'
			&& (int) @$menu->query['id'] === (int) $tour->id);

		// Add tour pathway item if no current menu
		if ($menu && !$current)
		{
			$paths = array(array('title' => $tour->name, 'link' => ''));

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

			// Add pathway items
			$pathway = $app->getPathway();
			foreach (array_reverse($paths) as $path)
			{
				$pathway->addItem($path['title'], $path['link']);
			}
		}

		// Set meta title
		$title    = (!$current) ? $tour->name : $this->params->get('page_title');
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

		// Set meta description
		if ($current && $this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}
		elseif (!empty($tour->description))
		{
			$this->document->setDescription(JHtmlString::truncate($tour->description, 150, false, false));
		}

		// Set meta keywords
		if ($current && $this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		// Set meta image
		if ($current && $this->params->get('menu-meta_image'))
		{
			$this->document->setDescription($this->params->get('menu-meta_image'));
		}
		elseif (!empty($tour->image))
		{
			$this->document->setMetadata('image', $root . '/' . $tour->image->medium);
		}

		// Set meta robots
		if ($this->state->get('debug', 0))
		{
			$this->document->setMetadata('robots', 'noindex');
		}
		elseif ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}

		// Set meta url
		$url = $root . $tour->link;
		$this->document->setMetaData('url', $url);

		// Set meta twitter
		$this->document->setMetaData('twitter:card', 'summary_large_image');
		$this->document->setMetaData('twitter:site', $sitename);
		$this->document->setMetaData('twitter:creator', $sitename);
		$this->document->setMetaData('twitter:title', $title);
		$this->document->setMetaData('twitter:url', $url);
		if ($description = $this->document->getMetaData('description'))
		{
			$this->document->setMetaData('twitter:description', $description);
		}
		if ($image = $this->document->getMetaData('image'))
		{
			$this->document->setMetaData('twitter:image', $image);
		}

		// Set meta open graph
		$this->document->setMetadata('og:type', 'website', 'property');
		$this->document->setMetaData('og:site_name', $sitename, 'property');
		$this->document->setMetaData('og:title', $title, 'property');
		$this->document->setMetaData('og:url', $url, 'property');
		if ($description)
		{
			$this->document->setMetaData('og:description', $description, 'property');
		}
		if ($image)
		{
			$this->document->setMetaData('og:image', $image, 'property');
		}
	}
}