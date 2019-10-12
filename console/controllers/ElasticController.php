<?php

namespace console\controllers;


use common\models\Product;
use common\models\es\Product as ElasticProduct;
use yii\console\Controller;
use yii\helpers\Console;
use yii\console\Exception;

/**
 * Class ElasticController
 * @package console\controllers
 */
class ElasticController extends Controller
{
    public function actionCreateIndex()
    {
        ElasticProduct::createIndex();
        $this->log(true);
    }

    public function actionDeleteIndex()
    {
        ElasticProduct::deleteIndex();
        $this->log(true);
    }

    public function actionAddProducts()
    {
        $products = Product::find()->all();

        try {
            $counter = 0;
            foreach ($products as $product) {
                /** @var ElasticProduct $esProduct */
                $esProduct = new ElasticProduct();
                $esProduct->id = $product->id;
                $esProduct->name = $product->name;
                $esProduct->barcode = $product->barcode;
                $this->log($esProduct->save(), $counter++);
            }
        } catch (Exception $e) {
            $this->log(false);
        }
    }

    public function actionAddProductById()
    {
        $id = $this->prompt('Id:', ['required' => true]);
        $product = $this->findProductById($id);

        $esProduct = new ElasticProduct();
        $esProduct->primaryKey = $product->id;
        $esProduct->id = $product->id;
        $esProduct->name = $product->name;
        $esProduct->barcode = $product->barcode;
        $this->log($esProduct->save());
    }

    public function actionDeleteProductById()
    {
        $id = $this->prompt('Id:', ['required' => true]);

        if ($esProduct = ElasticProduct::find()->andWhere(['id' => $id])->one()) {
            $this->log($esProduct->delete());
        } else {
            throw new Exception('Product not found');
        }


    }

    /**
     * @param $id
     * @return Product|null
     * @throws Exception
     */
    private function findProductById($id)
    {
        if (!$model = Product::findOne(['id' => $id])) {
            throw new Exception('Product not found');
        }
        return $model;
    }

    private function log($success, $counter = 0)
    {
        if ($success) {
            $this->stdout('Success!' . $counter, Console::FG_GREEN, Console::BOLD);
        } else {
            $this->stderr('Error!', Console::FG_RED, Console::BOLD);
        }
        echo PHP_EOL;
    }
}