<?php
namespace Vivo\Backend\UI\Explorer;

use Vivo\Backend\UI\EntityManagerInterface;
use Vivo\CMS\Api\CMS;
use Vivo\CMS\Model;
use Vivo\CMS\UI\Manager\SiteSelector;
use Vivo\Service\Initializer\RequestAwareInterface;
use Vivo\UI\ComponentContainer;
use Vivo\UI\PersistableInterface;

use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\SharedEventManager;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\RequestInterface;

/**
 * Explorer component.
 */
class Explorer extends ComponentContainer implements EventManagerAwareInterface,
        EntityManagerInterface, RequestAwareInterface, PersistableInterface
{
    /**
     * Entity beeing explored.
     * @var \Vivo\CMS\Model\Entity
     */
    protected $entity;

    /**
     * Current component name.
     * @var string
     */
    protected $currentName = 'browser';

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var SiteSelector
     */
    protected $siteSelector;

    /**
     * @var ExplorerComponentFactory
     */
    protected $explorerComponentFactory;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    protected $explorerTabs = array(
                'editor' => 'Vivo\CMS\UI\Manager\Explorer\Editor',
                'viewer' => 'Vivo\Backend\UI\Explorer\Viewer',
                'browser' => 'Vivo\Backend\UI\Explorer\Browser',
                'inspect' => 'Vivo\Backend\UI\Explorer\Inspect',
                );

    /**
     * Constructor.
     * @param CMS $cmsApi
     * @param SiteSelector $siteSelector
     * @param ExplorerComponentFactory $explorerComponentFactory
     */
    public function __construct(CMS $cmsApi,
            \Vivo\Backend\UI\SiteSelector $siteSelector,
            ServiceManager $serviceManager)
    {
        $this->cmsApi = $cmsApi;
        $this->siteSelector = $siteSelector;
        $this->serviceManager = $serviceManager;
    }

    /**
     * (non-PHPdoc)
     * @see \Vivo\UI\ComponentContainer::init()
     */
    public function init()
    {
        $this->loadEntity();
        $this->setCurrent($this->currentName);

        //attach events
        $this->siteSelector->getEventManager()
                ->attach('setSite', array($this, 'onSiteChange'));
        $this->ribbon->getEventManager()
                ->attach('itemClick', array($this, 'onRibbonClick'));
        parent::init();
    }

    /**
     * (non-PHPdoc)
     * @see \Vivo\UI\PersistableInterface::loadState()
     */
    public function loadState($state)
    {
        $this->entity = $state['entity'];
        $this->currentName = isset($state['current_name']) ?
                $state['current_name'] : $this->currentName;
        $component = $this->serviceManager->get($this->explorerTabs[$this->currentName]);
        $this->addComponent($component, $this->currentName);
    }

    /**
     * (non-PHPdoc)
     * @see \Vivo\UI\PersistableInterface::saveState()
     */
    public function saveState()
    {
        $state['entity'] = $this->entity;
        $state['current_name'] = $this->currentName;
        return $state;
    }

    /**
     * Set current component
     * @param string $name
     */
    public function setCurrent($name)
    {
        $this->removeComponent($this->currentName);
        $this->currentName = $name;
        $component = $this->serviceManager->get($this->explorerTabs[$this->currentName]);
        $this->addComponent($component, $name);
        $component->init();
    }

    /**
     * Loads entity from url
     */
    protected function loadEntity()
    {
        if ($site = $this->getSite()) {
            if ($relPath = $this->request->getQuery('url', false)) {
                $entity = $this->cmsApi->getSiteEntity($relPath, $site);
                $this->setEntity($entity);
            } elseif ($this->entity === null) {
                $entity = $this->cmsApi->getSiteEntity('/', $site);
                $this->setEntity($entity);
            }
        }
    }

    /**
     * Callback for site change event.
     *
     * When site is changed, load root document.
     * @param Event $event
     */
    public function onSiteChange(Event $event)
    {
        $this->setEntityByRelPath('/');
    }

    /**
     * Callback for ribbon click event.
     * @param Event $event
     */
    public function onRibbonClick(Event $event)
    {
        $this->setCurrent($event->getParam('itemName'));
    }

    /**
     * Returns entity beeing explored.
     * @return \Vivo\CMS\Model\Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param \Vivo\CMS\Model\Entity $entity
     */
    public function setEntity(Model\Entity $entity)
    {
        $this->eventManager
                ->trigger(__FUNCTION__, $this, array('entity' => $entity));
        $this->entity = $entity;
    }

    /**
     * (non-PHPdoc)
     * @see \Vivo\CMS\UI\Manager\Explorer\EntityManagerInterface::setEntityByRelPath()
     */
    public function setEntityByRelPath($relPath)
    {
        $this->setEntity($this->cmsApi->getSiteEntity($relPath, $this->getSite()));
    }

    /**
     * (non-PHPdoc)
     * @see \Vivo\UI\ComponentContainer::view()
     */
    public function view()
    {
        $this->view->currentName = $this->currentName;
        return parent::view();
    }

    /**
     * (non-PHPdoc)
     * @see \Zend\EventManager\EventManagerAwareInterface::setEventManager()
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
        $this->eventManager->addIdentifiers(__CLASS__);
    }

    /**
     * (non-PHPdoc)
     * @see \Zend\EventManager\EventsCapableInterface::getEventManager()
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * Returns site beeing explored.
     * @return \Vivo\CMS\Model\Site
     */
    public function getSite()
    {
        return $this->siteSelector->getSite();
    }

    /**
     * @param RequestInterface $request
     */
    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;
    }
}
