<?php
use Sunra\PhpSimple\HtmlDomParser;

$html = HtmlDomParser::file_get_html('https://www.groupon.co.uk/discount-codes/categories', false, null, 0);
// Print it!
foreach($html->find('.store-pod') as $category) {
	echo $category->find('li.category-links-item')[0]->innertext; 
}