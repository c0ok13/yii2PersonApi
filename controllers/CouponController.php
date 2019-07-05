<?php

namespace app\controllers;

use yii\web\Controller;
use yii\data\Pagination;
use app\models\Store;
use app\models\Coupon;

class CouponController extends Controller
{
	
    public function actionParce()
    {
		return $this->render('parse');
	}

	public function actionIndex()
    {

        $query = Store::find();

        $pagination = new Pagination([
            'defaultPageSize' => 20,
            'totalCount' => $query->count(),
        ]);

        $stores = $query->orderBy('store_name')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->render('index', [
            'stores' => $stores,
            'pagination' => $pagination,
        ]);
    }

    public function actionIndexcoupon()
    {
        $query = Coupon::find();

        $pagination = new Pagination([
            'defaultPageSize' => 5,
            'totalCount' => $query->count(),
        ]);

        $coupons = $query->orderBy('id_coupon')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->render('indexcoupon', [
            'coupons' => $coupons,
            'pagination' => $pagination,
        ]);
    }
}