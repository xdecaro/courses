<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\ToolbarHelper;

final class AdminToolbarHelper
{
    public static function dashboard(bool $canConfigure): void
    {
        if ($canConfigure) {
            ToolbarHelper::preferences('com_decarocourses');
        }
    }

    public static function courses(
        bool $canCreate,
        bool $canEditState,
        bool $canDelete,
        bool $isTrashFilter
    ): void {
        if ($canCreate) {
            ToolbarHelper::addNew('course.add', 'COM_DECAROCOURSES_COURSE_NEW');
        }

        if ($canEditState) {
            ToolbarHelper::publish('courses.publish', 'COM_DECAROCOURSES_ACTION_PUBLISH', true);

            if (!$isTrashFilter) {
                ToolbarHelper::unpublish('courses.unpublish', 'COM_DECAROCOURSES_ACTION_SUSPEND', true);
                ToolbarHelper::trash('courses.trash', 'COM_DECAROCOURSES_ACTION_TRASH', true);
            }
        }

        if ($isTrashFilter && $canDelete) {
            ToolbarHelper::deleteList('', 'courses.delete', 'COM_DECAROCOURSES_ACTION_DELETE_PERMANENTLY');
        }
    }

    public static function courseForm(bool $canSave): void
    {
        if ($canSave) {
            ToolbarHelper::apply('course.apply', 'COM_DECAROCOURSES_ACTION_SAVE');
            ToolbarHelper::save('course.save', 'COM_DECAROCOURSES_ACTION_SAVE_CLOSE');
        }

        ToolbarHelper::cancel('course.cancel', 'COM_DECAROCOURSES_ACTION_CANCEL');
    }

    public static function editions(
        bool $canCreate,
        bool $canEditState
    ): void {
        ToolbarHelper::link(
            Route::_('index.php?option=com_decarocourses&view=courses'),
            'COM_DECAROCOURSES_COURSES',
            'arrow-left'
        );

        if ($canCreate) {
            ToolbarHelper::addNew('edition.add', 'COM_DECAROCOURSES_EDITION_NEW');
        }

        if ($canEditState) {
            ToolbarHelper::publish('editions.publish', 'COM_DECAROCOURSES_ACTION_PUBLISH', true);
            ToolbarHelper::unpublish('editions.unpublish', 'COM_DECAROCOURSES_ACTION_SUSPEND', true);
            ToolbarHelper::trash('editions.trash', 'COM_DECAROCOURSES_ACTION_TRASH', true);
        }
    }

    public static function editionForm(bool $canSave): void
    {
        if ($canSave) {
            ToolbarHelper::apply('edition.apply', 'COM_DECAROCOURSES_ACTION_SAVE');
            ToolbarHelper::save('edition.save', 'COM_DECAROCOURSES_ACTION_SAVE_CLOSE');
        }

        ToolbarHelper::cancel('edition.cancel', 'COM_DECAROCOURSES_ACTION_CANCEL');
    }
}
