<?php

    ob_start();
    session_start();

    require_once '../includes/config.php';
    require_once '../includes/db.php';

    $errors = [];

    if (isset($_POST)) 
    {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);


        if (empty($email)) 
        {
            $errors[] = "Email cannot be Blank";
        }

        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) 
        {
            $errors[] = "Invalid Email Address";
        }

        if (empty($password)) 
        {
            $errors[] = "Password cannot be Blank";
        }

        if (!empty($errors)) 
        {
            $_SESSION['errors'] = $errors;
            header('location:' . SITEURL . "login.php");
        }

        // If no errors

        if (!empty($email) && !empty($password)) 
        {
            $conn = db_connect();
            $sanitizeEmail = mysqli_real_escape_string($conn, $email);  // This is done to prevent from SQL Injection problem
            $sql = "SELECT * FROM `users` WHERE `email` = '{$sanitizeEmail}'";
            $sqlResult = mysqli_query($conn, $sql);
            if(mysqli_num_rows($sqlResult)>0)
            {
                $userInfo = mysqli_fetch_assoc($sqlResult); 
                if(!empty($userInfo))
                {
                    $passwordinDB = $userInfo['password'];
                    if(password_verify($password, $passwordinDB))
                    {
                        unset($userInfo['password']);   // password hash of user should not be seen on the frontend 
                        $_SESSION['user'] = $userInfo;  // we are creating a session of user to show the data of the user on frontend 
                        header('location:'.SITEURL);
                    }
                    else{
                        $errors[] = "Incorrect Password";
                        $_SESSION['errors'] = $errors;
                        header('location:'.SITEURL."login.php");
                        exit();
                    }
                }
                else{
                    $errors[] = "Incorrect Email Address";
                    $_SESSION['errors'] = $errors;
                    header('location:'.SITEURL."login.php");
                    exit();
                }
            }

        }

    }

?>