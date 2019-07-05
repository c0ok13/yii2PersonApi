<?php
/* @var $this yii\web\View */

use GuzzleHttp\Client;
use Sunra\PhpSimple\HtmlDomParser;
use app\models\Surname;
use app\models\Name;
use app\models\BannedWord;
use app\models\Wiki;
use app\models\Famousbirthdays;

?>
<h1>person/index</h1>

<table border = 1>
    <tr> <td> Trend </td> <td> Surname Name Combo </td> <td> Wikipedia </td> <td> BornGlorious</td> <td> Percentage</td> <td> Real Person</td> </tr>

<?php
$handle = fopen(dirname(__FILE__)."/table.csv", "r");
set_time_limit(500);
// $realPerson: 0 - not person; 1 - maybe person; 2 - 100% real person
class Trend {
    public $trend;
    public $name= "";
    public $surname = "";
    public $wiki = "✘";
    public $bornGlorious = "✘";
    public $percent = 0;
    public $realPerson = 0;
}
if ($handle) {
    while ((($line = fgetcsv($handle, 1000, ",")) !== false)) {
        $trend = new Trend();
        $person = explode(' ', strtolower($line[0]));
        $trend->trend = strtolower($line[0]);
        $persMatches = 0;
        $percentage = 0;
        $deleted = false;
        if((count($person) > 1) && (count($person) <= 3))
        {
            if($name = Name::find()->where(['name' => $person])->one())
            {
                $persMatches++;
                $key = array_search(strtolower($name->name), $person);
                $delName = $person[$key];
                unset($person[$key]);
                $deleted = true;
                $trend->name= $name->name;
            }
            if($surname = Surname::findOne(['surname' => $person]))
            {
                $persMatches++;
                $trend->surname = $surname->surname;
            }
            if($deleted)
            {
                array_unshift($person, $delName);

            }


            //parse from wikipedia
            $accuracy = 0;
            $wikiData = strtolower(implode(' ', $person));
            if($wikiData = Wiki::findOne(['wiki_name' => $trend->trend ])){
                $trend->wiki = '✔';
                $accuracy++;
            }

            $bornGlorData = $wikiData;
            //parse from bornglorious
            if($bornGlorData = Famousbirthdays::findOne(['full_name' => $trend->trend])){
                $trend->bornGlorious = '✔';
                $accuracy++;
            }

            $percentage = round($persMatches / count($person), 2) * 100;
            $trend->percent = $percentage ;
            if($accuracy > 0){
                $trend->realPerson = "real";
                $trend->percent = 100;
            } elseif ($percentage == 100){
                $trend->percent = 90;
            }

        }
        echo "<tr> <td> {$trend->trend}</td> ";

        echo "<td>Surname:{$trend->surname}<br>  Name:{$trend->name}</td>";
            echo "<td align=\"center \"> {$trend->wiki} </td>";
            echo "<td align=\"center \"> {$trend->bornGlorious} </td>";
            echo "<td align=\"center \"> {$trend->percent}% </td>";
            echo "<td align=\"center \"> {$trend->realPerson} </td></tr>";

        echo '';
    }
}
/* $person[1] = strtoupper(substr($person[1], 0, 1)). substr($person[1], 1);
                $wikiClient = implode(' ', $person);
                $client = new Client();
                $res = $client->request('GET', "https://en.wikipedia.org//w/api.php?action=parse&format=json&page={$wikiClient}");
                $json = json_decode($res->getBody(), true);
                $jsonText = $json['parse']['text']['*'];
                if(strpos($jsonText,'infobox biography vcard')){

                    echo implode(' ', $person);
                    echo ' - name='. $person[0].'; surname='.$person[1].';&nbsp&nbsp&nbsp';

                    echo"IT'S JSON !!!!!!!!!!!!<br>";*/
?>
</table>

