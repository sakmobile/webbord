<div class="panel panel-default">
	<div class="panel-heading">
		<h4>Title</h4>
	</div>
	<div class="panel-body">
		Annoucement
	</div>
	<table class="table table-condensed table-bordered table-striped table-hover">
		<thead>
			<tr>
				<th class="text-center width-15">กระทู้โดย</th>
				<th class="text-center width-50">หัวข้อ</th>
				<th class="text-center text-info width-10">
					<span class="glyphicon glyphicon-comment" title="จำนวนความเห็น"></span>
				</th>
				<th class="text-center text-info width-15">ความเห็นล่าสุดโดย</th>
				<?php if(isset($_SESSION["fullname"])){?>
				<th class="text-center text-info width-10">Acion</th>
				<?php }?>
			</tr>
		</head>
		<tbody>
			<?php
			/********** เริ่ม LOOP แสดงกระทู้ **********/
			foreach ($ITEMS as $item):
			?>
			<tr class="<?php
			/*
			เพิ่ม class 'success' เข้าไปใน <tr> นี้ หาก id ของกระทู้ ตรงกับ $_GET['highlight_id']
			ซึ่งจะถูกส่งมาจาก post.php (ดู post.php)
			*/
			if ($item['id'] === $HIGHLIGHT_ID) {
				echo 'success';
			}
			?>">
				<td>
					<strong>
					<?php
					echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8');
					?>
					</strong>
					<br>
					<small class="text-muted" title="<?php
					/*
					แปลงวันที่ให้เป็นภาษาไทยด้วยฟังก์ชั่น thai_datetime() ที่กำหนดไว้ใน inc/main.inc.php
					โดยใส่ไว้ใน attribute title เพื่อให้แสดงขึ้นมาเมื่อผู้ใช้เอาเมาส์ไปชี้
					*/
					echo thai_datetime($item['created']);
					?>">
						<?php
						/*
						แปลงวันที่ให้เป็นช่วงห่างของเวลาภาษาไทยด้วยฟังก์ชั่น thai_time()
						ที่กำหนดไว้ใน inc/main.inc.php
						*/
						echo thai_time($item['created']);
						?>
					</small>
				</td>
				<td>
					<a href="view.php?topic_id=<?php echo $item['id']; ?>">
					<?php
					echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8');
					?>
					</a>
				</td>
				<td class="text-center text-info">
					<?php
					echo $item['num_comments'];
					?>
				</td>
				<td>
					<?php
					/*
					หาก $item['last_commented_name'] ไม่ใช่ค่าว่าง นั่นหมายถึงกระทู้นี้มีผู้แสดงความเห็น
					ก็ให้แสดงชื่อผู้แสดงความเห็น
					*/
					if ($item['last_commented_name'] !== ''):
					?>
					<strong class="text-info">
						<?php
						echo htmlspecialchars($item['last_commented_name'], ENT_QUOTES, 'UTF-8');
						?>
					</strong>
					<br>
					<small class="text-muted" title="<?php
					echo thai_datetime($item['last_commented']);
					?>">
						<?php
						echo thai_time($item['last_commented']);
						?>
					</small>
					<?php
					endif;
					?>
				</td>
				<?php if(isset($_SESSION["fullname"])){?>
				<td>
				<a href="edit_post.php?id=<?php echo $item['id']; ?>" class="btn btn-warning"><span class="glyphicon glyphicon-pencil"></span></a>
				<a href="del_post.php?id=<?php echo $item['id']; ?>"  onclick="return confirm('คุณต้องการลบข้อมูลนี้หรือไม่? !!!')" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span></a>
				</td>
				<?php }?>
			</tr>
			<?php
			endforeach;
			/********** จบ LOOP แสดงกระทู้ **********/
			?>
		</tbody>
	</table>
	<?php
	/*
	หากจำนวนหน้ามากกว่า 1 เราจะสร้าง pagination
	*/
	if ($NUM_PAGES > 1):
	?>
	<div class="panel-footer text-center">
		<ul class="pagination">
			<?php
			/********** เริ่ม LOOP แสดงหน้าของ pagination **********/
			/*
			โดยให้ $i เริ่มจาก 1 ไปถึงจำนวนหน้าซึ่งคือ $NUM_PAGES
			และหาก $i เท่ากับ $PAGE ที่เป็นหมายเลขหน้าปัจจุบัน
			เราก็จะเพิ่ม class 'active' เข้าไปใน <li> เพื่อให้เน้นว่าเป็นหน้าปัจจุบัน
			ใน href ของ <a> จะกำหนด query string ได้แก่
			page เพื่อส่งต่อไปเป็นค่าใน array $_GET ใน index.php
			ซึ่งค่าเหล่านี้จะทำให้ index.php รู้ว่าจะต้อง SELECT ข้อมูลจากตาราง topic
			โดยเริ่มจาก offset ใด
			*/
			for ($page = 1; $page <= $NUM_PAGES; ++$page):
			?>
			<li class="<?php
			if ($page === $PAGE) {
				echo 'active';
			}
			?>">
				<a href="index.php?page=<?php echo $page; ?>">
					<?php echo $page; ?>
				</a>
			</li>
			<?php
			endfor;
			/********** จบ LOOP แสดงหน้าของ pagination **********/
			?>
		</ul>
	</div>
	<?php
	endif;
	?>
</div>
