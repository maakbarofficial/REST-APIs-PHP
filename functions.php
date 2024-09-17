<?php
// functions.php
include_once 'config.php';

// CREATE with UNIQUE email validation
function createUser($name, $email)
{
    global $conn;
    try {
        $sql = "INSERT INTO users (name, email) VALUES (:name, :email)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    } catch (PDOException $e) {
        // Check if the error is due to a duplicate email
        if ($e->errorInfo[1] == 1062) { // MySQL error code 1062 indicates duplicate entry
            return "duplicate";
        } else {
            return false;
        }
    }
}

// READ
function getUsers()
{
    global $conn;
    $sql = "SELECT * FROM users";
    $stmt = $conn->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// READ by ID
function getUserById($id)
{
    global $conn;
    $sql = "SELECT * FROM users WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// UPDATE
function updateUser($id, $name, $email)
{
    global $conn;
    $sql = "UPDATE users SET name = :name, email = :email WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
}

// DELETE
function deleteUser($id)
{
    global $conn;
    $sql = "DELETE FROM users WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
}
