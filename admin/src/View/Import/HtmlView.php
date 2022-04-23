<?php
/**
 * CG Parallax Component  - Joomla 4.0.0 Component 
 * Version			: 2.1.2
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2022 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
namespace ConseilGouz\Component\CGParallax\Administrator\View\Import;

\defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
	protected $pages;
	protected $pagination;
	protected $state;
    protected $modules;
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Initialise variables.
		$this->pages		= $this->get('Items');
        $this->modules          = $this->getModules();
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
        $input = Factory::getApplication()->input;
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			Factory::getApplication()->enqueueMessage(implode("\n", $errors),'error');
			return false;
		}

		$this->addToolbar();
		// $this->sidebar = JHtmlSidebar::render();
		
		parent::display($tpl);
	}

	protected function addToolbar()
	{
        $canDo = ContentHelper::getActions('com_cgparallax');
		$user	= Factory::getUser();
		ToolBarHelper::title(Text::_('COM_CGPARALLAX_PAGES'), 'page.png');

		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_cgparallax', 'core.create'))) > 0 ) {
			ToolBarHelper::custom('import.add','checkbox-partial','','import');
		}
		if ($canDo->get('core.admin')) {
			ToolBarHelper::divider();
			ToolBarHelper::preferences('com_cgparallax');			
		}
	}
        protected function getModules() {
            $db    = Factory::getDBo();
            $result = $db->setQuery(
                $db->getQuery(true)
                ->select('*')
                ->from($db->quoteName('#__modules'))
                ->where($db->quoteName('module') . ' like ' . $db->quote('mod_cg_parallax'))
            )->loadAssocList();
            return $result;   
        }
	
}
