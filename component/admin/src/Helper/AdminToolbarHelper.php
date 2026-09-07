<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
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
        bool $canEdit,
        bool $canEditState,
        bool $canDelete,
        bool $isTrashFilter
    ): void {
        $toolbar = Factory::getApplication()->getDocument()->getToolbar();

        if ($canCreate) {
            $toolbar->addNew('course.add', 'COM_DECAROCOURSES_COURSE_NEW');
        }

        if ($canEdit) {
            ToolbarHelper::editList('course.edit', 'COM_DECAROCOURSES_ACTION_EDIT');
        }

        self::addStateActions('courses', $canEditState, $isTrashFilter, false);

        if ($isTrashFilter && $canDelete) {
            $toolbar->delete('courses.delete', 'COM_DECAROCOURSES_ACTION_DELETE_PERMANENTLY')
                ->message('JGLOBAL_CONFIRM_DELETE')
                ->listCheck(true);
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
        bool $canEdit,
        bool $canEditState,
        bool $canDelete,
        bool $isTrashFilter
    ): void {
        $toolbar = Factory::getApplication()->getDocument()->getToolbar();

        ToolbarHelper::link(
            Route::_('index.php?option=com_decarocourses&view=courses'),
            'COM_DECAROCOURSES_COURSES',
            'arrow-left'
        );

        if ($canCreate) {
            $toolbar->addNew('edition.add', 'COM_DECAROCOURSES_EDITION_NEW');
        }

        if ($canEdit) {
            ToolbarHelper::editList('edition.edit', 'COM_DECAROCOURSES_ACTION_EDIT');
        }

        self::addStateActions('editions', $canEditState, $isTrashFilter, true);

        if ($isTrashFilter && $canDelete) {
            $toolbar->delete('editions.delete', 'COM_DECAROCOURSES_ACTION_DELETE_PERMANENTLY')
                ->message('JGLOBAL_CONFIRM_DELETE')
                ->listCheck(true);
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

    private static function addStateActions(
        string $context,
        bool $canEditState,
        bool $isTrashFilter,
        bool $supportsFeatured
    ): void {
        if (!$canEditState) {
            return;
        }

        $toolbar = Factory::getApplication()->getDocument()->getToolbar();
        $dropdown = $toolbar->dropdownButton($context . '-status-group', 'COM_DECAROCOURSES_TOOLBAR_ACTIONS')
            ->toggleSplit(false)
            ->icon('icon-ellipsis-h')
            ->buttonClass('btn btn-action')
            ->listCheck(true);

        $childBar = $dropdown->getChildToolbar();

        if ($isTrashFilter) {
            $childBar->standardButton('publish', 'COM_DECAROCOURSES_ACTION_RESTORE', $context . '.restore')
                ->listCheck(true);
            $childBar->checkin($context . '.checkin')->listCheck(true);

            return;
        }

        $childBar->publish($context . '.publish')->listCheck(true);
        $childBar->unpublish($context . '.unpublish')->listCheck(true);

        if ($supportsFeatured) {
            $childBar->standardButton('featured', 'JFEATURE', $context . '.featured')
                ->listCheck(true);
            $childBar->standardButton('unfeatured', 'JUNFEATURE', $context . '.unfeatured')
                ->listCheck(true);
        }

        $childBar->checkin($context . '.checkin')->listCheck(true);
        $childBar->trash($context . '.trash')->listCheck(true);
    }
}
