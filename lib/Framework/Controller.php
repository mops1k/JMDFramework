<?php
namespace Framework;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    /** @var array */
    private $config;

    /**
     * Controller constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param $path
     * @param array $vars
     * @return Response
     */
    protected function render($path, array $vars = [])
    {
        $loader = new \Twig_Loader_Filesystem($this->config['twig']['template_path']);

        $twig = new \Twig_Environment($loader, [
            'cache' => $this->config['twig']['cache_enabled'] ? $this->config['twig']['cache'] : false
        ]);

        return new Response($twig->render($path, $vars));
    }

    protected function getEntityManager($name = 'default')
    {
        $path = [ __DIR__ . '/../../src/Entity' ];
        $params = $this->config['doctrine'][$name];

        $config = Setup::createConfiguration(true);
        $driver = new AnnotationDriver(new AnnotationReader(), $path);
        AnnotationRegistry::registerLoader('class_exists');
        $config->setMetadataDriverImpl($driver);

        $manager = EntityManager::create($params, $config);

        return $manager;
    }
}
