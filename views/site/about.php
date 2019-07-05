<?php
use app\models\Store;
$db = new yii\db\Connection([
    'dsn' => 'mysql:host=127.0.0.1;dbname=couponstores',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
]);

$db = Store::find();
require "simple html/simple_html_dom.php";
$html = file_get_html('https://www.groupon.co.uk/discount-codes/abbott-lyon');
$largeCoupon = count($html->find('.coupon-uber'));

foreach($html->find('.coupons-list-row') as $coupon) {
	$largeCoupon = count($coupon->find('.coupon-uber'));

	if($largeCoupon == 1){
		$date = strtotime(str_replace('/', '-', $coupon->find('span.coupon-uber-expiry')[0]->find(span)[1]->innertext));
		$db->createCommand()->insert('harvestervouchers', [
		'title' => $coupon->find('p.coupon-uber-title')[0]->innertext,
		'image_url'	=> $coupon->find('img.coupon-uber-media-image', 0)->src,
		'text' => $coupon->find('p.coupon-tile-description')[0]->innertext,
		'ending_date' => date("Y-m-d", $date),
		])->execute();
	} else {
		$date = strtotime(str_replace('/', '-', $coupon->find('span.coupon-tile-expiry')[0]->find(span)[1]->innertext));
		$db->createCommand()->insert('harvestervouchers', [
		'title' => $coupon->find('p.coupon-tile-title')[0]->innertext,
		'image_url'	=> $coupon->find('img.coupon-tile-callout-image')[0]->src,
		'text' => $coupon->find('p.coupon-tile-description')[0]->innertext,
		'ending_date' => date("Y-m-d", $date),
		])->execute();
	}
	
}
/*
echo '<table>';
foreach($html->find('.coupons-list-row') as $coupon) {
	$strArray = explode(' ',$coupon->find('span.coupon-tile-expiry')[0]->innertext);
	echo "<tr>";
	echo '<td>'.$coupon->find('p.coupon-tile-title')[0]->innertext.' </td>';
	echo '<td>'.$coupon->find('img.coupon-tile-callout-image')[0]->src.' </td>';
	echo '<td>'.$coupon->find('p.coupon-tile-description')[0]->innertext.' </td>';
	echo '<td>'. $strArray[1].'</td>';
	echo'</tr>';
}
*/
echo '</table>';
