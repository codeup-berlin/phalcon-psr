<?php
namespace Codeup\PhalconPsr\Mvc\View;

class InteropAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InteropAdapter
     */
    private $classUnderTest;

    public function setUp()
    {
        $phalconView = new \Phalcon\Mvc\View();
        $phalconView->setViewsDir(__DIR__ . '/views/');
        $this->classUnderTest = new InteropAdapter($phalconView);
    }

    /**
     * @test
     * @outputBuffering enabled
     */
    public function render_defaultView()
    {
        $result = $this->classUnderTest->render();
        $this->assertSame('Main', $result);
        $this->expectOutputString('');
    }

    /**
     * @test
     * @outputBuffering enabled
     */
    public function renderAction_existingView()
    {
        $result = $this->classUnderTest->renderAction('controller', 'action');
        $this->assertSame('MainControllerAction', $result);
        $this->expectOutputString('');
    }

    /**
     * @test
     * @outputBuffering enabled
     */
    public function renderAction_missingView()
    {
        $result = $this->classUnderTest->renderAction('controller', 'wtf');
        $this->assertSame('MainController', $result);
        $this->expectOutputString('');
    }

    /**
     * @test
     * @outputBuffering enabled
     */
    public function renderView_notAnAction()
    {
        $result = $this->classUnderTest->renderView('controller/pick/view');
        $this->assertSame('MainPick', $result);
        $this->expectOutputString('');
    }
}
