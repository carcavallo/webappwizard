<?php
namespace PR24;

use Bramus\Router\Router;
use PR24\Controller\DoctorController;
use PR24\Controller\PatientController;
//use PR24\Controller\ScoreController;
use PR24\Controller\ErrorController;
use PR24\Model\DoctorModel;
use PR24\Model\PatientModel;
//use PR24\Model\ScoreModel;
use PDO;

class ApiRouter {
    private $router;
    private $pdo;

    public function __construct() {
        $this->router = new Router();
        $this->pdo = new PDO('mysql:host=localhost;dbname=webappwizard', 'root', 'kU7~51ft7`aQ');
        $this->setupRoutes();
    }

    private function authenticateJWT($token) {
        try {
            $decoded = JWT::decode($token, new Key('BhPGQGyPuIkRz+hzMlfCsgaFNjKPSYgFjX73+LPf5k4=', 'HS256'));
            return $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function setupRoutes() {
        $doctorModel = new DoctorModel($this->pdo);
        $patientModel = new PatientModel($this->pdo);
        //$scoreModel = new ScoreModel($this->pdo);
        $errorController = new ErrorController();

        $doctorController = new DoctorController($doctorModel);
        $patientController = new PatientController($patientModel);
        //$scoreController = new ScoreController($scoreModel);

        $this->router->setNamespace('\PR24\Controller');

        $this->router->post('/api/login', function() use ($doctorController) {
            $response = $doctorController->authenticate();
            echo json_encode($response);
        });

        $this->router->post('/api/register', function() use ($doctorController) {
            $response = $doctorController->register();
            echo json_encode($response);
        });
        
        $this->router->get('/api/activate/{userId}', function($userId) use ($doctorController) {
            $response = $doctorController->activateUser($userId);
            echo json_encode($response);
        });

        $this->router->post('/api/patient', function() use ($patientController) {
            $headers = getallheaders();
            $jwt = $headers['Authorization'] ?? '';
        
            if (!$this->authenticateJWT($jwt)) {
                header('HTTP/1.0 401 Unauthorized');
                echo json_encode(['message' => 'Unauthorized']);
                exit();
            }
        
            $response = $patientController->createPatient();
            echo json_encode($response);
        });
        

        /*
        $this->router->post('/api/patient/create', function() use ($patientController) {
            $patientController->createPatient($_REQUEST);
        });
        */
            
        // Additional patient CRUD routes

        // Score related routes
        /*
        $this->router->post('/api/score/calculate', function() use ($scoreController) {
            $scoreController->calculateScore($_REQUEST);
        });
        */
        // Additional score CRUD routes

        // Error handling routes
        //$this->router->set404($errorController->handleNotFound());
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
