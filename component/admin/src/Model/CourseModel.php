<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Database\ParameterType;

class CourseModel extends AdminModel
{
    public function getTable($type = 'Course', $prefix = 'Administrator', $config = [])
    {
        return parent::getTable($type, $prefix, $config);
    }

    public function getForm($data = [], $loadData = true)
    {
        return $this->loadForm('com_decarocourses.course', 'course', ['control' => 'jform', 'load_data' => $loadData]);
    }

    public function save($data)
    {
        $identity = Factory::getApplication()->getIdentity();

        if (!$identity->authorise('core.edit.state', 'com_decarocourses')) {
            $id = (int) ($data['id'] ?? 0);

            if ($id > 0) {
                $current = $this->getItem($id);
                $data['state'] = (int) ($current->state ?? 0);
                $data['ordering'] = (int) ($current->ordering ?? 0);
            } else {
                $data['state'] = 0;
                $data['ordering'] = 0;
            }
        }

        return parent::save($data);
    }

    protected function canDelete($record): bool
    {
        if (!parent::canDelete($record)) {
            return false;
        }

        $id = (int) ($record->id ?? 0);

        if ($id <= 0) {
            return false;
        }

        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__decarocourses_editions'))
            ->where($db->quoteName('course_id') . ' = :courseId')
            ->bind(':courseId', $id, ParameterType::INTEGER);

        if ((int) $db->setQuery($query)->loadResult() > 0) {
            $this->setError(Text::_('COM_DECAROCOURSES_ERROR_COURSE_DELETE_HAS_EDITIONS'));
            return false;
        }

        return true;
    }

    protected function loadFormData()
    {
        $app = Factory::getApplication();
        $data = $app->getUserState('com_decarocourses.edit.course.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }
}
