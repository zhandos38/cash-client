    let ordersApp = new Vue({
        el: '#cash-draw-orders-app',
        data: {
            orders: [{
                id: null,
                number: null,
                pay: null,
                status: null,
                sum: null,
                products: []
            }],
            updatedOrders: [],
            currentOrder: 0,
            productsToReturn: {},
            productsToBeReturned: {},
            isReturnModalActive: false
        },
        methods: {
            getOrders() {
                $.get({
                    url: 'get-orders',
                    success: result => {
                        this.orders.splice(0, 1);
                        result.forEach(item => {
                            this.orders.push({
                                id: item['id'],
                                number: item['number'],
                                pay: item['pay'],
                                status: item['status'],
                                sum: item['sum'],
                                products: item['items']
                            });
                        });
                        console.log(this.orders);
                    },
                    error: function () {
                        alert('Ошибка получение заказов');
                    }
                });
            },
            updateOrders() {
                console.log('Orders updated');
                this.updatedOrders = [];
                $.get({
                    url: 'get-orders',
                    success: result => {
                        result.forEach(item => {
                            console.log(result);
                            this.updatedOrders.push({
                                id: item['id'],
                                number: item['number'],
                                pay: item['pay'],
                                status: item['status'],
                                sum: item['sum'],
                                products: item['items']
                            });
                        });

                        this.orders = this.updatedOrders;
                    },
                    error: function () {
                        alert('Ошибка получение заказов');
                    }
                });
            },
            setCurrentOrder(index) {
                this.currentOrder = index;
            },
            openReturnModal(index) {
                this.productsToReturn = {};
                this.productsToBeReturned = {};
                this.orders[this.currentOrder].products.forEach((item, index) => {
                    this.productsToReturn[index] = {
                        id: item['id'],
                        name: item['name'],
                        quantity: item['quantity'],
                        real_price: item['real_price']
                    }
                });

                console.log(this.productsToReturn);
                this.isReturnModalActive = true;
            },
            closeReturnModal() {
                this.isReturnModalActive = false;
            },
            cancelReturn() {
                this.isReturnModalActive = false;
            },
            applyReturn() {
                console.log('clicked');
            },
            toReturnOne(index) {
                if(typeof this.productsToBeReturned[index] !== 'undefined') {
                    this.productsToBeReturned[index].quantity += 1;
                } else {
                    Vue.set(this.productsToBeReturned, index, {
                        id: this.productsToReturn[index].id,
                        name: this.productsToReturn[index].name,
                        quantity: 1,
                        real_price: this.productsToReturn[index].real_price
                    });
                }

                this.productsToReturn[index].quantity -= 1;
                if (this.productsToReturn[index].quantity === 0) {
                    Vue.delete(this.productsToReturn, index);
                }
            },
            toReturnAll(index) {
                Vue.set(this.productsToBeReturned, index, this.productsToReturn[index]);
                Vue.delete(this.productsToReturn, index);
            },
            cancelOne(index) {
                if(typeof this.productsToReturn[index] !== 'undefined') {
                    this.productsToReturn[index].quantity += 1;
                } else {
                    Vue.set(this.productsToReturn, index, {
                        id: this.productsToBeReturned[index].id,
                        name: this.productsToBeReturned[index].name,
                        quantity: 1,
                        real_price: this.productsToBeReturned[index].real_price
                    });
                }

                this.productsToBeReturned[index].quantity -= 1;
                if (this.productsToBeReturned[index].quantity === 0) {
                    Vue.delete(this.productsToBeReturned, index);
                }
            },
            cancelAll(index) {
                Vue.set(this.productsToReturn, index, this.productsToBeReturned[index]);
                Vue.delete(this.productsToBeReturned, index);
            },
            applyReturn() {
                $.post({
                    url: 'return-order',
                    data: {id: this.orders[this.currentOrder].id, products: this.productsToBeReturned, isReturnTotal: Object.keys(this.productsToReturn).length > 0 ? 0 : 1},
                    success: function (result) {
                        console.log(result);
                    },
                    error: function () {
                        alert('Ошибка возврата');
                    }
                });
            },
            cancelOrder(index) {
                $.post({
                    url: 'cancel-order',
                    data: {id: this.orders[index].id},
                    success: result => {
                        console.log('Order canceled successful!');
                        this.updateOrders();
                    },
                    error: function () {
                        alert('Order cancel error!');
                    }
                });
            },
            printOrder() {
                $.post({
                    url: 'print-order',
                    data: {id: this.orders[this.currentOrder].id},
                    success: result => {
                        console.log('Order printed');
                    },
                    error: function () {
                        alert('Order print error!');
                    }
                });
            }
        },
        mounted() {
            this.getOrders();
        }
    });
