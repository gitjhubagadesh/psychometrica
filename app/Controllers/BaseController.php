<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Session\Session;

class BaseController extends Controller
{
    protected $session;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->session = session();
    }
}
