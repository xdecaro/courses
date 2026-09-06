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
            )->bind(':search', $token);
        }

        $courseId = (int) $this->getState('filter.course_id', 0);

        if ($courseId > 0) {
            $query->where($db->quoteName('e.course_id') . ' = :courseId')
                ->bind(':courseId', $courseId, ParameterType::INTEGER);
        }

        $status = trim((string) $this->getState('filter.status', ''));

        if (in_array($status, self::ALLOWED_STATUSES, true)) {
            $query->where($db->quoteName('e.status') . ' = :status')
                ->bind(':status', $status);
        }

        $query->order($db->quoteName('e.id') . ' DESC');

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

    protected function populateState($ordering = 'e.id', $direction = 'DESC'): void
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string');
        $status = $this->getUserStateFromRequest($this->context . '.filter.status', 'filter_status', '', 'cmd');

        $this->setState('filter.search', mb_substr(trim((string) $search), 0, 100));
        $this->setState(
            'filter.course_id',
            (int) $this->getUserStateFromRequest($this->context . '.filter.course_id', 'filter_course_id', 0, 'int')
        );
        $this->setState('filter.status', in_array($status, self::ALLOWED_STATUSES, true) ? $status : '');

        parent::populateState($ordering, $direction);
    }
}
