<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;
use app\models\Famousbirthdays;
use app\models\Surname;
use app\models\Name;

use app\models\Url;
use app\models\Wiki;
use Yii;
use GuzzleHttp\Client;
use Sunra\PhpSimple\HtmlDomParser;
use yii\console\Controller;

class Trend {
    public $trend;
    public $name= "";
    public $surname = "";
    public $wiki = "✘";
    public $bornGlorious = "✘";
    public $percent = 0;
    public $realPerson = 0;
}

class PersonController extends Controller
{

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
	const MAX_BATCH = 10;
	function actionIndex()
	{	
		// Print it!
		$db = Yii::$app->db;
		$db->createCommand()->truncateTable('url')->execute();
		$client = new Client();
		$res = $client->request('GET', 'https://www.behindthename.com/names/list');
        $html = HtmlDomParser::str_get_html($res->getBody(), false, null, 0);
        foreach($html->find('div.usagelist')[0]->find('li') as $category) {
            $category = new Url([
                'url' => "https://www.behindthename.com{$category->find('a')[0]->href}",
            ]);
            if(!$category->save()){
                echo 'Cant save store'. $category->category ;
            }
		}
	}

    public function singleNameParse($html){
        $db = Yii::$app->db;

        $count = 0;
        $names = array();
        foreach($html->find('div.browsename') as $name) {
            $text = $name->find('a')[0]->innertext;
            $text = explode(' ', $text);
            if(!$overlap = Name::findOne(['name' => $text[0]])){
                $singleName = new Name([
                    'name' => $text[0],
                ]);
                array_push($names, $singleName);
                $count++;
            }
            if($count == self::MAX_BATCH)
            {
                Yii::$app->db->createCommand()->batchInsert('name', Name::getAllAttributes(), $names)->execute();
                $names = array();
                $count = 0;
            }
        }
        if($count > 0)
        {
            Yii::$app->db->createCommand()->batchInsert('name', Name::getAllAttributes(), $names)->execute();

        }

    }

    function actionNames()
    {
        Yii::$app->db;
        $urls= Url::find()->indexBy('id_url')->all();
        foreach ($urls as $url){
            $client = new Client();
            $i = 1;
            while($i > 0){
                $res = $client->request('GET', "{$url->url}/{$i}");
                $html = HtmlDomParser::str_get_html($res->getBody(), false, null, 0);
                echo count($html->find('.browsename')). ' ' ;
                if (count($html->find('.browsename')) > 0){
                    $this->singleNameParse($html);
                    $i++;
                } else {
                    $i = 0;
                }
            }

        }

    }


    public function singleSurNameParse($html){
        $db = Yii::$app->db;

        $count = 0;
        $surnames = array();
        foreach($html->find('li') as $surname) {
            $text = $surname->find('a')[0]->innertext;
            if(!$overlap = Surname::findOne(['surname' => $text])){
                $singleSurName = new Surname([
                    'surname' => $text,
                ]);
                array_push($surnames, $singleSurName);
                $count++;
            }
            if($count == self::MAX_BATCH)
            {
                Yii::$app->db->createCommand()->batchInsert('Surname', Surname::getAllAttributes(), $surnames)->execute();
                $surnames = array();
                $count = 0;
            }
        }
        if($count > 0)
        {
            Yii::$app->db->createCommand()->batchInsert('Surname', Surname::getAllAttributes(), $surnames)->execute();

        }

    }

    function actionSurnames()
    {
        Yii::$app->db;
        $client = new Client();
        $i = 1;
        while($i <= 50){
            $res = $client->request('GET', "https://en.geneanet.org/genealogy/?page={$i}");
            $html = HtmlDomParser::str_get_html($res->getBody(), false, null, 0);
            $this->singleSurNameParse($html->find('.display-cols')[0]);
            $i++;
        }
    }


    public function singleFamousParse($html){
        Yii::$app->db;

        $count = 0;
        $famoouses = array();
        echo count($html->find('div.data-container')). ' ' ;
        foreach($html->find('div.data-container') as $famous) {
            $text = mb_strtolower($famous->find('a.name-link')[0]->innertext);
            if($text != NULL){
                if(!$overlap = Famousbirthdays::findOne(['full_name' => $text])){
                    $singleFamous = new Famousbirthdays([
                        'full_name' => $text,
                    ]);
                    array_push($famoouses, $singleFamous);
                    $count++;
                }
                if($count == self::MAX_BATCH)
                {
                    Yii::$app->db->createCommand()->batchInsert('famousbirthdays', Famousbirthdays::getAllAttributes(), $famoouses)->execute();
                    $famoouses = array();
                    $count = 0;
                }
            }

        }
        if($count > 0)
        {
            Yii::$app->db->createCommand()->batchInsert('famousbirthdays', Famousbirthdays::getAllAttributes(), $famoouses)->execute();

        }

    }

    function actionFamous()
    {
        Yii::$app->db;
        $client = new Client();
        $i = 1;
        while($i <= 12){
            if ($i < 10) {
                $ind = '0'.$i;
            } else {
                $ind = $i;
            }
            $res = $client->request('GET', "http://www.bornglorious.com/birthday/?ct=world&pd={$ind}");
            $html = HtmlDomParser::str_get_html($res->getBody(), false, null, 0);
            //echo $html."\n";
            $list= explode(' ', ($html->find('div.index')[0]->find('p')[0]->innertext()));
            //  print_r($list);
            for($page = 1; $page < $list[136]; $page++){
                $res = $client->request('GET', "http://www.bornglorious.com/birthday/?ct=world&pd={$ind}&pg={$page}");

                echo "http://www.bornglorious.com/birthday/?ct=world&pd={$ind}&pg={$page}\n";
                $html = HtmlDomParser::str_get_html($res->getBody(), false, null, 0);
                $this->singleFamousParse($html->find('.inner-content')[0]);
            }
            $i++;
        }
    }

    public function singleWikiParse($html){
        Yii::$app->db;

        $count = 0;
        $wikis= array();
        echo 'сheck ';
        echo count($html->find('li')). ' ' ;

        foreach($html->find('li') as $fwiki) {
           $text = mb_strtolower($fwiki->find('a')[0]->innertext);
            if($text != NULL){
                if(!$overlap = Wiki::findOne(['wiki_name' => $text])){
                    $singleWiki = new Wiki([
                        'wiki_name' => $text,
                    ]);
                    array_push($wikis, $singleWiki);
                    $count++;
                }
                if($count == self::MAX_BATCH)
                {
                    Yii::$app->db->createCommand()->batchInsert('wiki', Wiki::getAllAttributes(), $wikis)->execute();
                    $wikis = array();
                    $count = 0;
                }
            }

        }
        if($count > 0)
        {
            Yii::$app->db->createCommand()->batchInsert('wiki', Wiki::getAllAttributes(), $wikis)->execute();

        }

    }

    function actionWiki()
    {
        Yii::$app->db;
        $client = new Client();
        $year = 1900;
        while($year < 2019)
        {
            $res = $client->request('GET', "https://en.wikipedia.org/w/index.php?title=Category:{$year}_births");
            $html = HtmlDomParser::file_get_html("https://en.wikipedia.org/w/index.php?title=Category:{$year}_births", false, null, 0);
            $haveNextPage = true;
            $pageUrl = $html->find('div#mw-pages')[0]->find('a')[1]->href;
            while($haveNextPage){
                $pageUrl = str_replace('&amp;','&', $pageUrl);
                if((strpos($pageUrl, "Category" )) == false){
                    $haveNextPage = false;
                    echo 'geted';
                    echo 'end year'."\n";
                } else {

                    $html = HtmlDomParser::file_get_html("https://en.wikipedia.org{$pageUrl}", false, null, 0);
                    $pageUrl = $html->find('div#mw-pages')[0]->find('a')[2]->href;
                }
                $this->singleWikiParse($html->find('div#mw-pages')[0]);


            }
            $year++;
        }
    }

    function actionShowpersons()
    {
        $start = microtime(true);
        $handle = fopen( dirname(__FILE__)."\csv\\table.csv", "r");
        $countWiki = 0;
        $countBorn = 0;
        $countFamous = 0;
        $countCombo = 0;
        if ($handle) {
            while ((($line = fgetcsv($handle, 1000, ",")) !== false)) {
                $trend = new Trend();
                $person = explode(' ', strtolower($line[0]));
                $trend->trend = strtolower($line[0]);
                $persMatches = 0;
                $percentage = 0;
                $deleted = false;
                if ((count($person) > 1) && (count($person) <= 3)) {
                    if ($name = Name::find()->where(['name' => $person])->one()) {
                        $persMatches++;
                        $key = array_search(strtolower($name->name), $person);
                        $delName = $person[$key];
                        unset($person[$key]);
                        $deleted = true;
                        $trend->name = $name->name;
                    }
                    if ($surname = Surname::findOne(['surname' => $person])) {
                        $persMatches++;
                        $trend->surname = $surname->surname;
                    }
                    if ($deleted) {
                        array_unshift($person, $delName);

                    }
                    $percentage = round($persMatches / count($person), 2) * 100;


                    //parse from wikipedia
                    $accuracy = 0;
                    if ($wikiData = Wiki::findOne(['wiki_name' => $trend->trend])) {
                        $trend->wiki = '✔';
                        $accuracy++;
                        $countWiki++;
                    }

                    //parse from bornglorious=
                    if (($accuracy == 0) && ($bornGlorData = Famousbirthdays::findOne(['full_name' => $trend->trend]))) {
                        $trend->bornGlorious = '✔';
                        $accuracy++;
                        $countBorn++;
                    }

                    //parse famousbirthdays
                    if (($accuracy == 0) && ($percentage >= 50)){
                        $url = str_replace(' ', '-', $trend->trend);
                        $client = new Client(['http_errors' => false]);
                        $res = $client->request('GET', "https://www.famousbirthdays.com/people/{$url}.html");
                        if($res->getStatusCode() == 200)
                        {
                            $countFamous++;
                            $accuracy++;
                        }
                    }

                    $trend->percent = $percentage;
                    if ($accuracy > 0) {
                        $trend->realPerson = "real";
                        $trend->percent = 100;
                        echo $trend->trend . " Person\n";
                    } elseif ($percentage == 100) {
                        $trend->percent = 90;
                        $countCombo++;
                        echo $trend->trend . " Maybe Person\n";
                    }

                }
            }
        }
        echo "Script time: ".(microtime(true) - $start)."\n";
        echo "Founded \nFrom Wikipedia: {$countWiki}\nFrom BornGlorious: {$countBorn}\nFrom FamousBirthdays: {$countFamous}\nCombinations: {$countCombo}\n";
    }

}

