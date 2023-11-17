<?php
namespace PR24\Controller;

use PR24\Model\DoctorModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Firebase\JWT\JWT;

class DoctorController {
    protected $doctorModel;

    public function __construct(DoctorModel $doctorModel) {
        $this->doctorModel = $doctorModel;
    }
    
    public function authenticate() {
        $credentials = json_decode(file_get_contents('php://input'), true);
    
        if ($this->doctorModel->validateCredentials($credentials['email'], $credentials['password'])) {
            $token = [
                "iss" => $_ENV['BASEDOMAIN'],
                "iat" => time(),
                "exp" => time() + 3600,
                "data" => [
                    "email" => $credentials['email']
                ]
            ];
            
            $jwt = JWT::encode($token, $_ENV['SECRET_KEY'], 'HS256');
    
            return ['status' => 'success', 'message' => 'Authorized', 'token' => $jwt];
        } else {
            return ['status' => 'error', 'message' => 'Invalid credentials'];
        }
    }

    public function register() {
        $requestBody = json_decode(file_get_contents('php://input'), true);

        $registrationData = [
            'anrede' => $requestBody['anrede'] ?? null,
            'titel' => $requestBody['titel'] ?? null,
            'vorname' => $requestBody['vorname'] ?? null,
            'nachname' => $requestBody['nachname'] ?? null,
            'email' => $requestBody['email'] ?? null,
            'arbeitsstelle_name' => $requestBody['arbeitsstelle_name'] ?? null,
            'arbeitsstelle_adresse' => $requestBody['arbeitsstelle_adresse'] ?? null,
            'arbeitsstelle_stadt' => $requestBody['arbeitsstelle_stadt'] ?? null,
            'arbeitsstelle_plz' => $requestBody['arbeitsstelle_plz'] ?? null,
            'arbeitsstelle_land' => $requestBody['arbeitsstelle_land'] ?? null,
            'taetigkeitsbereich' => $requestBody['taetigkeitsbereich'] ?? null,
            'taetigkeitsbereich_sonstiges' => $requestBody['taetigkeitsbereich_sonstiges'] ?? null
        ];
        
        if (!$this->validateRegistrationData($registrationData)) {
            return ['status' => 'error', 'message' => 'Invalid registration data'];
        }

        $doctorId = $this->doctorModel->createDoctor($registrationData);
        if ($doctorId) {
            $this->sendRegistrationConfirmationEmail($registrationData, $doctorId);
            return ['status' => 'success', 'message' => 'Registration successful', 'doctorId' => $doctorId];
        } else {
            return ['status' => 'error', 'message' => 'Registration failed'];
        }
    }

    public function activateUser($userId) {
        $isActivated = $this->doctorModel->isDoctorActivated($userId);
        if ($isActivated === true) {
            return ['status' => 'error', 'message' => 'Doctor already activated'];
        } elseif ($isActivated === null) {
            return ['status' => 'error', 'message' => 'Doctor not found'];
        }
    
        $newPassword = $this->doctorModel->activateDoctorAndSetPassword($userId);
        if ($newPassword) {
            $doctorEmail = $this->doctorModel->getDoctorEmailById($userId);
            if ($this->sendPasswordEmail($doctorEmail, $newPassword)) {
                return ['status' => 'success', 'message' => 'Doctor activated and password sent'];
            } else {
                return ['status' => 'error', 'message' => 'Doctor activated but failed to send password email'];
            }
        } else {
            return ['status' => 'error', 'message' => 'Activation failed'];
        }
    }
    
    private function sendRegistrationConfirmationEmail($registrationData, $userId) {
        $mail = new PHPMailer(true);

        try {
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = $_ENV['EMAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['EMAIL_USERNAME'];
            $mail->Password = $_ENV['EMAIL_PASSWORD'];;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom($_ENV['EMAIL_FROM'], 'CK-Care Registration');
            $mail->addAddress($_ENV['EMAIL_TO']);
    
            $mail->isHTML(true);
            $mail->Subject = 'Flip-Flop-Score Anmeldung';
    
            $body = "Es gibt eine neue Anmeldung von " . htmlspecialchars($registrationData['anrede']) . " " . htmlspecialchars($registrationData['titel']) . " " . htmlspecialchars($registrationData['vorname']) . " " . htmlspecialchars($registrationData['nachname']) . ".<br/><br>Die folgenden Daten wurden eingetragen:<br>";
            $keyMap = [
                'email' => 'E-Mail',
                'arbeitsstelle_name' => 'Arbeitsstelle Name',
                'arbeitsstelle_adresse' => 'Arbeitsstelle Adresse',
                'arbeitsstelle_stadt' => 'Arbeitsstelle Stadt',
                'arbeitsstelle_plz' => 'Arbeitsstelle PLZ',
                'arbeitsstelle_land' => 'Arbeitsstelle Land',
                'taetigkeitsbereich' => 'Tätigkeitsbereich',
                'taetigkeitsbereich_sonstiges' => 'Tätigkeitsbereich Sonstiges'
            ];
    
            $fieldsToSkip = ['anrede', 'titel', 'vorname', 'nachname'];

            foreach ($registrationData as $key => $value) {
                if (!in_array($key, $fieldsToSkip)) {
                    $displayName = $keyMap[$key] ?? ucfirst($key);
                    $body .= htmlspecialchars($displayName . ": " . $value) . "<br>";
                }
            }
    
            $activateUrl = $_ENV['BASEDOMAIN'] . "/api/auth/user/activate/" . $userId;
    
            $body .= "<br><a href='" . $activateUrl . "'>Benutzer freischalten und Zugang zusenden</a>";
    
            $mail->Body = $body;
            $mail->AltBody = strip_tags(str_replace("<br>", "\n", $body));
    
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
            return false;
        }
    }

    private function sendPasswordEmail($email, $password) {
        $mail = new PHPMailer(true);
    
        try {
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = $_ENV['EMAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['EMAIL_USERNAME'];
            $mail->Password = $_ENV['EMAIL_PASSWORD'];
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
    
            $mail->setFrom($_ENV['EMAIL_FROM'], 'CK-Care Registration');
            $mail->addAddress($email);
    
            $mail->isHTML(true);
            $mail->Subject = 'Ihre Flip-Flop-Score Anmeldung';
            $mail->Body    = 'Ihr Konto wurde aktiviert.<br>Ihr Benutzername: ' . htmlspecialchars($email) . '<br>Ihr Passwort: ' . htmlspecialchars($password);
            $mail->AltBody = 'Ihr Konto wurde aktiviert. Ihr Benutzername: ' . htmlspecialchars($email) . '. Ihr Passwort: ' . htmlspecialchars($password);
    
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
            return false;
        }
    }
    
    private function validateRegistrationData($data) {
        return filter_var($data['email'], FILTER_VALIDATE_EMAIL) && !empty($data['vorname']) && !empty($data['nachname']);
    }
}
