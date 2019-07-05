<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
include 'parceStores.php';
use app\models\Coupon;
use app\models\Store;
?>
<h1>Stores</h1>
<ul>

<?php foreach ($stores as $store): ?>
    <li>
        <?php echo "{$store->store_name} (<a href=\"{$store->url})\"> {$store->url}</a>)" ?>:
    </li>
<?php endforeach; ?>
</ul>
<a href='index.php?r=coupon%2Findex&parsed=1'>Parce Stores</a>
<?php	

	?>
<?= LinkPager::widget(['pagination' => $pagination]) ?>