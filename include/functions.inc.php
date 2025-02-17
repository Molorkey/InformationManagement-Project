<?php
   
    function emptyInputSignup($name,  $email, $username, $pwd, $pwdRepeat, $phone, $address){
        $result = null;
        if(empty($name) || empty($email) || empty($username) || empty($pwd) || empty($pwdRepeat) || empty($phone) || empty($address)){
            $result = true;
        }
        else{
            $result = false;
        }
    
        return $result;
        
    } 
    
    function invalidUid($username){
        $result = null;
        if(!preg_match("/^[a-zA-Z0-9]*$/", $username)){
            $result = true;
        }
        else{
            $result =false;
        }
        return $result;
    }
    
    function invalidEmail($email){
        $result = null;
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $result = true;
        }
        else{
            $result = false;
        }
        return $result;
    
    }
    
    function pwdMatch($pwd, $pwdRepeat){
        $result = null;
        if($pwd !== $pwdRepeat){
            $result = true;
        }
        else{
            $result = false;
        }
        return $result;
    }    
    function uidExists($conn, $username, $email){
        $sql = "SELECT * FROM users where usersUid = ? OR usersEmail = ?;";
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt,$sql)){
            header("location: ../signup.php?error=stmtfailed");
            exit();
        }
        mysqli_stmt_bind_param($stmt, "ss",$username, $email);
        mysqli_stmt_execute($stmt);
    
        $resultData = mysqli_stmt_get_result($stmt);
    
        if($row = mysqli_fetch_assoc($resultData)){
            return $row;
        }
        else{
            $result = false;
            return $result;
        }    
        mysqli_stmt_close($stmt);
    }
    
    function createUser($conn,$name,  $email, $username, $pwd,$phone, $address){
        $sql = "INSERT INTO users (usersName, usersEmail,usersUid,usersPwd,usersPhone,usersAddress) VALUES (?,?,?,?,?,?);";
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt,$sql)){
            header("location: ../signup.php?error=stmtfailed");
            exit();
        }
    
        $hashedPwd = password_hash($pwd,PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($stmt, "ssssss",$name, $email,$username,$hashedPwd,$phone,$address);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("location: ../signup.php?error=none");
        
    }

    function emptyInputLogin($username, $pwd){
        $result = null;
        if(empty($username) || empty($pwd)){
            $result = true;
        }
        else{
            $result = false;
        }
        return $result;

    }

    function loginUser($conn, $username, $pwd)
    {
        $uidExists = uidExists($conn, $username, $username);
        
        if ($uidExists === false) {
            header("location: ../login.php?error=wronglogin");
            exit();
        }

        $pwdHashed = $uidExists["usersPwd"];
        $checkPwd = password_verify($pwd, $pwdHashed);

        if ($checkPwd === false) {
            header("location: ../login.php?error=wronglogin");
            exit();
        } else if ($checkPwd === true) {
            session_start();
            $_SESSION["userid"] = $uidExists["usersID"];
            $_SESSION["useruid"] = $uidExists["usersUid"];
            
            header("location: ../index.php");
        exit();
        }
    }

    
    