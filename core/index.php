<?php

require_once 'vendor/autoload.php';

use Bramus\Router\Router;
use PR24\Controller\UserController;
use PR24\Controller\PatientController;
use PR24\Controller\ErrorController;

$router = new Router();

$router->setNamespace('\PR24\Controller');

$router->post('/api/register', 'UserController@register');
$router->get('/api/activate/{userId}', 'UserController@activateUser');
$router->post('/api/patient/create', 'PatientController@createPatient');

$router->set404('ErrorController@handleNotFound');

try {
    $router->run();
} catch (Exception $e) {
    $errorController = new ErrorController();
    $errorController->handleServerError($e);
}
