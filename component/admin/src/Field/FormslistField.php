<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use Throwable;

class FormslistField extends ListField
{
    protected $type = 'Formslist';

    protected function getOptions(): array
    {
        $options = parent::getOptions();
        $options[] = HTMLHelper::_('select.option', '0', Text::_('COM_DECAROCOURSES_FORMS_NONE'));

        try {
            $db = Factory::getContainer()->get(DatabaseInterface::class);
            $table = $db->getPrefix() . 'decaroforms_forms';

            if (!in_array($table, $db->getTableList(), true)) {
                $options[] = HTMLHelper::_('select.option', '', Text::_('COM_DECAROCOURSES_FORMS_NOT_AVAILABLE'), 'value', 'text', true);
                return $options;
            }

            $query = $db->getQuery(true)
                ->select([$db->quoteName('id'), $db->quoteName('title')])
                ->from($db->quoteName('#__decaroforms_forms'))
                ->order($db->quoteName('title') . ' ASC');

            $forms = (array) $db->setQuery($query)->loadObjectList();

            if (!$forms) {
                $options[] = HTMLHelper::_('select.option', '', Text::_('COM_DECAROCOURSES_FORMS_EMPTY'), 'value', 'text', true);
                return $options;
            }

            foreach ($forms as $form) {
                $id = (int) $form->id;
                $title = trim((string) $form->title);
                $label = ($title !== '' ? $title : Text::sprintf('COM_DECAROCOURSES_FORMS_UNTITLED', $id)) . ' (#' . $id . ')';
                $options[] = HTMLHelper::_('select.option', (string) $id, $label);
            }
        } catch (Throwable) {
            $options[] = HTMLHelper::_('select.option', '', Text::_('COM_DECAROCOURSES_FORMS_NOT_AVAILABLE'), 'value', 'text', true);
        }

        return $options;
    }
}
