<?php

include "C:\opensewrver\OSPanel\domains\localhost\basic\widgets\simple html\simple_html_dom.php";

$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "couponstores";
$conn = new mysqli($servername, $username, $password, $dbname);

$sql = "DELETE FROM `coupon` WHERE ending_date < CONCAT(CURRENT_DATE(), ' 00:00:00')";
mysqli_query($conn, $sql);
$inputFile = file('C:\opensewrver\OSPanel\domains\localhost\basic\cron\input_url.txt',FILE_IGNORE_NEW_LINES );
foreach($inputFile as $url) {
	$html = file_get_html($url);
	foreach($html->find('.coupons-list-row') as $coupon) {
	$largeCoupon = count($coupon->find('.coupon-uber'));
	if($largeCoupon == 1){
		$date = date("Y-m-d", strtotime(str_replace('/', '-', $coupon->find('span.coupon-uber-expiry')[0]->find(span)[1]->innertext)));
		$title = $coupon->find('p.coupon-uber-title')[0]->innertext;
		$image_url =$coupon->find('img.coupon-uber-media-image', 0)->src;
		$text = $coupon->find('p.coupon-tile-description')[0]->innertext;
		$ending_date = $date;
	} else {
		
		$date = date("Y-m-d", strtotime(str_replace('/', '-', $coupon->find('span.coupon-tile-expiry')[0]->find(span)[1]->innertext)));
		$title = $coupon->find('p.coupon-tile-title')[0]->innertext;
		$image_url = $coupon->find('img.coupon-tile-callout-image')[0]->src;
		$text = $coupon->find('p.coupon-tile-description')[0]->innertext;
		$ending_date = $date;
	}
		
		$sql = "INSERT INTO coupon (image_url, title, text, ending_date)
				SELECT * FROM (SELECT '$image_url', '$title', '$text', '$ending_date') AS tmp
				WHERE NOT EXISTS (
					SELECT title FROM coupon WHERE title = '$title'
				) LIMIT 1
					";
		mysqli_query($conn, $sql);
	}
}
$conn->close();
