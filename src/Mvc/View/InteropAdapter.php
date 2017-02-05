<?php
namespace Codeup\PhalconPsr\Mvc\View;

class InteropAdapter implements \Codeup\InteropMvc\View
{
    /**
     * @var \Phalcon\Mvc\ViewInterface
     */
    protected $view = null;

    /**
     * InteropAdapter constructor.
     *
     * @param \Phalcon\Mvc\ViewInterface $view
     */
    public function __construct(\Phalcon\Mvc\ViewInterface $view)
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
        return $this->view->getContent();
    }

    /**
     * @return void
     */
    public function disable()
    {
        $this->view->disable();
    }
}
