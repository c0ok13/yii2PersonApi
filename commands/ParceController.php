<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;
use Yii;
use GuzzleHttp\Client;
use Sunra\PhpSimple\HtmlDomParser;
use yii\console\Controller;
use app\models\Category;
use app\models\Coupon;


class ParceController extends Controller
{

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
	const MAX_BATCH = 10;
	function actionIndex()
	{	
		// Print it!
		$db = Yii::$app->db;
		$db->createCommand()->truncateTable('category')->execute();
		$client = new Client();
		$res = $client->request('GET', 'https://www.groupon.co.uk/discount-codes/categories');
        $html = HtmlDomParser::str_get_html($res->getBody(), false, null, 0);
        foreach($html->find('.category-links-item') as $category) {
            $category = new Category([
                'category' => $category->find('a')[0]->innertext,
                'url' => "https://www.groupon.co.uk{$category->find('a')[0]->href}",
                'visited' => '0',
            ]);
            if(!$category->save()){
                echo 'Cant save store'. $category->category ;
            }
		}
	}

	public function actionOldcoupon(){
        $db = Yii::$app->db;
        $sqlDelete = "DELETE FROM {{coupon}} WHERE [[ending_date]] < CURRENT_DATE()";
        $command = Yii::$app->db->createCommand($sqlDelete)->execute();

    }


    public function singleCatParse($url){
        $db = Yii::$app->db;
        $html = HtmlDomParser::str_get_html($url, false, null, 0);

        $count = 0;
        $coupons = array();
        foreach($html->find('.coupons-list-row') as $coupon) {
            $largeCoupon = count($coupon->find('.coupon-uber'));
            if ($largeCoupon == 1) {
                $ending_date = date("Y-m-d", strtotime(str_replace('/', '-', $coupon->find('span.coupon-uber-expiry')[0]->find('span')[1]->innertext)));
                $title = $coupon->find('p.coupon-uber-title')[0]->innertext;
                $image_url = $coupon->find('img.coupon-uber-media-image', 0)->src;
                $text = $coupon->find('p.coupon-tile-description')[0]->innertext;
            } else {
                $ending_date = date("Y-m-d", strtotime(str_replace('/', '-', $coupon->find('span.coupon-tile-expiry')[0]->find('span')[1]->innertext)));
                $title = $coupon->find('p.coupon-tile-title')[0]->innertext;
                $image_url = $coupon->find('img.lazy-load')[0]->src;
                $text = $coupon->find('p.coupon-tile-description')[0]->innertext;
            }

            if($overlap = Coupon::findOne(['title' => $title])){
                $overlap->ending_date = $ending_date;
                $overlap->text = $text;
                $overlap->save();
            } else {
                $singleCoupon = new Coupon([
                    'image_url' => $image_url,
                    'title' => $title,
                    'text' => $text,
                    'ending_date' => $ending_date,
                    ]);
                array_push($coupons, $singleCoupon);
                $count++;
            }
            if($count == self::MAX_BATCH)
            {
                Yii::$app->db->createCommand()->batchInsert('coupon', Coupon::getAllAttributes(), $coupons)->execute();
                $coupons = array();
                $count = 0;
            }
        }

        if($count > 0)
        {
            Yii::$app->db->createCommand()->batchInsert('coupon', Coupon::getAllAttributes(), $coupons)->execute();

        }

    }

	function actionCategories()
    {
        $db = Yii::$app->db;
        $categories= Category::findAll(['visited' => 0,
            ]);
        foreach ($categories as $category){
            $client = new Client();
            $res = $client->request('GET', "{$category->url}");
            $this->singleCatParse($res->getBody());
            $category->visited = 1;
            $category->save();
            echo '111';
        }

        $categories= Category::findAll(['visited' => 1,
        ]);
        foreach ($categories as $category){
            $category->visited = 0;
            $category->save();

        }

     }

}

