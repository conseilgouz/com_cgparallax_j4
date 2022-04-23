<?php
/**
 * CG Parallax Component  - Joomla 4.0.0 Component 
 * Version			: 2.1.2
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2022 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/

namespace ConseilGouz\Component\CGParallax\Administrator\Model;
defined('_JEXEC') or die;
use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Form\Form;

class PageModel extends AdminModel {

    protected function preprocessForm(Form $form, $data, $group = 'content')
    {
        parent::preprocessForm($form, $data, $group);
    }
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_cgparallax.page', 'page', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form))
        {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return    mixed    The data for the form.
     */
    protected function loadFormData()
    {
		
		$data = Factory::getApplication()->getUserState('com_cgparallax.edit.item.data', array());

		if (empty($data)) $data = $this->getItem();
        // split general parameters
		$compl = new Registry($data->page_params);
        $data->intro = $compl['intro'];
        $data->bottom = $compl['bottom'];
		$data->menu = $compl['menu'];
        $data->sticky = $compl['sticky'];
        $data->navbar_bg = $compl['navbar_bg'];
        $data->navbar_color = $compl['navbar_color'];
        $data->magic = $compl['magic'];
        $data->magic_active = $compl['magic_active'];
        $data->css_gen = $compl['css_gen'];
        $data->sectionsList = $data->sections;
		return $data;
    }
    /**
     *  Method to validate form data.
     */
    public function validate($form, $data, $group = null)
    {
        $name = $data['name'];
        unset($data["name"]);

        return array(
            'name'   => $name,
            'params' => json_encode($data)
        );
    }
	
}
