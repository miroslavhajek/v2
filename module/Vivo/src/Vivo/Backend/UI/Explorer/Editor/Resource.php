<?php
namespace Vivo\Backend\UI\Explorer\Editor;

use Vivo\UI\AbstractForm;
use Vivo\Form\Form;
use Vivo\CMS\Model;
use Vivo\Util\RedirectEvent;

class Resource extends AbstractForm
{
    /**
     * @var \Vivo\CMS\Api\CMS
     */
    private $cmsApi;

    /**
     * @var \Vivo\CMS\Model\Entity
     */
    private $entity;

    /**
     * @var array
     */
    private $resources = array();

    /**
     * @param \Vivo\CMS\Api\CMS $cmsApi
     */
    public function __construct(\Vivo\CMS\Api\CMS $cmsApi)
    {
        $this->cmsApi = $cmsApi;
    }

    public function init()
    {
        $this->resources = $this->cmsApi->scanResources($this->entity);

        parent::init();
    }

    public function setEntity(Model\Entity $entity)
    {
        $this->entity = $entity;
    }

    protected function doGetForm()
    {
        $id = $this->entity->getUuid();
        $action = $this->request->getUri()->getPath();

        $form = new Form('resource-'.$id);
        $form->setAttribute('action', $action);
        $form->setAttribute('enctype', 'multipart/form-data');
        $form->add(array(
            'name' => 'resource',
            'type' => 'Vivo\Form\Element\File',
        ));
        $form->add(array(
            'name' => 'save',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Save',
            ),
        ));
        $form->add(array(
            'name' => 'act',
            'attributes' => array(
                'type' => 'hidden',
                'value' => $this->getPath('save'),
            ),
        ));

        return $form;
    }

    /**
     * Saves new resource.
     */
    public function save()
    {
        $form = $this->getForm();

        if ($this->request->isPost()) {
            // Make certain to merge the files info!
            $post = array_merge_recursive($this->request->getPost()->toArray(), $this->request->getFiles()->toArray());
            $form->setData($post);

            if($form->isValid()) {
                $data = $form->getData();
                $data = $data['resource'];

                $fileName = strtolower($data['tmp_name']);
                $fileExt = strtolower(pathinfo($data['name'], PATHINFO_EXTENSION));
                $name = sprintf('%s.%s', $fileName, $fileExt);

                if($this->cmsApi->getResource($this->entity, $name)) {

                    return;
                }

                $this->cmsApi->saveResource($this->entity, $name, file_get_contents($data['tmp_name']));

                $this->events->trigger(new RedirectEvent());
            }

            $form->get('resource')->setValue('');
        }
    }

    /**
     * Delete.
     *
     * @param string $name Resource name.
     */
    public function delete($name)
    {
        $this->cmsApi->removeResource($this->entity, $name);
    }

    public function view()
    {
        $view = parent::view();
        $view->resources = $this->resources;

        return $view;
    }
}