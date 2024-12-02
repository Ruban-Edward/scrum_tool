<?php
namespace App\Filters;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

/** 
 * @author yuvansri <yuvansri@infinitisoftware.net>
 * 
*/
class Auth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = Services::auth();
        if (! $session->isLoggedIn()){
            if(current_url(true) != base_url()){
                session()->set(['url' => str_replace(base_url(),"",current_url(true))]);
            }
            return redirect('/');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $aruments = null)
    {
        return $response;
    }
}