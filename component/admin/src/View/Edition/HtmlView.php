<?php
namespace Xdecaro\Component\Decarocourses\Administrator\View\Edition;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Xdecaro\Component\Decarocourses\Administrator\Helper\UiHelper;

class HtmlView extends BaseHtmlView
{
    public $form;
    public $item;
    public bool $canSave = false;
    public bool $canCreate = false;

    public function display($tpl = null): void
    {
        UiHelper::loadAssets();

        $this->form = $this->get('Form');
        $this->item = $this->get('Item');

        $isNew = empty($this->item->id);
        $identity = Factory::getApplication()->getIdentity();

        $this->canCreate = $identity->authorise('core.create', 'com_decarocourses');
        $this->canSave = $isNew
            ? $this->canCreate
            : $identity->authorise('core.edit', 'com_decarocourses');

        ToolbarHelper::title(
            $isNew ? Text::_('COM_DECAROCOURSES_EDITION_NEW') : Text::_('COM_DECAROCOURSES_EDITION_EDIT'),
            'pencil-2'
        );

        if ($this->canSave) {
            ToolbarHelper::apply('edition.apply');
            ToolbarHelper::save('edition.save');
        }

        if ($this->canCreate) {
            ToolbarHelper::save2new('edition.save2new');
        }

        ToolbarHelper::cancel('edition.cancel', 'JTOOLBAR_CLOSE');

        parent::display($tpl);
    }
}
