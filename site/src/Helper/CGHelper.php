<?php
/**
 * CG Parallax Component  - Joomla 4.0.0 Component 
 * Version			: 2.1.2
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2022 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
namespace ConseilGouz\Component\CGParallax\Site\Helper;
 
\defined('_JEXEC') or die;
use Joomla\Registry\Registry;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Component\Content\Site\Model\ArticleModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use  Joomla\CMS\Filter\OutputFilter as FilterOutput;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\Folder;

class CGHelper {

    public static function getParams($id,$model = null)
    {
		$table = $model->getTable();
		$table->load((int)$id);
		$lesparams = json_decode($table->page_params,true);
		$params = new Registry(json_encode($lesparams));
        $params->sectionsList = $table->sections;
		$params->metakey = $table->metakey; // J4 : no more keymord
		return $params;
    }
	static function getParallax($params)
	{   // apply contents plugins
		PluginHelper::importPlugin('content');
		$parallax_sections = [];
		// create parallax layout
		$result = "<style>".$params->get('css_gen','')."</style>"; // custom module css
		$result .= "<div class='cg_parallax' id='cg_parallax'>";	
		$ix = 1;
		$sectionsList = json_decode($params->sectionsList);
		foreach ($sectionsList as $item) {
			if ($item->sf_type == 'menu') {
				continue;
			}
			$title = $item->section_title;
			$title_alias = FilterOutput::stringURLSafe($title);
			$imgname = $item->image;
			$result .= "<style>".$item->css."</style>";
			$result .= "<a id='".$title_alias."' class='cg_anchor'></a><div class='cg_bg_section cg_bg_img_".$ix."'><h2>".$title."</h2>";
			if ($item->sf_type == "content") { // one article selected
				$article = self::getArticle($item->article,$item);
				$article = $article[0];
				// apply contents plugins
				$item_tmp = new \stdClass;
				if ($item->intro_full == "full") {
					$item_tmp->text = $article->articletext;
				} elseif ($item->intro_full == "introfull") {
					$item_tmp->text = $article->introtext.$article->articletext;
				} else 	{ // intro
					$item_tmp->text = $article->introtext;
					if ($item->readmore == "true") {
						$readmore = '<p class="cg-readmore"><a class="cg-readmore-title btn btn-primary" href="'.$article->link.'">'.Text::_('CG_LIB_READMORE').'</a></p>';			
						$item_tmp->text .= $readmore;
					}
				}
				$item_tmp->params = $params;
				Factory::getApplication()->triggerEvent('onContentPrepare', array ('com_content.article', &$item_tmp, &$item_tmp->params, 0));
				$result .= $item_tmp->text;
			} else { // free text
				$article = $item->text;
				// apply contents plugins
				$item_tmp = new \stdClass;
				$item_tmp->text = $article;
				$item_tmp->params = $params;
				Factory::getApplication()->triggerEvent('onContentPrepare', array ('com_content.article', &$item_tmp, &$item_tmp->params, 0));
				$result .=  $item_tmp->text;
			}
			// "background-image: linear-gradient(rgba(255, 255, 255, 0.".$item->lighten."), rgba(250, 250, 250, 0.".$item->lighten."))"
			$images =array();
			$animation ="random";
			$trans= "fade";
			$duration = "1000";
			$delay = "12000";
			if (!isset($item->cg_img_type))  {
				$item->cg_img_type = "one";
				$item->cg_anim = "false";
			}
			if ($item->cg_anim == "false") {
				$animation = "none";
			} else {
				$trans = $item->vegas_trans;
                $animation = $item->vegas_anim;
                $delay = $item->vegas_delay;
				$duration = $item->vegas_duration;
			}
			if ($item->cg_img_type == "one") {
				$une = array("src" => URI::root()."/".$item->image);
				if ($item->cg_anim != "false")  {// on double juste pour garder l'animation
					array_push($images,$une);
					array_push($images,$une);
				} else { // pas d'animation
					array_push($images,$une);
				}
			} elseif ($item->cg_img_type == "dir"){
				$dir = $item->dir;
				if ($dir == '-1') { // pas de selection
					$dir = "";
                } elseif ($dir == "selected") {
					$dir = "images";
                } else {
					$dir = "images/".$dir;
                }
                if(!Folder::exists($dir) ) { // le repertoire n'existe pas : on cree
					Folder::create($dir,755);
                }
				$files = Folder::files($dir,null,null ,null , array('desc.txt','index.html','.htaccess'));
				foreach ($files as $file) {
					if (strlen(trim($file)) == 0) continue;
					$une = array("src" => URI::base(true).'/'.$dir."/".$file);
					array_push($images,$une);
				}
			
			} elseif ($item->cg_img_type == "files"){
				// $imglist = json_decode($item->slideslist);
			    foreach($item->slideslist as $uneimage) {
    				if (strlen(trim($uneimage->file_name)) == 0) continue;
    				$une = array("src" => URI::root()."/".$uneimage->file_name);
					array_push($images,$une);
				}
			}
			if ($item->cg_img_type != "none") {
				$parallax_sections[$ix] = array("slides" => json_encode($images), "delay"=>$delay, "transitionDuration" =>$duration,"cover" => true, "transition" => $trans,"animation"=>$animation,"loop" => true,"lighten"=>$item->lighten);
			}
			$result .="</div>";
			$ix +=1;
		}
		if (count($parallax_sections) > 0)  { // CSP Compliance
			$doc = Factory::getDocument();
			$doc->addScriptOptions('com_cg_parallax_sections',$parallax_sections);
		}
		return $result."</div>";
	}
	static function getArticle($id, $params) {
		// Get an instance of the generic articles model
		$model     = new ArticleModel(array('ignore_request' => true));
        if ($model) {
		// Set application parameters in model
		$app       = Factory::getApplication();
		$appParams = $app->getParams();
		$model->setState('params', $appParams);

		// Set the filters based on the module params
		$model->setState('list.start', 0);
		$model->setState('list.limit', 1);
		$model->setState('filter.published', 1);
		// Access filter
		$access = ComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = Access::getAuthorisedViewLevels(Factory::getUser()->get('id'));
		$model->setState('filter.access', $access);

		$model->setState('filter.language', $app->getLanguageFilter());
		// Ordering
		$model->setState('list.ordering', 'a.hits');
		$model->setState('list.direction', 'DESC');

		$item = $model->getItem($id);

		$item->slug    = $item->id . ':' . $item->alias;
		$item->catslug = $item->catid . ':' . $item->category_alias;
		if ($access || in_array($item->access, $authorised))
		{
			// We know that user has the privilege to view the article
			$item->link = self::getArticleRoute($item->slug, $item->catid, $item->language);
		}
		else
		{
			$item->link = Route::_('index.php?option=com_users&view=login');
		}
		$arr[0] = $item;
        }
        else {
        	$arr = false;
        }
		return $arr;
	}
	public static function getArticleRoute($id, $catid = 0, $language = 0, $layout = null)
	{
		// Create the link
		$link = 'index.php?option=com_content&view=article&id=' . $id;

		if ((int) $catid > 1)
		{
			$link .= '&catid=' . $catid;
		}

		if ($language && $language !== '*' && PluginHelper::isEnabled('system', 'languagefilter'))
		{
			$link .= '&lang=' . $language;
		}

		if ($layout)
		{
			$link .= '&layout=' . $layout;
		}

		return $link;
	}
        

    
}
