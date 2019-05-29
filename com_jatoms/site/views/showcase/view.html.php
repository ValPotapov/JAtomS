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
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

class JAtomSViewShowcase extends HtmlView
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
	 * Tours array.
	 *
	 * @var  array
	 *
	 * @since  1.0.0
	 */
	protected $items;

	/**
	 * Pagination object.
	 *
	 * @var  Pagination
	 *
	 * @since  1.0.0
	 */
	protected $pagination;

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
		$this->state      = $this->get('State');
		$this->params     = $this->state->get('params');
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->showcase   = $this->get('Item');
		$this->menu       = Factory::getApplication()->getMenu()->getActive();

		// Check for errors
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode('\n', $errors), 500);
		}

		// Create a shortcut for item
		$showcase = $this->showcase;

		// Check to see which parameters should take priority
		$temp = clone $this->params;
		$menu = $this->menu;

		if ($menu
			&& $menu->query['option'] == 'com_jatoms'
			&& $menu->query['view'] == 'showcase'
			&& @$menu->query['id'] == $showcase->id)
		{
			if (isset($menu->query['layout']))
			{
				$this->setLayout($menu->query['layout']);
			}
			elseif ($layout = $showcase->params->get('showcase_layout'))
			{
				$this->setLayout($layout);
			}

			$showcase->params->merge($temp);
		}
		else
		{
			$temp->merge($showcase->params);
			$showcase->params = $temp;

			if ($layout = $showcase->params->get('showcase_layout'))
			{
				$this->setLayout($layout);
			}
		}
		$this->params = $showcase->params;

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
		$app      = Factory::getApplication();
		$root     = Uri::getInstance()->toString(array('scheme', 'host', 'port'));
		$showcase = $this->showcase;
		$menu     = $this->menu;
		$current  = ($menu && $menu->query['option'] === 'com_jatoms'
			&& $menu->query['view'] === 'showcase'
			&& (int) @$menu->query['id'] === (int) $showcase->id);

		// Add showcase pathway item if no current menu
		if ($menu && !$current)
		{
			$paths = array(array('title' => $showcase->title, 'link' => ''));

			// Add showcases pathway item if no current menu
			if ($menu->query['option'] !== 'com_jatoms'
				|| $menu->query['view'] !== 'showcases')
			{
				$paths[] = array('title' => Text::_('COM_JATOMS_SHOWCASES'),
				                 'link'  => Route::_(JAtomSHelperRoute::getShowcasesRoute()));
			}

			// Add pathway items
			$pathway = $app->getPathway();
			foreach (array_reverse($paths) as $path)
			{
				$pathway->addItem($path['title'], $path['link']);
			}
		}

		// Set meta title
		$title    = (!$current && $showcase->id > 0) ? $showcase->title : $this->params->get('page_title');
		$sitename = $app->get('sitename');
		$page     = ((int) $this->state->get('list.start', 0) / (int) $this->state->get('list.limit', 10)) + 1;
		if ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = Text::sprintf('JPAGETITLE', $sitename, $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = Text::sprintf('JPAGETITLE', $title, $sitename);
		}
		if ($page > 1)
		{
			$title = Text::sprintf('COM_JATOMS_META_PAGINATION_TITLE', $title, $page);
		}
		$this->document->setTitle($title);

		// Set meta description
		if ($current && $page <= 1 && $this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}
		elseif ($page <= 1 && !empty($showcase->description))
		{
			$this->document->setDescription(JHtmlString::truncate($showcase->description, 150, false, false));
		}

		// Set meta image
		if ($current && $this->params->get('menu-meta_image'))
		{
			$this->document->setDescription($this->params->get('menu-meta_image'));
		}
		elseif (!empty($showcase->images->get('icon')))
		{
			$this->document->setMetadata('image', $root . '/' . $showcase->images->get('icon'));
		}

		// Set meta keywords
		if ($current && $this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
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
		$url = $root . $showcase->link;
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