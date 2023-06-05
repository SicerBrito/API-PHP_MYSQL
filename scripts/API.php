<?php
// Configuración de la conexión a la base de datos
$host = 'localhost';
$db = 'API';  // TODO: Nombre de la base de datos
$user = 'campus';  // TODO: Usuario de mysql
$password = 'campus2023';   // TODO: Contraseña de mysql

// Establecer la conexión a la base de datos
$conexion = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $password);

// Establecer encabezados para permitir el acceso desde cualquier origen
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

// Verificar el método de solicitud HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Obtener el recurso solicitado y dividirlo en partes
$resource = rtrim($_GET['resource'], '/');
$parts = explode('/', $resource);

// Obtener los datos de la solicitud
$data = json_decode(file_get_contents("php://input"), true);

// Manejar las solicitudes
switch ($method) {
    case 'GET':
        // Leer datos
        if ($resource === 'users') {
            getUsers();
        } else {
            // Recurso no encontrado
            header("HTTP/1.1 404 Not Found");
            echo json_encode(['error' => 'Recurso no encontrado']);
        }
        break;

    case 'POST':
        // Crear nuevo registro
        if ($resource === 'users') {
            createUser($data);
        } else {
            // Recurso no encontrado
            header("HTTP/1.1 404 Not Found");
            echo json_encode(['error' => 'Recurso no encontrado']);
        }
        break;

    case 'PUT':
        // Actualizar registro
        if ($resource === 'users' && isset($parts[1])) {
            $id = $parts[1];
            updateUser($id, $data);
        } else {
            // Recurso no encontrado
            header("HTTP/1.1 404 Not Found");
            echo json_encode(['error' => 'Recurso no encontrado']);
        }
        break;

    case 'DELETE':
        // Eliminar registro
        if ($resource === 'users' && isset($parts[1])) {
            $id = $parts[1];
            deleteUser($id);
        } else {
            // Recurso no encontrado
            header("HTTP/1.1 404 Not Found");
            echo json_encode(['error' => 'Recurso no encontrado']);
        }
        break;

    default:
        // Método no permitido
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(['error' => 'Método no permitido']);
        break;
}

// Función para obtener todos los usuarios
function getUsers()
{
    global $conexion;

    $query = "SELECT * FROM users";
    $stmt = $conexion->prepare($query);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);
}

// Función para crear un nuevo usuario
function createUser($data)
{
    global $conexion;

    $name = $data['name'];
    $email = $data['email'];

    $query = "INSERT INTO users (name, email) VALUES (:name, :email)";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);

    if ($stmt->execute()) {
        // Registro creado exitosamente
        header("HTTP/1.1 201 Created");
        echo json_encode(['message' => 'Registro creado exitosamente']);
    } else {
        // Error al crear el registro
        header("HTTP/1.1 500 Internal Server Error");
        echo json_encode(['error' => 'Error al crear el registro']);
    }
}

// Función para actualizar un usuario existente
function updateUser($id, $data)
{
    global $conexion;

    $name = $data['name'];
    $email = $data['email'];

    $query = "UPDATE users SET name = :name, email = :email WHERE id = :id";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        // Registro actualizado exitosamente
        echo json_encode(['message' => 'Registro actualizado exitosamente']);
    } else {
        // Error al actualizar el registro
        header("HTTP/1.1 500 Internal Server Error");
        echo json_encode(['error' => 'Error al actualizar el registro']);
    }
}

// Función para eliminar un usuario
function deleteUser($id)
{
    global $conexion;

    $query = "DELETE FROM users WHERE id = :id";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        // Registro eliminado exitosamente
        echo json_encode(['message' => 'Registro eliminado exitosamente']);
    } else {
        // Error al eliminar el registro
        header("HTTP/1.1 500 Internal Server Error");
        echo json_encode(['error' => 'Error al eliminar el registro']);
    }
}
?>