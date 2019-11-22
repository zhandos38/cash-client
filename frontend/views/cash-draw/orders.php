<?php

use frontend\assets\TestOrderAsset;
TestOrderAsset::register($this);

/** @var \yii\web\View $this */

$this->title = 'Чеки';
?>
<div id="cash-draw-orders-app" class="cash-draw-orders">
    <div class="cash-draw-orders__container">
        <div class="cash-draw-orders__table">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Номер чека</th>
                    <th>Статус</th>
                    <th>Сумма</th>
                    <th>Тип оплаты</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(order, key) in orders" @click="setCurrentOrder(key)">
                    <th>{{ order.number }}</th>
                    <th>{{ order.status }}</th>
                    <th>{{ order.sum }}</th>
                    <th>{{ order.pay }}</th>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="cash-draw-orders__item-view">
            <table class="table">
                <thead>
                <tr>
                    <th>Товар</th>
                    <th>Количествао</th>
                    <th>Всего</th>
                </tr>
                </thead>
                <tbody>
                <tr v-if="orders[currentOrder].products.length > 0" v-for="product in orders[currentOrder].products">
                    <th>{{ product.name }}</th>
                    <th>{{ product.quantity }}</th>
                    <th>{{ product.quantity * product.real_price }}</th>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="cash-draw-orders__button-group">
            <button class="cash-draw-orders__return-btn" @click="openReturnModal(currentOrder)">Возврат</button>
            <button class="cash-draw-orders__cancel-btn" @click="cancelOrder(currentOrder)">Аннулировать</button>
            <button class="cash-draw-orders__print-copy" @click="printOrder(currentOrder)">Распечатать копию</button>
        </div>
    </div>

    <!-- Return Modal  -->
    <div class="return-modal__wrapper" v-show="isReturnModalActive">
        <div class="return-modal">
            <div class="return-modal__name">
                Возврат
            </div>
            <div class="return-modal__close pull-right" @click="closeReturnModal">
                <i class="fas fa-times"></i>
            </div>
            <div class="return-modal__container">
                <div class="return-modal__tables">
                    <div class="return-modal__table-left">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Товар</th>
                                <th>Кол.</th>
                                <th>Всего</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(product, key) in productsToReturn">
                                <th>{{ product.name }}</th>
                                <th>{{ product.quantity }}</th>
                                <th>{{ product.quantity * product.real_price }}</th>
                                <th @click="toReturnOne(key)"> > </th>
                                <th @click="toReturnAll(key)"> >> </th>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="return-modal__table">
                        <table class="table">
                            <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th>Товар</th>
                                <th>Кол.</th>
                                <th>Всего</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(product, key) in productsToBeReturned">
                                <th @click="cancelAll(key)"> << </th>
                                <th @click="cancelOne(key)"> < </th>
                                <th>{{ product.name }}</th>
                                <th>{{ product.quantity }}</th>
                                <th>{{ product.quantity * product.real_price }}</th>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <button class="return-modal__cancel" @click="cancelReturn">
                    Отменить
                </button>
                <button class="return-modal__apply" @click="applyReturn">
                    Применить
                </button>
            </div>
        </div>
    </div>
</div>