<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Controller;

defined('_JEXEC') or die;

use InvalidArgumentException;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Session\Session;
use Xdecaro\Component\Decarocourses\Administrator\Helper\LiveDataHelper;

class LiveController extends BaseController
{
    public function options(): void
    {
        $app = Factory::getApplication();

        if (!Session::checkToken('get')) {
            $this->sendError(Text::_('JINVALID_TOKEN'), 403);
        }

        $identity = $app->getIdentity();
        $authorised = $identity->authorise('core.create', 'com_decarocourses')
            || $identity->authorise('core.edit', 'com_decarocourses');

        if (!$authorised) {
            $this->sendError(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $source = $app->input->getCmd('source', '');

        try {
            $data = LiveDataHelper::getOptions($source);
        } catch (InvalidArgumentException) {
            $this->sendError(Text::_('COM_DECAROCOURSES_LIVE_REFRESH_INVALID_SOURCE'), 400);
        }

        echo new JsonResponse($data, null, false, true);
        $app->close();
    }

    private function sendError(string $message, int $status): never
    {
        http_response_code($status);
        echo new JsonResponse(null, $message, true, true);
        Factory::getApplication()->close();
    }
}
