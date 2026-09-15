<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports;

use Piwik\Menu\MenuAdmin;
use Piwik\Piwik;
use Piwik\Plugins\UsersManager\UserPreferences;
use Piwik\Request;

class Menu extends \Piwik\Plugin\Menu
{
    public function configureAdminMenu(MenuAdmin $menu)
    {
        $defaultIdSite = (int) (new UserPreferences())->getDefaultWebsiteId();
        $idSite = Request::fromRequest()->getIntegerParameter('idSite', $defaultIdSite);

        if ($idSite > 0 && Piwik::isUserHasAdminAccess($idSite)) {
            $menu->addMeasurableItem('WeatherReports_Weather', $this->urlForAction('manage', ['idSite' => $idSite]), 42);
        }
    }
}
