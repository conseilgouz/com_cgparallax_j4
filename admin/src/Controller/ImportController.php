<?php
/**
 * CG Parallax Component  - Joomla 4.0.0 Component 
 * Version			: 2.1.2
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2022 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
namespace ConseilGouz\Component\CGParallax\Administrator\Controller;
\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\String\StringHelper;
use Joomla\CMS\Router\Route;

class ImportController extends FormController
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_CGPARALLAX_IMPORT';

    public function add($key = null, $urlVar = null) 
    {
        // Check for request forgeries.
        Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));
        $app = Factory::getApplication();
        $input = $app->input;
		$pks = $input->post->get('cid', array(), 'array');
        $db    = Factory::getDbo();
		foreach ($pks as $id)	{
            $result = $db->setQuery(
                $db->getQuery(true)
                ->select('*')
                ->from($db->quoteName('#__modules'))
                ->where($db->quoteName('id') . ' = ' . (int)$id)
            )->loadAssocList();
            if (count($result) != 1) {
                $this->setMessage(Text::sprintf('CG_PX_MODULE_SELECT_ERROR', $id), 'warning');
                $this->setRedirect(Route::_('index.php?option=com_cgparallax&view=import', false));
                return false;
            }
            $data = new \StdClass();
            $data->id= 0;
            $data->title = $this->check_title($result[0]['title']);
            $data->state = $result[0]['published'];
            $data->language = $result[0]['language'];            
            $data->metakey = $result[0]['metakey'];            
            $page_params = [];
            $compl = json_decode($result[0]['params']);
			$page_params['menu'] = $compl->menu;
			$page_params['sticky'] = $compl->sticky;
			$page_params['navbar_bg'] = $compl->navbar_bg;
			$page_params['navbar_color'] = $compl->navbar_color;
			$page_params['magic'] = $compl->magic;
			$page_params['magic_active'] = $compl->magic_active;
			$page_params['css_gen'] = $compl->css_gen;
			$page_params['intro'] = $compl->intro;
			$page_params['bottom'] = $compl->bottom;
			$data->page_params =  json_encode($page_params);
            foreach ($compl->sectionsList as $section=>$onesection) { // vegas in module = array => convert it in object
                $vegas = json_decode($onesection->vegas_config);
                $onesection->vegas_delay = $vegas->vegas_delay[0] ? $vegas->vegas_delay[0] : "12000" ;
                $onesection->vegas_trans = $vegas->vegas_trans[0] ? $vegas->vegas_trans[0] : "fade" ;
                $onesection->vegas_duration = $vegas->vegas_duration[0] ? $vegas->vegas_duration[0] : "1000";
                $onesection->vegas_anim = $vegas->vegas_anim[0] ? $vegas->vegas_anim[0] : "random";
                if (($onesection->cg_img_type == 'one') && (strlen($onesection->image) == 0) ) {
                    $onesection->cg_img_type = 'none';
                }
				$onesection->section_title = $onesection->title; 
                unset($onesection->vegas_config);
                unset($onesection->title);
                $compl->sectionsList->{$section} = $onesection;
            }
            $data->sections = json_encode($compl->sectionsList);
            $ret = $db->insertObject('#__cgparallax_page', $data,'id');
            if (!$ret) {
                $this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $ret), 'warning');
                $this->setRedirect(JRoute::_('index.php?option=com_cgparallax&view=import', false));
                return false;
            }
        }
        $this->setMessage(Text::sprintf('CG_PX_MODULE_IMPORTED', count($pks)), 'notice');
        $this->setRedirect(Route::_('index.php?option=com_cgparallax&view=import', false));
        return false;
        }
	function check_title($title) {
        $db    = Factory::getDbo();
        do {
			$result = $db->setQuery(
                $db->getQuery(true)
                ->select(count('*'))
                ->from($db->quoteName('#__cgparallax_page'))
                ->where($db->quoteName('title') . ' like ' . $db->quote($title) .' AND state in (0,1)')
            )->loadResult();
			if ($result > 0) $title = StringHelper::increment($title);
		}
		while ($result > 0);
		return $title;
	}

}