<?php
namespace Vp;

use Vivo\Http\StreamResponseSender;
use Vivo\Service\Listener\RegisterTemplateResolverListener;
use Vivo\Service\Listener\InitializeViewHelpersListener;

use VpLogger\Log\Logger;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\ResponseSender\SendResponseEvent;
use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\SendResponseListener;

class Module
{
    /**
     * Module bootstrap method.
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        //Get basic objects
        /** @var $sm ServiceManager */
        $sm             = $e->getApplication()->getServiceManager();
        $eventManager   = $e->getApplication()->getEventManager();
        $config         = $sm->get('config');

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        //Bootstrap session
        $this->bootstrapSession($e);

        //Initialize logger
        $logger = $sm->get('VpLogger\logger');

        //Performance log
        $eventManager->trigger('log', $this,
            array ('message'    => 'VP Module (the Application) bootstrap start',
                'priority'   => Logger::PERF_BASE));

        //Initialize translator
        $sm->get('translator');
        //Attach a listener to set up the SiteManager object
        $runSiteManagerListener = $sm->get('run_site_manager_listener');
        $runSiteManagerListener->attach($eventManager);
        //Register Vmodule stream
        $moduleStorage  = $sm->get('module_storage');
        $streamName     = $config['modules']['stream_name'];
        \Vivo\Module\StreamWrapper::register($streamName, $moduleStorage);
        //Register template resolver
        $eventManager->attach(MvcEvent::EVENT_ROUTE,
            array (new RegisterTemplateResolverListener(), 'registerTemplateResolver'));
        //Initialize view helpers
        $eventManager->attach(MvcEvent::EVENT_ROUTE,
            array (new InitializeViewHelpersListener(), 'initializeViewHelpers'));
        //Log the matched route
        $eventManager->attach(MvcEvent::EVENT_ROUTE,
            function ($e) use ($logger){
                $logger->info('Matched route: '.$e->getRouteMatch()->getMatchedRouteName());
            }
        );
        //Register output filter listener
        $filterListener = $sm->get('Vivo\Http\Filter\OutputFilterListener');
        $filterListener->attach($eventManager);
        //Register response senders
        /** @var $sendResponseListener SendResponseListener */
        $sendResponseListener   = $sm->get('send_response_listener');
        $srlEvents              = $sendResponseListener->getEventManager();
        $streamResponseSender   = new StreamResponseSender();
        $srlEvents->attach(SendResponseEvent::EVENT_SEND_RESPONSE, $streamResponseSender, -500);

        //Performance log
        $eventManager->trigger('log', $this,
            array ('message'    => 'VP Module (the Application) bootstrapped',
                   'priority'   => Logger::PERF_BASE));
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    /**
     * Bootstraps session
     * @param $e
     */
    public function bootstrapSession($e)
    {
        /** @var $sessionManager \Zend\Session\SessionManager */
        $sessionManager = $e->getApplication()->getServiceManager()->get('Zend\Session\SessionManager');
        $sessionManager->start();
    }
}
