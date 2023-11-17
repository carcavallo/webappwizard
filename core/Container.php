<?php

namespace PR24\Dependencies;

use Bramus\Router\Router;
use PR24\Model\DoctorModel;
use PR24\Model\PatientModel;
use PR24\Model\ScoreModel;
use PR24\Model\AdminModel;
use PR24\Controller\DoctorController;
use PR24\Controller\PatientController;
use PR24\Controller\ScoreController;
use PR24\Controller\AdminController;

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
        $this->services['ScoreModel'] = new ScoreModel($this->pdo);
        $this->services['AdminModel'] = new AdminModel($this->pdo);

        $this->services['DoctorController'] = new DoctorController($this->services['DoctorModel']);
        $this->services['PatientController'] = new PatientController($this->services['PatientModel']);
        $this->services['ScoreController'] = new ScoreController($this->services['ScoreModel']);
        $this->services['AdminController'] = new AdminController($this->services['AdminModel']);
    }

    public function setupRoutes(Router $router) {
        $doctorController = $this->get('DoctorController');
        $patientController = $this->get('PatientController');
        $scoreController = $this->get('ScoreController');
        $adminController = $this->get('AdminController');

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

        $router->post("/patient", function() use ($patientController) {
            $response = $patientController->createPatient();
            Utils::sendJsonResponse($response);
        });

        $router->post("/score/calculate", function() use ($scoreController) {
            $response = $scoreController->calculateAndSaveScore();
            Utils::sendJsonResponse($response);
        });
        
        $router->post("/admin/login", function() use ($adminController) {
            $response = $adminController->authenticate();
            Utils::sendJsonResponse($response);
        });

        $router->get("/admin/export-patients", function() use ($adminController) {
            $adminController->exportPatientData();
        });
    }

    public function get($serviceName) {
        return $this->services[$serviceName] ?? null;
    }
}