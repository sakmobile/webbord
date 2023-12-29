phpinfo.in.th Webboard Workshop
===============================

### ตัวอย่างการสร้าง Webboard ด้วย PHP + MySQLi และ Bootstrap

ตัวอย่างนี้เหมาะสำหรับผู้ที่อยากจะเริ่มต้นเขียน PHP ติดต่อกับ MySQL แต่อยากได้ตัวอย่างโค้ดที่มี comment อธิบายโดยละเอียด แต่ไม่อยากให้เอาไปต่อยอดทันทีนะครับ อยากให้พยายามอ่าน comment จนเข้าใจมากกว่า

ซึ่งตัวอย่างนี้ก็ใช้วิธีการเขียนแบบที่คนส่วนใหญ่คุ้นเคย คือเป็นแบบ procedural ไม่ได้ใช้ class เพราะหลายคนบอกว่า OOP มันยากเกินไปสำหรับผู้เริ่มต้น หรือยังไม่พร้อมที่จะศึกษา

ซึ่งจริงๆ จุดประสงค์ของตัวอย่างนี้คือต้องการให้แนวคิดเริ่มต้นเกี่ยวการเขียน PHP web application แบบแยกส่วนการคำนวณออกจากส่วนแสดงผล และเน้นพื้นฐานการเขียน PHP ที่ถูกต้องและเน้นเรื่องความปลอดภัย หรือเทคนิคอื่นๆ เช่น

* การกำหนดค่าของตัวแปรก่อนใช้งาน
* การตรวจสอบการมีอยู่ของตัวแปรก่อนเข้าถึงด้วย `isset()` และ `empty()`
* การตรวจสอบความถูกต้องของข้อมูลก่อน `INSERT` [Data validation](http://en.wikipedia.org/wiki/Data_validation)
* การใช้ `mysqli::escape_string()` เพื่อป้องกัน [SQL Injection](http://en.wikipedia.org/wiki/SQL_injection)
* การใช้ตัวแปรที่อยู่ใน double quote string `"` เพื่อแทนที่ค่าใน string ([String interpolation](http://en.wikipedia.org/wiki/String_interpolation)) แทนการใช้ concat operator `.`
* การกำหนด id ให้ element เพื่อใช้ร่วมกับ hash tag ใน URL มีผลให้ browser scroll มายังตำแหน่งที่ต้องการเมื่อ page load

และตั้งใจให้เป็นตัวอย่างพื้นฐานเพื่อเปรียบเทียบกับตัวอย่างต่อไปที่ตั้งใจจะทำโดยใช้โค้ดเดิมนี่แหละ แต่จะทำให้อยู่ในรูปแบบ OOP จะได้สามารถพิจารณากันได้ว่าถ้าใช้ OOP แล้วมันจะดีกว่าอย่างไร

แต่ก็ใช่ว่าตัวอย่างนี้จะไม่ใช้ object เลยเสียทีเดียว เพราะใช้ฟังก์ชั่น mysqli ในแบบ OOP เหตุผลมีอธิบายอยู่ใน Source Code ครับ

โครงสร้างฐานข้อมูลอาจจะเห็นมีบางอย่างที่ยังไม่ได้ใช้งานในตัวอย่างนี้ เช่น `user_id` ซึ่งตั้งใจดีไซน์เผื่อไว้สำหรับระบบสมาชิกครับ

---

#### language construct ที่ใช้ในตัวอย่างนี้
* [$_GET](http://php.net/manual/en/reserved.variables.get.php)
* [$_POST](http://php.net/manual/en/reserved.variables.post.php)
* [$_SERVER](http://php.net/manual/en/reserved.variables.server.php)
* [(int)](http://php.net/manual/en/language.types.integer.php#language.types.integer.casting)
* [array()](http://php.net/manual/en/function.array.php)
* [echo](http://php.net/manual/en/function.echo.php)
* [else](http://php.net/manual/en/control-structures.else.php)
* [elseif](http://php.net/manual/en/control-structures.elseif.php)
* [empty()](http://php.net/manual/en/function.empty.php)
* [exit()](http://php.net/manual/en/function.exit.php)
* [for](http://php.net/manual/en/control-structures.for.php)
* [foreach](http://php.net/manual/en/control-structures.foreach.php)
* [function](http://php.net/manual/en/functions.user-defined.php)
* [if](http://php.net/manual/en/control-structures.if.php)
* [isset()](http://php.net/manual/en/function.isset.php)
* [new](http://php.net/manual/en/language.oop5.basic.php#language.oop5.basic.new)
* [require](http://php.net/manual/en/function.require.php)
* [return](http://php.net/manual/en/functions.returning-values.php)
* [static](http://php.net/manual/en/language.variables.scope.php#language.variables.scope.static)
* [while](http://php.net/manual/en/control-structures.while.php)

#### ฟังก์ชั่นที่ใช้ในตัวอย่างนี้
* [current()](http://php.net/manual/en/function.current.php)
* [get_magic_quotes_gpc()](http://php.net/manual/en/function.get-magic-quotes-gpc.php)
* [getdate()](http://php.net/manual/en/function.getdate.php)
* [header()](http://php.net/manual/en/function.header.php)
* [htmlspecialchars()](http://php.net/manual/en/function.htmlspecialchars.php)
* [ini_get()](http://php.net/manual/en/function.ini-get.php)
* [is_numeric()](http://php.net/manual/en/function.is-numeric.php)
* [mb_strlen()](http://php.net/manual/en/function.mb-strlen.php)
* [microtime()](http://php.net/manual/en/function.microtime.php)
* [number_format()](http://php.net/manual/en/function.number-format.php)
* [ob_get_flush()](http://php.net/manual/en/function.ob-get-flush.php)
* [ob_start()](http://php.net/manual/en/function.ob-start.php)
* [pathinfo()](http://php.net/manual/en/function.pathinfo.php)
* [sprintf()](http://php.net/manual/en/function.sprintf.php)
* [strtotime()](http://php.net/manual/en/function.strtotime.php)
* [substr()](http://php.net/manual/en/function.substr.php)
* [time()](http://php.net/manual/en/function.time.php)
* [trim()](http://php.net/manual/en/function.trim.php)

#### property/method ของ class ที่ใช้ในตัวอย่างนี้
* [mysqli::__construct()](http://php.net/manual/en/mysqli.construct.php)
* [mysqli::$connect_errno](http://php.net/manual/en/mysqli.connect-errno.php)
* [mysqli::$connect_error](http://php.net/manual/en/mysqli.connect-error.php)
* [mysqli::$insert_id](http://php.net/manual/en/mysqli.insert-id.php)
* [mysqli::connect()](http://php.net/manual/en/mysqli.connect.php)
* [mysqli::escape_string()](http://php.net/manual/en/mysqli.escape-string.php)
* [mysqli::query()](http://php.net/manual/en/mysqli.query.php)
* [mysqli_result::fetch_assoc()](http://php.net/manual/en/mysqli-result.fetch-assoc.php)
* [mysqli_result::fetch_row()](http://php.net/manual/en/mysqli-result.fetch-row.php)
* [mysqli_result::free()](http://php.net/manual/en/mysqli-result.free.php)
