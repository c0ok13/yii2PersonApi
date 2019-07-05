<?php

use Sunra\PhpSimple\HtmlDomParser;
use yii\helpers\Html;

$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "couponstores";
$conn = new mysqli($servername, $username, $password, $dbname);
$html = HtmlDomParser::file_get_html('https://www.coupons.com/store-loyalty-card-coupons/', false, null, 0);
foreach($html->find('.store-pod') as $store) {	
		$url =	"https://www.coupons.com". $store->href;
		$name = $store->find('div')[2]->find('div')[0]->innertext;
		$sql = "INSERT INTO store (store_name, url)
				VALUES
					('$name', 
					'$url')
					";
		mysqli_query($conn, $sql);
}	