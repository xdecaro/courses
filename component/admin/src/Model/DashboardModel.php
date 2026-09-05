<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Throwable;

class DashboardModel extends BaseDatabaseModel
{
    public function getSummary(): array
    {
        $db = $this->getDatabase();
        $summary = [
            'courses' => 0,
            'editions' => 0,
            'active' => 0,
            'registrations_open' => 0,
            'forms_available' => false,
            'forms_count' => 0,
        ];

        $db->setQuery($db->getQuery(true)->select('COUNT(*)')->from($db->quoteName('#__decarocourses_courses'))->where($db->quoteName('state') . ' >= 0'));
        $summary['courses'] = (int) $db->loadResult();

        $db->setQuery($db->getQuery(true)->select('COUNT(*)')->from($db->quoteName('#__decarocourses_editions'))->where($db->quoteName('state') . ' >= 0'));
        $summary['editions'] = (int) $db->loadResult();

        $db->setQuery($db->getQuery(true)->select('COUNT(*)')->from($db->quoteName('#__decarocourses_editions'))->where($db->quoteName('status') . " = 'active'")->where($db->quoteName('state') . ' = 1'));
        $summary['active'] = (int) $db->loadResult();

        $db->setQuery($db->getQuery(true)->select('COUNT(*)')->from($db->quoteName('#__decarocourses_editions'))->where($db->quoteName('status') . " = 'registrations_open'")->where($db->quoteName('state') . ' = 1'));
        $summary['registrations_open'] = (int) $db->loadResult();

        try {
            $formsTable = $db->getPrefix() . 'decaroforms_forms';
            $summary['forms_available'] = in_array($formsTable, $db->getTableList(), true);
            if ($summary['forms_available']) {
                $db->setQuery($db->getQuery(true)->select('COUNT(*)')->from($db->quoteName('#__decaroforms_forms')));
                $summary['forms_count'] = (int) $db->loadResult();
            }
        } catch (Throwable $e) {
            $summary['forms_available'] = false;
        }

        return $summary;
    }

    public function getRecentEditions(int $limit = 5): array
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select(['e.*', $db->quoteName('c.title', 'course_title')])
            ->from($db->quoteName('#__decarocourses_editions', 'e'))
            ->leftJoin($db->quoteName('#__decarocourses_courses', 'c') . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('e.course_id'))
            ->where($db->quoteName('e.state') . ' >= 0')
            ->order($db->quoteName('e.id') . ' DESC');
        $db->setQuery($query, 0, $limit);

        return (array) $db->loadObjectList();
    }
}
