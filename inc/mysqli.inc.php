<?php
if (function_exists("get_magic_quotes_gpc") || ini_get('register_globals')) {
	/*
	ตรวจสอบการตั้งค่า magic_quotes_gpc และ register_globals ใน php.ini ว่าเปิดใช้งานอยู่หรือไม่
	ถ้ามีการเปิดใช้งาน เราจะไม่ให้โปรแกรมติดต่อกับฐานข้อมูลโดยเด็ดขาด
	ดู
	http://php.net/manual/en/info.configuration.php#ini.magic-quotes-gpc
	http://php.net/manual/en/ini.core.php#ini.register-globals
	*/
	$FATAL_ERROR = 'มีการตั้งค่าที่ไม่ปลอดภัยใน php.ini';
	require 'inc/main.inc.php';
}
/*
สร้าง object ของ class mysqli
*/
$mysqli = new mysqli();
/*
ทำการเชื่อมต่อกับฐานข้อมูล
โดยเราจำเป็นต้องใช้ @ operator เพื่อกัน PHP แสดง warning
เพราะ mysqli::connect() จะทำให้เกิด PHP warning หากมี error
*/
@$mysqli->connect(
	'localhost', // host
	'root', // username
	'boom123', // password
	'webboard' // default database
);
/*
แต่เราจะมาตรวจสอบ error ตรงนี้แทน
หากมี error ที่เกิดขึ้นระหว่างการเชื่อมต่อ เช่น username หรือ password ผิด
จะมี error message ส่งมาจาก MySQL Server โดยจะไปอยู่ใน mysqli::$connect_error
ก็ให้แสดง error message ที่ได้จาก MySQL Server และจบการทำงาน (ดู inc/main.inc.php)
*/
if ($mysqli->connect_error) {
	$TITLE = $mysqli->connect_error;
	/*
	ให้ error message อยู่ในรูปแบบ #<error code> - <error message>
	*/
	$FATAL_ERROR = "#{$mysqli->connect_errno} - {$mysqli->connect_error}";
	require 'inc/main.inc.php';
}
/*
กำหนด charset สำหรับการเชื่อมต่อครั้งนี้ให้เป็น utf8
*/
$mysqli->set_charset('utf8');
