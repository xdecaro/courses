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

    protected function loadFormData()
    {
        $app = Factory::getApplication();
        $data = $app->getUserState('com_decarocourses.edit.edition.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        $courseId = $app->input->getInt('course_id', 0);

        if ($courseId > 0) {
            if (is_object($data) && empty($data->id) && empty($data->course_id)) {
                $data->course_id = $courseId;
            } elseif (is_array($data) && empty($data['id']) && empty($data['course_id'])) {
                $data['course_id'] = $courseId;
            }
        }

        return $data;
    }
}
