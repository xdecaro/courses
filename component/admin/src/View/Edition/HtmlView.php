<?php
namespace Xdecaro\Component\Decarocourses\Administrator\View\Edition;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Xdecaro\Component\Decarocourses\Administrator\Helper\UiHelper;

class HtmlView extends BaseHtmlView
{
    public $form;
    public $item;
    public bool $canSave = false;
    public bool $canCreate = false;
    public bool $canEditState = false;

    public function display($tpl = null): void
    {
        UiHelper::loadAssets();

        $this->form = $this->get('Form');
        $this->item = $this->get('Item');

        $isNew = empty($this->item->id);
        $identity = Factory::getApplication()->getIdentity();

        $this->canCreate = $identity->authorise('core.create', 'com_decarocourses');
        $this->canEditState = $identity->authorise('core.edit.state', 'com_decarocourses');
        $this->canSave = $isNew
            ? $this->canCreate
            : $identity->authorise('core.edit', 'com_decarocourses');

        if (!$this->canEditState && $this->form) {
            $this->form->setFieldAttribute('state', 'disabled', 'true');
        }

        parent::display($tpl);
    }
}
