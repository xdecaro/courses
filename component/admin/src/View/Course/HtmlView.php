<?php
namespace Xdecaro\Component\Decarocourses\Administrator\View\Course;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Xdecaro\Component\Decarocourses\Administrator\Helper\UiHelper;

class HtmlView extends BaseHtmlView
{
    public $form;
    public $item;

    public function display($tpl = null): void
    {
        UiHelper::loadAssets();
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');

        $isNew = empty($this->item->id);
        ToolbarHelper::title($isNew ? Text::_('COM_DECAROCOURSES_COURSE_NEW') : Text::_('COM_DECAROCOURSES_COURSE_EDIT'), 'pencil-2');
        ToolbarHelper::apply('course.apply');
        ToolbarHelper::save('course.save');
        ToolbarHelper::save2new('course.save2new');
        ToolbarHelper::cancel('course.cancel', 'JTOOLBAR_CLOSE');

        parent::display($tpl);
    }
}
