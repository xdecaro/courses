<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;

class CoursesModel extends ListModel
{
    public function __construct($config = [])
    {
        $config['filter_fields'] ??= ['id', 'a.id', 'title', 'a.title', 'code', 'a.code', 'state', 'a.state', 'ordering', 'a.ordering'];
        parent::__construct($config);
    }

    protected function getListQuery(): QueryInterface
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('a.*')
            ->from($db->quoteName('#__decarocourses_courses', 'a'));

        $search = trim((string) $this->getState('filter.search'));
        if ($search !== '') {
            $token = '%' . str_replace(' ', '%', $search) . '%';
            $query->where('(' . $db->quoteName('a.title') . ' LIKE :search OR ' . $db->quoteName('a.code') . ' LIKE :search)')
                ->bind(':search', $token);
        }

        $query->order($db->escape($this->getState('list.ordering', 'a.ordering')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

        return $query;
    }

    protected function populateState($ordering = 'a.ordering', $direction = 'ASC'): void
    {
        $this->setState('filter.search', $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search'));
        parent::populateState($ordering, $direction);
    }
}
