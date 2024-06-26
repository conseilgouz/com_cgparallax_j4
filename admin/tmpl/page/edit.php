<?php
/**
 * @component     CG Parallax
 * Version			: 2.2.0
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @copyright (c) 2024 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz
**/
// no direct access
\defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;

// JHtml::_('behavior.modal');
//HtmlHelper::_('behavior.tooltip');
//HtmlHelper::_('behavior.tabstate');

$comfield	= 'components/com_cgisotope/';
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();

$wa->registerAndUseStyle('cggene', $comfield.'admincss/css_gene.css');
$wa->registerAndUseStyle('cssparallax', $comfield.'admincss/css_parallax.css');
$wa->registerAndUseStyle('csssection', $comfield.'admincss/css_sections.css');

$wa->useScript('keepalive')
    ->useScript('form.validate');

?>
<script type="text/javascript">
	Joomla.submitbutton = function(task){

	if (task == 'page.cancel' || document.formvalidator.isValid(document.adminForm)) {
			Joomla.submitform(task, document.getElementById('page-form'));
		}
}
</script>

<div class="span12">
<div class="nr-app nr-app-page">
    <div class="nr-row">
        <div class="nr-main-container">
            <div class="nr-main-content">
        		<form action="<?php echo Route::_('index.php?option=com_cgparallax&layout=edit&id='.(int) $this->page->id); ?>" method="post" name="adminForm"  class="form-validate" id="page-form" >
    		      <div class="form-horizontal">
                    	<?php
                            echo HtmlHelper::_('uitab.startTabSet', 'tab', array('active' => 'gene'));
foreach ($this->form->getFieldSets() as $key => $fieldset) {
    echo HtmlHelper::_('uitab.addTab', 'tab', $fieldset->name, Text::_($fieldset->label));
    echo $this->form->renderFieldSet($fieldset->name);
    echo HtmlHelper::_('uitab.endTab');
}
echo HtmlHelper::_('uitab.endTabSet');
?>
        		    </div>

        		    <?php echo HtmlHelper::_('form.token'); ?>
				    <input type="hidden" name="task" value="" />

        		</form>
            </div>
        </div>
    </div>
</div>
</div>