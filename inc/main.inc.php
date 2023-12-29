<?php
/*
ไฟล์ template หลักของทุกหน้า ซึ่งอาจจะ require template รองอีกที
จากตัวแปร $PAGE_TEMPLATE ที่ต้องกำหนดไว้ก่อนเรียกใช้ไฟล์นี้
และจะใช้ Alternative syntax for control structures ใน template
(ดู http://php.net/manual/en/control-structures.alternative-syntax.php)
เช่น
if (1 > 2) {
	...
}
จะเป็น
if (1 > 2):
	...
endif;
และในตัวอย่างนี้ จะยังไม่ใช้ JavaScript ใดใดทั้งสิ้น
*/

/*
ฟังก์ชั่นตัวช่วยแปลง unix timestamp หรือ SQL DATETIME ให้เป็นวันที่ภาษาไทยแบบย่อ
เช่น '2014-12-01 04:14:00' จะกลายเป็น '1 ธ.ค. 57 04:14'
*/
function thai_datetime($timestamp)
{
	static $thaiMonthAbbrs = array(
		'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
		'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.',
	);
	/*
	หาก $timestamp ไม่ใช่ตัวเลข
	*/
	if (!is_numeric($timestamp)) {
		/*
		ให้ใช้ฟังก์ชั่น strtotime() แปลง $timestamp ให้เป็น unix timestamp
		*/
		$timestamp = strtotime($timestamp);
	}
	/*
	และใช้ฟังก์ชั่น getdate() ดึงข้อมูล array เกี่ยวกับ timestamp นั้นๆ
	เช่น วันที่, เดือน, ปี, ชั่วโมง, นาที, วินาที ฯลฯ
	*/
	$info = getdate($timestamp);
	/*
	เราใช้ฟังก์ชั่น sprintf() เพื่อทำการจัดรูปแบบข้อความ
	โดย argument แรกจะเป็น 'รูปแบบ' และ argument ที่เหลือจะเป็นค่าที่จะส่งไปแทนที่ใน 'รูปแบบ'
	ซึ่งรูปแบบที่จะเป็น ตัวแทนที่ นั้น จะเริ่มต้นด้วย % ตามด้วยตัวอักษร s, d หรืออื่นๆ (ดู PHP Manual)
	*/
	return sprintf(
		/*
		%d คือให้แทนที่ค่าที่ส่งมา ในรูปแบบตัวเลขจำนวนเต็ม
		%s คือให้แทนที่ค่าที่ส่งมา ในรูปแบบ string หากค่านั้นไม่ใช่ string ก็จะทำการแปลงให้เป็น string
		%02d คือให้แทนที่ค่าที่ส่งมา ในรูปแบบตัวเลขจำนวนเต็ม และเติม 0 เข้าไปข้างหน้าสูงสุด 2 ตัว
		เช่น ค่าที่ส่งมาคือ 1 ก็จะกลายเป็น 01
		*/
		'%d %s %d %02d:%02d',
		/*
		$info['mday'] จะเป็นตัวเลขวันที่ 1 - 31
		ค่าจะไปแทนที่ %d ตัวแรก
		*/
		$info['mday'],
		/*
		$info['mon'] จะเป็นตัวเลขเดือน 1 - 12
		ซึ่งเราจะเอาไปใช้เป็น key ในการเลือกค่าจากตัวแปร $thaiMonthAbbrs
		ซึ่ง $thaiMonthAbbrs นั้นมี key เริ่มที่ 0 เราจึงต้องลบ $info['mon'] ด้วย 1 เสียก่อน
		ค่านี้จะไปแทนที่ %s
		*/
		$thaiMonthAbbrs[$info['mon'] - 1],
		/*
		แปลง $info['year'] ให้เป็น พ.ศ. โดย + ด้วย 543
		และใช้ substr() ตัดเฉพาะตัวเลข 2 หลักสุดท้ายของ พ.ศ. ออกมา
		ค่าจะไปแทนที่ %d ตัวที่สอง
		*/
		substr($info['year'] + 543, -2),
		/*
		เลขชั่วโมง
		ค่านี้จะไปแทนที่ %02d ตัวแรก
		*/
		$info['hours'],
		/*
		เลขนาที
		ค่านี้จะไปแทนที่ %02d ตัวที่สอง
		*/
		$info['minutes']
	);
}

/*
ฟังก์ชั่นตัวช่วยแปลง unix timestamp หรือ SQL DATETIME ให้เป็นช่วงห่างของเวลาภาษาไทย
เช่น 15 นาทีที่แล้ว
และหากช่วงห่างเกินจำนวนวันที่กำหนดไว้ใน $daysBeforeFullDate ก็จะแสดงวันที่เต็ม
โดยเรียกใช้ thai_datetime() อีกทอดหนึ่ง
*/
function thai_time($timestamp, $daysBeforeFullDate = 3)
{
	/*
	หาก $timestamp ไม่ใช่ตัวเลข
	*/
	if (!is_numeric($timestamp)) {
		/*
		ให้ใช้ฟังก์ชั่น strtotime() แปลง $timestamp ให้เป็น unix timestamp
		*/
		$timestamp = strtotime($timestamp);
	}
	/*
    เราจะหาค่าช่วงห่างของเวลาปัจจุบันกับ $timestamp
    โดยเวลาปัจจุบันนั้นหาได้จากฟังก์ชั่น time()
	*/
	$diff = time() - $timestamp;
	/*
    หากความต่างของเวลาปัจจุบันกับ $timestamp น้อยกว่า 10 วินาที
	*/
	if (!$diff) {
		return 'ไม่กี่วินาทีที่แล้ว';
	}
	/*
	หากความต่างของเวลาปัจจุบันกับ $timestamp น้อยกว่า 1 นาที
	*/
	if ($diff < 60) {
		return $diff . ' วินาทีที่แล้ว';
	}
	/*
    หากความต่างของเวลาปัจจุบันกับ $timestamp น้อยกว่า 1 ชั่วโมง
	*/
	if ($diff < 3600) {
		return (int)($diff / 60) . ' นาทีที่แล้ว';
	}
	/*
	หากความต่างของเวลาปัจจุบันกับ $timestamp น้อยกว่า 1 วัน
	*/
	if ($diff < 86400) {
		return (int)($diff / 3600) . ' ชั่วโมงที่แล้ว';
	}
	/*
	หากความต่างของเวลาปัจจุบันกับ $timestamp น้อยกว่าจำนวนวันที่กำหนดไว้
    ในตัวแปร $daysBeforeFullDate ที่เราจะใช้เป็นตัวบอกว่า
    ควรจะแสดงวันที่เต็มเมื่อช่วงห่างเกินกี่วัน
	*/
	if ($diff < 86400 * $daysBeforeFullDate) {
		return (int)($diff / 86400) . ' วันที่แล้ว';
	}
	/*
	หากช่วงห่างไม่อยู่ในเงื่อนไขข้างต้นเลย ให้แสดงวันที่เต็ม
	*/
	return thai_datetime($timestamp);
}

/*
หากมีการเชื่อมต่อฐานข้อมูล ให้ทำการตัดการเชื่อมต่อเสีย
*/
if (isset($mysqli)) {
	/*
	ใช้ @ operator ด้วย เพื่อป้องกัน PHP Warning หากก่อนหน้านี้ทำการเชื่อมต่อฐานข้อมูลไม่สำเร็จ
	*/
	@$mysqli->close();
}
/*
กำหนดค่า default ให้กับตัวแปร $HIGHLIGHT_ID
ซึ่งจะถูกใช้ในไฟล์ inc/index.inc.php (และอื่นๆ ในอนาคต)
*/
$HIGHLIGHT_ID = isset($_GET['highlight_id'])
	? $_GET['highlight_id']
	: null;
/*
กำหนดค่า default ให้กับตัวแปร $TITLE หากไม่ได้กำหนดไว้ก่อนหน้านี้
*/
if (!isset($TITLE)) {
	$TITLE = 'Webboard';
}
/*
กำหนดตัวแปร $PARENT_FILENAME ให้เป็นชื่อไฟล์ที่ผู้ใช้เรียก
โดยตรวจจาก $_SERVER['SCRIPT_FILENAME'] ซึ่งจะมีค่าเป็นชื่อไฟล์ PHP ที่ผู้ใช้เรียก
เช่น C:\xampp\htdocs\workshop-webboard\index.php
แต่เนื่องจากเราต้องการเพียงส่วนท้ายสุด คือ index.php เราจึงใช้ฟังก์ชั่น pathinfo()
ดึงข้อมูลส่วนนี้ออกมา ซึ่งปกติ pathinfo() จะคืนค่าออกมาเป็น array รายละเอียดของชื่อไฟล์
แต่ถ้าเรากำหนด argument ตัวที่สอง ก็จะดึงเฉพาะส่วนออกมาได้
ซึ่ง PATHINFO_BASENAME หมายถึง ให้เอาเฉพาะชื่อไฟล์และนามสกุลมา
*/
$PARENT_FILENAME = pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_BASENAME);
?>
<!DOCTYPE html>
<html>

<head>
	<title><?php
			/*
		ก่อน echo ค่าของตัวแปรใดใดก็ตามที่ไม่แน่ใจว่าค่าของมันจะเป็นอะไรกันแน่ ออกมาเป็นส่วนหนึ่งของ HTML
		และไม่ต้องการให้ค่าเหล่านั้นมีความหมายพิเศษ เช่น เราอยากแสดงผลคำว่า '<div>'
		แต่หากเรา echo มันออกมาตรงๆ browser ก็จะมองว่ามันเป็น tag <div> ไม่ใช่ข้อความ '<div>'
		ดังนั้นเราจึงจำเป็นต้อง escape ตัวอักษรพิเศษ < > & " '
		ที่อาจจะมีอยู่ในค่าของตัวแปรให้เป็น html entity เสียก่อน ด้วยฟังก์ชั่น htmlspecialchars()
		เช่น <div> ก็จะกลายเป็น &lt;div&gt;
		*/
			echo htmlspecialchars($TITLE, ENT_QUOTES, 'UTF-8');
			?></title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="css/workshop-webboard.css">
</head>

<body>
	<div class="container">
		<div class="navbar navbar-default" role="navigation">
			<div class="container-fluid">
				<div class="navbar-header">
					<a class="navbar-brand" href="index.php">
						Webboard
					</a>
				</div>
				<ul class="nav navbar-nav">
					<li class="<?php
								/*
						ตรวจว่าขณะนี้ผู้ใช้อยู่ที่หน้าแรกหรือไม่
						*/
								if ($PARENT_FILENAME === 'index.php') {
									/*
							ถ้าใช่ ก็ให้เพิ่ม class 'active' เข้าไปใน <li> นี้
							เพื่อเน้นว่าในขณะนี้ ผู้ใช้อยู่ที่หน้านี้
							*/
									echo 'active';
								}
								?>">
						<a href="index.php">
							<span class="glyphicon glyphicon-home"></span>
							หน้าแรก
						</a>
					</li>
					
						<li class="<?php
									/*
						ทำการตรวจสอบเมนูอื่นเช่นเดียวกัน
						*/

									if ($PARENT_FILENAME === 'post.php') {
										echo 'active';
									}
									?>">
							<a href="post.php">
								<span class="glyphicon glyphicon-pencil"></span>
								ตั้งกระทู้ใหม่
							</a>
						</li>
					
						<li class="<?php
									/*
						ทำการตรวจสอบเมนูอื่นเช่นเดียวกัน
						*/

									if ($PARENT_FILENAME === 'login.php') {
										echo 'active';
									}
									?>">
							<a href="login.php">
								<span class="glyphicon glyphicon-log-in"></span>
								เข้าสู่ระบบ
							</a>
						</li>

					<?php
					/*
						สำหรับเมนูนี้ ถ้าไม่ได้อยู่ที่ view.php ก็จะไม่แสดงผลเลย
						*/
					if ($PARENT_FILENAME === 'view.php') :
					?>
						<li class="active">
							<a href="#">
								<span class="glyphicon glyphicon-eye-open"></span>
								<?php
								echo htmlspecialchars($TITLE, ENT_QUOTES, 'UTF-8');
								?>
							</a>
						</li>
					<?php
					endif;
					?>
				</ul>

				<?php if (isset($_SESSION["fullname"])) { ?>
					<ul class="nav navbar-nav navbar-right">
						<li><a href="#"><?php echo$_SESSION["fullname"] ?></a></li>
						<li><a href="logout.php" onclick="return confirm('คุณต้องการออกจากระบบหรือไม่? !!!')">ออกจากระบบ</a></li>
						
					</ul>
				<?php } ?>
			</div>
		</div>
		<?php
		/*
			หากมีตัวแปร $FATAL_ERROR ถูกกำหนดไว้ก่อนหน้า
			แสดงว่ามี error ที่ทำให้ไม่สามารถแสดงผลข้อมูลหน้านั้นได้อย่างถูกต้อง
			เช่น ไม่สามารถเชื่อมต่อฐานข้อมูลได้ หรือผู้ใช้ส่ง id ของกระทู้ที่ไม่มีอยู่จริงมาให้
			เราก็จะแสดงผลข้อความที่กำหนดไว้ใน $FATAL_ERROR
			*/
		if (isset($FATAL_ERROR)) :
		?>
			<div class="alert alert-danger">
				<?php
				echo htmlspecialchars($FATAL_ERROR, ENT_QUOTES, 'UTF-8');
				?>
			</div>
		<?php
		/*
			นอกนั้นจะ require ไฟล์ที่กำหนดไว้ในตัวแปร $PAGE_TEMPLATE
			ซึ่งจะตรวจสอบด้วย isset() ก่อนว่ามีตัวแปรนี้กำหนดไว้หรือไม่
			ถ้าไม่ได้กำหนด ก็จะไม่ require ไฟล์ใดใด และแสดงหน้าเปล่าๆ ที่มีแค่ส่วน navigation
			*/
		elseif (isset($PAGE_TEMPLATE)) :
			/*
				และไม่ได้ตรวจสอบว่าไฟล์ตามค่าของ $PAGE_TEMPLATE นั้นมีอยู่จริงหรือไม่
				หากไม่มีอยู่จริงก็จะเกิด PHP fatal error และจบการทำงาน
				เพราะ require เป็นคำสั่งที่ต่างจาก include (ซึ่งไม่ได้ใช้ในตัวอย่างนี้)
				จะจบการทำงานทันทีหากหาไฟล์ที่กำหนดไม่เจอ
				*/
			require $PAGE_TEMPLATE;
		endif;
		?>
	</div>
	<?php
	/*
		ตรวจสอบว่ามี key 'REQUEST_TIME_FLOAT' อยู่ใน array $_SERVER หรือไม่
		ซึ่ง $_SERVER['REQUEST_TIME_FLOAT'] จะเป็นเวลาที่ PHP เริ่มต้นทำงาน
		เป็นหน่วย microsecond (1/1000000 วินาที)
		และมีให้ใช้ตั้งแต่ PHP 5.4 เราจึงต้องตรวจสอบการมีอยู่ของมันก่อนใช้งาน
		หากมีตัวแปรนี้อยู่ เราจะแสดงผลเวลาการทำงานของ request นี้ ว่าใช้เวลาไปเท่าไหร่
		โดยเอาค่าจาก microtime(true) ที่จะ return microsecond ปัจจุบันกลับมา
		ไปลบกับ $_SERVER['REQUEST_TIME_FLOAT'] ก็จะได้เวลาที่ request นี้ใช้
		และจัดรูปแบบให้เป็นทศนิยม 4 ตำแหน่งด้วย number_format()
		การจับเวลาเป็นวิธีหนึ่งที่จะบอกเราได้ว่า เราเขียนโปรแกรมได้อย่างมีประสิทธิภาพหรือไม่ในด้านความเร็ว
		*/
	if (isset($_SERVER['REQUEST_TIME_FLOAT'])) :
	?>
		<div class="text-center">
			<span class="label label-info">Time: <?php
													echo number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 4);
													?>s</span>
		</div>
	<?php
	endif;
	?>
</body>

</html>
<?php
/*
จบการทำงานเสมอ
ดังนั้น ณ จุดใดก็ตามที่มีการ require หรือ include ไฟล์นี้ ก็มั่นใจได้ว่าจะจบการทำงานแน่นอน
*/
exit;
