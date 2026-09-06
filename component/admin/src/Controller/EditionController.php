<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;

class EditionController extends FormController
{
    protected $view_list = 'editions';

    protected function allowAdd($data = []): bool
    {
        return Factory::getApplication()
            ->getIdentity()
            ->authorise('core.create', 'com_decarocourses');
    }

    protected function allowEdit($data = [], $key = 'id'): bool
    {
        return Factory::getApplication()
            ->getIdentity()
            ->authorise('core.edit', 'com_decarocourses');
    }

    public function add()
    {
        $courseId = $this->input->getInt('course_id', $this->input->getInt('filter_course_id', 0));
        $result = parent::add();

        if ($result && $courseId > 0) {
            $this->setRedirect(
                Route::_('index.php?option=com_decarocourses&view=edition&layout=edit&course_id=' . $courseId, false)
            );
        }

        return $result;
    }
}
