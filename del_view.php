<?php

session_start();
require 'inc/mysqli.inc.php';

if(isset($_GET['id'])){
    $id = $_GET['id'];
    $sql1 = "DELETE FROM comment WHERE id=$id";
    $update = "UPDATE  topic  SET num_comments = 0 WHERE id='".$_GET['topic_id']."' ";
    $result1 = $mysqli->query($sql1);
    $result = $mysqli->query($update);

    if ($result1 == TRUE ) {
        echo "Record deleted successfully.";
        header('Location: view.php?topic_id='.$_GET['topic_id']);
    }else{
        $message = "Error:" . $sql . "<br>" . $conn->error;
        echo "<script type='text/javascript'>alert('$message');</script>";
    }
}


 

?>
