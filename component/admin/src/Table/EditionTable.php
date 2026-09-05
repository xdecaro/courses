<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

class EditionTable extends Table
{
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__decarocourses_editions', 'id', $db);
    }

    public function check(): bool
    {
        $this->title = trim((string) $this->title);
        $this->academic_year = trim((string) $this->academic_year);

        if ((int) $this->course_id <= 0) {
            $this->setError('Seleziona un corso.');
            return false;
        }

        if ($this->title === '') {
            $this->setError('Il titolo dell’edizione è obbligatorio.');
            return false;
        }

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
