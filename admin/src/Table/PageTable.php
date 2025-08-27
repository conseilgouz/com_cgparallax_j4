<?php
/**
 * CG Parallax Component  - Joomla 4.x/5.x/6.x Component 
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @copyright (c) 2025 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
*
*/
namespace ConseilGouz\Component\CGParallax\Administrator\Table;

\defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Versioning\VersionableTableInterface;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseInterface;
use Joomla\Utilities\ArrayHelper;

class PageTable extends Table implements VersionableTableInterface
{
	/**
	 * An array of key names to be json encoded in the bind function
	 *
	 * @var    array
	 * @since  4.0.0
	 */
	protected $_jsonEncode = ['params', 'metadata', 'urls', 'images'];

	/**
	 * Indicates that columns fully support the NULL value in the database
	 *
	 * @var    boolean
	 * @since  4.0.0
	 */
	protected $_supportNullValue = true;

	/**
	 * Constructor
	 *
	 * @param   DatabaseDriver  $db  A database connector object
	 */

	public function __construct(DatabaseDriver $db)
	{
		parent::__construct('#__cgparallax_page', 'id', $db);
	}

	function check()
	{
		jimport('joomla.filter.output');
		return true;
	}
	function store($key = 0)
	{
        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $table = $this->_tbl;
        $key   = empty($this->id) ? $key : $this->id;

        // Check if key exists
        $result = $db->setQuery(
            $db->getQuery(true)
                ->select('COUNT(*)')
                ->from($db->quoteName($this->_tbl))
                ->where($db->quoteName('id') . ' = ' . $db->quote($key))
        )->loadResult();

        $exists = $result > 0 ? true : false;

        // Prepare object to be saved
        $data = new \stdClass();
        $data->id   = $key;
        $data->title = $this->title;
        $data->sections = $this->sections;
        $input = Factory::getApplication()->getInput();
		$compl = $input->getVar('jform', array(), 'post', 'array');
        $page_params = [];
        $page_params['menu'] = $compl['menu'];
        $page_params['sticky'] = $compl['sticky'];
        $page_params['navbar_bg'] = $compl['navbar_bg'];
        $page_params['navbar_color'] = $compl['navbar_color'];
        $page_params['magic'] = $compl['magic'];
        $page_params['magic_active'] = $compl['magic_active'];
        $page_params['css_gen'] = $compl['css_gen'];
		$compl = $input->getRaw('jform', array(), 'post', 'array');
		$page_params['intro'] = $compl['intro'];
		$page_params['bottom'] = $compl['bottom'];
        $data->page_params =  json_encode($page_params);
        $data->sections = json_encode($compl['sectionsList']);
		$data->state = $this->state;
		$data->language = $this->language;
		$data->metakey = $this->metakey;
        if ($exists)
        {
            return $db->updateObject($table, $data, 'id');
        }
		// insert a new object
		$ret = $db->insertObject($table, $data,'id');
		$this->id = $data->id;
		return $ret;
	}
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		$k = $this->_tbl_key;
		ArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state  = (int) $state;
		if (empty($pks))
		{
			if ($this->$k) {
				$pks = array($this->$k);
			}
			else {
				$this->setError(Text::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				return false;
			}
		}
		$table = Table::getInstance('PageTable', __NAMESPACE__ . '\\', array('dbo' => $db)); // J4.0
		foreach ($pks as $pk)
		{
			if(!$table->load($pk))
			{
				$this->setError($table->getError());
			}
			if($table->checked_out==0 || $table->checked_out==$userId)
			{
				$table->state = $state;
				$table->checked_out=0;
				$table->checked_out_time=0;
				$table->check();
				if (!$table->store())
				{
					$this->setError($table->getError());
				}
			}
		}
		return count($this->getErrors())==0;
	}
	/**
	 * Get the type alias for the history table
	 *
	 * @return  string  The alias as described above
	 *
	 * @since   4.0.0
	 */
	public function getTypeAlias()
	{
		return $this->typeAlias;
	}
	

}