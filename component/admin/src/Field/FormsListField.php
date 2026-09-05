<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Database\DatabaseInterface;
use Throwable;

class FormsListField extends ListField
{
    protected $type = 'FormsList';

    protected function getOptions(): array
    {
        $options = [HTMLHelper::_('select.option', '0', '— Nessun modulo associato —')];

        try {
            $db = Factory::getContainer()->get(DatabaseInterface::class);
            $table = $db->getPrefix() . 'decaroforms_forms';

            if (!in_array($table, $db->getTableList(), true)) {
                $options[] = HTMLHelper::_('select.option', '', 'Forms by xdecaro non installato', 'value', 'text', true);
                return $options;
            }

            $query = $db->getQuery(true)
                ->select([$db->quoteName('id'), $db->quoteName('title')])
                ->from($db->quoteName('#__decaroforms_forms'))
                ->order($db->quoteName('title') . ' ASC');
            $db->setQuery($query);

            foreach ((array) $db->loadObjectList() as $form) {
                $options[] = HTMLHelper::_('select.option', (string) $form->id, (string) $form->title);
            }
        } catch (Throwable $e) {
            $options[] = HTMLHelper::_('select.option', '', 'Forms non disponibile', 'value', 'text', true);
        }

        return array_merge(parent::getOptions(), $options);
    }
}
