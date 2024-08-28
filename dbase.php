<?php

/**
** Dbase configuration and functions for Assemble
** © Wardiyono, 2024 - wynerst@gmail.com
**/

// be sure that this file not accessed directly
if (!defined('INDEX_AUTH')) {
    die("can not access this file directly");
} else if (INDEX_AUTH != 1) {
    die("can not access this file directly");
}

// Database connection (replace with your credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Art_Assemble";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and execute SQL statements with parameterized queries
function create($data) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO tbl_search (title, creator, description, subject, publisher, date, type, format, identifier, source, language, relation) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssss", $data['title'], $data['creator'], $data['description'], $data['subject'], $data['publisher'], $data['date'], $data['type'], $data['format'], $data['identifier'], $data['source'], $data['language'], $data['relation']);
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

function read() {
    global $conn;
    $result = $conn->query("SELECT * FROM tbl_search");
    if ($result->num_rows > 0) {
        $rows = array();
        while($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    } else {
        return false;
    }
}

function update($id, $data) {
    global $conn;
    $stmt = $conn->prepare("UPDATE tbl_search SET field1 = ?, field2 = ? WHERE id = ?");
    $stmt->bind_param("ssi", $data['field1'], $data['field2'], $id);
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

function delete($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM tbl_search WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}
?>
