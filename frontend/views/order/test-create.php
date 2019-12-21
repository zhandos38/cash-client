<?php

use common\models\Product;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use frontend\assets\OrderAsset;

OrderAsset::register($this);

/* @var $this yii\web\View */
/* @var $modelOrder common\models\Order */
/* @var $modelsOrderItem common\models\OrderItems */
/* @var $form yii\widgets\ActiveForm */
/* @var $products Product */
/* @var $suggestProducts Product */

$this->title = 'Создать заказ';
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<a href="<?= Url::to(['site/index']) ?>" class="back-button"><i class="fa fa-undo" aria-hidden="true"></i>  Назад</a>
<div id="checkout" class="order-create" xmlns:v-on="http://www.w3.org/1999/xhtml">
    <div class="order-form__wrapper">
        <div class="order-form">

            <div class="product-search">
                <input id="product-search__input" type="text" class="form-control product-search__input" @keyup="searchProduct($event.target.value)">
                <i id="product-search__keyboard" class="product-search__keyboard fas fa-keyboard"></i>
                <i id="product-search__clear" class="product-search__clear fas fa-times"></i>
            </div>
            <div class="simple-keyboard" style="display: none; position: absolute; bottom: 0; margin: 0 auto; z-index: 3; width: 90%;"></div>

            <br>

            <div class="panel panel-default">
                <div class="order-tabs">
                    <div class="order-tabs__item" v-for="(order, index) in orders" @click="setCurrentOrder(index)">Заказ {{ index + 1 }}</div>
                </div>
                <div class="panel-heading">
                    <h4>
                        <i class="glyphicon glyphicon-envelope"></i> Товары
                    </h4>
                </div>
                <div class="panel-body">
                    <table class="table table-striped table-responsive">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Наименование</th>
                            <th scope="col">Цена</th>
                            <th scope="col">Кол.</th>
                            <th scope="col">Всего</th>
                        </tr>
                        </thead>
                        <tbody class="container-items">
                        <tr class="order-item" v-for="(product, i) in orders[currentOrder].products"><!-- widgetItem -->
                            <th>
                                <button type="button" class="remove-item btn btn-danger btn-xs" @click="deleteProduct(i)"><i class="glyphicon glyphicon-minus"></i></button>
                                <div class="clearfix"></div>
                            </th>
                            <th>{{ product.name }}</th>
                            <th>{{ product.priceRetail }}</th>
                            <th><input type="text" :id="'order-item__quantity-'+product.id" class="order-item__quantity" v-model="product.quantity"></th>
                            <th>{{ (product.quantity * product.priceRetail) | number }}</th>
                        </tr>

                        </tbody>
                    </table>
                </div><!-- .panel -->
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="order-total-block">
                        Итого: <div class="order-total pull-right">{{ preTotal | number }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div id="category-grid" class="products__suggest grid">
            <div v-if="currentCategoryParentId != null" class="category-card grid-item grid-sizer" @click="showCategories(currentCategoryParentId)">
                <div class="category-card__name">Назад</div>
                <div class="category-card__price"></div>
            </div>
            <div class="category-card grid-item grid-sizer" v-for="category in currentCategories" @click="showCategories(category.id)">
                <div class="category-card__name">{{ category.name }}</div>
                <div class="category-card__price"></div>
            </div>
            <div class="product-card grid-item grid-sizer" v-for="product in productCards" @click="addProduct(product.id)">
                <div class="product-card__name">{{ product.name }}</div>
                <div class="product-card__price"></div>
            </div>
        </div>
    </div>

    <div class="checkout__footer">
        <div v-if="currentOrder != 0" type="button" class="checkout__clean" @click="deleteOrder">
            Удалить
        </div>
        <div type="button" class="checkout__clean" @click="cleanProducts">
            Очистить
        </div>
        <div class="checkout__discount" @click="openDiscountModal">
            Скидка
        </div>
        <div class="checkout__comment" @click="openCommentModal">
            Коммент
        </div>
        <div class="checkout__right-part">
            <div class="checkout__put-off" @click="addOrder">
                Отложить
            </div>
            <div class="checkout__pay" @click="openPayModal">
                Оплатить
            </div>
        </div>
    </div>

    <div class="simple-numPad" style="display: none; position: absolute; z-index: 3">
        <div class="simple-keyboard-numpad"></div>
        <div class="simple-keyboard-numpadEnd"></div>
    </div>

    <!-- Pay modal begin -->
    <div class="pay-modal__wrapper" v-show="payModalActive">
        <div class="pay-modal">
            <div class="pay-modal__cancel pull-right" @click="closePayModal">
                <i class="fas fa-times"></i>
            </div>
            <div class="pay-modal__main-content">
                <div class="pay-modal__pay-methods">
                    <label class="pay-modal__pay-container">
                        <span class="pay-modal__pay-method-name">Наличные</span>
                        <input type="radio" checked="checked" name="radio" value="0" v-model="orders[currentOrder].payMethod">
                        <span class="pay-modal__check-mark"></span>
                    </label>
                    <label class="pay-modal__pay-container">
                        <span class="pay-modal__pay-method-name">Без налич.</span>
                        <input type="radio" name="radio" value="1" v-model="orders[currentOrder].payMethod">
                        <span class="pay-modal__check-mark"></span>
                    </label>
                    <label class="pay-modal__pay-container" @click="openCustomerModal">
                        <span class="pay-modal__pay-method-name">В долг.</span>
                        <input type="radio" name="radio" value="2" v-model="orders[currentOrder].payMethod">
                        <span class="pay-modal__check-mark"></span>
                    </label>
                    <label class="pay-modal__pay-container">
                        <span class="pay-modal__pay-method-name">Комбин.</span>
                        <input type="radio" name="radio" value="3" v-model="orders[currentOrder].payMethod">
                        <span class="pay-modal__check-mark"></span>
                    </label>
                </div>
                <div class="pay-modal__container">
                    <div class="pay-modal__products">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Товар</th>
                                <th>Цена</th>
                                <th>Кол.</th>
                                <th>Всего</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="product in orders[this.currentOrder].products">
                                <td>{{ product.name }}</td>
                                <td>{{ product.priceRetail }}</td>
                                <td>{{ product.quantity }}</td>
                                <td>{{ (product.priceRetail * product.quantity) | number }}</td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="pay-modal__total-wrapper">
                            <div class="pay-modal__pre-total pull-right">Подитог: {{ preTotal | number }}</div>
                            <div class="pay-modal__discount-info">Скидка: {{ orders[this.currentOrder].discountSum ? orders[this.currentOrder].discountSum : 0 }}</div>
                            <div class="pay-modal__total pull-right">Итого: {{ total | number }}</div>
                        </div>
                    </div>
                    <div class="pay-modal__payment">
                        <div class="pay-modal__calculator">
                            <div class="pay-modal__calculator-info"><span v-show="isTakenCashRelevant">Введите полную сумму оплаты</span></div>
                            <div class="pay-modal__taken-cash">{{ orders[this.currentOrder].takenCash }}</div>
                            <div class="numpad">
                                <button :disabled="isNumpadDisabled" type="button" class="numpad__button" @click="setTakenCash('1')">1</button>
                                <button :disabled="isNumpadDisabled" type="button" class="numpad__button" @click="setTakenCash('2')">2</button>
                                <button :disabled="isNumpadDisabled" type="button" class="numpad__button" @click="setTakenCash('3')">3</button>
                                <button :disabled="isNumpadDisabled" type="button" class="numpad__button" @click="setTakenCash('4')">4</button>
                                <button :disabled="isNumpadDisabled" type="button" class="numpad__button" @click="setTakenCash('5')">5</button>
                                <button :disabled="isNumpadDisabled" type="button" class="numpad__button" @click="setTakenCash('6')">6</button>
                                <button :disabled="isNumpadDisabled" type="button" class="numpad__button" @click="setTakenCash('7')">7</button>
                                <button :disabled="isNumpadDisabled" type="button" class="numpad__button" @click="setTakenCash('8')">8</button>
                                <button :disabled="isNumpadDisabled" type="button" class="numpad__button" @click="setTakenCash('9')">9</button>
                                <button :disabled="isNumpadDisabled" type="button" class="numpad__button" @click="setTakenCash('.')">,</button>
                                <button :disabled="isNumpadDisabled" type="button" class="numpad__button" @click="setTakenCash('0')">0</button>
                                <button :disabled="isNumpadDisabled" type="button" class="numpad__button" @click="cleanTakenCash">C</button>
                            </div>
                        </div>
                        <div class="pay-modal__calculator-extension">
                            <div class="pay-model__change">{{ change | number }}</div>
                            <div class="pay-modal__taken-cash-modifier">
                                <div class="pay-modal__sub" @click="subTakenCash">
                                    <i class="fas fa-arrow-left"></i>
                                </div>
                                <div class="pay-modal__equal" @click="equalTakenCash">
                                    <i class="fas fa-equals"></i>
                                </div>
                            </div>
                            <div class="numpad-extension">
                                <button :disabled="isNumpadDisabled" type="button" class="numpad-extension__button" @click="setTakenCash(0.5)">+0.5</button>
                                <button :disabled="isNumpadDisabled" type="button" class="numpad-extension__button" @click="setTakenCash(1)">+1</button>
                                <button :disabled="isNumpadDisabled" type="button" class="numpad-extension__button" @click="setTakenCash(2)">+2</button>
                                <button :disabled="isNumpadDisabled" type="button" class="numpad-extension__button" @click="setTakenCash(5)">+5</button>
                                <button :disabled="isNumpadDisabled" type="button" class="numpad-extension__button" @click="setTakenCash(10)">+10</button>
                                <button :disabled="isNumpadDisabled" type="button" class="numpad-extension__button" @click="setTakenCash(20)">+20</button>
                                <button :disabled="isNumpadDisabled" type="button" class="numpad-extension__button" @click="setTakenCash(50)">+50</button>
                                <button :disabled="isNumpadDisabled" type="button" class="numpad-extension__button" @click="setTakenCash(100)">+100</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pay-modal__footer">
                    <div class="pay-modal__print">
                        <label for="print-check">Печать товарный чек</label>
                        <input id="print-check" type="checkbox" class="pay-modal__print-check" value="1" v-model="isPrintActive">
                    </div>
                    <div class="pay-modal__discount" @click="openDiscountModal">Скидка</div>
                    <div class="pay-modal__comment" @click="openCommentModal">Коммент.</div>
                    <div class="pay-modal__open-cash-box" @click="openCashDraw">Откырть денежный ящик</div>
                    <div class="pay-modal__send-check">Отправить данные</div>
                    <div class="pay-modal__pay" @click="payOrder">Оплатить</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Pay modal end -->

    <!-- Comment modal start -->
    <div class="comment-modal__wrapper" v-show="commentModalActive">
        <div class="comment-modal">
            <div class="comment-modal__close pull-right" @click="closeCommentModal">
                <i class="fas fa-times"></i>
            </div>
            <div class="comment-modal__container">
                <h2>Оставить комментарий</h2>
                <textarea class="comment-modal__textarea" cols="30" rows="10" ref="commentModalTextArea"></textarea>
                <div class="comment-modal__footer">
                    <div class="comment-modal__cancel" @click="closeCommentModal">Отмена</div>
                    <div class="comment-modal__accept" @click="setComment">Принять</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Comment modal end -->

    <!-- Discount modal start -->
    <div class="discount-modal__wrapper" v-show="discountModalActive">
        <div class="discount-modal">
            <div class="discount-modal__close pull-right" @click="closeDiscountModal">
                <i class="fas fa-times"></i>
            </div>
            <div class="discount-modal__container">
                <div class="discount-calculator">
                    <h2>Суммовая скидка</h2>
                    <div class="discount-calculator__input">{{ tempDiscountSum }}</div>
                    <div class="discount-calculator__max-sum">Не более {{ preTotal }}</div>
                    <div class="numpad">
                        <button type="button" class="numpad__button" @click="setTempDiscountSum('1')">1</button>
                        <button type="button" class="numpad__button" @click="setTempDiscountSum('2')">2</button>
                        <button type="button" class="numpad__button" @click="setTempDiscountSum('3')">3</button>
                        <button type="button" class="numpad__button" @click="setTempDiscountSum('4')">4</button>
                        <button type="button" class="numpad__button" @click="setTempDiscountSum('5')">5</button>
                        <button type="button" class="numpad__button" @click="setTempDiscountSum('6')">6</button>
                        <button type="button" class="numpad__button" @click="setTempDiscountSum('7')">7</button>
                        <button type="button" class="numpad__button" @click="setTempDiscountSum('8')">8</button>
                        <button type="button" class="numpad__button" @click="setTempDiscountSum('9')">9</button>
                        <button type="button" class="numpad__button" @click="setTempDiscountSum('.')">,</button>
                        <button type="button" class="numpad__button" @click="setTempDiscountSum('0')">0</button>
                        <button type="button" class="numpad__button" @click="cleanTempDiscountSum">C</button>
                    </div>
                    <div class="discount-calculator__accept" @click="setDiscountSum">Применить</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Discount modal end -->

    <!-- Customer modal start -->
    <div class="customer-modal__wrapper" v-show="customerModalActive">
        <div class="customer-modal">
            <div class="customer-modal__close pull-right" @click="closeCustomerModal">
                <i class="fas fa-times"></i>
            </div>
            <div class="customer-modal__container">
                <div class="customer-modal__search-form">
                    <input type="text" class="customer-modal__customer-name" v-model="customerName">
                    <input type="text" class="customer-modal__customer-phone" v-model="customerPhone">
                    <button class="customer-modal__search-btn" @click="getCustomers">
                        Искать
                    </button>
                </div>
                <div class="customer-modal__list">
                    <div class="customer-modal__item" v-for="customer in customers" @click="setCustomer(customer.id)">{{ customer.name }} | {{ customer.phone }} | {{ customer.address }}</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Customer modal end -->
</div>