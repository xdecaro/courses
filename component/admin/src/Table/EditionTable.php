<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\ParameterType;

class EditionTable extends Table
{
    private const ALLOWED_FORMATS = ['annual', 'intensive', 'evening', 'weekend', 'custom'];
    private const ALLOWED_STATUSES = ['draft', 'registrations_open', 'scheduled', 'active', 'completed', 'archived'];

    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__decarocourses_editions', 'id', $db);
        $this->setColumnAlias('published', 'state');
    }

    public function check(): bool
    {
        $this->course_id = (int) $this->course_id;
        $this->academic_year = trim((string) $this->academic_year);
        $this->format = trim((string) $this->format);
        $this->format_custom = trim((string) ($this->format_custom ?? ''));
        $this->capacity = max(0, (int) $this->capacity);
        $this->status = trim((string) $this->status);
        $this->forms_form_id = max(0, (int) $this->forms_form_id);
        $this->notes = trim((string) $this->notes);
        $this->state = (int) $this->state;
        $this->featured = (int) ((int) ($this->featured ?? 0) === 1);

        $courseTitle = $this->getCourseTitle($this->course_id);

        if ($this->course_id <= 0 || $courseTitle === '') {
            $this->setError(Text::_('COM_DECAROCOURSES_ERROR_EDITION_COURSE_INVALID'));
            return false;
        }

        if (!$this->isValidPeriod($this->academic_year)) {
            $this->setError(Text::_('COM_DECAROCOURSES_ERROR_EDITION_PERIOD_INVALID'));
            return false;
        }

        if (!in_array($this->format, self::ALLOWED_FORMATS, true)) {
            $this->setError(Text::_('COM_DECAROCOURSES_ERROR_EDITION_FORMAT_INVALID'));
            return false;
        }

        if ($this->format === 'custom') {
            if ($this->format_custom === '') {
                $this->setError(Text::_('COM_DECAROCOURSES_ERROR_EDITION_FORMAT_CUSTOM_REQUIRED'));
                return false;
            }

            if (mb_strlen($this->format_custom) > 120) {
                $this->setError(Text::_('COM_DECAROCOURSES_ERROR_EDITION_FORMAT_CUSTOM_TOO_LONG'));
                return false;
            }
        } else {
            $this->format_custom = '';
        }

        if (!in_array($this->status, self::ALLOWED_STATUSES, true)) {
            $this->setError(Text::_('COM_DECAROCOURSES_ERROR_EDITION_STATUS_INVALID'));
            return false;
        }

        if (!in_array($this->state, [-2, 0, 1], true)) {
            $this->state = 0;
        }

        foreach (['start_date', 'end_date', 'registration_start', 'registration_end'] as $field) {
            if (!$this->normaliseDateField($field)) {
                return false;
            }
        }

        if ($this->start_date !== null && $this->end_date !== null && $this->end_date < $this->start_date) {
            $this->setError(Text::_('COM_DECAROCOURSES_ERROR_EDITION_END_BEFORE_START'));
            return false;
        }

        if ($this->registration_start !== null && $this->registration_end !== null && $this->registration_end < $this->registration_start) {
            $this->setError(Text::_('COM_DECAROCOURSES_ERROR_EDITION_REGISTRATION_END_BEFORE_START'));
            return false;
        }

        if ($this->forms_form_id > 0 && !$this->formsFormExists($this->forms_form_id)) {
            $this->setError(Text::_('COM_DECAROCOURSES_ERROR_EDITION_FORMS_INVALID'));
            return false;
        }

        $this->title = mb_substr($this->buildGeneratedTitle($courseTitle), 0, 255);

        return true;
    }

    private function getCourseTitle(int $courseId): string
    {
        if ($courseId <= 0) {
            return '';
        }

        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('title'))
            ->from($db->quoteName('#__decarocourses_courses'))
            ->where($db->quoteName('id') . ' = :courseId')
            ->bind(':courseId', $courseId, ParameterType::INTEGER);

        return trim((string) ($db->setQuery($query)->loadResult() ?? ''));
    }

    private function isValidPeriod(string $period): bool
    {
        if (preg_match('/^(\d{4})$/', $period, $matches) === 1) {
            $year = (int) $matches[1];
            return $year >= 1900 && $year <= 2200;
        }

        if (preg_match('/^(\d{4})\/(\d{4})$/', $period, $matches) !== 1) {
            return false;
        }

        $start = (int) $matches[1];
        $end = (int) $matches[2];

        return $start >= 1900 && $start <= 2200 && $end === $start + 1;
    }

    private function formsFormExists(int $formId): bool
    {
        $db = $this->getDbo();
        $table = $db->getPrefix() . 'decaroforms_forms';

        if (!in_array($table, $db->getTableList(), true)) {
            return false;
        }

        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__decaroforms_forms'))
            ->where($db->quoteName('id') . ' = :formId')
            ->bind(':formId', $formId, ParameterType::INTEGER);

        return (int) $db->setQuery($query)->loadResult() === 1;
    }

    private function buildGeneratedTitle(string $courseTitle): string
    {
        $formatLabel = match ($this->format) {
            'intensive' => Text::_('COM_DECAROCOURSES_FORMAT_INTENSIVE'),
            'evening' => Text::_('COM_DECAROCOURSES_FORMAT_EVENING'),
            'weekend' => Text::_('COM_DECAROCOURSES_FORMAT_WEEKEND'),
            'custom' => $this->format_custom,
            default => Text::_('COM_DECAROCOURSES_FORMAT_ANNUAL'),
        };

        return $courseTitle . ' — ' . $this->academic_year . ' — ' . $formatLabel;
    }

    private function normaliseDateField(string $field): bool
    {
        $value = trim((string) ($this->$field ?? ''));

        if ($value === '' || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
            $this->$field = null;
            return true;
        }

        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);

        if ($date !== false && $date->format('Y-m-d') === $value) {
            $this->$field = $value;
            return true;
        }

        $date = \DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', $value);

        if ($date !== false && $date->format('Y-m-d H:i:s') === $value) {
            $this->$field = $date->format('Y-m-d');
            return true;
        }

        $this->setError(Text::_('COM_DECAROCOURSES_ERROR_EDITION_DATE_INVALID'));
        return false;
    }

    public function store($updateNulls = true): bool
    {
        $date = Factory::getDate()->toSql();
        $userId = (int) Factory::getApplication()->getIdentity()->id;

        if ((int) $this->id === 0) {
            $this->created = $this->created ?: $date;
            $this->created_by = $this->created_by ?: $userId;
        }

        $this->modified = $date;
        $this->modified_by = $userId;

        return parent::store($updateNulls);
    }
}
