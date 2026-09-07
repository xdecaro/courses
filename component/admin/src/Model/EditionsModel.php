<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use Joomla\Database\QueryInterface;

class EditionsModel extends ListModel
{
    private const ALLOWED_STATUSES = [
        'draft',
        'registrations_open',
        'scheduled',
        'active',
        'completed',
        'archived',
    ];

    private const FILTER_STATES = ['-2', '0', '1'];

    public function __construct($config = [])
    {
        $config['filter_fields'] ??= [
            'id', 'e.id',
            'title', 'e.title',
            'course_id', 'e.course_id',
            'academic_year', 'e.academic_year',
            'format', 'e.format',
            'status', 'e.status',
            'state', 'e.state',
            'featured', 'e.featured',
        ];

        parent::__construct($config);
    }

    protected function getListQuery(): QueryInterface
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select(['e.*', $db->quoteName('c.title', 'course_title')])
            ->from($db->quoteName('#__decarocourses_editions', 'e'))
            ->leftJoin(
                $db->quoteName('#__decarocourses_courses', 'c')
                . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('e.course_id')
            );

        $search = trim((string) $this->getState('filter.search'));

        if ($search !== '') {
            $search = mb_substr(preg_replace('/\s+/u', ' ', $search) ?: '', 0, 100);
            $token = '%' . str_replace(' ', '%', $search) . '%';
            $query->where(
                '(' . $db->quoteName('e.title') . ' LIKE :search'
                . ' OR ' . $db->quoteName('e.academic_year') . ' LIKE :search'
                . ' OR ' . $db->quoteName('e.format_custom') . ' LIKE :search'
                . ' OR ' . $db->quoteName('c.title') . ' LIKE :search)'
            )->bind(':search', $token, ParameterType::STRING);
        }

        $courseId = (int) $this->getState('filter.course_id', 0);

        if ($courseId > 0) {
            $query->where($db->quoteName('e.course_id') . ' = :courseId')
                ->bind(':courseId', $courseId, ParameterType::INTEGER);
        }

        $status = trim((string) $this->getState('filter.status', ''));

        if (in_array($status, self::ALLOWED_STATUSES, true)) {
            $query->where($db->quoteName('e.status') . ' = :status')
                ->bind(':status', $status, ParameterType::STRING);
        }

        $this->applyPublicationFilter($query, 'e.state', 'listState');

        $query->order($db->quoteName('e.featured') . ' DESC')
            ->order($db->quoteName('e.id') . ' DESC');

        return $query;
    }

    public function getSelectedCourseTitle(): string
    {
        $courseId = (int) $this->getState('filter.course_id', 0);

        if ($courseId <= 0) {
            return '';
        }

        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select($db->quoteName('title'))
            ->from($db->quoteName('#__decarocourses_courses'))
            ->where($db->quoteName('id') . ' = :courseId')
            ->bind(':courseId', $courseId, ParameterType::INTEGER);

        return (string) ($db->setQuery($query)->loadResult() ?? '');
    }

    public function getStats(): object
    {
        $db = $this->getDatabase();
        $statusColumn = $db->quoteName('status');
        $query = $db->getQuery(true)
            ->select('COUNT(*) AS ' . $db->quoteName('total'))
            ->select(
                'SUM(CASE WHEN ' . $statusColumn . ' = ' . $db->quote('registrations_open')
                . ' THEN 1 ELSE 0 END) AS ' . $db->quoteName('registrations_open')
            )
            ->select(
                'SUM(CASE WHEN ' . $statusColumn . ' = ' . $db->quote('scheduled')
                . ' THEN 1 ELSE 0 END) AS ' . $db->quoteName('scheduled')
            )
            ->select(
                'SUM(CASE WHEN ' . $statusColumn . ' = ' . $db->quote('active')
                . ' THEN 1 ELSE 0 END) AS ' . $db->quoteName('active')
            )
            ->from($db->quoteName('#__decarocourses_editions'));

        $courseId = (int) $this->getState('filter.course_id', 0);

        if ($courseId > 0) {
            $query->where($db->quoteName('course_id') . ' = :statsCourseId')
                ->bind(':statsCourseId', $courseId, ParameterType::INTEGER);
        }

        $this->applyPublicationFilter($query, 'state', 'statsState');

        $stats = $db->setQuery($query)->loadObject();

        return $stats ?: (object) [
            'total' => 0,
            'registrations_open' => 0,
            'scheduled' => 0,
            'active' => 0,
        ];
    }

    private function applyPublicationFilter(QueryInterface $query, string $column, string $parameter): void
    {
        $db = $this->getDatabase();
        $state = (string) $this->getState('filter.state', '');

        if (in_array($state, self::FILTER_STATES, true)) {
            $placeholder = ':' . $parameter;
            $query->where($db->quoteName($column) . ' = ' . $placeholder)
                ->bind($placeholder, (int) $state, ParameterType::INTEGER);

            return;
        }

        // The normal list deliberately excludes the trash. Trashed records
        // are shown only when the explicit Cestino filter is selected.
        $query->where($db->quoteName($column) . ' IN (0, 1)');
    }

    protected function populateState($ordering = 'e.id', $direction = 'DESC'): void
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string');
        $status = $this->getUserStateFromRequest($this->context . '.filter.status', 'filter_status', '', 'cmd');
        $state = (string) $this->getUserStateFromRequest(
            $this->context . '.filter.state',
            'filter_state',
            '',
            'string'
        );

        if ($state !== '' && !in_array($state, self::FILTER_STATES, true)) {
            $state = '';
        }

        $this->setState('filter.search', mb_substr(trim((string) $search), 0, 100));
        $this->setState(
            'filter.course_id',
            (int) $this->getUserStateFromRequest($this->context . '.filter.course_id', 'filter_course_id', 0, 'int')
        );
        $this->setState('filter.status', in_array($status, self::ALLOWED_STATUSES, true) ? $status : '');
        $this->setState('filter.state', $state);

        parent::populateState($ordering, $direction);
    }
}
