<?php
/**
 * CG Parallax Component  - Joomla 4.0.0 Component 
 * Version			: 2.1.2
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2022 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
namespace ConseilGouz\Component\CGParallax\Administrator\Field;
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
/* cette classe est utile pour le choix menu : affichage des pages */
class PagesField extends ListField
{
    public $type = 'Pages';

    public function getOptions()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('s.id AS value')
            ->select('s.title AS text')
            ->from('#__cgparallax_page AS s')
            ->where('s.state = ' . $db->quote(1));
        $db->setQuery($query);

        $options = $db->loadObjectList();

        array_unshift($options, HtmlHelper::_('select.option', '', Text::_('JSELECT')));

        return array_merge(parent::getOptions(), $options);
    }

}