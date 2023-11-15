<?php
namespace PR24;

use Bramus\Router\Router;
use PR24\Controller\DoctorController;
use PR24\Controller\PatientController;
use PR24\Controller\ScoreController;
use PR24\Controller\ErrorController;
use PR24\Model\DoctorModel;
use PR24\Model\PatientModel;
use PR24\Model\ScoreModel;
use PDO;

class ApiRouter {
    private $router;
    private $pdo;

    public function __construct() {
        $this->router = new Router();
        $this->pdo = new PDO('mysql:host=localhost;dbname=flip_flop_score_db', 'root', 'toor');
        $this->setupRoutes();
    }

    private function setupRoutes() {
        $doctorModel = new DoctorModel($this->pdo);
        $patientModel = new PatientModel($this->pdo);
        $scoreModel = new ScoreModel($this->pdo);
        $errorController = new ErrorController();

        $DoctorController = new DoctorController($doctorModel);
        $patientController = new PatientController($patientModel);
        $scoreController = new ScoreController($scoreModel);

        $this->router->setNamespace('\PR24\Controller');

        $this->router->post('/api/register', function() use ($DoctorController) {
            $DoctorController->register($_REQUEST);
        });
        $this->router->get('/api/activate/{userId}', function($userId) use ($DoctorController) {
            $DoctorController->activateUser($userId);
        });

        $this->router->post('/api/patient/create', function() use ($patientController) {
            $patientController->createPatient($_REQUEST);
        });
        // Additional patient CRUD routes

        // Score related routes
        $this->router->post('/api/score/calculate', function() use ($scoreController) {
            $scoreController->calculateScore($_REQUEST);
        });
        // Additional score CRUD routes

        // Error handling routes
        $this->router->set404([$errorController, 'handleNotFound']);
        $this->router->setErrorHander([$errorController, 'handleServerError']);
    }

    public function run() {
        try {
            $this->router->run();
        } catch (\Exception $e) {
            $errorController = new ErrorController();
            $errorController->handleServerError($e);
        }
    }
}
