<?php
/**
 * @component     CG Parallax
 * Version			: 2.1.4
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2023 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use ConseilGouz\Component\CGParallax\Site\Helper\CGHelper;
use  Joomla\CMS\Filter\OutputFilter as FilterOutput;

PluginHelper::importPlugin('content');

$doc = Factory::getDocument();

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('jquery.framework');

$user = Factory::getUser();
$userId = $user->get('id');
$app = Factory::getApplication();
$com_id = $app->input->getInt('Itemid');
$comfield	= 'media/com_cgparallax/';
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = Factory::getDocument()->getWebAssetManager();

$wa->registerAndUseStyle('cgparallax',$comfield.'css/parallax.css');
$wa->registerAndUseStyle('vegas',$comfield.'css/vegas.min.css'); 
$wa->registerAndUseScript('cgparallax',$comfield .'js/.js');
$wa->registerAndUseScript('color_anim',$comfield .'js/color_anim.js');
$wa->registerAndUseScript('vegas',$comfield .'js/vegas.min.js');
if ((bool)Factory::getConfig()->get('debug')) { // debug : addscript to be able to debug script
	$doc->addScript(''.URI::base(true).'/media/com_cgparallax/js/parallax.js'); 
} else {
	$wa->registerAndUseScript('cgparallax',$comfield .'js/parallax.js');
}
$params = CGHelper::getParams($this->page,$this->getModel());
$parallax =  CGHelper::getParallax($params);
$doc->setMetadata('keywords', $params->metakey); // J4 : no more keymord
$doc->addScriptOptions('com_cg_parallax', 
	array('navbar_bg' => $params->get('navbar_bg','lightgrey'),'navbar_color' => $params->get('navbar_color', 'black')
		  ,'menu' => $params->get('menu','true'),'sticky' => $params->get('sticky','true')
		  ,'magic' => $params->get('sticky','false')
		  ,'magic_active' => $params->get('magic_active', 'blue')
		  ));

$buttons = "";
if ($params->get('menu','true') == 'true') {
// create buttons
	$sectionsList = json_decode($params->sectionsList);
	$magic = $params->get('magic','false');
	$buttons = "<div id='cg_navbar'>";
	if ($magic == "true") {
		$buttons .=  "<ul id='cg_magic'>";
	} else {
		$buttons .=  "<ul>";
	}
	$first = true;
	foreach ($sectionsList as $item) {
		$title = $item->section_title;
		if (trim($title) == "") continue;		// 1.0.6
		$title_alias = FilterOutput::stringURLSafe($title);
		$class = "";
		$rel = "";
		if ($magic == "true") {
			$rel = "rel='".$item->magic_bg."'";
		}
		if ($first) $class="class='active'"; 
		if ($item->sf_type == "menu") {
			$menualias = Factory::getApplication()->getMenu()->getItem($item->menu)->alias;
			$buttons .= "<li ".$class."><a ".$rel." class='cg_bg_btn' href='".$menualias."'>".$title."</a></li>";
		} else {
			$buttons .= "<li ".$class."><a ".$rel." class='cg_bg_btn' href='#".$title_alias."'>".$title."</a></li>";
		}
		$first = false;
	}
	$buttons .= "</ul>";
	$buttons .= "<a href='' class='cg_para_icon'>&#9776;</a></div>";
}
if ($this->params->get('show_page_heading')) {
	echo "<h1>";
	echo $this->escape($this->params->get('page_heading')); 
	echo "</h1>";
}
if (strlen(trim($params->get('intro'))) > 0) {
	// apply content plugins on weblinks
	$item_cls = new stdClass;
	$item_cls->text = $params->get('intro');
	$item_cls->params = $params;
    $item_cls->id= $com_id;
	Factory::getApplication()->triggerEvent('onContentPrepare', array ('com_cgparallax.content', &$item_cls, &$item_cls->params, 0)); // Joomla 4.0
	$intro = 	$item_cls->text;	
	echo $intro; 
}
?>
	<div  class="cg-parallax-main" >
		<?php echo $buttons.$parallax;?>
	</div>
<?php
if (strlen(trim($params->get('bottom'))) > 0) {
	// apply content plugins on weblinks
	$item_cls = new stdClass;
	$item_cls->text = $params->get('bottom');
	$item_cls->params = $params;
    $item_cls->id= $com_id;
	Factory::getApplication()->triggerEvent('onContentPrepare', array ('com_cgparallax.content', &$item_cls, &$item_cls->params, 0)); // Joomla 4.0
	$bottom = 	$item_cls->text;	
	echo $bottom; 
}
?>
	
