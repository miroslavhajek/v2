<?php
namespace Vivo\Module\Feature;

use Vivo\CMS\Model\Site;
use Vivo\CMS\CMS;

/**
 * SiteInstallableInterface
 * Classes implementing this interface provide installation script for module installation into a site
 */
interface SiteInstallableInterface
{
    /**
     * Runs installation script
     * @param string $moduleName
     * @param string $siteName
     * @param Site $site
     * @param CMS $cms
     * @return void
     */
    public function install($moduleName, $siteName, Site $site, CMS $cms);
}