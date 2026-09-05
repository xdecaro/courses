<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\ParameterType;

class EditionTable extends Table
{
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__decarocourses_editions', 'id', $db);
    }

    public function check(): bool
    {
        $this->course_id = (int) $this->course_id;
        $this->title = trim((string) $this->title);
        $this->academic_year = trim((string) $this->academic_year);
        $this->format = trim((string) $this->format);
        $this->capacity = max(0, (int) $this->capacity);
        $this->status = trim((string) $this->status);
        $this->forms_form_id = max(0, (int) $this->forms_form_id);
        $this->notes = trim((string) $this->notes);
        $this->state = (int) $this->state;

        if ($this->course_id <= 0 || !$this->courseExists($this->course_id)) {
            $this->setError('Seleziona un corso valido.');
            return false;
        }

        if ($this->title === '') {
            $this->setError('Il titolo dell’edizione è obbligatorio.');
            return false;
        }

        if (mb_strlen($this->title) > 255) {
            $this->setError('Il titolo dell’edizione è troppo lungo.');
            return false;
        }

        if (mb_strlen($this->academic_year) > 20) {
            $this->setError('L’anno accademico è troppo lungo.');
            return false;
        }

        if (!in_array($this->format, ['annual', 'intensive', 'evening', 'custom'], true)) {
            $this->setError('La formula del corso non è valida.');
            return false;
        }

        if (!in_array($this->status, ['draft', 'registrations_open', 'scheduled', 'active', 'completed', 'archived'], true)) {
            $this->setError('Lo stato operativo dell’edizione non è valido.');
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
            $this->setError('La data di fine non può precedere la data di inizio.');
            return false;
        }

        if ($this->registration_start !== null && $this->registration_end !== null && $this->registration_end < $this->registration_start) {
            $this->setError('La chiusura delle iscrizioni non può precedere l’apertura.');
            return false;
        }

        return true;
    }

    private function courseExists(int $courseId): bool
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__decarocourses_courses'))
            ->where($db->quoteName('id') . ' = :courseId')
            ->bind(':courseId', $courseId, ParameterType::INTEGER);

        return (int) $db->setQuery($query)->loadResult() === 1;
    }

    private function normaliseDateField(string $field): bool
    {
        $value = trim((string) ($this->$field ?? ''));

        if ($value === '') {
            $this->$field = null;
            return true;
        }

        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);

        if ($date === false || $date->format('Y-m-d') !== $value) {
            $this->setError('Una delle date inserite non è valida.');
            return false;
        }

        $this->$field = $value;
        return true;
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
