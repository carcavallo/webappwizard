<?php
namespace PR24\Controller;

use PR24\Model\DoctorModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class DoctorController {
    protected $doctorModel;

    public function __construct(DoctorModel $doctorModel) {
        $this->doctorModel = $doctorModel;
    }
    
    public function authenticate() {
        $credentials = json_decode(file_get_contents('php://input'), true);

        if ($this->doctorModel->validateCredentials($credentials['email'], $credentials['password'])) {
            $issuedAt = time();
            $expirationTime = $issuedAt + 3600;
            $payload = [
                'iat' => $issuedAt,
                'exp' => $expirationTime,
                'email' => $credentials['email']
            ];

            $jwt = JWT::encode($payload, 'BhPGQGyPuIkRz+hzMlfCsgaFNjKPSYgFjX73+LPf5k4=', 'HS256');

            return ['status' => 'success', 'token' => $jwt];
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
    
    private function sendPasswordEmail($email, $password) {
        $mail = new PHPMailer(true);
    
        try {
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = 'asmtp.mail.hostpoint.ch';
            $mail->SMTPAuth = true;
            $mail->Username = 'sendmail@pr24.dev';
            $mail->Password = 'SN262!9-1*G8Pj8uF2pP';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
    
            $mail->setFrom('sendmail@pr24.dev', 'Mailer');
            $mail->addAddress($email);
    
            $mail->isHTML(true);
            $mail->Subject = 'Your Account Activation';
            $mail->Body    = 'Your account has been activated.<br>Your new password: ' . $password;
            $mail->AltBody = 'Your account has been activated. Your new password: ' . $password;
    
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
