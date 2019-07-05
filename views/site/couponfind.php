<?php
use yii\helpers\Html;
$db = new yii\db\Connection([
    'dsn' => 'mysql:host=127.0.0.1;dbname=couponstores',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
]);

Yii::$app->db;

include "simple html/simple_html_dom.php";

$html = file_get_html('https://www.coupons.com/store-loyalty-card-coupons/');
echo'<table border="1">';

foreach($html->find('.store-pod') as $store) {
	$db->createCommand()->insert('store', [
		'store_name' => $store->find('div')[2]->find('div')[0]->innertext,
		'url' => "https://www.coupons.com{$store->href}",
	])->execute();
}

/*
foreach($html->find('.store-pod') as $store) {
	echo '<tr>';
	echo '<td>'.$store->find('div')[2]->find('div')[0].' </td>';
	echo'<td>https://www.coupons.com'. $store->href. '</td>';
	echo'</tr>';
}*/

echo '</table>';