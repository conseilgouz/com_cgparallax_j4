<?php
/**
 * @component     CG Parallax
 * Version			: 2.2.0
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @copyright (c) 2024 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz
**/
// no direct access
defined('_JEXEC') or die;
use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;

// HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.multiselect');
// Joomla 6.0 : list-view.js might not be loaded 
$wa = $this->getDocument()->getWebAssetManager();
$wa->useScript('list-view');

$user		= Factory::getApplication()->getIdentity();

$userId		= $user->id;
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canDo 		= ContentHelper::getActions('com_cgparallax');
$saveOrder	= $listOrder == 'ordering';
?>
<form action="<?php echo Route::_('index.php?option=com_cgparallax&view=import'); ?>" method="post" name="adminForm" id="adminForm">
	<?php if (!empty($this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
	<?php else : ?>
	<div id="j-main-container">
	<?php endif; ?>

	<div class="clr"> </div>
	<h2><?php echo Text::_('CG_PX_IMPORT_ALREADY');?></h2>
    <?php if (empty($this->pages)) : ?>
        <div class="alert alert-no-items">
            <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
        </div>
    <?php else : ?>   
    <ul>
		<?php foreach ($this->pages as $i => $page) :
		    ?>
				<li><?php echo $this->escape($page->title); ?>
                    <?php $text = "";
		    $sectionsList = json_decode($page->sections);
		    if ($sectionsList) {
		        foreach ($sectionsList as $item) {
		            if (strlen($text) > 0) {
		                $text .= ",";
		            }
		            $text .= $item->section_title;
		        }
		    }
		    if (strlen($text) > 70) {
		        $text = substr($text, 0, 70).'...';
		    }
		    echo $this->escape($text); ?>                     
				</li>
			<?php endforeach; ?>
	</ul>
<?php endif; ?>        
	<h2><?php echo Text::_('CG_PX_IMPORT_TODO');?></h2>
        <table class="table table-striped" id="articleList">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th class="center">
					<?php echo Text::_('CG_PX_TITLE'); ?>
				</th>
                                 <th width="15%">
                                    <?php echo Text::_('CG_PX_LANGUAGE'); ?>
                                </th>
				<th width="5%">
					<?php echo Text::_('JSTATUS'); ?>
				</th>
				
			</tr>
		</thead>
		<tbody>
                    <?php foreach ($this->modules as $i => $module) :
                        ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo HTMLHelper::_('grid.id', $i, $module['id']); ?>
				</td>
				<td class="center">
                    <?php echo $this->escape($module['title']); ?>                     
				</td>
                <td align="center">
                    <?php
                                    $lang = new stdClass();
                        $lang->language = $module['language'];
                        $lang->language_image = str_replace('-', '_', strtolower($module['language']));
                        $lang->language_title = $module['language'];
                        echo LayoutHelper::render('joomla.content.language', $lang); ?>
                </td>
				<td>
				      <?php echo HTMLHelper::_('jgrid.published', $module['published'], $i, 'import.', false, 'cb'); ?>                  
				</td>
			</tr>
                <?php endforeach; ?>
		</tbody>
	</table>
	<div>
		<input type="hidden" name="task" value="import" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
	</div>
</form>
