<?php
/**
 * CG Parallax Component  - Joomla 4.x/5.x/6.x Component 
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @copyright (c) 2025 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
namespace ConseilGouz\Component\CGParallax\Administrator\View\Pages;
// No direct access
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
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Initialise variables.
		$this->pages		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			Factory::getApplication()->enqueueMessage(implode("\n", $errors),'error');
			return false;
		}

		$this->addToolbar();
		// $this->sidebar = HtmlSidebar::render();
		
		parent::display($tpl);
	}

	protected function addToolbar()
	{
        $canDo = ContentHelper::getActions('com_cgparallax');
		$user	= Factory::getApplication()->getIdentity();
		ToolBarHelper::title(Text::_('COM_CGPARALLAX_PAGES'), 'page.png');

		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_cgparallax', 'core.create'))) > 0 ) {
			ToolBarHelper::addNew('page.add');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own'))) {
			ToolBarHelper::editList('page.edit');
		}

		if ($canDo->get('core.edit.state')) {
			ToolBarHelper::divider();
			ToolBarHelper::publish('pages.publish', 'JTOOLBAR_PUBLISH', true);
			ToolBarHelper::unpublish('pages.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			ToolBarHelper::divider();
		}

		if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			ToolBarHelper::deleteList('', 'pages.delete','JTOOLBAR_EMPTY_TRASH');			
		}
		else if ($canDo->get('core.edit.state')) {
			ToolBarHelper::trash('pages.trash');
		} 

		if ($canDo->get('core.admin')) {
			ToolBarHelper::divider();
			ToolBarHelper::preferences('com_cgparallax');			
		}
	}
}
