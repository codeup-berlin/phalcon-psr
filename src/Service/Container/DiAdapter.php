<?php

namespace Codeup\PhalconPsr\Service\Container;

class DiAdapter implements \Interop\Container\ContainerInterface
{
    /**
     * @var \Phalcon\DiInterface
     */
    protected $dependencyInjector;

    /**
     * DiAdapter constructor.
     *
     * @param \Phalcon\DiInterface $dependencyInjector
     */
    public function __construct(\Phalcon\DiInterface $dependencyInjector)
    {
        $this->dependencyInjector = $dependencyInjector;
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     * @throws \Interop\Container\Exception\NotFoundException  No entry was found for this identifier.
     * @throws \Interop\Container\Exception\ContainerException Error while retrieving the entry.
     * @return mixed Entry.
     */
    public function get($id)
    {
        try {
            return $this->dependencyInjector->get($id);
        } catch (\Phalcon\Di\Exception $diException) {
            throw new NotFoundException(
                'No service container entry found for identifier "' . $id . '"',
                $diException->getCode(),
                $diException
            );
        } catch (\Exception $diException) {
            throw new ContainerException(
                'Retrieving the service container entry failed for identifier "' . $id . '"',
                $diException->getCode(),
                $diException
            );
        }
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * @param string $id Identifier of the entry to look for.
     * @return boolean
     */
    public function has($id)
    {
        return $this->dependencyInjector->has($id);
    }
}
