<?php
session_start();
require 'inc/mysqli.inc.php';

if(isset($_GET['id'])){
    $id = $_GET['id'];
    $sql = "DELETE FROM topic WHERE id=$id";
    $sql1 = "DELETE FROM comment WHERE topic_id=$id";
    $result = $mysqli->query($sql);
    $result1 = $mysqli->query($sql1);

    if ($result1 == TRUE && $result == TRUE) {
        echo "Record deleted successfully.";
        header('Location: index.php');
    }else{
        $message = "Error:" . $sql . "<br>" . $conn->error;
        echo "<script type='text/javascript'>alert('$message');</script>";
    }
}


 

?>