<?php
// index.php
header("Content-Type: application/json");
include_once 'functions.php';

// Helper function to send HTTP responses
function sendResponse($status_code, $message)
{
    http_response_code($status_code);
    echo json_encode(["message" => $message]);
}

// Parse the URL for routing
$request = $_SERVER['REQUEST_URI'];
$request = str_replace("/api/v1/", "", $request);
$request = rtrim($request, "/");
$method = $_SERVER['REQUEST_METHOD'];

// Routing
switch ($method) {
    case 'GET':
        if ($request == "users") {
            $users = getUsers();
            echo json_encode($users);
        } elseif (preg_match('/users\/(\d+)/', $request, $matches)) {
            $user = getUserById($matches[1]);
            if ($user) {
                echo json_encode($user);
            } else {
                sendResponse(404, "User with ID {$matches[1]} not found.");
            }
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->name) && isset($data->email)) {
            $result = createUser($data->name, $data->email);
            if ($result === "duplicate") {
                sendResponse(409, "Email already exists.");
            } elseif ($result) {
                sendResponse(201, "User created successfully.");
            } else {
                sendResponse(500, "User creation failed.");
            }
        } else {
            sendResponse(400, "Invalid input. Name and email are required.");
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (preg_match('/users\/(\d+)/', $request, $matches)) {
            $userId = $matches[1];
            if (isset($data->name) && isset($data->email)) {
                $user = getUserById($userId);
                if ($user) {
                    if (updateUser($userId, $data->name, $data->email)) {
                        sendResponse(200, "User updated successfully.");
                    } else {
                        sendResponse(500, "User update failed.");
                    }
                } else {
                    sendResponse(404, "User with ID {$userId} not found.");
                }
            } else {
                sendResponse(400, "Invalid input. Name and email are required.");
            }
        }
        break;

    case 'DELETE':
        if (preg_match('/users\/(\d+)/', $request, $matches)) {
            $userId = $matches[1];
            $user = getUserById($userId);
            if ($user) {
                if (deleteUser($userId)) {
                    sendResponse(200, "User deleted successfully.");
                } else {
                    sendResponse(500, "User deletion failed.");
                }
            } else {
                sendResponse(404, "User with ID {$userId} not found.");
            }
        }
        break;

    default:
        sendResponse(405, "Request method not supported.");
        break;
}
