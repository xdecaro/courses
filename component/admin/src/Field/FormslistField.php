<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Xdecaro\Component\Decarocourses\Administrator\Helper\LiveDataHelper;

class FormslistField extends ListField
{
    protected $type = 'Formslist';

    protected function getOptions(): array
    {
        $options = parent::getOptions();
        $payload = LiveDataHelper::getOptions(LiveDataHelper::SOURCE_FORMS);

        foreach ($payload['options'] as $option) {
            $options[] = HTMLHelper::_(
                'select.option',
                (string) ($option['value'] ?? ''),
                (string) ($option['text'] ?? ''),
                'value',
                'text',
                (bool) ($option['disabled'] ?? false)
            );
        }

        return $options;
    }
}
