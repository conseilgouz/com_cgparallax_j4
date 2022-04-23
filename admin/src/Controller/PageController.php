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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

class PageController extends FormController
{
	protected function allowAdd($data = array())
	{
		// Initialise variables.
		$user		= Factory::getUser();
		$allow		= null;
		if ($allow === null) {
			// In the absense of better information, revert to the component permissions.
			return parent::allowAdd($data);
		} else {
			return $allow;
		}
	}

	protected function allowEdit($data = array(), $key = 'id')
	{
		// Initialise variables.
		$user		= Factory::getUser();
		$recordId	= (int) isset($data[$key]) ? $data[$key] : 0;

		return parent::allowEdit($data, $key);
	}

	public function cancel($key = null)
	{
		// $result = parent::cancel();
		$app = Factory::getApplication();
		$return = Uri::base().'index.php?option=com_cgparallax&view=pages';
		$app->redirect($return);
		return true;
	}

    public function save($key = null, $urlVar = null)
    {       
        // Check for request forgeries.
        Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

        // Initialise variables.
        $app = Factory::getApplication();
        $model= $this->getModel('page'); 
        $data = $app->input->getVar('jform', array(), 'post', 'array');
        $task = $this->getTask();
        $context = 'com_cgparallax.edit.page';
        $recordId = $app->input->getInt('id');
        
        $jinput = Factory::getApplication()->input;
        $files = $jinput->files->get('jform');
        $file = $files['avatar']; 
          
        if (!$this->checkEditId($context, $recordId))
        {
            // Somehow the person just went to the form and saved it - we don't allow that.
            $this->setError(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $recordId));
            $this->setMessage($this->getError(), 'error');
            $this->setRedirect(Route::_('index.php?option=com_cgparallax&view=pages' . $this->getRedirectToListAppend(), false));

            return false;
        }

        // Populate the row id from the session.
        $data['id'] = $recordId;
        // Check for validation errors.
        if ($data === false)
        {
            // Get the validation messages.
            $errors = $model->getErrors();

            // Push up to three validation messages out to the user.
            for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
            {
                if ($errors[$i] instanceof Exception)
                {
                    $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
                }
                else
                {
                    $app->enqueueMessage($errors[$i], 'warning');
                }
            }

            // Save the data in the session.
            $app->setUserState('com_cgparallax.edit.page.data', $data);

            // Redirect back to the edit screen.
            $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));

            return false;
        }
        // Attempt to save the data.
        if (!$model->save($data))
        {
            // Save the data in the session.
            $app->setUserState('com_cgparallax.edit.page.data', $data);

            // Redirect back to the edit screen.
            $this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
            $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));

            return false;
        }

        // Save succeeded, check-in the row.
        // Save succeeded, check-in the row.
        /* FIXME : checkin ?
        if ($model->checkin($data['id']) === false)
        {
            // Check-in failed, go back to the row and display a notice.
            $this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()), 'warning');
            $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));

            return false;
        }
*/
        $this->setMessage(Text::_('Save sucess!'));

        // Redirect the user and adjust session state based on the chosen task.
        switch ($task)
        {
            case 'apply':
                // Set the row data in the session.
                $recordId = $model->getState($this->context . '.id');
                $this->holdEditId($context, $recordId);
                $app->setUserState('com_cgparallax.edit.page.data', null);

                // Redirect back to the edit screen.
                $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));
                break;

            case 'save2new':
                // Clear the row id and data in the session.
                $this->releaseEditId($context, $recordId);
                $app->setUserState('com_cgparallax.edit.page.data', null);

                // Redirect back to the edit screen.
                $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend(), false));
                break;

            default:
                // Clear the row id and data in the session.
                $this->releaseEditId($context, $recordId);
                $app->setUserState('com_cgparallax.edit.page.data', null);

                // Redirect to the list screen.
                $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend(), false));
                break;
        }
    }                    
}