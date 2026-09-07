<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

final class AdminRowActionsHelper
{
    private const CONTEXTS = ['courses', 'editions'];

    public static function render(
        string $context,
        int $itemId,
        int $state,
        bool $canEdit,
        bool $canEditState,
        bool $canDelete,
        string $editUrl = '',
        array $links = [],
        bool $supportsFeatured = false,
        int $featured = 0
    ): string {
        if (!in_array($context, self::CONTEXTS, true) || $itemId <= 0) {
            return '—';
        }

        $items = [];

        if ($canEdit && $editUrl !== '') {
            $items[] = self::linkItem(
                $editUrl,
                Text::_('COM_DECAROCOURSES_ACTION_EDIT'),
                'icon-edit'
            );
        }

        foreach ($links as $link) {
            $url = trim((string) ($link['url'] ?? ''));
            $label = trim((string) ($link['label'] ?? ''));

            if ($url === '' || $label === '') {
                continue;
            }

            $items[] = self::linkItem(
                $url,
                $label,
                trim((string) ($link['icon'] ?? 'icon-folder-open'))
            );
        }

        if ($canEditState) {
            if ($state === -2) {
                $items[] = self::taskItem(
                    $context . '.restore',
                    $itemId,
                    Text::_('COM_DECAROCOURSES_ACTION_RESTORE'),
                    'icon-publish'
                );
                $items[] = self::taskItem(
                    $context . '.checkin',
                    $itemId,
                    Text::_('COM_DECAROCOURSES_ACTION_CHECKIN'),
                    'icon-checkin'
                );
            } else {
                if ($state !== 1) {
                    $items[] = self::taskItem(
                        $context . '.publish',
                        $itemId,
                        Text::_('COM_DECAROCOURSES_ACTION_PUBLISH'),
                        'icon-publish'
                    );
                }

                if ($state !== 0) {
                    $items[] = self::taskItem(
                        $context . '.unpublish',
                        $itemId,
                        Text::_('COM_DECAROCOURSES_ACTION_SUSPEND'),
                        'icon-unpublish'
                    );
                }

                if ($supportsFeatured) {
                    if ($featured === 1) {
                        $items[] = self::taskItem(
                            $context . '.unfeatured',
                            $itemId,
                            Text::_('COM_DECAROCOURSES_ACTION_UNFEATURE'),
                            'icon-unfeatured'
                        );
                    } else {
                        $items[] = self::taskItem(
                            $context . '.featured',
                            $itemId,
                            Text::_('COM_DECAROCOURSES_ACTION_FEATURE'),
                            'icon-featured'
                        );
                    }
                }

                $items[] = self::taskItem(
                    $context . '.checkin',
                    $itemId,
                    Text::_('COM_DECAROCOURSES_ACTION_CHECKIN'),
                    'icon-checkin'
                );
                $items[] = self::taskItem(
                    $context . '.trash',
                    $itemId,
                    Text::_('COM_DECAROCOURSES_ACTION_TRASH'),
                    'icon-trash'
                );
            }
        }

        if ($state === -2 && $canDelete) {
            $items[] = self::taskItem(
                $context . '.delete',
                $itemId,
                Text::_('COM_DECAROCOURSES_ACTION_DELETE_PERMANENTLY'),
                'icon-delete',
                true,
                Text::_('JGLOBAL_CONFIRM_DELETE')
            );
        }

        if (!$items) {
            return '—';
        }

        $buttonLabel = self::escape(Text::_('COM_DECAROCOURSES_TOOLBAR_ACTIONS'));
        $ariaLabel = self::escape(Text::sprintf('COM_DECAROCOURSES_ROW_ACTIONS_ARIA', $itemId));

        return '<div class="dropdown dc-row-menu">'
            . '<button class="btn dc-btn dc-btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="' . $ariaLabel . '">'
            . '<span class="icon-ellipsis-h" aria-hidden="true"></span>'
            . '<span>' . $buttonLabel . '</span>'
            . '</button>'
            . '<ul class="dropdown-menu dropdown-menu-end">'
            . implode('', $items)
            . '</ul>'
            . '</div>';
    }

    private static function linkItem(string $url, string $label, string $icon): string
    {
        return '<li><a class="dropdown-item d-flex align-items-center gap-2" href="' . self::escape($url) . '">'
            . '<span class="' . self::escape($icon) . '" aria-hidden="true"></span>'
            . '<span>' . self::escape($label) . '</span>'
            . '</a></li>';
    }

    private static function taskItem(
        string $task,
        int $itemId,
        string $label,
        string $icon,
        bool $danger = false,
        string $confirm = ''
    ): string {
        $class = 'dropdown-item d-flex align-items-center gap-2' . ($danger ? ' text-danger' : '');
        $confirmAttribute = $confirm !== ''
            ? ' data-dc-confirm="' . self::escape($confirm) . '"'
            : '';

        return '<li><button class="' . $class . '" type="button"'
            . ' data-dc-row-task="' . self::escape($task) . '"'
            . ' data-dc-item-id="' . $itemId . '"'
            . $confirmAttribute
            . '>'
            . '<span class="' . self::escape($icon) . '" aria-hidden="true"></span>'
            . '<span>' . self::escape($label) . '</span>'
            . '</button></li>';
    }

    private static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
