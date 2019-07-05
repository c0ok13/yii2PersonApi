<?php

include "simple html/simple_html_dom.php";

$html = file_get_html('https://www.coupons.com/store-loyalty-card-coupons/');
echo'<table>';
foreach($html->find('.store-pod') as $store) {
	echo"<tr> $store</tr>";
}
echo '</table>';