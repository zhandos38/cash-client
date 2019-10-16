<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p><?= Yii::$app->user->identity->username ?></p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => [
                    ['label' => 'Разделы', 'options' => ['class' => 'header']],
                    ['label' => 'Пользователи', 'icon' => 'fas fa-user', 'url' => ['/user/index']],
                    ['label' => 'Компании', 'icon' => 'fas fa-cubes', 'url' => ['/company/index']],
                    ['label' => 'Склады', 'icon' => 'fas fa-home', 'url' => ['/warehouse/index']],
                    [
                        'label' => 'Штрихкоды',
                        'icon' => 'share',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Штрихкоды', 'icon' => 'file-code-o', 'url' => ['/barcode'],],
                            ['label' => 'Временный штрихкоды', 'icon' => 'file-code-o', 'url' => ['/barcode-temp'],],
                        ],
                    ],
                    ['label' => 'Debug', 'icon' => 'dashboard', 'url' => ['/debug']],
                    ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                    [
                        'label' => 'Rbac',
                        'icon' => 'share',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Roles', 'icon' => 'file-code-o', 'url' => ['/rbac/role'],],
                            ['label' => 'Permissions', 'icon' => 'file-code-o', 'url' => ['/rbac/permission'],],
                            ['label' => 'Rule', 'icon' => 'file-code-o', 'url' => ['/rbac/rule'],],
                            ['label' => 'Assigment', 'icon' => 'file-code-o', 'url' => ['/rbac/assignment'],],
                            [
                                'label' => 'Level One',
                                'icon' => 'circle-o',
                                'url' => '#',
                                'items' => [
                                    ['label' => 'Level Two', 'icon' => 'circle-o', 'url' => '#',],
                                    [
                                        'label' => 'Level Two',
                                        'icon' => 'circle-o',
                                        'url' => '#',
                                        'items' => [
                                            ['label' => 'Level Three', 'icon' => 'circle-o', 'url' => '#',],
                                            ['label' => 'Level Three', 'icon' => 'circle-o', 'url' => '#',],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        ) ?>

    </section>

</aside>
