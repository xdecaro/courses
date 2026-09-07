<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Router\Route;
use Joomla\Utilities\ArrayHelper;

class EditionsController extends AdminController
{
    public function getModel($name = 'Edition', $prefix = 'Administrator', $config = ['ignore_request' => true])
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

    public function featured(): void
    {
        $this->setFeaturedState(1);
    }

    public function unfeatured(): void
    {
        $this->setFeaturedState(0);
    }

    public function delete()
    {
        $this->assertAuthorised('core.delete');
        parent::delete();
    }

    private function setFeaturedState(int $value): void
    {
        $this->checkToken();
        $this->assertAuthorised('core.edit.state');

        $ids = ArrayHelper::toInteger((array) $this->input->get('cid', [], 'int'));
        $ids = array_values(array_filter($ids));
        $redirect = Route::_(
            'index.php?option=com_decarocourses&view=editions' . $this->getRedirectToListAppend(),
            false
        );

        if (!$ids) {
            $this->setRedirect($redirect, Text::_('COM_DECAROCOURSES_NO_ITEM_SELECTED'), 'warning');
            return;
        }

        $model = $this->getModel();

        if (!$model->featured($ids, $value)) {
            $this->setRedirect($redirect, $model->getError(), 'error');
            return;
        }

        $messageKey = $value === 1
            ? 'COM_DECAROCOURSES_N_ITEMS_FEATURED'
            : 'COM_DECAROCOURSES_N_ITEMS_UNFEATURED';

        $this->setRedirect($redirect, Text::plural($messageKey, count($ids)));
    }

    private function assertAuthorised(string $action): void
    {
        if (!Factory::getApplication()->getIdentity()->authorise($action, 'com_decarocourses')) {
            throw new \RuntimeException(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }
    }
}
