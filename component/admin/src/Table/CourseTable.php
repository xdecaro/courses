<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\ParameterType;

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
        $this->description = trim((string) $this->description);
        $this->ordering = (int) $this->ordering;
        $this->state = (int) $this->state;

        if ($this->title === '') {
            $this->setError('Il titolo del corso è obbligatorio.');
            return false;
        }

        if (mb_strlen($this->title) > 255) {
            $this->setError('Il titolo del corso è troppo lungo.');
            return false;
        }

        if ($this->code === '') {
            $this->code = $this->generateCourseCode($this->title);
        }

        if (mb_strlen($this->code) > 80) {
            $this->setError('Il codice del corso è troppo lungo.');
            return false;
        }

        if (!in_array($this->state, [-2, 0, 1], true)) {
            $this->state = 0;
        }

        $sourceAlias = trim((string) $this->alias);
        $baseAlias = ApplicationHelper::stringURLSafe($sourceAlias !== '' ? $sourceAlias : $this->title);

        if ($baseAlias === '') {
            $baseAlias = 'corso';
        }

        $this->alias = $this->getUniqueAlias($baseAlias);

        return true;
    }

    private function generateCourseCode(string $title): string
    {
        $slug = ApplicationHelper::stringURLSafe($title);

        if ($slug === '') {
            return 'CORSO';
        }

        $skip = [
            'corso', 'course', 'livello', 'level',
            'di', 'del', 'della', 'dei', 'degli', 'delle',
            'the', 'of',
        ];
        $roman = [
            'i' => '1', 'ii' => '2', 'iii' => '3', 'iv' => '4', 'v' => '5',
            'vi' => '6', 'vii' => '7', 'viii' => '8', 'ix' => '9', 'x' => '10',
        ];
        $parts = [];

        foreach (array_filter(explode('-', $slug)) as $part) {
            $part = strtolower($part);

            if (in_array($part, $skip, true)) {
                continue;
            }

            $parts[] = strtoupper($roman[$part] ?? $part);

            if (count($parts) >= 4) {
                break;
            }
        }

        $code = implode('', $parts);

        return $code !== '' ? mb_substr($code, 0, 80) : 'CORSO';
    }

    private function getUniqueAlias(string $baseAlias): string
    {
        $db = $this->getDbo();
        $id = (int) $this->id;
        $pattern = $baseAlias . '-%';

        $query = $db->getQuery(true)
            ->select($db->quoteName('alias'))
            ->from($db->quoteName('#__decarocourses_courses'))
            ->where(
                '(' . $db->quoteName('alias') . ' = :baseAlias'
                . ' OR ' . $db->quoteName('alias') . ' LIKE :aliasPattern)'
            )
            ->where($db->quoteName('id') . ' <> :currentId')
            ->bind(':baseAlias', $baseAlias)
            ->bind(':aliasPattern', $pattern)
            ->bind(':currentId', $id, ParameterType::INTEGER);

        $aliases = array_fill_keys($db->setQuery($query)->loadColumn(), true);

        if (!isset($aliases[$baseAlias])) {
            return $baseAlias;
        }

        $suffix = 2;

        while (isset($aliases[$baseAlias . '-' . $suffix])) {
            ++$suffix;
        }

        return $baseAlias . '-' . $suffix;
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
