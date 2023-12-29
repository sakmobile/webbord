<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['email'], $_POST['password'])) {

		header('Location: index.php');
		exit;
	}

    $DATA = $_POST;
    foreach ($DATA as $key => $value) {
		$DATA[$key] = trim($value);
	}
    if ($DATA['email'] === '') {
		
		$FORM_ERRORS['title'] = "กรุณาระบุ 'Email'";
	}
    if ($DATA['password'] === '') {
        $FORM_ERRORS['title'] = "กรุณาระบุ 'รหัสผ่าน'";
    }
    if (!isset($FORM_ERRORS)) {

        require 'inc/mysqli.inc.php';
        $sql="SELECT id,fullname,email FROM users 
        WHERE  email='".$DATA['email']."' 
        AND  password='".$DATA['password']."' ";
        $result = $mysqli->query( $sql );
        $row = mysqli_fetch_array($result);
        if($row >= 1){
           
              $_SESSION["id"] = $row["id"];
              $_SESSION["fullname"] = $row["fullname"];
              $_SESSION["email"] = $row["email"];
              var_dump($_SESSION["fullname"]);
              header('Location: index.php');
        }else {
            $FORM_ERRORS['title'] = "ชื่อผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง";
        }
        
        
           

       
     
        



    }
}

$TITLE = 'Login';
$PAGE_TEMPLATE = 'inc/login.inc.php';
require 'inc/main.inc.php';