<?php
session_start();
require 'inc/mysqli.inc.php';

if(isset($_GET['id'])){
    $id = $_GET['id'];
    $sql = "SELECT * FROM topic WHERE id=$id";
    $result = $mysqli->query($sql);
    $row = mysqli_fetch_array($result);
}

	$DATA = array(
        'id'=>$row['id'],
		'title' => $row['title'],
		'description' => $row['description'],
		'name' => $row['name'],
	);

$TAGS = array('PHP', 'JavaScript', 'SQL', 'HTML', 'CSS');

$TITLE = 'แก้ไขตั้งกระทู้';
$PAGE_TEMPLATE = 'inc/edit_post.inc.php';
require 'inc/main.inc.php';
?>