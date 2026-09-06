<?php
namespace Xdecaro\Component\Decarocourses\Administrator\View\Edition;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Xdecaro\Component\Decarocourses\Administrator\Helper\AdminToolbarHelper;
use Xdecaro\Component\Decarocourses\Administrator\Helper\UiHelper;

class HtmlView extends BaseHtmlView
{
    public $form;
    public $item;
    public bool $canSave = false;
    public bool $canCreate = false;
    public bool $canEditState = false;
    public bool $isLocked = false;

    public function display($tpl = null): void
    {
        UiHelper::loadAssets();

        $document = Factory::getApplication()->getDocument();
        $document->getWebAssetManager()
            ->useScript('keepalive')
            ->useScript('form.validate');

        $this->form = $this->get('Form');
        $this->item = $this->get('Item');

        $isNew = empty($this->item->id);
        $identity = Factory::getApplication()->getIdentity();
        $checkedOut = (int) ($this->item->checked_out ?? 0);

        $this->canCreate = $identity->authorise('core.create', 'com_decarocourses');
        $this->canEditState = $identity->authorise('core.edit.state', 'com_decarocourses');
        $this->isLocked = !$isNew && $checkedOut > 0 && $checkedOut !== (int) $identity->id;
        $this->canSave = !$this->isLocked && ($isNew
            ? $this->canCreate
            : $identity->authorise('core.edit', 'com_decarocourses'));

        if (!$this->canEditState && $this->form) {
            $this->form->setFieldAttribute('state', 'disabled', 'true');
        }

        AdminToolbarHelper::editionForm($this->canSave);

        parent::display($tpl);
    }
}
