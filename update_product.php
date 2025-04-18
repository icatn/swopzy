<?php
session_start();
$conn = new mysqli("localhost", "root", "", "swopzy");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$response = ['success' => false];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id']) && isset($_POST['name']) && isset($_POST['price']) && isset($_POST['description'])) {
        $id = intval($_POST['id']);
        $name = $conn->real_escape_string($_POST['name']);
        $price = floatval($_POST['price']);
        $description = $conn->real_escape_string($_POST['description']);

        $query = "UPDATE products SET name=?, price=?, description=? WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sdsi", $name, $price, $description, $id);

        if ($stmt->execute()) {
            if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
                $targetDir = "uploads/";
                $fileName = basename($_FILES["file"]["name"]);
                $targetFilePath = $targetDir . $fileName;
                $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

                $allowTypes = array('jpg', 'png', 'jpeg', 'mp4', 'webm');
                if (in_array($fileType, $allowTypes)) {
                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
                        $updatePictureQuery = "UPDATE products SET picture=? WHERE id=?";
                        $stmtPicture = $conn->prepare($updatePictureQuery);
                        $stmtPicture->bind_param("si", $fileName, $id);
                        if ($stmtPicture->execute()) {
                            $response['success'] = true;
                        } else {
                            $response['message'] = "Error updating picture.";
                        }
                        $stmtPicture->close();
                    } else {
                        $response['message'] = "Error uploading file.";
                    }
                } else {
                    $response['message'] = "Invalid file format.";
                }
            } else {
                $response['success'] = true; // No file uploaded, but product details updated
            }
        } else {
            $response['message'] = "Error updating product.";
        }
        $stmt->close();
    }
}

echo json_encode($response);
$conn->close();
?>