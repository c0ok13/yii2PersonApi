<?php
use Sunra\PhpSimple\HtmlDomParser;
use app\models\Store;
use GuzzleHttp\Client;
function runMyFunction() {
    $client = new Client();
    $res = $client->request('GET', 'https://www.coupons.com/store-loyalty-card-coupons/');
    $html = HtmlDomParser::str_get_html($res->getBody(), false, null, 0);
	foreach($html->find('.store-pod') as $store) {
	    $store = new Store([
	        'store_name' =>   $store->find('div')[2]->find('div')[0]->innertext,
	        'url' => "https://www.coupons.com{$store->href}"
        ]);

        if(!$store->save()){
	        echo 'Cant save store'. $store->store_name ;
        }
	}
  }

  if (isset($_GET['parsed'])) {
    runMyFunction();
	header('Location: http://localhost/basic/web/index.php?r=coupon%2Findex ');
  }