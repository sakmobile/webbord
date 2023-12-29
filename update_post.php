<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['title'], $_POST['description'], $_POST['name'])) {
        header('Location: index.php');
        exit;
    }

    $DATA = $_POST;

    foreach ($DATA as $key => $value) {
        $DATA[$key] = trim($value);
    }

    if ($DATA['title'] === '') {

        $FORM_ERRORS['title'] = "กรุณาระบุ 'หัวข้อ'";
    } elseif (mb_strlen($DATA['title'], 'UTF-8') > 255) {
        $FORM_ERRORS['title'] = "'หัวข้อ' ต้องมีความยาวไม่เกิน 255 ตัวอักษร";
    }

    if ($DATA['description'] === '') {
        $FORM_ERRORS['description'] = "กรุณาระบุ 'รายละเอียด'";
    } elseif (mb_strlen($DATA['description'], 'UTF-8') > 65535) {
        $FORM_ERRORS['description'] = "'รายละเอียด' ต้องมีความยาวไม่เกิน 65535 ตัวอักษร";
    }
    if ($DATA['name'] === '') {
        $FORM_ERRORS['name'] = "กรุณาระบุ 'ชื่อ'";
    } elseif (mb_strlen($DATA['name'], 'UTF-8') > 64) {
        $FORM_ERRORS['name'] = "'ชื่อ' ต้องมีความยาวไม่เกิน 64 ตัวอักษร";
    }

    if (!isset($FORM_ERRORS)) {

        require 'inc/mysqli.inc.php';
        $sql = "UPDATE  topic  SET last_commented = NOW() , title = '".$DATA['title']."', description = '".$DATA['description']."', name = '".$DATA['name']."' ,ip = '".$_SERVER['REMOTE_ADDR']."' WHERE id='".$DATA['id']."' ";
        $mysqli->query($sql);


        header('Location: index.php');

        exit;
    }
}
?>