<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
/**
 * Инициализатор RBAC выполняется в консоли php yii rbac/init
 */
class RbacController extends Controller {

    public function actionInit() {
        $auth = Yii::$app->authManager;

        $auth->removeAll(); //На всякий случай удаляем старые данные из БД...

        // Создадим роли админа и редактора новостей
        $admin = $auth->createRole('admin');
        $manager = $auth->createRole('manager');
        $director = $auth->createRole('director');
        $administrator = $auth->createRole('administrator');
        $cashier = $auth->createRole('cashier');

        // запишем их в БД
        $auth->add($admin);
        $auth->add($manager);
        $auth->add($director);
        $auth->add($administrator);
        $auth->add($cashier);

        // Создаем наше правило, которое позволит проверить автора новости
//        $authorRule = new \app\rbac\AuthorRule;

        // Запишем его в БД
//        $auth->add($authorRule);

        // Создаем разрешения. Например, просмотр админки viewAdminPage и редактирование новости updateNews
        $viewAdminPage = $auth->createPermission('viewAdminIndex');
        $viewAdminPage->description = 'Просмотр главной страницы админки';

        $updateNews = $auth->createPermission('updateNews');
        $updateNews->description = 'Редактирование новости';

        // Создадим еще новое разрешение «Редактирование собственной новости» и ассоциируем его с правилом AuthorRule
//        $updateOwnNews = $auth->createPermission('updateOwnNews');
//        $updateOwnNews->description = 'Редактирование собственной новости';

        // Указываем правило AuthorRule для разрешения updateOwnNews.
//        $updateOwnNews->ruleName = $authorRule->name;

        // Запишем все разрешения в БД
        $auth->add($viewAdminPage);
        $auth->add($updateNews);
//        $auth->add($updateOwnNews);

        // Теперь добавим наследования. Для роли editor мы добавим разрешение updateOwnNews (редактировать собственную новость),
        // а для админа добавим собственные разрешения viewAdminPage и updateNews (может смотреть админку и редактировать любую новость)

        // Роли «Редактор новостей» присваиваем разрешение «Редактирование собственной новости»
//        $auth->addChild($manager,$updateOwnNews);

        // админ имеет собственное разрешение - «Редактирование новости»
        $auth->addChild($admin, $updateNews);

        // Еще админ имеет собственное разрешение - «Просмотр админки»
        $auth->addChild($admin, $viewAdminPage);
    }
}