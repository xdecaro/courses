<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Xdecaro\Component\Decarocourses\Administrator\Helper\UiHelper;

class CoursesController extends AdminController
{
    public function getModel($name = 'Course', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }

    public function publish()
    {
        $this->assertAuthorised('core.edit.state');
        parent::publish();
    }

    public function checkin()
    {
        $this->assertAuthorised('core.edit.state');
        parent::checkin();
    }

    public function delete()
    {
        $this->assertAuthorised('core.delete');
        parent::delete();
    }

    private function assertAuthorised(string $action): void
    {
        UiHelper::loadLanguage();

        if (!Factory::getApplication()->getIdentity()->authorise($action, 'com_decarocourses')) {
            throw new \RuntimeException(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }
    }
}
