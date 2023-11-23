<?php

namespace PR24\Controller;

use PR24\Model\DoctorModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Firebase\JWT\JWT;

/**
 * DoctorController handles doctor-related actions such as authentication, registration, and activation.
 */
class DoctorController {
    protected $doctorModel;

    /**
     * Constructor for the DoctorController class.
     *
     * @param DoctorModel $doctorModel The model handling doctor data.
     */
    public function __construct(DoctorModel $doctorModel) {
        $this->doctorModel = $doctorModel;
    }
    
    /**
     * Authenticates a doctor and generates a JWT token.
     *
     * @return array Result of authentication with JWT token if successful.
     */
    public function authenticate() {
        $credentials = json_decode(file_get_contents('php://input'), true);
    
        if ($this->doctorModel->validateCredentials($credentials['email'], $credentials['password'])) {
            $userId = $this->doctorModel->getDoctorIdByEmail($credentials['email']);
            $token = [
                "iss" => $_ENV['BASEDOMAIN'],
                "iat" => time(),
                "exp" => time() + 7200,
                "data" => [
                    "email" => $credentials['email'],
                    "userId" => $userId
                ]
            ];
            
            $jwt = JWT::encode($token, $_ENV['SECRET_KEY'], 'HS256');
    
            return [
                'status' => 'success', 
                'message' => 'Authorized',
                'userId' => $userId, 
                'token' => $jwt
            ];
        } else {
            return ['status' => 'error', 'message' => 'Invalid credentials'];
        }
    }

    /**
     * Registers a new doctor in the system.
     *
     * @return array The result of the registration process.
     */
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

    /**
     * Activates a doctor's account and sends a new password.
     *
     * @param int $userId The ID of the doctor to activate.
     * @return array The result of the activation process.
     */
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
    
    /**
     * Sends an email for registration confirmation.
     *
     * @param array $registrationData The registration data of the doctor.
     * @param int $userId The ID of the newly registered doctor.
     * @return bool True if the email was sent successfully, false otherwise.
     */
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
                if (!in_array($key, $fieldsToSkip) && !($key === 'taetigkeitsbereich_sonstiges' && empty($value))) {
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

    /**
     * Sends an email to a doctor with their new password.
     *
     * @param string $email The email address of the doctor.
     * @param string $password The new password for the doctor.
     * @return bool True if the email was sent successfully, false otherwise.
     */    
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
            $mail->CharSet = 'UTF-8';
    
            $mail->setFrom($_ENV['EMAIL_FROM'], 'CK-Care Registration');
            $mail->addAddress($email);
    
            $mail->isHTML(true);
            $mail->Subject = 'Ihre Flip-Flop-Score Anmeldung';
            
            $mail->Body = 'Ihr Konto wurde aktiviert.<br>'
                . 'Ihr Benutzername: ' . htmlspecialchars($email) . '<br>'
                . 'Ihr Passwort: ' . htmlspecialchars($password) . '<br><br>'
                . 'Wir freuen uns über Ihr Interesse zur Nutzung des Flip-Flop-Scores. Wir empfehlen die Anwendung des Flip-Flop-Scores bei Diagnosestellung von atopischer Dermatitis, Psoriasis und klinischen Überlappungsformen von atopischer Dermatitis und Psoriasis sowie die erneute Anwendung im weiteren Therapieverlauf der Patienten (Anwendung bis zu vier Mal pro Patienten möglich).'
                . '<br><br>'
                . 'Der Flip-Flop-Score setzt sich aus insgesamt 20 Anamnese- und Untersuchungskriterien zusammen. Wir empfehlen zur Nutzung folgendes Vorgehen:'
                . '<br><br>'
                . 'Füllen Sie in der Flip-Flop-App die Patientenangaben aus, um ein neues Patienten-Dossier zu erstellen.'
                . '<br><br>'
                . 'Die Flip-Flop-App generiert automatisch eine eindeutige Identifikationsnummer, welche Sie künftig zur Identifizierung des Patienten in der Flip-Flop-App verwenden müssen.'
                . '<br><br>'
                . 'Damit trotz der Anonymisierung der Patientendaten eine Zuordnung geschehen kann, besteht die Identifikationsnummer aus den folgenden Informationen: Geschlecht, Geburtsdatum, zufällige Nummer'
                . '<br><br>'
                . 'Bearbeiten Sie den Score, indem Sie hinter jedem Kriterium anklicken, ob das Kriterium vorliegt ("ja") oder nicht vorliegt ("nein").'
                . '<br><br>'
                . 'Anschließend wird der Score berechnet und Sie erhalten unmittelbar im Anschluss das Ergebnis, welches Ihnen bei der Einordnung Ihrer Patienten in die Krankheitsgruppen atopische Dermatitis, Psoriasis oder Flip-Flop helfen kann.'
                . '<br><br>'
                . 'Bestimmen Sie im Therapieverlauf erneut den Flip-Flop-Score bei weiteren Verlaufsterminen Ihrer Patienten, um Ihre Patienten im weiteren Therapieverlauf verfolgen zu können. Tragen Sie auch hier Datum und Scoreergebnis in Ihre Patientenliste ein. Sie können den Flip-Flop-Score pro Patienten bis zu viermal bestimmen. Wir empfehlen die Score-Bestimmung vor allem vor und nach Einleitung einer immunmodulierenden Systemtherapie sowie im Falle eines eintretenden Flip-Flops.'
                . '<br><br>'
                . 'Nach jeder Eingabe erhalten Sie das Ergebnis der Scoreberechnung per Mail zugeschickt, damit Sie das Ergebnis zusätzlich in Ihrer Patientenakte ablegen können, falls gewünscht.'
                . '<br><br>'
                . 'Für Rückfragen stehen wir gerne unter info@ck-care.ch zur Verfügung.';
            
            $mail->AltBody = 'Ihr Konto wurde aktiviert. Ihr Benutzername: ' . htmlspecialchars($email) . '. Ihr Passwort: ' . htmlspecialchars($password);
    
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
            return false;
        }
    }
    
    /**
     * Validates the registration data of a doctor.
     *
     * @param array $data Registration data to be validated.
     * @return bool True if the data is valid, false otherwise.
     */
    private function validateRegistrationData($data) {
        return filter_var($data['email'], FILTER_VALIDATE_EMAIL) && !empty($data['vorname']) && !empty($data['nachname']);
    }
}
