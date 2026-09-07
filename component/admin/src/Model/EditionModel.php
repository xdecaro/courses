<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Database\ParameterType;
use Joomla\Utilities\ArrayHelper;

class EditionModel extends AdminModel
{
    public function getTable($type = 'Edition', $prefix = 'Administrator', $config = [])
    {
        return parent::getTable($type, $prefix, $config);
    }

    public function getForm($data = [], $loadData = true)
    {
        return $this->loadForm('com_decarocourses.edition', 'edition', ['control' => 'jform', 'load_data' => $loadData]);
    }

    public function save($data)
    {
        $identity = Factory::getApplication()->getIdentity();

        if (!$identity->authorise('core.edit.state', 'com_decarocourses')) {
            $id = (int) ($data['id'] ?? 0);

            if ($id > 0) {
                $current = $this->getItem($id);
                $data['state'] = (int) ($current->state ?? 0);
                $data['featured'] = (int) ($current->featured ?? 0);
            } else {
                $data['state'] = 0;
                $data['featured'] = 0;
            }
        }

        return parent::save($data);
    }

    public function featured($pks, $value = 0): bool
    {
        $pks = array_values(array_filter(ArrayHelper::toInteger((array) $pks)));
        $value = (int) ((int) $value === 1);

        if (!$pks) {
            $this->setError(Text::_('COM_DECAROCOURSES_NO_ITEM_SELECTED'));
            return false;
        }

        $db = $this->getDatabase();
        $modified = Factory::getDate()->toSql();
        $modifiedBy = (int) Factory::getApplication()->getIdentity()->id;

        try {
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__decarocourses_editions'))
                ->set($db->quoteName('featured') . ' = :featured')
                ->set($db->quoteName('modified') . ' = :modified')
                ->set($db->quoteName('modified_by') . ' = :modifiedBy')
                ->whereIn($db->quoteName('id'), $pks)
                ->where($db->quoteName('state') . ' <> -2')
                ->bind(':featured', $value, ParameterType::INTEGER)
                ->bind(':modified', $modified, ParameterType::STRING)
                ->bind(':modifiedBy', $modifiedBy, ParameterType::INTEGER);

            $db->setQuery($query)->execute();
            $this->cleanCache();
        } catch (\Throwable $e) {
            $this->setError($e->getMessage());
            return false;
        }

        return true;
    }

    protected function loadFormData()
    {
        $app = Factory::getApplication();
        $data = $app->getUserState('com_decarocourses.edit.edition.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        $courseId = $app->input->getInt('course_id', 0);
        $currentYear = Factory::getDate()->format('Y');

        if (is_object($data) && empty($data->id)) {
            if ($courseId > 0 && empty($data->course_id)) {
                $data->course_id = $courseId;
            }

            if (empty($data->academic_year)) {
                $data->academic_year = $currentYear;
            }

            $data->featured = (int) ($data->featured ?? 0);
        } elseif (is_array($data) && empty($data['id'])) {
            if ($courseId > 0 && empty($data['course_id'])) {
                $data['course_id'] = $courseId;
            }

            if (empty($data['academic_year'])) {
                $data['academic_year'] = $currentYear;
            }

            $data['featured'] = (int) ($data['featured'] ?? 0);
        }

        return $data;
    }
}
