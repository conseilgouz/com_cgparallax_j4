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

$user		= Factory::getApplication()->getIdentity();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canDo 		= ContentHelper::getActions('com_cgparallax');
$saveOrder	= $listOrder == 'ordering';
?>
<form action="<?php echo Route::_('index.php?option=com_cgparallax&view=pages'); ?>" method="post" name="adminForm" id="adminForm">
	<?php if (!empty($this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
	<?php else : ?>
	<div id="j-main-container">
	<?php endif; ?>

	<div id="filter-bar" class="btn-toolbar">
		<div class="filter-search btn-group pull-left">
			<label for="filter_search" class="element-invisible"><?php echo Text::_('JSEARCH_FILTER_LABEL'); ?></label>  
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo Text::_('COM_CGPARALLAX_SEARCH_IN_TITLE'); ?>" />
        </div>
        <div class="btn-group pull-left">            
			<button type="submit" class="btn hasTooltip"><?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" class="btn hasTooltip" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="btn-group pull-right hidden-phone">
			
					<select name="filter_state" class="inputbox" onchange="this.form.submit()">
						<option value=""><?php echo Text::_('JOPTION_SELECT_PUBLISHED');?></option>
						<?php echo HTMLHelper::_('select.options', HTMLHelper::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true);?>
					</select>
		</div>
	</div>
	<div class="clr"> </div>

    <?php if (empty($this->pages)) : ?>
        <div class="alert alert-no-items">
            <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
        </div>
    <?php else : ?>   
    <table class="table table-striped" id="articleList">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th width="5%" class="nowrap">
					<?php echo HTMLHelper::_('grid.sort', 'JGRID_HEADING_ID', 't.id', $listDirn, $listOrder); ?>
				</th>
				<th class="center">
					<?php echo HTMLHelper::_('grid.sort', 'CG_PX_TITLE', 't.title', $listDirn, $listOrder); ?>
				</th>
				<th class="center">
					<?php echo HTMLHelper::_('grid.sort', 'CG_PX_SECTIONS', 't.info', $listDirn, $listOrder); ?>
				</th>
               <th width="15%">
                    <?php echo HTMLHelper::_('searchtools.sort', 'CG_PX_LANGUAGE', 'language', $listDirn, $listOrder); ?>
                </th>
				<th width="5%">
					<?php echo HTMLHelper::_('grid.sort', 'JSTATUS', 'state', $listDirn, $listOrder); ?>
				</th>
				
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="13">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->pages as $i => $page) :
		    $ordering	= ($listOrder == 'ordering');
		    $canCreate	= $user->authorise('core.create');
		    $canEdit	= $user->authorise('core.edit');
		    $canCheckin	= $user->authorise('core.manage', 'com_checkin') || $page->checked_out == $userId || $page->checked_out == 0;
		    $canChange	= $user->authorise('core.edit.state') && $canCheckin;
		    ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo HTMLHelper::_('grid.id', $i, $page->id); ?>
				</td>
				<td class="center">
                    <a href="<?php echo Route::_('index.php?option=com_cgparallax&task=page.edit&id='.(int) $page->id); ?>">
                    <?php echo $this->escape($page->id); ?>                     
					</a>
				</td>
				<td class="center">
					<a href="<?php echo Route::_('index.php?option=com_cgparallax&task=page.edit&id='.(int) $page->id); ?>">
                    <?php echo $this->escape($page->title); ?>                     
					</a>
				</td>
				<td class="center">
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
				</td>
                <td align="center">
                    <?php echo LayoutHelper::render('joomla.content.language', $page); ?>
                </td>
				<td class="center">
					<?php echo HTMLHelper::_('jgrid.published', $page->state, $i, 'pages.', $canChange, 'cb'); ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
    <?php endif; ?> 
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
	</div>
</form>
