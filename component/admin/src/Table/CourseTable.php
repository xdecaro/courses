<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\DatabaseDriver;
use Joomla\String\StringHelper;

class CourseTable extends Table
{
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__decarocourses_courses', 'id', $db);
    }

    public function check(): bool
    {
        $this->title = trim((string) $this->title);
        $this->code = trim((string) $this->code);

        if ($this->title === '') {
            $this->setError('Il titolo del corso è obbligatorio.');
            return false;
        }

        if (trim((string) $this->alias) === '') {
            $this->alias = $this->title;
        }

        $this->alias = StringHelper::strtolower(trim((string) $this->alias));
        $this->alias = preg_replace('/[^a-z0-9\-]+/u', '-', $this->alias) ?: 'corso-' . time();
        $this->alias = trim($this->alias, '-');

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
