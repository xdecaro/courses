<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use InvalidArgumentException;
use Throwable;

final class LiveDataHelper
{
    public const SOURCE_COURSES = 'courses';
    public const SOURCE_FORMS = 'forms';

    public static function getOptions(string $source): array
    {
        return match ($source) {
            self::SOURCE_COURSES => self::getCourseOptions(),
            self::SOURCE_FORMS => self::getFormsOptions(),
            default => throw new InvalidArgumentException('Unsupported live data source.'),
        };
    }

    private static function getCourseOptions(): array
    {
        try {
            $db = Factory::getContainer()->get(DatabaseInterface::class);
            $query = $db->getQuery(true)
                ->select([$db->quoteName('id'), $db->quoteName('title')])
                ->from($db->quoteName('#__decarocourses_courses'))
                ->where($db->quoteName('state') . ' >= 0')
                ->order($db->quoteName('title') . ' ASC');

            $items = [];

            foreach ((array) $db->setQuery($query)->loadObjectList() as $course) {
                $id = (int) $course->id;
                $title = trim((string) $course->title);

                if ($id <= 0) {
                    continue;
                }

                $items[] = [
                    'value' => (string) $id,
                    'text' => $title !== '' ? $title : Text::sprintf('COM_DECAROCOURSES_COURSE_NUMBER', $id),
                    'disabled' => false,
                ];
            }

            return self::buildPayload(self::SOURCE_COURSES, true, $items);
        } catch (Throwable) {
            return self::buildPayload(self::SOURCE_COURSES, false, []);
        }
    }

    private static function getFormsOptions(): array
    {
        $items = [[
            'value' => '0',
            'text' => Text::_('COM_DECAROCOURSES_FORMS_NONE'),
            'disabled' => false,
        ]];

        try {
            $db = Factory::getContainer()->get(DatabaseInterface::class);
            $table = $db->getPrefix() . 'decaroforms_forms';

            if (!in_array($table, $db->getTableList(), true)) {
                $items[] = [
                    'value' => '',
                    'text' => Text::_('COM_DECAROCOURSES_FORMS_NOT_AVAILABLE'),
                    'disabled' => true,
                ];

                return self::buildPayload(self::SOURCE_FORMS, false, $items);
            }

            $query = $db->getQuery(true)
                ->select([$db->quoteName('id'), $db->quoteName('title')])
                ->from($db->quoteName('#__decaroforms_forms'))
                ->order($db->quoteName('title') . ' ASC');

            $forms = (array) $db->setQuery($query)->loadObjectList();

            if (!$forms) {
                $items[] = [
                    'value' => '',
                    'text' => Text::_('COM_DECAROCOURSES_FORMS_EMPTY'),
                    'disabled' => true,
                ];

                return self::buildPayload(self::SOURCE_FORMS, true, $items);
            }

            foreach ($forms as $form) {
                $id = (int) $form->id;
                $title = trim((string) $form->title);

                if ($id <= 0) {
                    continue;
                }

                $items[] = [
                    'value' => (string) $id,
                    'text' => ($title !== '' ? $title : Text::sprintf('COM_DECAROCOURSES_FORMS_UNTITLED', $id)) . ' (#' . $id . ')',
                    'disabled' => false,
                ];
            }

            return self::buildPayload(self::SOURCE_FORMS, true, $items);
        } catch (Throwable) {
            $items[] = [
                'value' => '',
                'text' => Text::_('COM_DECAROCOURSES_FORMS_NOT_AVAILABLE'),
                'disabled' => true,
            ];

            return self::buildPayload(self::SOURCE_FORMS, false, $items);
        }
    }

    private static function buildPayload(string $source, bool $available, array $options): array
    {
        return [
            'source' => $source,
            'available' => $available,
            'options' => $options,
            'revision' => hash('sha256', json_encode($options, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: ''),
        ];
    }
}
