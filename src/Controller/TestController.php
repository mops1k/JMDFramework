<?php
namespace Framework\App\Controller;

use Framework\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TestController
 * @package Framework\App\Controller
 */
class TestController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }
}
