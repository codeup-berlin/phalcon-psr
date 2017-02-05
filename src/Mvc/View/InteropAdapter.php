<?php
namespace Codeup\PhalconPsr\Mvc\View;

class InteropAdapter implements \Codeup\InteropMvc\View
{
    /**
     * @var \Phalcon\Mvc\View
     */
    protected $view = null;

    /**
     * InteropAdapter constructor.
     *
     * @param \Phalcon\Mvc\View $view
     */
    public function __construct(\Phalcon\Mvc\View $view)
    {
        $this->view = $view;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set(string $name, $value)
    {
        $this->view->{$name} = $value;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->view->{$name};
    }

    /**
     * @return string
     */
    public function render()
    {
        return $this->view->getRender('', '');
    }

    /**
     * @return void
     */
    public function disable()
    {
        $this->view->disable();
    }

    /**
     * @param string $controllerName
     * @param string $actionName
     * @return string
     */
    public function renderAction(string $controllerName, string $actionName)
    {
        return $this->view->getRender($controllerName, $actionName);
    }

    /**
     * @param string $viewPath path of the view file without any extension
     * @return string
     */
    public function renderView(string $viewPath)
    {
        $this->view->pick($viewPath);
        return $this->view->getRender('', '');
    }
}
