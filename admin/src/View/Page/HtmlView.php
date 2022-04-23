<?php
/**
 * CG Parallax Component  - Joomla 4.0.0 Component 
 * Version			: 2.1.2
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2022 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
namespace ConseilGouz\Component\CGParallax\Administrator\View\Page;
// No direct access
\defined('_JEXEC') or die;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView {
    protected $form;
    protected $pagination;
    protected $state;
	protected $page;

    /**
     * Display the view
     */
    public function display($tpl = null) {

		$this->form		= $this->get('Form');
		$this->page		= $this->get('Item');
		$this->formControl = $this->form ? $this->form->getFormControl() : null;	
		$this->page_params  = new Registry($this->page->page_params);			
	
        $this->addToolbar();

        // $this->sidebar = HtmlSidebar::render();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since	1.6
     */
    protected function addToolbar() {
		$canDo = ContentHelper::getActions('com_cgparallax');
        $state = $this->get('State');
        /*$input = Factory::getApplication()->input;
		$input->setVar('hidemainmenu', true);
		*/
		$user		= Factory::getUser();
		$userId		= $user->get('id');
		if (!isset($this->page->id)) $this->page->id = 0;
		$isNew		= ($this->page->id == 0);

		ToolBarHelper::title($isNew ? Text::_('CG_PX_ITEM_NEW') : Text::_('CG_PX_ITEM_EDIT'), '#xs#.png');

		// If not checked out, can save the item.
		if ($canDo->get('core.edit')) {
			ToolBarHelper::apply('page.apply');
			ToolBarHelper::save('page.save');
		}

		if (empty($this->page->id))  {
			ToolBarHelper::cancel('page.cancel');
		}
		else {
			ToolBarHelper::cancel('page.cancel', 'JTOOLBAR_CLOSE');
		}
    }

}
