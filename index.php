<?php
session_start();
/*
ตัวอย่างนี้จะเน้นการแยกส่วนการทำงาน
คือส่วนที่ติดต่อกับฐานข้อมูลก็จะเป็นตัวสร้างข้อมูลเพื่อส่งไปให้ template แสดงผล
โดยตัวแปรที่จะถูกใช้ใน template (inc/index.inc.php, inc/post.inc.php, inc/view.inc.php)
จะเป็นตัวแปรที่เป็นตัวพิมพ์ใหญ่ทั้งหมด เช่น $PAGE, $ITEMS
ซึ่งเราจะใช้แนวทางนี้ทั้งหมดสำหรับตัวอย่างนี้
*/

/*
ทำการเชื่อมต่อกับฐานข้อมูล ดู (inc/mysqli.inc.php)
*/
require 'inc/mysqli.inc.php';
/*
กำหนดค่า default ให้กับตัวแปร $PAGE
*/
$PAGE = empty($_GET['page'])
	? 1
	: (int)$_GET['page'];
/*
จำนวนกระทู้ที่จะแสดงใน 1 หน้า
*/
$ITEMS_PER_PAGE = 100;
/*
คำนวณ offset เริ่มต้นที่จะกำหนดใน LIMIT ซึ่ง offset ของแถวแรกเริ่มที่ 0 ไม่ใช่ 1
ถ้าอยู่ที่หน้า 1 ก็จะได้ LIMIT 0, 100
ถ้าอยู่ที่หน้า 3 ก็จะได้ LIMIT 200, 100
*/
$START_OFFSET = ($PAGE - 1) * $ITEMS_PER_PAGE;
/*
ส่ง SQL query ไปยัง MySQL Server ด้วย mysqli::query()
หากไม่มี error และชนิดของ query ที่ส่งไปเป็น SELECT หรืออื่นๆ
ที่คืนแถวกลับมาเช่น SHOW DATABASES, SHOW VARIABLES
mysqli::query() จะ return instance ของ class mysqli_result กลับมา
นอกนั้นจะ return true หรือ false
โดยเราจะเลือก SELECT เฉพาะฟิลด์ที่ต้องใช้มาเท่านั้น
จะไม่ใช้ SELECT * เพื่อลดการใช้หน่อยความจำและเพื่อเพิ่มประสิทธิภาพ
และเราจะเรียงลำดับตามวันที่มีผู้แสดงความเห็นล่าสุด (last_commented)
*/
$result = $mysqli->query(
	"
	SELECT
		`id`,
		`created`,
		`last_commented`,
		`title`,
		`name`,
		`num_comments`,
		`last_commented_name`
	FROM `topic`
	ORDER BY `last_commented` DESC
	LIMIT {$START_OFFSET}, {$ITEMS_PER_PAGE}
	"
);
/*
เมื่อได้ $result ซึ่งจะเป็น instance ของ class mysqli_result
เราก็จะอ่านข้อมูลที่ได้ลง array ด้วย mysqli_result::fetch_assoc()
คืออ่านมาเป็น associative array และใส่เข้าไปใน array $ITEMS เพื่อนำไปใช้ใน template ต่อไป
โดยเราจะอ่านเข้ามาพักในตัวแปร $item ก่อน ซึ่งหากยังมีข้อมูลอยู่ $item จะไม่ใช่ null
จะทำให้เงื่อนไขใน while เป็นจริง $item ก็จะถูกเพิ่มเข้าไปใน $ITEMS
แต่หากอ่านข้อมูลจนหมดแล้ว (หรือไม่มีข้อมูลตั้งแต่ต้น) mysqli_result::fetch_assoc() จะ return null
และทำให้ $item เป็น null เงื่อนไขก็จะเป็นเท็จ และออกจาก while
*/
$ITEMS = array();
while ($item = $result->fetch_assoc()) {
	$ITEMS[] = $item;
}
/*
ปล่อยหน่วยความจำที่ $result ใช้
*/
$result->free();
/*
หาจำนวนกระทู้ทั้งหมด ซึ่งในที่นี้ไม่ใช้ SELECT SQL_CALC_FOUND_ROWS ร่วมกับ FOUND_ROWS()
เพราะพิสูจน์จากหลายแหล่งข้อมูลแล้วว่าทำงานช้ากว่า COUNT(*)
*/
$result = $mysqli->query('SELECT COUNT(*) FROM `topic`');
/*
mysqli_result::fetch_row() หรือ mysqli_result::fetch_assoc()
จะคืนค่ากลับมาเป็น array เสมอแม้จะมีแค่ฟิลด์เดียวที่ SELECT มาก็ตาม
แต่เนื่องจากเราอยากได้ค่าของฟิลด์แรกใน array ไม่ใช่ตัว array เอง
เราจึงใช้ current() เพื่ออ่านค่าของสมาชิกตัวแรกใน array
*/
$FOUND_ROWS = current($result->fetch_row());
/*
ปล่อยหน่วยความจำที่ $result ใช้
*/
$result->free();
/*
หาจำนวนหน้าทั้งหมด โดยหารจำนวนแถวทั้งหมดด้วยจำนวนที่แสดงผลต่อหน้า และปัดเศษขึ้นด้วย ceil()
*/
$NUM_PAGES = ceil($FOUND_ROWS / $ITEMS_PER_PAGE);
/*
บอก inc/main.inc.php ให้ require ไฟล์ inc/index.inc.php เป็น template
*/
$PAGE_TEMPLATE = 'inc/index.inc.php';
require 'inc/main.inc.php';
