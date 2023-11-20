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
        $this->pdo = new PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
        $this->initServices();
    }

    private function initServices() {
        $this->services['DoctorModel'] = new DoctorModel($this->pdo);
        $this->services['PatientModel'] = new PatientModel($this->pdo);
        $this->services['ScoreModel'] = new ScoreModel($this->pdo, $this->services['DoctorModel']);
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

        $router->before('GET|POST|PUT|DELETE', '/(?!auth/user/register|auth/user/activate|auth/user/login|auth/admin/login).*', function() {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        
            if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];
        
                $validationResult = JWTMiddleware::validateToken($token);
        
                if ($validationResult['status'] !== 'success') {
                    Utils::sendJsonResponse(['status' => 'error', 'message' => $validationResult['message']]);
                    exit();
                }
            } else {
                Utils::sendJsonResponse(['status' => 'error', 'message' => 'Unauthorized']);
                exit();
            }
        });        

        $router->mount("/auth", function () use ($router, $doctorController) {
            $router->post("/user/login", function() use ($doctorController) {
                $response = $doctorController->authenticate();
                Utils::sendJsonResponse($response);
            });

            $router->post("/user/register", function() use ($doctorController) {
                $response = $doctorController->register();
                Utils::sendJsonResponse($response);
            });

            $router->get("/user/activate/{userId}", function($userId) use ($doctorController) {
                $response = $doctorController->activateUser($userId);
                Utils::sendJsonResponse($response);
            });
        });

        $router->post("/patient", function() use ($patientController) {
            $response = $patientController->createPatient();
            Utils::sendJsonResponse($response);
        });

        $router->get("/patient/{patientId}", function($patientId) use ($patientController) {
            $response = $patientController->readPatient($patientId);
            Utils::sendJsonResponse($response);
        });
        
        $router->put("/patient/{patientId}", function($patientId) use ($patientController) {
            $response = $patientController->updatePatient($patientId);
            Utils::sendJsonResponse($response);
        });
        
        $router->delete("/patient/{patientId}", function($patientId) use ($patientController) {
            $response = $patientController->deletePatient($patientId);
            Utils::sendJsonResponse($response);
        });

        $router->post("/score", function() use ($scoreController) {
            $response = $scoreController->createScore();
            Utils::sendJsonResponse($response);
        });

        $router->get('/scores/{patientId}', function($patientId) use ($scoreController) {
            $response = $scoreController->getScores($patientId);
            Utils::sendJsonResponse($response);
        });
            
        $router->put('/score/{scoreId}', function($scoreId) use ($scoreController) {
            $data = json_decode(file_get_contents('php://input'), true);
        
            $criteria = isset($data['criteria']) ? $data['criteria'] : [];
            $totalScore = isset($data['totalScore']) ? $data['totalScore'] : null;
        
            $response = $scoreController->updateScore($scoreId, $criteria, $totalScore);
            Utils::sendJsonResponse($response);
        });
    
        $router->delete('/score/{scoreId}', function($scoreId) use ($scoreController) {
            $response = $scoreController->deleteScore($scoreId);
            Utils::sendJsonResponse($response);
        });
        
        $router->post("/auth/admin/login", function() use ($adminController) {
            $response = $adminController->authenticate();
            Utils::sendJsonResponse($response);
        });

        $router->get("/admin/export", function() use ($adminController) {
            $adminController->exportPatientData();
        });
    }

    public function get($serviceName) {
        return $this->services[$serviceName] ?? null;
    }
}
