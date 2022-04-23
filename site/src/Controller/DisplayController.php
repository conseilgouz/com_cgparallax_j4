<?php
/**
 * CG Parallax Component  - Joomla 4.0.0 Component 
 * Version			: 2.1.2
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2022 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
*
*/
namespace ConseilGouz\Component\CGParallax\Site\Controller;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Language\Text;

class DisplayController extends BaseController {

    public function display($cachable = false, $urlparams = false) {
        require_once JPATH_COMPONENT . '/src/Helper/CGHelper.php';

        $view = Factory::getApplication()->input->getCmd('view', 'page');
        Factory::getApplication()->input->set('view', $view);

        parent::display($cachable, $urlparams);

        return $this;
    }
	public function getArticle() {
        if (!JSession::checkToken('get')) 
        {
            echo new JResponseJson(null, Text::_('JINVALID_TOKEN'), true);
        }
        else 
        {
            parent::display();
        }
		
	}
}
