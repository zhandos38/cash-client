<?php

namespace frontend\widgets;

use yii\base\Widget;

class NavWidget extends Widget
{
    public function run()
    {
        return $this->render('navigator',[

        ]);
    }
}