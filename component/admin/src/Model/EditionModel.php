<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;

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
            } else {
                $data['state'] = 0;
            }
        }

        return parent::save($data);
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
        } elseif (is_array($data) && empty($data['id'])) {
            if ($courseId > 0 && empty($data['course_id'])) {
                $data['course_id'] = $courseId;
            }

            if (empty($data['academic_year'])) {
                $data['academic_year'] = $currentYear;
            }
        }

        return $data;
    }
}
