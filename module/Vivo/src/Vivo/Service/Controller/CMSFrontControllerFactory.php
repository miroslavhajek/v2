<?php
namespace Vivo\Service\Controller;

use Vivo\Controller\CMSFrontController;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for CMSFrontController
 */
class CMSFrontControllerFactory implements FactoryInterface
{
    /**
     * Creates CMS front controller.
     * @param ServiceLocatorInterface $serviceLocator
     * @return CMSFrontController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $fc = new CMSFrontController();
        $sm = $serviceLocator->getServiceLocator();
        $siteEvent = $sm->get('site_event');
        if ($siteEvent->getSite()) {
            $fc->setComponentFactory($sm->get('component_factory'));
        }
        $fc->setComponentTreeController($sm->get('Vivo\UI\ComponentTreeController'));
        $fc->setCMS($sm->get('Vivo\CMS\Api\CMS'));
        $fc->setSiteEvent($siteEvent);
        $fc->setRedirector($sm->get('redirector'));
        return $fc;
    }
}
