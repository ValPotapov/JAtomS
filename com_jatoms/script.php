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
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;

class com_jatomsInstallerScript
{
	/**
	 * Runs right after any installation action.
	 *
	 * @param   string            $type    Type of PostFlight action. Possible values are:
	 * @param   InstallerAdapter  $parent  Parent object calling object.
	 *
	 * @throws  Exception
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	function postflight($type, $parent)
	{
		// Check databases
		$this->checkTables($parent);

		// Clean caches
		if ($type == 'update')
		{
			$this->cleanCaches();
		}
	}

	/**
	 * Method to create database tables in not exist.
	 *
	 * @param   InstallerAdapter  $parent  Parent object calling object.
	 *
	 * @since  1.0.0
	 */
	protected function checkTables($parent)
	{
		if ($sql = file_get_contents($parent->getParent()->getPath('extension_administrator')
			. '/sql/install.mysql.utf8.sql'))
		{
			$db = Factory::getDbo();

			foreach ($db->splitSql($sql) as $query)
			{
				$db->setQuery($db->convertUtf8mb4QueryToUtf8($query));
				try
				{
					$db->execute();
				}
				catch (JDataBaseExceptionExecuting $e)
				{
					Log::add(Text::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $e->getMessage()), Log::WARNING, 'jerror');
				}
			}
		}
	}

	/**
	 * Method to clean caches after update.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected function cleanCaches()
	{
		JLoader::register('JAtomSHelperCache', JPATH_SITE . '/components/com_jatoms/helpers/cache.php');
		$folders = JAtomSHelperCache::$paths;
		foreach ($folders as $folder)
		{
			$folder = str_replace('administrator/', '', $folder);
			if (Folder::exists($folder))
			{
				Folder::delete($folder);
			}
		}
	}
}