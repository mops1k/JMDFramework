<?php
namespace Framework;

use Framework\Loader\PhpFileLoader as FrameworkPhpFileLoader;
use Framework\Loader\YamlFileLoader as FrameworkYamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class Core implements HttpKernelInterface
{
    /** @var RouteCollection */
    protected $routes;

    public function __construct()
    {
        $this->routes = $this->loadRoutes();
    }

    /**
     * Handles a Request to convert it to a Response.
     *
     * When $catch is true, the implementation must catch all exceptions
     * and do its best to convert them to a Response instance.
     *
     * @param Request $request A Request instance
     * @param int $type The type of the request
     *                         (one of HttpKernelInterface::MASTER_REQUEST or HttpKernelInterface::SUB_REQUEST)
     * @param bool $catch Whether to catch exceptions or not
     *
     * @return Response A Response instance
     *
     * @throws \Exception When an Exception occurs during processing
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true) : ?Response
    {
        $context = new RequestContext();
        $context->fromRequest($request);

        $matcher = new UrlMatcher($this->routes, $context);

        try {
            $locator = new FileLocator(array(__DIR__));
            $resolver = new LoaderResolver([
                new FrameworkYamlFileLoader($locator),
                new FrameworkPhpFileLoader($locator),
            ]);

            $delegate = new DelegatingLoader($resolver);
            $config = $delegate->load(__DIR__ . '/../../config/config.yml');
            if ($config === null) {
                $config = [];
            }

            $attributes = $matcher->match($request->getPathInfo()) + [ 'request' => $request ];
            $controller = $attributes['_controller'];
            $controller = [
                new $controller[0]($config),
                $controller[1]
            ];

            unset($attributes['_controller'], $attributes['_route']);
            $response = call_user_func_array($controller, $attributes);
        } catch (ResourceNotFoundException $e) {
            $response = new Response('Не найден!', Response::HTTP_NOT_FOUND);
        }

        return $response;
    }

    /**
     * @return RouteCollection
     */
    public function loadRoutes(): RouteCollection
    {
        $locator = new FileLocator(array(__DIR__));
        $loader = new YamlFileLoader($locator);
        $collection = $loader->load(__DIR__ . '/../../config/routing.yml');

        return $collection;
    }
}
