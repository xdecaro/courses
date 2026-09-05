<?php
namespace Xdecaro\Component\Decarocourses\Administrator\View\Edition;

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
        ToolbarHelper::title($isNew ? Text::_('COM_DECAROCOURSES_EDITION_NEW') : Text::_('COM_DECAROCOURSES_EDITION_EDIT'), 'pencil-2');
        ToolbarHelper::apply('edition.apply');
        ToolbarHelper::save('edition.save');
        ToolbarHelper::save2new('edition.save2new');
        ToolbarHelper::cancel('edition.cancel', 'JTOOLBAR_CLOSE');

        parent::display($tpl);
    }
}
