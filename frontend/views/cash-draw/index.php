<?php

use frontend\assets\CashDrawAsset;
CashDrawAsset::register($this);

/* @var \yii\web\View $this*/

$this->title = 'Касса';
?>
<div id="cash-draw-app" class="cash-draw">
    <div class="cash-draw__container">
        <div class="transactions-list">
            <table class="table">
                <tbody>
                <tr>
                    <th>Создано в</th>
                    <th>Сумма</th>
                    <th>Тип транзакции</th>
                    <th>Комментарий</th>
                    <th>Сотрудник</th>
                </tr>
                </tbody>
                <tbody>
                <tr v-for="transaction in transactions">
                    <th>{{ transaction.createdAt }}</th>
                    <th>{{ transaction.sum }}</th>
                    <th>{{ transaction.type }}</th>
                    <th>{{ transaction.comment }}</th>
                    <th>{{ transaction.user }}</th>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="cash-draw__shift">
            <div class="cash-draw__opened-by">
                {{ shift.openedBy }}
            </div>
            <div class="cash-draw__opened-at">
                {{ shift.openedAt }}
            </div>
            <div class="cash-draw__balance-at-start">
                {{ shift.balanceAtStart }}
            </div>
            <div class="cash-draw__inserted-money">
                {{ shift.insertedMoney }}
            </div>
            <div class="cash-draw__current-balance">
                {{ shift.currentBalance }}
            </div>
        </div>
        <button @click="openTransactionModal">
            Внесение/Изъятие
        </button>
        <button @click="closeShift">
            Закрыть смену
        </button>
    </div>

    <!-- Transaction Modal -->
    <div class="transaction-modal__wrapper" v-show="isTransactionModalActive">
        <div class="transaction-modal">
            <div class="transaction-modal__name">
                Внесение/Изъятие
            </div>
            <div class="transaction-modal__close pull-right" @click="closeTransactionModal">
                <i class="fas fa-times"></i>
            </div>
            <div class="transaction-modal__container">
                <div class="transaction-modal__form">
                    <label>
                        Сумма транзакции:
                        <input type="text" class="transaction-modal__value" v-model.number="transactionValue">
                    </label>
                    <label>
                        Внесение
                        <input type="radio" name="radio" value="0" v-model.number="transactionType">
                    </label>
                    <label>
                        Изъятие
                        <input type="radio" name="radio" value="1" v-model.number="transactionType">
                    </label>
                    <label>
                        Кассир:
                        <textarea class="transaction-modal__comment" v-model="transactionComment"></textarea>
                    </label>
                    <button class="transaction-modal__accept" @click="addTransaction">
                        Применить
                    </button>
                </div>
                <div class="transaction-modal__calculator">
                    <div class="numpad">
                        <button type="button" class="numpad__button" @click="setTransactionValue('1')">1</button>
                        <button type="button" class="numpad__button" @click="setTransactionValue('2')">2</button>
                        <button type="button" class="numpad__button" @click="setTransactionValue('3')">3</button>
                        <button type="button" class="numpad__button" @click="setTransactionValue('4')">4</button>
                        <button type="button" class="numpad__button" @click="setTransactionValue('5')">5</button>
                        <button type="button" class="numpad__button" @click="setTransactionValue('6')">6</button>
                        <button type="button" class="numpad__button" @click="setTransactionValue('7')">7</button>
                        <button type="button" class="numpad__button" @click="setTransactionValue('8')">8</button>
                        <button type="button" class="numpad__button" @click="setTransactionValue('9')">9</button>
                        <button type="button" class="numpad__button" @click="setTransactionValue('.')">,</button>
                        <button type="button" class="numpad__button" @click="setTransactionValue('0')">0</button>
                        <button type="button" class="numpad__button" @click="cleanTransactionValue">C</button>
                    </div>
                    <div class="numpad-extension">
                        <button type="button" class="numpad-extension__button" @click="setTransactionValue(0.5)">+0.5</button>
                        <button type="button" class="numpad-extension__button" @click="setTransactionValue(1)">+1</button>
                        <button type="button" class="numpad-extension__button" @click="setTransactionValue(2)">+2</button>
                        <button type="button" class="numpad-extension__button" @click="setTransactionValue(5)">+5</button>
                        <button type="button" class="numpad-extension__button" @click="setTransactionValue(10)">+10</button>
                        <button type="button" class="numpad-extension__button" @click="setTransactionValue(20)">+20</button>
                        <button type="button" class="numpad-extension__button" @click="setTransactionValue(50)">+50</button>
                        <button type="button" class="numpad-extension__button" @click="setTransactionValue(100)">+100</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
