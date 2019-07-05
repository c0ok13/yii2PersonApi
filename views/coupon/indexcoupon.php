<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
?>
<h1>Coupon</h1>
<table border = 1>
<tr> <td> Title </td> <td> Image URL </td> <td> text </td> <td> Expires</td>
<?php foreach ($coupons as $coupon): ?>
    <tr>
        <?php echo "<td>{$coupon->title} </td><td>(<a href=\"{$coupon->image_url}\"> {$coupon->image_url}</a>) </td><td> {$coupon->text}</td> <td> {$coupon->ending_date}</td>" ?>
    </tr>
<?php endforeach; ?>
</table>

<?= LinkPager::widget(['pagination' => $pagination]) ?>