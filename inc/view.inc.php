<?php
/********** เริ่ม LOOP แสดงกระทู้และความเห็น **********/
/*
เนื่องจากเราได้รวมกระทู้และความเห็นเข้าเป็น array เดียวกัน (ดู view.php)
จึงทำให้ไม่ต้องเขียนโค้ด HTML ซ้ำซ้อน 
เพียงแค่ตรวจว่า $no เป็น 0 หรือไม่ ก็จะทราบว่า $item นั้นเป็นกระทู้
*/
foreach ($ITEMS as $no => $item):
	/*
	กำหนดตัวแปรเพื่อบอกว่า $item ปัจจุบันเป็นกระทู้หรือความเห็น
	*/
	$isTopic = $no === 0;
	/*
	ตรวจเงื่อนไขว่า $no เป็น 1 หรือไม่ หากมันเป็น 1 แสดงว่าขณะนี้ $item คือความเห็นแรก
	ดังนั้นเราจะสร้าง <div id="comments"></div> เพื่อให้ browser scroll มายังจุดนี้
	เมื่อมี hash tag #comments อยู่ใน URL
	*/
	if ($no === 1):
	?>
		<div id="comments"></div>
		<?php
		/*
		หากจำนวนหน้ามากกว่า 1 เราจะสร้าง pagination
		*/
		if ($NUM_PAGES > 1):
			/*
			เนื่องจากเราจะแสดง pagination ทั้งก่อนแสดงความเห็น และหลังแสดงความเห็น
			จึงใช้ ob_start() เพื่อเก็บ output ที่ได้ไปใช้ในภายหลัง โดยไม่ต้องเขียนโค้ดซ้ำซ้อน
			*/
			ob_start();
			?>
			<div class="text-center">
				<ul class="pagination">
					<?php
					/********** เริ่ม LOOP แสดงหน้าของ pagination **********/
					/*
					โดยให้ $i เริ่มจาก 1 ไปถึงจำนวนหน้าซึ่งคือ $NUM_PAGES
					และหาก $i เท่ากับ $PAGE ที่เป็นหมายเลขหน้าปัจจุบัน
					เราก็จะเพิ่ม class 'active' เข้าไปใน <li> เพื่อให้เน้นว่าเป็นหน้าปัจจุบัน
					ใน href ของ <a> จะกำหนด query string ได้แก่
					topic_id และ page เพื่อส่งต่อไปเป็นค่าใน array $_GET ใน view.php
					ซึ่งค่าเหล่านี้จะทำให้ view.php รู้ว่าจะแสดงกระทู้ id อะไร
					และรู้ว่าจะต้อง SELECT ข้อมูลจากตาราง comment โดยเริ่มจาก offset ใด
					และกำหนด hash tag #comments เพื่อให้ browser scroll ไปที่ความเห็นแรก
					*/
					for ($i = 1; $i <= $NUM_PAGES; ++$i):
					?>
					<li class="<?php if ($i === $PAGE) { echo 'active'; } ?>">
						<a href="view.php?topic_id=<?php
						echo $TOPIC_ID;
						?>&page=<?php
						echo $i;
						?>#comments">
							<?php echo $i; ?>
						</a>
					</li>
					<?php
					endfor;
					/********** จบ LOOP แสดงหน้าของ pagination **********/
					?>
				</ul>
			</div>
			<?php
			/*
			เก็บ output ที่อยู่ใน output buffer หมดหลังเรียกใช้ ob_start()
			เข้ามาไว้ในตัวแปร $PAGINATION พร้อมกับแสดงผล และปิด output buffering
			*/
			$PAGINATION = ob_get_flush();
		endif;
	endif;
	?>
	<div id="<?php
	/*
	หาก $item ไม่ใช่กระทู้
	กำหนด id ให้กับ <div> เพื่อให้ browser scroll มายังจุดนี้
	เมื่อมี hash tag #comment-<id ของความเห็น> อยู่ใน URL
	*/
	if (!$isTopic) {
		echo "comment-{$item['id']}";
	}
	?>" class="panel <?php
	/*
	หาก $item เป็นกระทู้ ให้ใช้ class 'panel-info' เพื่อเน้นสีว่าเป็นกระทู้
	นอกนั้นใช้ 'panel-default'
	*/
	echo $isTopic
		? 'panel-info'
		: 'panel-default';
	?>">
		<?php
		/*
		หาก $item เป็นกระทู้ เราจะแสดงหัวข้อกระทู้ด้วย
		*/
		if ($isTopic):
		?>
		<div class="panel-heading">
			<h4>
				<span class="badge pull-right">
					<span class="glyphicon glyphicon-eye-open"></span>&nbsp;<?php
					/*
					แสดงจำนวนการเข้าชมและจำนวนความเห็น
					โดยใช้ number_format() เพื่อใส่ , เข้าไปในตัวเลขให้ดูสวยงาม
					เช่น 1234 จะเป็น 1,234
					*/
					echo number_format($item['num_views']);
					?>&nbsp;&nbsp;<span class="glyphicon glyphicon-comment"></span>&nbsp;<?php
					echo number_format($item['num_comments']);
					?>
				</span>
				<?php
				echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8');
				?>
			</h4>
		</div>
		<?php
		/*
		หาก $item เป็นความเห็น
		*/
		else:
		?>
		<span class="badge">
		<?php
		/*
		แสดงเลขลำดับความเห็น
		*/
		echo $START_OFFSET + $no;
		?>
		</span>
		<?php
		endif;
		?>
		<div class="panel-body">
			<?php
			/*
			แสดงรายละเอียดของกระทู้หรือข้อความของความเห็น
			โดยใช้ nl2br() เพื่อเปลี่ยน newline (\n) ให้เป็น tag <br>
			*/
			echo nl2br(htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8'));
			?>
			<?php if(isset($_SESSION["fullname"])){?>
			<?php if($no){?>
			<a  href="del_view.php?id=<?php echo $item['id']; ?>&topic_id=<?php echo $item['topic_id']; ?>" type="button" class="btn btn-danger navbar-right" style="margin: auto;"><span class="glyphicon glyphicon-trash"></span></a>
			<?php } ?>
			<?php }?>
		</div>
		<div class="panel-footer">
			<small class="text-muted">โดย:</small>
			<strong class="text-info"><?php
			/*
			แสดงชื่อ
			*/
			echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8');
			?></strong>
			<small class="text-muted">เมื่อ:</small>
			<span class="text-info" title="<?php
			/*
			แปลงวันที่ให้เป็นภาษาไทยด้วยฟังก์ชั่น thai_datetime() ที่กำหนดไว้ใน inc/main.inc.php
			โดยใส่ไว้ใน attribute title เพื่อให้แสดงขึ้นมาเมื่อผู้ใช้เอาเมาส์ไปชี้
			*/
			echo thai_datetime($item['created']);
			?>"><?php
			/*
			แปลงวันที่ให้เป็นช่วงห่างของเวลาภาษาไทยด้วยฟังก์ชั่น thai_time()
			ที่กำหนดไว้ใน inc/main.inc.php
			*/
			echo thai_time($item['created']);
			?></span>
			<small class="text-muted">จาก:</small>
			<span class="text-warning"><?php
			/*
			แสดง IP
			*/
			echo $item['ip'];
			?></span>
		</div>
	</div>
<?php
endforeach;
/********** จบ LOOP แสดงกระทู้และความเห็น **********/
/*
ตรวจสอบว่ามีตัวแปร $PAGINATION ถูกกำหนดค่าไว้หรือไม่
ถ้ามี ก็ให้แสดงผล pagination อีกครั้ง
*/
if (isset($PAGINATION)) {
	echo $PAGINATION;
}
/********** เริ่ม FORM แสดงความเห็น **********/
/*
โดย form นี้จะใช้ method POST ในการส่งข้อมูลไปยัง view.php
จะเห็นว่าใน action ของ form มี hash tag #comment-form อยู่ด้วย
ใช้เพื่อให้ browser scroll มาจุดนี้เมื่อมี error เกิดขึ้น เช่น เมื่อไม่ได้ระบุ 'ชื่อ'
ข้อมูลที่จะส่งให้กับ view.php ก็ได้แก่
topic_id เป็น hidden input ซึ่งจะไม่แสดงผลให้ผู้ใช้เห็น
description เป็น textarea
และ name เป็น input type=text
*/
?>
<form action="view.php#comment-form" method="post" id="comment-form" class="form-horizontal panel panel-default">
	<input type="hidden" name="topic_id" value="<?php echo $TOPIC_ID; ?>">
	<div class="panel-heading">
		<h4>
			<span class="glyphicon glyphicon-comment"></span>
			แสดงความเห็น
		</h4>
	</div>
	<div class="panel-body">
		<?php
		/*
		แสดง errors (ถ้ามี)
		ดูคำอธิบายใน inc/form_errors.inc.php
		*/
		require 'inc/form_errors.inc.php';
		?>
		<div class="form-group <?php
		/*
		ถ้ามี key ชื่อ 'description' อยู่ใน array $FORM_ERRORS
		ให้เพิ่ม class 'has-error' เข้าไปใน <div> นี้
		*/
		if (isset($FORM_ERRORS['description'])) {
			echo 'has-error';
		}
		?>">
			<label for="descriptionTextarea" class="col-sm-2 control-label">*ข้อความ</label>
			<div class="col-sm-10">
				<textarea
					id="descriptionTextarea"
					name="description"
					rows="10"
					placeholder="ข้อความ"
					spellcheck="false"
					class="form-control"
				><?php
				echo htmlspecialchars($DATA['description'], ENT_QUOTES, 'UTF-8');
				?></textarea>
			</div>
		</div>
		<div class="form-group <?php
		/*
		ถ้ามี key ชื่อ 'name' อยู่ใน array $FORM_ERRORS
		ให้เพิ่ม class 'has-error' เข้าไปใน <div> นี้
		*/
		if (isset($FORM_ERRORS['name'])) {
			echo 'has-error';
		}
		?>">
			<label for="nameInput" class="col-sm-2 control-label">*ชื่อ</label>
			<div class="col-sm-4">
				<input
					type="text"
					id="nameInput"
					name="name"
					value="<?php
					echo htmlspecialchars($DATA['name'], ENT_QUOTES, 'UTF-8');
					?>"
					placeholder="ชื่อ"
					spellcheck="false"
					class="form-control"
				>
			</div>
		</div>
		<hr>
		<div class="form-group">
			<div class="col-sm-4 col-sm-offset-4">
				<button type="submit" class="btn btn-primary btn-block">
					แสดงความเห็น
				</button>
			</div>
		</div>
	</div>
</form>
<?php
/********** จบ FORM แสดงความเห็น **********/
