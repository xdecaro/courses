<?php
namespace Xdecaro\Component\Decarocourses\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class DashboardModel extends BaseDatabaseModel
{
    public function getSummary(): array
    {
        $db = $this->getDatabase();
        $result = ['courses' => 0, 'active' => 0, 'registrations_open' => 0];
        $db->setQuery($db->getQuery(true)->select('COUNT(*)')->from($db->quoteName('#__decarocourses_courses'))->where($db->quoteName('state') . ' = 1'));
        $result['courses'] = (int) $db->loadResult();
        $db->setQuery($db->getQuery(true)->select('COUNT(*)')->from($db->quoteName('#__decarocourses_editions'))->where($db->quoteName('state') . " = 1 AND " . $db->quoteName('status') . " = 'active'"));
        $result['active'] = (int) $db->loadResult();
        $db->setQuery($db->getQuery(true)->select('COUNT(*)')->from($db->quoteName('#__decarocourses_editions'))->where($db->quoteName('state') . " = 1 AND " . $db->quoteName('status') . " = 'registrations_open'"));
        $result['registrations_open'] = (int) $db->loadResult();
        return $result;
    }
}
