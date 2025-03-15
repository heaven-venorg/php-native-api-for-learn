<?php
require_once '../database/config.php';
header("Content-Type: application/json");

$method = $_SERVER["REQUEST_METHOD"];
$url = $_SERVER['REQUEST_URI'];
$url = explode('/', $url);
$id = end($url);

$input = json_decode(file_get_contents('php://input'), true);

function validateInput($input, $fields)
{
    foreach ($fields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            return false;
        }
    }
    return true;
}

function sanitizeInput($con, $data)
{
    return mysqli_real_escape_string($con, trim($data));
}

switch ($method) {
    case 'GET':
        if (!empty($id) && is_numeric($id)) {
            $result = $con->query("SELECT * FROM users WHERE id=$id");
            if ($result) {
                $data = $result->fetch_assoc();
                echo json_encode($data);
            } else {
                echo json_encode(['message' => "Error fetching data"]);
            }
        } else {
            $result = $con->query("SELECT * FROM users");
            if ($result) {
                $users = [];
                while ($row = $result->fetch_assoc()) {
                    $users[] = $row;
                }
                echo json_encode($users);
            } else {
                echo json_encode(['message' => "Error fetching data"]);
            }
        }
        break;
    case "POST":
        if (validateInput($input, ['name', 'kelas', 'jurusan'])) {
            $name = sanitizeInput($con, $input['name']);
            $kelas = sanitizeInput($con, $input['kelas']);
            $jurusan = sanitizeInput($con, $input['jurusan']);
            $post = $con->query("INSERT INTO users (name, kelas, jurusan) VALUES ('$name', '$kelas', '$jurusan')");
            if ($post) {
                echo json_encode(["message" => "Created Successfully"]);
            } else {
                echo json_encode(['message' => "Error creating data"]);
            }
        } else {
            echo json_encode(['message' => "Invalid input"]);
        }
        break;
    case "PUT":
        if (!empty($id) && is_numeric($id) && validateInput($input, ['name', 'kelas', 'jurusan'])) {
            $name = sanitizeInput($con, $input['name']);
            $kelas = sanitizeInput($con, $input['kelas']);
            $jurusan = sanitizeInput($con, $input['jurusan']);
            $put = $con->query("UPDATE users SET name = '$name', kelas = '$kelas', jurusan = '$jurusan' WHERE id=$id");
            if ($put) {
                echo json_encode(['message' => "Successfully Update Data"]);
            } else {
                echo json_encode(['message' => "Error updating data"]);
            }
        } else {
            echo json_encode(['message' => "Invalid input or Id Not Found"]);
        }
        break;
    case "DELETE":
        if (!empty($id) && is_numeric($id)) {
            $delete = $con->query("DELETE FROM users WHERE id=$id");
            if ($delete) {
                echo json_encode(['message' => "Successfully Delete data"]);
            } else {
                echo json_encode(['message' => "Error deleting data"]);
            }
        } else {
            echo json_encode(['message' => "Invalid Id"]);
        }
        break;
    default:
        echo json_encode(['message' => "Invalid Request"]);
        break;
}

$con->close();