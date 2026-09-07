<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Router\Route;
use Joomla\Utilities\ArrayHelper;
use Xdecaro\Component\Decarocourses\Administrator\Helper\UiHelper;

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

    public function restore(): void
    {
        $this->checkToken();
        $this->assertAuthorised('core.edit.state');

        $ids = array_values(array_filter(ArrayHelper::toInteger((array) $this->input->get('cid', [], 'int'))));
        $redirect = Route::_(
            'index.php?option=com_decarocourses&view=editions' . $this->getRedirectToListAppend(),
            false
        );

        if (!$ids) {
            $this->setRedirect($redirect, Text::_('COM_DECAROCOURSES_NO_ITEM_SELECTED'), 'warning');
            return;
        }

        try {
            $model = $this->getModel();
            $model->publish($ids, 1);

            if ($model->getErrors()) {
                $this->setRedirect($redirect, implode("\n", $model->getErrors()), 'error');
                return;
            }
        } catch (\Throwable $e) {
            $this->setRedirect($redirect, $e->getMessage(), 'error');
            return;
        }

        $this->setRedirect($redirect, Text::plural('COM_DECAROCOURSES_N_ITEMS_RESTORED', count($ids)));
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
        UiHelper::loadLanguage();

        if (!Factory::getApplication()->getIdentity()->authorise($action, 'com_decarocourses')) {
            throw new \RuntimeException(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }
    }
}
