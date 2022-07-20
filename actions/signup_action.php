<?php

    ob_start();
    session_start();

    require_once '../includes/config.php';
    require_once '../includes/db.php';

    $errors = [];
    if(isset($_POST)){

        $firstName = trim($_POST['fname']);
        $lastName = trim($_POST['lname']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $confirmPassword = trim($_POST['cpassword']);

        // print_arr($_POST);

        // validations

        if (empty($firstName)){
            $errors[] = "First Name cannot be blank";
        }
        
        if (empty($email)){
            $errors[] = "Email cannot be blank";
        }

        if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)){
            $errors[] = "Invalid Email Id";
        }

        if (empty($password)){
            $errors[] = "Password cannot be blank";
        }

        if (empty($confirmPassword)){
            $errors[] = "Confirm Password cannot be blank";
        }

        if(!empty($password) && !empty($confirmPassword) && $password != $confirmPassword){
            $errors[] = "Confirm Password doesn't match";
        }
        
        
        // If email already exists
        if(!empty($email))
        {
            $conn = db_connect();
            $sanitizeEmail = mysqli_real_escape_string($conn, $email); // This is done to prevent from SQL injection problem
            $emailSql = "SELECT id FROM `users` WHERE `email`= '{$sanitizeEmail}'";
            $sqlResult = mysqli_query($conn, $emailSql); // Executes SQL Queries
            $emailRow = mysqli_num_rows($sqlResult);  // Gives output as number rows in the given variable 
            if($emailRow > 0)
            {
                $errors[] = "Email Address already exists.";
            }
            db_close($conn);

        }


        if(!empty($errors)){
            $_SESSION['errors'] = $errors;
            header('location:'.SITEURL.'signup.php');  // This statement is used to send the flow of the code to the signup.php page.
            exit();
        }

        // If No errors
        

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO `users` ( `first_name`, `last_name`, `email`, `password` ) VALUES ('{$firstName}', 
        '{$lastName}', '{$email}', '{$passwordHash}') ";

        $conn = db_connect();

        if(mysqli_query($conn, $sql))
        {
            db_close($conn);
            $message = "You're Registered Successfully !!";
            $_SESSION['success'] = $message;
            header('location:'.SITEURL.'signup.php');
        }
        else{
            echo "Error";
        }

    }


?>