<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use Joomla\Database\QueryInterface;

class CoursesModel extends ListModel
{
    public function __construct($config = [])
    {
        $config['filter_fields'] ??= [
            'id', 'a.id',
            'title', 'a.title',
            'code', 'a.code',
            'state', 'a.state',
            'ordering', 'a.ordering',
            'modified', 'a.modified',
            'editions_count',
        ];

        parent::__construct($config);
    }

    protected function getListQuery(): QueryInterface
    {
        $db = $this->getDatabase();

        $editionCounts = $db->getQuery(true)
            ->select($db->quoteName('course_id'))
            ->select('COUNT(*) AS ' . $db->quoteName('editions_count'))
            ->from($db->quoteName('#__decarocourses_editions'))
            ->where($db->quoteName('state') . ' <> -2')
            ->group($db->quoteName('course_id'));

        $query = $db->getQuery(true)
            ->select('a.*')
            ->select('COALESCE(' . $db->quoteName('ec.editions_count') . ', 0) AS ' . $db->quoteName('editions_count'))
            ->from($db->quoteName('#__decarocourses_courses', 'a'))
            ->leftJoin(
                '(' . $editionCounts . ') AS ' . $db->quoteName('ec')
                . ' ON ' . $db->quoteName('ec.course_id') . ' = ' . $db->quoteName('a.id')
            );

        $search = trim((string) $this->getState('filter.search'));

        if ($search !== '') {
            $token = '%' . str_replace(' ', '%', $search) . '%';
            $query->where(
                '(' . $db->quoteName('a.title') . ' LIKE :search'
                . ' OR ' . $db->quoteName('a.code') . ' LIKE :search'
                . ' OR ' . $db->quoteName('a.alias') . ' LIKE :search)'
            )->bind(':search', $token);
        }

        $state = $this->getState('filter.state');

        if ($state !== '' && $state !== null) {
            $state = (int) $state;
            $query->where($db->quoteName('a.state') . ' = :state')
                ->bind(':state', $state, ParameterType::INTEGER);
        }

        $orderingMap = [
            'id' => 'a.id',
            'a.id' => 'a.id',
            'title' => 'a.title',
            'a.title' => 'a.title',
            'code' => 'a.code',
            'a.code' => 'a.code',
            'state' => 'a.state',
            'a.state' => 'a.state',
            'ordering' => 'a.ordering',
            'a.ordering' => 'a.ordering',
            'modified' => 'a.modified',
            'a.modified' => 'a.modified',
            'editions_count' => 'editions_count',
        ];

        $requestedOrdering = (string) $this->getState('list.ordering', 'a.ordering');
        $ordering = $orderingMap[$requestedOrdering] ?? 'a.ordering';
        $direction = strtoupper((string) $this->getState('list.direction', 'ASC')) === 'DESC' ? 'DESC' : 'ASC';

        $query->order($db->quoteName($ordering) . ' ' . $direction)
            ->order($db->quoteName('a.id') . ' ASC');

        return $query;
    }

    public function getStats(): object
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('COUNT(*) AS ' . $db->quoteName('total'))
            ->select('SUM(CASE WHEN ' . $db->quoteName('state') . ' = 1 THEN 1 ELSE 0 END) AS ' . $db->quoteName('active'))
            ->select('SUM(CASE WHEN ' . $db->quoteName('state') . ' = 0 THEN 1 ELSE 0 END) AS ' . $db->quoteName('inactive'))
            ->select('SUM(CASE WHEN ' . $db->quoteName('state') . ' = -2 THEN 1 ELSE 0 END) AS ' . $db->quoteName('trashed'))
            ->from($db->quoteName('#__decarocourses_courses'));

        $stats = $db->setQuery($query)->loadObject();

        return $stats ?: (object) [
            'total' => 0,
            'active' => 0,
            'inactive' => 0,
            'trashed' => 0,
        ];
    }

    protected function populateState($ordering = 'a.ordering', $direction = 'ASC'): void
    {
        $this->setState(
            'filter.search',
            $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string')
        );
        $this->setState(
            'filter.state',
            $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'string')
        );

        parent::populateState($ordering, $direction);
    }
}
