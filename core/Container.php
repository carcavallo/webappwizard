<?php

namespace PR24\Dependencies;

use Bramus\Router\Router;
use PR24\Model\DoctorModel;
use PR24\Model\PatientModel;
use PR24\Controller\DoctorController;
use PR24\Controller\PatientController;
use PDO;

class Container {
    private $pdo;
    private $services = [];

    public function __construct() {
        $this->pdo = new PDO('mysql:host=localhost;dbname=webappwizard', 'root', 'kU7~51ft7`aQ');
        $this->initServices();
    }

    private function initServices() {
        $this->services['DoctorModel'] = new DoctorModel($this->pdo);
        $this->services['PatientModel'] = new PatientModel($this->pdo);

        $this->services['DoctorController'] = new DoctorController($this->services['DoctorModel']);
        $this->services['PatientController'] = new PatientController($this->services['PatientModel']);
    }

    public function setupRoutes(Router $router) {
        $doctorController = $this->get('DoctorController');
        $patientController = $this->get('PatientController');

        $router->mount("/auth", function () use ($router, $doctorController) {
            $router->post("/login", function() use ($doctorController) {
                $response = $doctorController->authenticate();
                Utils::sendJsonResponse($response);
            });

            $router->post("/register", function() use ($doctorController) {
                $response = $doctorController->register();
                Utils::sendJsonResponse($response);
            });

            $router->get("/activate/{userId}", function($userId) use ($doctorController) {
                $response = $doctorController->activateUser($userId);
                Utils::sendJsonResponse($response);
            });
        });

        $router->mount("/patient", function () use ($router, $patientController) {
            $router->post("/", function() use ($patientController) {
                $response = $patientController->createPatient();
                Utils::sendJsonResponse($response);
            });
        });
    }

    public function get($serviceName) {
        return $this->services[$serviceName] ?? null;
    }
}
