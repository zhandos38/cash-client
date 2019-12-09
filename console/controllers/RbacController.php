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
        $admin->description = 'Супер админ';
        $manager = $auth->createRole('manager');
        $manager->description = 'Менеджер';
        $director = $auth->createRole('director');
        $director->description = 'Директор';
        $administrator = $auth->createRole('administrator');
        $administrator->description = 'Администратор';
        $cashier = $auth->createRole('cashier');
        $cashier->description = 'Кассир';

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
//        $viewAdminPage = $auth->createPermission('viewAdmin');
//        $viewAdminPage->description = 'Просмотр главной страницы админки';

        // Backend
        /* User */
     /*   $manageUser = $auth->createPermission('manageUser');
        $manageUser->description = 'Просмотр списка пользователей';

        $createUser = $auth->createPermission('createUser');
        $createUser->description = 'Добавление пользователей';

        $viewUser = $auth->createPermission('viewUser');
        $viewUser->description = 'Просмотр пользователей';

        $updateUser = $auth->createPermission('updateUser');
        $updateUser->description = 'Редактирование пользователей';

        $deleteUser = $auth->createPermission('deleteUser');
        $deleteUser->description = 'Удаление пользователей';

        // News
        $updateNews = $auth->createPermission('updateNews');
        $updateNews->description = 'Редактирование новости';

        // Company
        $manageCompany = $auth->createPermission('manageCompany');
        $manageCompany->description = 'Просмотр списка компаний';

        $createCompany = $auth->createPermission('createCompany');
        $createCompany->description = 'Добавление компаний';

        $viewCompany = $auth->createPermission('viewCompany');
        $viewCompany->description = 'Просмотр списка компаний';

        $updateCompany = $auth->createPermission('updateCompany');
        $updateCompany->description = 'Редактирование компаний';

        $deleteCompany = $auth->createPermission('deleteCompany');
        $deleteCompany->description = 'Удаление компаний';

        // Barcode
        $manageBarcode = $auth->createPermission('manageBarcode');
        $manageBarcode->description = 'Просмотр списка штрихкодов';

        $createBarcode = $auth->createPermission('createBarcode');
        $createBarcode->description = 'Добавление штрихкодов';

        $viewBarcode = $auth->createPermission('viewBarcode');
        $viewBarcode->description = 'Просмотр штрихкодов';

        $updateBarcode = $auth->createPermission('updateBarcode');
        $updateBarcode->description = 'Редактирование штрихкодов';

        $deleteBarcode = $auth->createPermission('deleteBarcode');
        $deleteBarcode->description = 'Удаление штрихкодов';

        // Temp Barcode
        $manageTempBarcode = $auth->createPermission('manageTempBarcode');
        $manageTempBarcode->description = 'Просмотр списка временных штрихкодов';

        $createTempBarcode = $auth->createPermission('createTempBarcode');
        $createTempBarcode->description = 'Добавление временных штрихкодов';

        $viewTempBarcode = $auth->createPermission('viewTempBarcode');
        $viewTempBarcode->description = 'Просмотр временных штрихкодов';

        $updateTempBarcode = $auth->createPermission('updateTempBarcode');
        $updateTempBarcode->description = 'Редактирование временных штрихкодов';

        $deleteTempBarcode = $auth->createPermission('deleteTempBarcode');
        $deleteTempBarcode->description = 'Удаление временных штрихкодов';  */

        // Frontend
        // Staff
        $manageStaff = $auth->createPermission('manageStaff');
        $manageStaff->description = 'Просмотр списка пользователей';

        $createStaff = $auth->createPermission('createStaff');
        $createStaff->description = 'Добавление пользователей';

        $deleteStaff = $auth->createPermission('deleteStaff');
        $deleteStaff->description = 'Удаление пользователей';

        $updateStaff = $auth->createPermission('updateStaff');
        $updateStaff->description = 'Обновление пользователей';

        $permissionStaff = $auth->createPermission('permissionStaff');
        $permissionStaff->description = 'Разрешение пользователей';

        // Customer
        $manageCustomer = $auth->createPermission('manageCustomer');
        $manageCustomer->description = 'Просмотр списка клиентов';

        $createCustomer = $auth->createPermission('createCustomer');
        $createCustomer->description = 'Добавление клиентов';

        $deleteCustomer = $auth->createPermission('deleteCustomer');
        $deleteCustomer->description = 'Удаление клиентов';

        $updateCustomer = $auth->createPermission('updateCustomer');
        $updateCustomer->description = 'Обновление клиентов';

        $viewCustomer = $auth->createPermission('viewCustomer');
        $viewCustomer->description = 'Просмотр клиентов';

        // Склад
        $manageWarehouse = $auth->createPermission('manageWarehouse');
        $manageWarehouse->description = 'Просмотр списка клиентов';

        $createWarehouse = $auth->createPermission('createWarehouse');
        $createWarehouse->description = 'Добавление клиентов';

        $deleteWarehouse = $auth->createPermission('deleteWarehouse');
        $deleteWarehouse->description = 'Удаление клиентов';

        $updateWarehouse = $auth->createPermission('updateWarehouse');
        $updateWarehouse->description = 'Обновление клиентов';

        $viewWarehouse = $auth->createPermission('viewWarehouse');
        $viewWarehouse->description = 'Просмотр клиентов';

        // Заказы
        $manageOrder = $auth->createPermission('manageOrder');
        $manageOrder->description = 'Просмотр списка заказов';

        $createOrder = $auth->createPermission('createOrder');
        $createOrder->description = 'Добавление заказов';

        $deleteOrder = $auth->createPermission('deleteOrder');
        $deleteOrder->description = 'Удаление заказов';

        $updateOrder = $auth->createPermission('updateOrder');
        $updateOrder->description = 'Обновление заказов';

        $viewOrder = $auth->createPermission('viewOrder');
        $viewOrder->description = 'Просмотр заказов';

        // Накладные
        $manageInvoice = $auth->createPermission('manageInvoice');
        $manageInvoice->description = 'Просмотр списка накладных';

        $createInvoice = $auth->createPermission('createInvoice');
        $createInvoice->description = 'Добавление накладных';

        $deleteInvoice = $auth->createPermission('deleteInvoice');
        $deleteInvoice->description = 'Удаление накладных';

        $updateInvoice = $auth->createPermission('updateInvoice');
        $updateInvoice->description = 'Обновление накладных';

        $viewInvoice = $auth->createPermission('viewInvoice');
        $viewInvoice->description = 'Просмотр накладных';

        // Поставщики
        $manageSupplier = $auth->createPermission('manageSupplier');
        $manageSupplier->description = 'Просмотр списка поставщиков';

        $createSupplier = $auth->createPermission('createSupplier');
        $createSupplier->description = 'Добавление поставщиков';

        $deleteSupplier = $auth->createPermission('deleteSupplier');
        $deleteSupplier->description = 'Удаление поставщиков';

        $updateSupplier = $auth->createPermission('updateSupplier');
        $updateSupplier->description = 'Обновление поставщиков';

        $viewSupplier = $auth->createPermission('viewSupplier');
        $viewSupplier->description = 'Просмотр поставщиков';

        // Создадим еще новое разрешение «Редактирование собственной новости» и ассоциируем его с правилом AuthorRule
//        $updateOwnNews = $auth->createPermission('updateOwnNews');
//        $updateOwnNews->description = 'Редактирование собственной новости';

        // Указываем правило AuthorRule для разрешения updateOwnNews.
//        $updateOwnNews->ruleName = $authorRule->name;

        // Запишем все разрешения в БД
        /*$auth->add($viewAdminPage);

        $auth->add($updateNews);

        $auth->add($manageUser);
        $auth->add($createUser);
        $auth->add($viewUser);
        $auth->add($updateUser);
        $auth->add($deleteUser);

        $auth->add($manageCompany);
        $auth->add($createCompany);
        $auth->add($viewCompany);
        $auth->add($updateCompany);
        $auth->add($deleteCompany);

        $auth->add($manageBarcode);
        $auth->add($createBarcode);
        $auth->add($viewBarcode);
        $auth->add($updateBarcode);
        $auth->add($deleteBarcode);

        $auth->add($manageTempBarcode);
        $auth->add($createTempBarcode);
        $auth->add($viewTempBarcode);
        $auth->add($updateTempBarcode);
        $auth->add($deleteTempBarcode); */

        $auth->add($manageStaff);
        $auth->add($createStaff);
        $auth->add($permissionStaff);
        $auth->add($updateStaff);
        $auth->add($deleteStaff);

        $auth->add($manageCustomer);
        $auth->add($createCustomer);
        $auth->add($viewCustomer);
        $auth->add($updateCustomer);
        $auth->add($deleteCustomer);

        $auth->add($manageWarehouse);
        $auth->add($createWarehouse);
        $auth->add($viewWarehouse);
        $auth->add($updateWarehouse);
        $auth->add($deleteWarehouse);

        $auth->add($manageOrder);
        $auth->add($createOrder);
        $auth->add($viewOrder);
        $auth->add($updateOrder);
        $auth->add($deleteOrder);

        $auth->add($manageInvoice);
        $auth->add($createInvoice);
        $auth->add($viewInvoice);
        $auth->add($updateInvoice);
        $auth->add($deleteInvoice);

        $auth->add($manageSupplier);
        $auth->add($createSupplier);
        $auth->add($viewSupplier);
        $auth->add($updateSupplier);
        $auth->add($deleteSupplier);

//        $auth->add($updateOwnNews);

        // Теперь добавим наследования. Для роли editor мы добавим разрешение updateOwnNews (редактировать собственную новость),
        // а для админа добавим собственные разрешения viewAdminPage и updateNews (может смотреть админку и редактировать любую новость)

        // Роли «Редактор новостей» присваиваем разрешение «Редактирование собственной новости»
//        $auth->addChild($manager,$updateOwnNews);

        // админ имеет собственное разрешение
    /*    $auth->addChild($admin, $updateNews);

        $auth->addChild($admin, $viewAdminPage);

        $auth->addChild($admin, $manageUser);
        $auth->addChild($admin, $createUser);
        $auth->addChild($admin, $viewUser);
        $auth->addChild($admin, $updateUser);
        $auth->addChild($admin, $deleteUser);

        $auth->addChild($admin, $manageCompany);
        $auth->addChild($admin, $createCompany);
        $auth->addChild($admin, $viewCompany);
        $auth->addChild($admin, $updateCompany);
        $auth->addChild($admin, $deleteCompany);

        $auth->addChild($admin, $manageBarcode);
        $auth->addChild($admin, $createBarcode);
        $auth->addChild($admin, $viewBarcode);
        $auth->addChild($admin, $updateBarcode);
        $auth->addChild($admin, $deleteBarcode);

        $auth->addChild($admin, $manageTempBarcode);
        $auth->addChild($admin, $createTempBarcode);
        $auth->addChild($admin, $viewTempBarcode);
        $auth->addChild($admin, $updateTempBarcode);
        $auth->addChild($admin, $deleteTempBarcode); */

        $auth->addChild($admin, $manageStaff);
        $auth->addChild($admin, $createStaff);
        $auth->addChild($admin, $permissionStaff);
        $auth->addChild($admin, $updateStaff);
        $auth->addChild($admin, $deleteStaff);

        $auth->addChild($admin, $manageCustomer);
        $auth->addChild($admin, $createCustomer);
        $auth->addChild($admin, $viewCustomer);
        $auth->addChild($admin, $updateCustomer);
        $auth->addChild($admin, $deleteCustomer);

        $auth->addChild($admin, $manageWarehouse);
        $auth->addChild($admin, $createWarehouse);
        $auth->addChild($admin, $viewWarehouse);
        $auth->addChild($admin, $updateWarehouse);
        $auth->addChild($admin, $deleteWarehouse);

        $auth->addChild($admin, $manageOrder);
        $auth->addChild($admin, $createOrder);
        $auth->addChild($admin, $viewOrder);
        $auth->addChild($admin, $updateOrder);
        $auth->addChild($admin, $deleteOrder);

        $auth->addChild($admin, $manageInvoice);
        $auth->addChild($admin, $createInvoice);
        $auth->addChild($admin, $viewInvoice);
        $auth->addChild($admin, $updateInvoice);
        $auth->addChild($admin, $deleteInvoice);

        $auth->addChild($admin, $manageSupplier);
        $auth->addChild($admin, $createSupplier);
        $auth->addChild($admin, $viewSupplier);
        $auth->addChild($admin, $updateSupplier);
        $auth->addChild($admin, $deleteSupplier);

        // Director child permissions default
        $auth->addChild($director, $manageStaff);
        $auth->addChild($director, $createStaff);
        $auth->addChild($director, $permissionStaff);
        $auth->addChild($director, $updateStaff);
        $auth->addChild($director, $deleteStaff);

        $auth->addChild($director, $manageCustomer);
        $auth->addChild($director, $createCustomer);
        $auth->addChild($director, $viewCustomer);
        $auth->addChild($director, $updateCustomer);
        $auth->addChild($director, $deleteCustomer);

        $auth->addChild($director, $manageWarehouse);
        $auth->addChild($director, $createWarehouse);
        $auth->addChild($director, $viewWarehouse);
        $auth->addChild($director, $updateWarehouse);
        $auth->addChild($director, $deleteWarehouse);

        $auth->addChild($director, $manageOrder);
        $auth->addChild($director, $createOrder);
        $auth->addChild($director, $viewOrder);
        $auth->addChild($director, $updateOrder);
        $auth->addChild($director, $deleteOrder);

        $auth->addChild($director, $manageInvoice);
        $auth->addChild($director, $createInvoice);
        $auth->addChild($director, $viewInvoice);
        $auth->addChild($director, $updateInvoice);
        $auth->addChild($director, $deleteInvoice);

        $auth->addChild($director, $manageSupplier);
        $auth->addChild($director, $createSupplier);
        $auth->addChild($director, $viewSupplier);
        $auth->addChild($director, $updateSupplier);
        $auth->addChild($director, $deleteSupplier);

        $auth->addChild($cashier, $createOrder);
    }
}