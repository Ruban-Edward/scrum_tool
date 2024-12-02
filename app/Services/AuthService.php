<?php
namespace App\Services;
use CodeIgniter\Config\Services;

class AuthService extends Services
{
 
    public function isLoggedIn()    
    {
        return session('is_user_logged') ?? false;
    }
}