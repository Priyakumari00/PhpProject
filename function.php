<?php
require "../inc/db.php";

function error422($message) {
    $data = [
        'status' => 422,
        'message' => $message,
    ];
    header("HTTP/1.0 422 Unprocessable Entity");
    echo json_encode($data);
    exit();
}

function storeuser($input) {
    global $conn;
    $name = mysqli_real_escape_string($conn, $input['name']);
    $password = mysqli_real_escape_string($conn, $input['password']);

    if (empty(trim($name))) {
        error422('Enter your name');
    } else if (empty(trim($password))) {
        error422('Enter your password');
    } else {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO user (Id, Password) VALUES (?, ?)";
        
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ss', $name, $hashedPassword);

            if (mysqli_stmt_execute($stmt)) {
                $data = [
                    'status' => 201,
                    'message' => 'New User Created',
                ];
                header('Location: Login.html'); // Redirect to Login.html
                exit; // Ensure no further code execution after the redirect
            } else {
                $data = [
                    'status' => 500,
                    'message' => 'Internal Server Error',
                ];
                header('HTTP/1.0 500 Internal Server Error');
                echo json_encode($data);
            }
            
            mysqli_stmt_close($stmt);
        } else {
            $data = [
                'status' => 500,
                'message' => 'Internal Server Error',
            ];
            header('HTTP/1.0 500 Internal Server Error');
            echo json_encode($data);
        }
    }
}




function getcustomer($input) {
    global $conn;

    if (!isset($input['id']) || !isset($input['password'])) {
        return error422("Both username and password are required.");
    }

    $user = mysqli_real_escape_string($conn, $input['id']);
    $password = mysqli_real_escape_string($conn, $input['password']);

    $query = "SELECT * FROM user WHERE Id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $res = mysqli_fetch_assoc($result);
            $hashedPasswordFromDatabase = $res['Password'];

            if (password_verify($password, $hashedPasswordFromDatabase)) {
                // Passwords match - redirect to the home page
                $data = [
                    'status' => 404,
                    'message' => 'Customer not found',
                    'data'=>$friends
                ];
                header("Location: hellow.html");
                echo json_encode($data);
                exit; // Important to stop further execution
            } else {
                // Passwords do not match - redirect to the login page
                header("Location: Login.html");
                exit; // Important to stop further execution
            }
        } else {
            $data = [
                'status' => 404,
                'message' => 'Customer not found',
            ];
            header("HTTP/1.0 404 Not Found");
            echo json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'message' => 'Internal Server Error',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
    }
}



    





?>




