<?php
// ====================================================================
// CONFIGURACIÓN PARA DEPURACIÓN
// Mantiene los errores visibles, lo cual es útil durante el desarrollo.
// ====================================================================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ====================================================================
// Establecer la cabecera para indicar que la respuesta será JSON
// El navegador esperará una respuesta en formato JSON.
// ====================================================================
header('Content-Type: application/json');

// ====================================================================
// Funciones de Ayuda para Respuestas JSON
// Estas funciones formatean las respuestas para el JavaScript del cliente.
// ====================================================================
function sendErrorResponse($message, $detail = null) {
    $response = ['success' => false, 'error' => $message];
    // Solo incluye detalles técnicos si el modo de depuración de errores está activado.
    if (ini_get('display_errors') && $detail !== null) {
        $response['detail'] = $detail;
    }
    echo json_encode($response);
    exit(); // Detiene la ejecución del script después de enviar la respuesta.
}

function sendSuccessResponse($message) {
    echo json_encode(['success' => true, 'message' => $message]);
    exit(); // Detiene la ejecución del script después de enviar la respuesta.
}

// =========================================================
// INCLUSIÓN DE ARCHIVOS DE PHPMailer
// Estas rutas son vitales para que PHP encuentre las clases de PHPMailer.
// Asumimos que la carpeta 'PHPMailer_src' está directamente dentro de 'pcc/'.
// Es decir: C:\xampp_copia\htdocs\pcc\PHPMailer_src\PHPMailer.php
// =========================================================
require __DIR__ . '/PHPMailer_src/PHPMailer.php';
require __DIR__ . '/PHPMailer_src/SMTP.php';
require __DIR__ . '/PHPMailer_src/Exception.php';

// Importar las clases de PHPMailer en el espacio de nombres global
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// ====================================================================
// 1. CONFIGURACIÓN DE LA BASE DE DATOS MYSQL
// Asumimos que la base de datos 'formulario_db' y la tabla 'mensajes' existen
// y que MySQL en XAMPP está funcionando con el usuario 'root' sin contraseña.
// ====================================================================
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "formulario_db";

// ====================================================================
// 2. RECOGER Y SANEAR LOS DATOS DEL FORMULARIO ENVIADOS POR POST
// Usamos htmlspecialchars y trim para proteger contra inyección de HTML y limpiar espacios.
// ====================================================================
$nombre  = htmlspecialchars(trim($_POST['nombre'] ?? ''));
$email   = htmlspecialchars(trim($_POST['email'] ?? ''));
$asunto  = htmlspecialchars(trim($_POST['asunto'] ?? ''));
$mensaje = htmlspecialchars(trim($_POST['mensaje'] ?? ''));

// ====================================================================
// 3. VALIDACIÓN BÁSICA DE DATOS EN EL SERVIDOR
// Siempre es bueno validar los datos en el servidor, no solo en el cliente.
// ====================================================================
if (empty($nombre) || empty($email) || empty($asunto) || empty($mensaje)) {
    sendErrorResponse("Todos los campos del formulario son obligatorios. Por favor, rellena el formulario completo.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendErrorResponse("El formato de correo electrónico proporcionado no es válido. Por favor, verifica tu email.");
}

// ====================================================================
// 4. CONECTAR A LA BASE DE DATOS MYSQL
// Intentamos establecer la conexión. Si falla, enviamos un error JSON.
// =================================================================
$conn = new mysqli("localhost", "lsi", "UnaContraFuerte123!", "base_de_datos");

if ($conn->connect_error) {
    sendErrorResponse("Error de conexión a la base de datos.", $conn->connect_error);
}

$conn->set_charset("utf8mb4"); // Asegura la correcta codificación de caracteres.

// ====================================================================
// 5. PREPARAR Y EJECUTAR LA CONSULTA DE INSERCIÓN (¡SEGURIDAD: Prepared Statements!)
// Usar Prepared Statements es crucial para prevenir ataques de inyección SQL.
// ====================================================================
$sql = "INSERT INTO mensajes (nombre, email, asunto, mensaje) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    sendErrorResponse("Error al preparar la consulta SQL.", $conn->error);
}

// "ssss" indica que todos los parámetros son strings (cadenas de texto).
$stmt->bind_param("ssss", $nombre, $email, $asunto, $mensaje);

// Intentamos ejecutar la inserción en la base de datos.
if ($stmt->execute()) {
    // Si los datos se guardaron correctamente en la DB, proceder a enviar el correo.

    // =========================================================
    // ENVÍO DE CORREO ELECTRÓNICO CON PHPMailer
    // =========================================================
    $mail = new PHPMailer(true); // El 'true' activa las excepciones para un mejor manejo de errores.

    try {
        // Configuración del servidor SMTP de Gmail
//        $mail->SMTPDebug = SMTP::DEBUG_SERVER;     // <--- MUY ÚTIL para depuración: muestra la comunicación SMTP//
	$mail->SMTPDebug = 0;
        $mail->isSMTP();                           // Le decimos a PHPMailer que use SMTP.
        $mail->Host = 'smtp.gmail.com';            // Servidor SMTP de Gmail.
        $mail->SMTPAuth = true;                    // Habilitar autenticación SMTP.
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // CAMBIAR A STARTTLS
        $mail->Port = 587;                               // CAMBIAR A PUERTO 587
        // ====================================================================
        // ¡¡¡ ATENCIÓN AQUÍ: CREDENCIALES DE GMAIL - USA TU CONTRASEÑA DE APLICACIÓN !!!
        // ====================================================================
        $mail->Username = 'experimentoiautn2@gmail.com'; // <--- Tu dirección de correo de Gmail.
        // Reemplaza 'TU_CONTRASEÑA_DE_APLICACION_DE_16_CARACTERES'
        // con la contraseña de 16 caracteres que GENERASTE en Google.
        // DEBE SER COPIADA EXACTAMENTE sin espacios ni caracteres extra.
        $mail->Password = 'yuvdpdlrliavqgph';

        // Remitente del correo (DEBE ser el mismo que el Username para Gmail)
        $mail->setFrom('experimentoiautn2@gmail.com', 'Formulario de Contacto Web');

        // Destinatario del correo (A quién se le enviará el mensaje).
        $mail->addAddress('experimentoiautn2@gmail.com', 'Experimento UTN'); // El destinatario final del correo.

        // Para permitir que el destinatario responda al correo del usuario.
        $mail->addReplyTo($email, $nombre);

        // Contenido del correo
        $mail->isHTML(false); // Enviamos el correo como texto plano (true para HTML).
        $mail->Subject = 'Nuevo mensaje del formulario web: ' . $asunto; // Asunto del correo.

        // Cuerpo del correo con los datos del formulario.
        $mail->Body    = "Se ha recibido un nuevo mensaje desde el formulario de contacto:\n\n"
                       . "Nombre: " . $nombre . "\n"
                       . "Email: " . $email . "\n"
                       . "Asunto: " . $asunto . "\n"
                       . "Mensaje: \n" . $mensaje . "\n\n"
                       . "--------------------------------------------------\n"
                       . "Este correo fue enviado automáticamente desde tu sitio web.";

        $mail->send(); // Intenta enviar el correo.
        // Si el correo se envió con éxito, envía la respuesta de éxito al cliente.
        sendSuccessResponse("Mensaje guardado en la base de datos y correo enviado con éxito.");

    } catch (Exception $e) {
        // Si hubo un error al enviar el correo (pero los datos ya se guardaron en la DB).
        sendErrorResponse(
            "El mensaje se guardó en la base de datos, pero el correo no pudo ser enviado.",
            "Error del Mailer: {$mail->ErrorInfo}" // Muestra el mensaje de error detallado de PHPMailer.
        );
    }

} else {
    // Si la ejecución de la inserción en la DB falló.
    sendErrorResponse("Error al guardar el mensaje en la base de datos.", $stmt->error);
}

// ====================================================================
// 6. CERRAR LA CONEXIÓN Y EL STATEMENT
// Liberamos los recursos de la base de datos.
// ====================================================================
$stmt->close();
$conn->close();

?>
