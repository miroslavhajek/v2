<?php
namespace Vivo\UI;

use Vivo\CMS\UI\Component;
use Vivo\UI\Exception\LogicException;
use Vivo\UI\Exception\ExceptionInterface as UIException;
use Vivo\UI\Exception\RuntimeException;

use Zend\Session\Container;
use Zend\Session\SessionManager;
use Zend\Http\PhpEnvironment\Request;

/**
 * Performs operation on UI component tree.
 */
class ComponentTreeController
{
    /**
     * @var ComponentInterface
     */
    protected $root;

    /**
     * @var \Zend\Session\Container
     */
    protected $session;

    /**
     * Construct.
     * @param SessionManager $sessionManager
     */
    public function __construct(SessionManager $sessionManager, Request $request)
    {
        $this->session = new Container('component_states', $sessionManager);
        $this->request = $request;
    }

    /**
     * Sets root component of tree.
     * @param ComponentInterface $root
     */
    public function setRoot(ComponentInterface $root)
    {
        $this->root = $root;
    }

    /**
     * Returns root component of tree.
     * @throws LogicException
     * @return \Vivo\UI\ComponentInterface
     */
    public function getRoot()
    {
        if (null === $this->root) {
            throw new LogicException(
                sprintf('%s: Root component is not set.', __METHOD__));
        }
        return $this->root;
    }


    /**
     * Returns component by its path in tree.
     * @param string $path
     * @throws RuntimeException
     * @return \Vivo\UI\ComponentInterface
     */
    public function getComponent($path)
    {
        $component = $this->getRoot();
        $names = explode(Component::COMPONENT_SEPARATOR, $path);
        for ($i = 1; $i < count($names); $i++) {
            try {
                $component = $component->getComponent($names[$i]);
            } catch (UIException $e) {
                throw new RuntimeException(
                    sprintf("%s: Component for path '%s' not found.",
                        __METHOD__, $path), null, $e);
            }
        }
        return $component;
    }

    /**
     * Initialize component tree.
     */
    public function init()
    {
        $this->root->init();
    }

    /**
     * Loads state of components.
     */
    public function loadState()
    {
        foreach ($this->getTreeIterator() as $name => $component){
            if ($component instanceof PersistableInterface){
                $key = $this->request->getUri()->getPath(). $component->getPath();
                $state = $this->session[$key];
                $component->loadState($state);
            }
        }
    }

    /**
     * Save state of components.
     */
    public function saveState()
    {
        foreach ($this->getTreeIterator() as $name => $component) {
            if ($component instanceof PersistableInterface){
                $key = $this->request->getUri()->getPath(). $component->getPath();
                $this->session[$key] = $component->saveState();
            }
        }
    }

    /**
     * Returns view model tree from component tree.
     * @return Ambigous <\Zend\View\Model\ModelInterface, string>
     */
    public function view()
    {
        return $this->root->view();
    }

    /**
     * Call done on components.
     */
    public function done()
    {
        $this->root->done();
    }

    /**
     * Only init branch of tree defined by path.
     * @param string $path
     */
    public function initComponent($path)
    {
        //TODO
    }

    /**
     * Invokes action on component.
     * @param string $path
     * @param string $action
     * @param array $params
     * @throws RuntimeException
     */
    public function invokeAction($path, $action, $params)
    {
        if (substr($action, 0, 2) == '__' || $action == 'init') // Init and php magic methods are not accessible.
            throw new RuntimeException("Method $action is not accessible");
        $component = $this->getComponent($path);

        if (!method_exists($component, $action)) {
            throw new RuntimeException(
                sprintf("%s: Component '%s' doesn't have method '%s'.",
                    __METHOD__, get_class($component), $action));
        }

        try {
            return call_user_func_array(array($component, $action), $params);
        } catch (UIException $e) {
            throw new RuntimeException(
                sprintf("%s: Can not call action on component.", __METHOD__),
                null, $e);
        }
    }

    /**
     * Returns iterator for iterating over component tree.
     * @param integer $mode
     * @return \RecursiveIteratorIterator
     * @see \RecursiveIteratorIterator for available modes.
     */
    public function getTreeIterator($mode = \RecursiveIteratorIterator::SELF_FIRST)
    {
        $iter  = new ComponentTreeIterator($this->getRoot());
        return new \RecursiveIteratorIterator ($iter, $mode);
    }
}
