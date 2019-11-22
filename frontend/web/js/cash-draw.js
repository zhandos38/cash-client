    let transactionsApp = new Vue({
        el: '#cash-draw-app',
        data: {
            shift: {
                openedBy: null,
                openedAt: null,
                balanceAtStart: null,
                insertedMoney: null,
                takenMoney: null,
                currentBalance: [{
                    cash: null,
                    card: null,
                    debt: null,
                    combined: null
                }]
            },
            transactions: [],
            isTransactionModalActive: false,
            transactionType: 0,
            transactionValue: '',
            transactionComment: null
        },
        methods: {
            addTransaction() {
                $.post({
                    url: 'add-transaction',
                    dataType: 'json',
                    data: {value: this.transactionValue, type: this.transactionType, comment: this.transactionComment},
                    success: result => {
                        this.getTransactions();
                        this.closeTransactionModal();
                    },
                    error: function () {
                        alert('Ошибка добавление транзакции');
                    }
                });
            },
            openTransactionModal() {
                this.isTransactionModalActive = true;
            },
            closeTransactionModal() {
                this.isTransactionModalActive = false;
            },
            setTransactionValue(value) {
                // this.order.takenCash = this.order.takenCash.toString();
                if (typeof value == 'number') {
                    this.transactionValue = parseFloat(this.transactionValue === '' ? 0 : this.transactionValue) + value;
                    return true;
                }
                if (this.transactionValue === '' && value === '0')
                    this.transactionValue = '';
                else
                    this.transactionValue += value;
            },
            cleanTransactionValue() {
                this.transactionValue = '';
            },
            getTransactions() {
                this.transactions = [];
                $.get({
                    url: 'get-transactions',
                    success: result => {
                        console.log(result);
                        result.forEach((item) => {
                            this.transactions.push({
                                createdAt: item['created_at'],
                                sum: item['sum'],
                                type: item['type'],
                                comment: item['comment'],
                                user: item['user']
                            });
                        });
                    }
                });
            },
            getShift() {
                $.get({
                    url: 'get-shift',
                    success: result => {
                        this.shift.openedBy = result['user'];
                        this.shift.openedAt = result['created_at'];
                        this.shift.balanceAtStart = result['balance_at_start'];
                        this.shift.insertedMoney = result['inserted_money'];
                        this.shift.currentBalance = result['current_balance'];
                    },
                    error: function () {
                        alert('Ошибка получение данных о смене');
                    }
                });
            },
            closeShift() {
                $.get({
                    url: 'close-shift'
                });
            }
        },
        mounted() {
            this.getTransactions();
            this.getShift();
        }
    });
