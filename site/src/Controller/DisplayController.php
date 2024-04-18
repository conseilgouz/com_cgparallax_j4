<?php
/**
 * CG Parallax Component  - Joomla 4.x/5.x Component
 * Version			: 2.2.0
 * @license httpd://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @copyright (c) 2024 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz
*
*/

namespace ConseilGouz\Component\CGParallax\Site\Controller;

\defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

class DisplayController extends BaseController
{
    public function display($cachable = false, $urlparams = false)
    {

        $view = Factory::getApplication()->input->getCmd('view', 'page');
        Factory::getApplication()->input->set('view', $view);

        parent::display($cachable, $urlparams);

        return $this;
    }
    public function getArticle()
    {
        if (!Session::checkToken('get')) {
            echo new JsonResponse(null, Text::_('JINVALID_TOKEN'), true);
        } else {
            parent::display();
        }

    }
}
