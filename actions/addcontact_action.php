<?php

    ob_start();
    session_start();

    require_once '../includes/config.php';
    require_once '../includes/db.php';

    $errors = [];

    if(isset($_POST) && !empty($_SESSION['user']))
    {
        $firstName = trim($_POST['fname']);
        $lastName = trim($_POST['lname']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);
        $photofile = !empty($_FILES['photo']) ? $_FILES['photo'] : [];
        $ownerId = (!empty($_SESSION['user']) && !empty($_SESSION['user']['id'])) ? $_SESSION['user']['id'] : 0 ;
        $cId = !empty($_POST['contactid']) ? $_POST['contactid'] : '';

        // Validations

        if(empty($firstName))
        {
            $errors[] = "First Name cannot be Blank";
        }

        if(empty($email))
        {
            $errors[] = "Email cannot be Blank";
        }

        if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL) )
        {
            $errors[] = "Invalid Email Address";
        }

        if(empty($phone))
        {
            $errors[] = "Phone Number cannot be Blank";
        }

        if(!empty($phone))
        {
            if(!is_numeric($phone) || (strlen($phone) != 10) )
            {
                $errors[] = "Invalid Phone Number";
            }
        }


        // If Errors are present

        if(!empty($errors))
        {
            $_SESSION['errors'] = $errors;
            header('location:'.SITEURL."addcontact.php");
            exit();
        }


        // Uploading user photo 
        $photoName = '';
        if(!empty($photofile['name']))
        {
            $fileTempPath = $photofile['tmp_name'];
            $filename = $photofile['name'];
            $fileNameCmp = explode('.', $filename);
            $fileExtn = strtolower(end($fileNameCmp));
            $fileNewName = md5(time().$filename).'.'.$fileExtn;
            $photoName = $fileNewName;

            //allowed extensions
            $allowed_extn = ["jpg", "jpeg", "png", "gif"];
            if(in_array($fileExtn, $allowed_extn))
            {
                $uploadFileDir = "../uploads/photos/";
                $destinationFilePath = $uploadFileDir.$photoName;

                if(!move_uploaded_file($fileTempPath, $destinationFilePath))
                {
                    $errors[] = "File couldn't be uploaded";
                }
            }
            else{
                $errors[] = "Invalid File(Photo) Extension";
            }
        }

        if(!empty($cId))
        {
            // If cId mila toh update karenge warna insert karenge. This is for updating existing record
            if(!empty($photoName))
            {   // If photoName exists that is if someone wants to update the photo so they will upload a new photo   
                $sql = "UPDATE `contacts` SET `first_name`='{$firstName}', `last_name`='{$lastName}', `email`='{$email}', `phone`='{$phone}', `address`='{$address}', `photo`='{$photoName}' WHERE id={$cId} AND owner_id = {$ownerId}";
            }
            else
            {   // if photoname doesn't exists i.e. if someone doesn't wants to update the photo so they will not upload 
                // new photo
                $sql = "UPDATE `contacts` SET `first_name`='{$firstName}', `last_name`='{$lastName}', `email`='{$email}', `phone`='{$phone}', `address`='{$address}' WHERE id={$cId} AND owner_id = {$ownerId} " ;
            }
            $message = "Contact has been updated Successfully ";
        }
        else{
            // This is to Insert new Record
            $sql = "INSERT INTO `contacts` (`first_name`, `last_name`, `email`, `phone`, `address`, `photo`, `owner_id`) VALUES ('{$firstName}','{$lastName}','{$email}','{$phone}','{$address}','{$photoName}','{$ownerId}')";
            $message = "New Contact has been added Successfully ";
        }

        
        $conn = db_connect();
        if(mysqli_query($conn, $sql))
        {
            db_close($conn);
            $_SESSION['success'] = $message;
            header('location:'.SITEURL);
        }


        















    }





?>