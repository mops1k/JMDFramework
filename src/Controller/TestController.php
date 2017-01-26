<?php
namespace Framework\App\Controller;

use Framework\Controller;

class TestController extends Controller
{
    public function index()
    {
        return $this->render('index.html.twig', [ 'name' => '' ]);
    }
}
