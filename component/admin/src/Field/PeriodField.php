<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

class PeriodField extends ListField
{
    protected $type = 'Period';

    protected function getOptions(): array
    {
        $options = parent::getOptions();
        $currentYear = (int) Factory::getDate()->format('Y');
        $startYear = $currentYear - 5;
        $endYear = $currentYear + 10;
        $currentValue = trim((string) $this->value);
        $generatedValues = [];

        if ($currentValue !== '' && $this->isValidPeriod($currentValue)) {
            $year = (int) substr($currentValue, 0, 4);

            if ($year < $startYear || $year > $endYear) {
                $options[] = HTMLHelper::_('select.option', $currentValue, $currentValue);
                $generatedValues[$currentValue] = true;
            }
        }

        $options[] = HTMLHelper::_('select.option', '', Text::_('COM_DECAROCOURSES_PERIOD_GROUP_SINGLE'), 'value', 'text', true);

        for ($year = $startYear; $year <= $endYear; $year++) {
            $value = (string) $year;

            if (!isset($generatedValues[$value])) {
                $options[] = HTMLHelper::_('select.option', $value, $value);
                $generatedValues[$value] = true;
            }
        }

        $options[] = HTMLHelper::_('select.option', '', Text::_('COM_DECAROCOURSES_PERIOD_GROUP_ACADEMIC'), 'value', 'text', true);

        for ($year = $startYear; $year <= $endYear; $year++) {
            $value = $year . '/' . ($year + 1);

            if (!isset($generatedValues[$value])) {
                $options[] = HTMLHelper::_('select.option', $value, $value);
                $generatedValues[$value] = true;
            }
        }

        return $options;
    }

    private function isValidPeriod(string $value): bool
    {
        if (preg_match('/^(\d{4})$/', $value, $matches) === 1) {
            $year = (int) $matches[1];
            return $year >= 1900 && $year <= 2200;
        }

        if (preg_match('/^(\d{4})\/(\d{4})$/', $value, $matches) !== 1) {
            return false;
        }

        $start = (int) $matches[1];
        $end = (int) $matches[2];

        return $start >= 1900 && $start <= 2200 && $end === $start + 1;
    }
}
