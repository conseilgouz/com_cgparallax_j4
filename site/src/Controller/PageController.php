<?php
/**
 * @component     CG Parallax
 * Version			: 2.1.2
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2022 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
**/
namespace ConseilGouz\Component\CGParallax\Site\Controller;

defined('_JEXEC') or die;
use Joomla\CMS\MVC\Controller\BaseController;

class PageController extends BaseController
{
	public function getModel($name = 'Page', $prefix = 'CGParallaxModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}