<?php
namespace Redmine\Config;


use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->group("redmine", function($routes) {
    $routes->get("users", '\Redmine\Controllers\UsersController::all');
});