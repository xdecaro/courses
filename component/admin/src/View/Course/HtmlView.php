<?php
namespace Xdecaro\Component\Decarocourses\Administrator\View\Course;

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
            $isNew ? Text::_('COM_DECAROCOURSES_COURSE_NEW') : Text::_('COM_DECAROCOURSES_COURSE_EDIT'),
            'pencil-2'
        );

        if ($this->canSave) {
            ToolbarHelper::apply('course.apply');
            ToolbarHelper::save('course.save');
        }

        if ($this->canCreate) {
            ToolbarHelper::save2new('course.save2new');
        }

        ToolbarHelper::cancel('course.cancel', 'JTOOLBAR_CLOSE');

        parent::display($tpl);
    }
}
