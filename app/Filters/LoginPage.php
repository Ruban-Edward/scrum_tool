<?php
namespace App\Filters;
use CodeIgniter\Config\Services;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class LoginPage implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = Services::auth();
        if ($session->isLoggedIn()){
            return redirect('dashboard/dashboardView');
        }   
    }

    public function after(RequestInterface $request, ResponseInterface $response, $aruments = null)
    {
        return $response;
    }
}