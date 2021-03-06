<?php
namespace Framework\Loader;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Exception\ParseException;

class PhpFileLoader extends FileLoader
{
    /**
     * Loads a resource.
     *
     * @param mixed $resource The resource
     * @param string|null $type The resource type or null if unknown
     *
     * @return mixed
     * @throws \Exception If something went wrong
     */
    public function load($resource, $type = null)
    {
        $path = $this->getLocator()->locate($resource);
        if (!stream_is_local($path)) {
            throw new \InvalidArgumentException(sprintf('This is not a local file "%s".', $path));
        }

        if (!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('File "%s" not found.', $path));
        }

        try {
            $parsedConfig = require $path;
        } catch (ParseException $e) {
            throw new \InvalidArgumentException(sprintf('The file "%s" does not contain valid PHP.', $path), 0, $e);
        }

        return $parsedConfig;
    }

    /**
     * Returns whether this class supports the given resource.
     *
     * @param mixed $resource A resource
     * @param string|null $type The resource type or null if unknown
     *
     * @return bool True if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource)
            && in_array(pathinfo($resource, PATHINFO_EXTENSION), ['php'], true)
            && (!$type || 'php' === $type);
    }
}
