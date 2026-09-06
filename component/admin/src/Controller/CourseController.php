<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;

class CourseController extends FormController
{
    protected $view_list = 'courses';

    protected function allowAdd($data = []): bool
    {
        return Factory::getApplication()
            ->getIdentity()
            ->authorise('core.create', 'com_decarocourses');
    }

    protected function allowEdit($data = [], $key = 'id'): bool
    {
        return Factory::getApplication()
            ->getIdentity()
            ->authorise('core.edit', 'com_decarocourses');
    }
}
