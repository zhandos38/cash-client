let orderListApp = new Vue({
    el: '#checkout',
    filters: {
        number(value) {
            return value.toFixed(2);
        },
    },
    data: {
        orders: [
            {
                payMethod: 0,
                customerId: null,
                preTotalSum: null,
                discountSum: 0,
                discountPercentage: null,
                totalSum: null,
                takenCash: '',
                comment: '',
                isDebt: null,
                products: []
            }
        ],
        currentOrder: 0,
        productCards: [],
        timer: null,
        lastQuantityInput: null,
        touchSpinSettings: [
            input_quantity_partial_settings = {
                min: 1,
                max: 100,
                step: 0.1,
                decimals: 2,
                boostat: 5,
                maxboostedstep: 10,
                postfix: 'кг'
            },
            input_quantity_settings = {
                min: 1,
                max: 1000000000,
                stepinterval: 50,
                maxboostedstep: 10000000,
            }
        ],
        payModalActive: false,
        commentModalActive: false,
        discountModalActive: false,
        customerModalActive: false,
        tempDiscountSum: '',
        customerName: null,
        customerPhone: null,
        customers: [],
        isPrintActive: 1,
        categories: [],
        currentCategories: [],
        currentCategoryParent: {
            'id': null,
            'parent_id': null
        },
        currentCategoryParentId: null
    },
    computed: {
        preTotal() {
            return this.orders[this.currentOrder].products.reduce(function(preTotal, item) {
                item.quantity = parseFloat(item.quantity);
                item.wholesaleValue = parseFloat(item.wholesaleValue);
                if (item.quantity < item.wholesaleValue) {
                    return preTotal + (item.quantity * item.priceRetail);
                } else if (item.quantity >= item.wholesaleValue) {
                    return preTotal + (item.quantity * item.priceWholesale);
                }
            }, 0);
        },
        total() {
            return this.preTotal - this.orders[this.currentOrder].discountSum;
        },
        isTakenCashRelevant() {
            return this.preTotal > parseFloat(this.orders[this.currentOrder].takenCash);
        },
        change() {
            return this.orders[this.currentOrder].takenCash - this.total;
        },
        isNumpadDisabled() {
            if (parseInt(this.orders[this.currentOrder].payMethod) === 1) {
                this.orders[this.currentOrder].takenCash = this.total;
                return true;
            } else {
                return false;
            }
        },
    },
    updated() {
        console.log('updated');
        if (this.orders[this.currentOrder].products.length > 0) {
            let lastQuantityInput = this.orders[this.currentOrder].products[this.orders[this.currentOrder].products.length - 1];

            if (lastQuantityInput)
                $(document).find('.order-item__quantity').TouchSpin(lastQuantityInput['isPartial'] === "0" ? input_quantity_settings : input_quantity_partial_settings);
        }
    },
    methods: {
        addOrder() {
            this.orders.push({
                payMethod: 0,
                customerId: null,
                preTotalSum: null,
                discountSum: 0,
                discountPercentage: null,
                totalSum: null,
                takenCash: '',
                comment: '',
                isDebt: null,
                products: []
            });

            this.currentOrder = this.orders.length - 1;
        },
        setCurrentOrder(index) {
            this.currentOrder = index;
        },
        deleteOrder() {
            this.orders.splice(this.currentOrder, 1);
            this.currentOrder = this.currentOrder - 1;
        },
        addProduct(id) {
            $.get({
                url: '/order/get-product-by-id',
                dataType: 'Json',
                data: {id: id},
            }).done(data => {
                this.setProduct(data);
            });
        },
        setProduct(data) {
            let flag = true;
            if (this.orders[this.currentOrder].products.length > 0) {
                this.orders[this.currentOrder].products.forEach(function(item) {
                    if (item.id === data.id) {
                        item.quantity = parseFloat(item.quantity) + 1;
                        flag = false;
                    }
                });
            }

            if (flag) {
                this.orders[this.currentOrder].products.push({
                    id: data.id,
                    name: data.name,
                    barcode: data.barcode,
                    quantity: 1,
                    priceRetail: data.price_retail,
                    isPartial: data.is_partial,
                    priceWholesale: data.price_wholesale,
                    wholesaleValue: data.wholesale_value,
                });
                console.log(this.orders[this.currentOrder].products);
            }
        },
        cleanProducts() {
            this.orders[this.currentOrder].products = [];
        },
        deleteProduct(i) {
            this.orders[this.currentOrder].products.splice(i, 1);
        },
        searchProduct(term = '') {
            // $('.grid').masonry(masonryOptions);
            clearTimeout(this.timer);
            let that = this;
            this.timer = setTimeout(function() {
                if (term != null) {
                    $('#products-list').loading({
                        message: 'Загрузка'
                    });

                    $.get({
                        url: 'search',
                        data: {term: term}
                    }).done(result => {
                        // let grid = $('.grid');
                        // grid.masonry('destroy');
                        if (result) {
                            that.productCards = [];
                            result.forEach(function(item) {
                                that.productCards.push({
                                    id: item['id'],
                                    name: item['label'],
                                });
                            });
                            // grid.html(products).masonry(masonryOptions);
                        } else {
                            grid.html('Ничего не найдено!');
                        }
                        $('#category-grid').loading('toggle');
                    });
                }
            }, 1000);
        },
        getCategories() {
            // $('.grid').masonry(masonryOptions);
            $('#category-grid').loading({
                message: 'Загрузка'
            });

            $.get({
                url: 'get-categories',
            }).done(result => {
                if (result) {
                    result.forEach(item => {
                        this.categories.push({
                            'id': item['id'],
                            'name': item['name'],
                            'parent_id': item['parent_id'] != null ? item['parent_id'] : 0,
                        });

                        if (!item['parent_id']) {
                            this.currentCategories.push({
                                'id': item['id'],
                                'name': item['name'],
                                'parent_id': item['parent_id'] != null ? item['parent_id'] : 0,
                            });
                        }
                    });
                } else {
                    // grid.html('Ничего не найдено!');
                }
                $('#category-grid').loading('toggle');
            });

            this.getProductsByCategory(0);

            console.log(this.currentCategories);
        },
        showCategories(id) {
            this.currentCategories = [];

            if (id === 0) {
                this.categories.forEach(item => {
                    if (item['parent_id'] === 0) {
                        this.currentCategories.push({
                            'id': item['id'],
                            'name': item['name'],
                            'parent_id': item['parent_id'] != null ? item['parent_id'] : 0,
                        });
                    }
                });
                this.currentCategoryParentId = null;
            } else {
                this.currentCategoryParent = this.categories.find(element => {
                    return element['id'] === id;
                });

                this.currentCategoryParentId = this.currentCategoryParent['parent_id'];

                this.categories.forEach(item => {
                    console.log(typeof item['parent_id'] + ': ' + item['parent_id']);
                    if (item['parent_id'] === id) {
                        this.currentCategories.push({
                            'id': item['id'],
                            'name': item['name'],
                            'parent_id': item['parent_id'] != null ? item['parent_id'] : 0,
                        });
                    }
                });
            }

            this.getProductsByCategory(id);
        },
        getProductsByCategory(id) {
            console.log(id);
            $.get({
                url: 'get-products-by-category',
                data: {id: id},
                success: result => {
                    console.log(result);
                    if (result) {
                        this.productCards = [];
                        result.forEach(item => {
                            this.productCards.push({
                                id: item['id'],
                                name: item['label'],
                            });
                        });
                    }
                },
                error: function () {
                    alert('Возникла ошибка при выводе товаров по категорям');
                }
            });
        },
        openPayModal() {
            if (this.orders[this.currentOrder].products.length > 0)
                this.payModalActive = true;
            else
                alert('Отсутствует товар');
        },
        closePayModal() {
            this.payModalActive = false
        },
        openCommentModal() {
            this.commentModalActive = true;
        },
        closeCommentModal() {
            this.commentModalActive = false
        },
        setTakenCash(value) {
            // this.order.takenCash = this.order.takenCash.toString();
            if (typeof value == 'number') {
                this.orders[this.currentOrder].takenCash = parseFloat(this.orders[this.currentOrder].takenCash === '' ? 0 : this.orders[this.currentOrder].takenCash) + value;
                return true;
            }

            if (this.orders[this.currentOrder].takenCash === '' && value === '0')
                this.orders[this.currentOrder].takenCash = '';
            else
                this.orders[this.currentOrder].takenCash += value;
        },
        cleanTakenCash() {
            this.orders[this.currentOrder].takenCash = '';
        },
        subTakenCash() {
            this.orders[this.currentOrder].takenCash = this.orders[this.currentOrder].takenCash.toString();
            this.orders[this.currentOrder].takenCash = this.orders[this.currentOrder].takenCash.slice(0, this.orders[this.currentOrder].takenCash.length - 1);
        },
        equalTakenCash() {
            this.orders[this.currentOrder].takenCash = this.total;
        },
        payOrder() {
            if ((this.orders[this.currentOrder].payMethod === 0 || parseInt(this.orders[this.currentOrder].payMethod) === 1) && this.change !== 0) {
                alert('Не правильная сумма');
                return
            }
            if ((parseInt(this.orders[this.currentOrder].payMethod) === 2 || parseInt(this.orders[this.currentOrder].payMethod) === 3) && this.orders[this.currentOrder].takenCash > this.total) {
                alert('Сумма первоначального взноса должен быть меньше чем общая сумма');
                return
            }

            this.orders[this.currentOrder].preTotalSum = this.preTotal;
            if (this.orders[this.currentOrder].discountPercentage || this.orders[this.currentOrder].discountSum) {
                this.orders[this.currentOrder].totalSum = this.orders[this.currentOrder].preTotalSum * this.orders[this.currentOrder].discountPercentage ? this.orders[this.currentOrder].discountPercentage : 1;
                this.orders[this.currentOrder].totalSum = this.orders[this.currentOrder].preTotalSum - this.orders[this.currentOrder].discountSum ? this.orders[this.currentOrder].discountSum : 0;
            }
            this.orders[this.currentOrder].totalSum = this.preTotal;

            $.post({
                url: '/order/test-create',
                data: {order: this.orders[this.currentOrder], print: this.isPrintActive}
            })
                .done(result => {
                    this.closePayModal();

                    if (this.orders.length > 1) {
                        this.orders.splice(this.currentOrder, 1);
                        this.currentOrder = 0;
                    } else {
                        this.orders[0].customerId = null;
                        this.orders[0].isDebt = null;
                        this.orders[0].payMethod = 0;
                        this.orders[0].takenCash = null;
                        this.orders[0].preTotalSum = null;
                        this.orders[0].totalSum = null;
                        this.orders[0].discountSum = null;
                        this.orders[0].discountPercentage = null;
                        this.orders[0].comment = '';
                        this.orders[0].products = [];
                    }
                })
                .fail(function () {
                    console.log('Something goes wrong on pay order!');
                });
        },
        setComment() {
            this.orders[this.currentOrder].comment = this.$refs.commentModalTextArea.value;
            this.commentModalActive = false;
        },
        cleanComment() {
            this.orders[this.currentOrder].comment = null;
        },
        openDiscountModal() {
            if (!this.preTotal > 0) {
                alert('Отсутвуют товары');
                return
            }
            this.discountModalActive = true;
        },
        closeDiscountModal() {
            this.discountModalActive = false;
        },
        setTempDiscountSum(value) {
            if (this.tempDiscountSum === '' && value === '0')
                this.tempDiscountSum = '';
            else
                this.tempDiscountSum += value;
        },
        cleanTempDiscountSum() {
            this.tempDiscountSum = '';
        },
        setDiscountSum() {
            if (this.tempDiscountSum > this.preTotal) {
                alert('Сумма скидки превышает общую стоимость заказа');
                return
            }
            this.orders[this.currentOrder].discountSum = this.tempDiscountSum;
            this.closeDiscountModal();
        },
        openCustomerModal() {
            this.customerModalActive = true;
        },
        closeCustomerModal() {
            this.customerModalActive = false;
        },
        getCustomers() {
            $.post({
                url: 'test-customer-list',
                data: {name: this.customerName, phone: this.customerPhone}
            }).done(result => {
                result.forEach((item) => {
                    this.customers.push({
                        id: item['id'],
                        name: item['full_name'],
                        phone: item['phone'],
                        address: item['address']
                    });
                });
            }).fail(function () {
                alert('Ошибка пойска пользователей');
            });
        },
        setCustomer(id) {
            this.orders[this.currentOrder].customerId = id;
            this.orders[this.currentOrder].isDebt = 1;
            this.customerModalActive = false;
        },
        openCashDraw() {
            $.get({
                url: 'open-cash-draw',
                success: function () {
                    console.log('Cash draw opened!');
                },
                error: function () {
                    console.log('Cash draw open error!');
                }
            });
        }
    },
    mounted() {
        // this.searchProduct();
        this.getCategories();

        let Keyboard = window.SimpleKeyboard.default;
        let KeyboardLayouts = window.SimpleKeyboardLayouts.default;

        const english = {
            default: [
                "` 1 2 3 4 5 6 7 8 9 0 - = {bksp}",
                "{tab} q w e r t y u i o p [ ]",
                "{lock} a s d f g h j k l ; ' {enter}",
                "{shift} z x c v b n m , . / {shift}",
                ".com {lang} @ {space}"
            ],
            shift: [
                "~ ! @ # $ % ^ & * ( ) _ + {bksp}",
                "{tab} Q W E R T Y U I O P { } |",
                '{lock} A S D F G H J K L : " {enter}',
                "{shift} Z X C V B N M < > ? {shift}",
                ".com {lang} @ {space}"
            ]
        };

        const russian = {
            default: [
                "\u0451 1 2 3 4 5 6 7 8 9 0 - = {bksp}",
                "{tab} \u0439 \u0446 \u0443 \u043a \u0435 \u043d \u0433 \u0448 \u0449 \u0437 \u0445 \u044a",
                "{lock} \u0444 \u044b \u0432 \u0430 \u043f \u0440 \u043e \u043b \u0434 \u0436 \u044d {enter}",
                "{shift} \\ \u044f \u0447 \u0441 \u043c \u0438 \u0442 \u044c \u0431 \u044e / {shift}",
                ".com {lang} @ {space}"
            ],
            shift: [
                '\u0401 ! " \u2116 ; % : ? * ( ) _ + {bksp}',
                "{tab} \u0419 \u0426 \u0423 \u041a \u0415 \u041d \u0413 \u0428 \u0429 \u0417 \u0425 \u042a /",
                "{lock} \u0424 \u042b \u0412 \u0410 \u041f \u0420 \u041e \u041b \u0414 \u0416 \u042d {enter}",
                "{shift} / \u042f \u0427 \u0421 \u041c \u0418 \u0422 \u042c \u0411 \u042e / {shift}",
                ".com {lang} @ {space}"
            ]
        };

        let commonKeyboardOptions = {
            onChange: input => onChange(input),
            onKeyPress: button => onKeyPress(button),
            theme: "simple-keyboard hg-theme-default hg-layout-default",
            physicalKeyboardHighlight: true,
            syncInstanceInputs: true,
            mergeDisplay: true,
            debug: true,
            preventMouseDownDefault: true
        };

        let myKeyboard = new Keyboard({
            ...commonKeyboardOptions,
            layout: russian,
            display: {
                '{lang}': 'language',
            },
            buttonTheme: [
                {
                    class: 'lang-key',
                    buttons: '{lang}'
                }
            ],
        });

        myKeyboard.setInput = 'product-search__input';

        function onChange(input) {
            document.querySelector("#product-search__input").value = input;
            console.log("Input changed", input);
        }

        function onKeyPress(button) {
            console.log("Button pressed", button);
            if (button === "{shift}") handleShiftButton();
            if (button === "{lang}") handleLangButton();
        }

        function handleShiftButton() {
            let currentLayout = myKeyboard.options.layoutName;
            let shiftToggle = currentLayout === "shift" ? "default" : "shift";

            myKeyboard.setOptions({
                layoutName: shiftToggle
            });
        }

        function handleLangButton() {
            console.log('lang button pressed!');
            let currentLayout = myKeyboard.options.layout;
            let langToggle = currentLayout === russian ? english : russian;

            myKeyboard.setOptions({
                layout: langToggle
            });
        }

        /* Numpad init */
        let commonKeyboardOptionsNumpad = {
            onChange: input => onChangeNumpad(input),
            onKeyPress: button => onKeyPressNumpad(button),
            theme: "simple-keyboard hg-theme-default hg-layout-default",
            physicalKeyboardHighlight: true,
            syncInstanceInputs: true,
            mergeDisplay: true,
            debug: true,
            disableCaretPositioning: true,
            preventMouseDownDefault: true
        };

        let keyboardNumPad = new Keyboard(".simple-keyboard-numpad", {
            ...commonKeyboardOptionsNumpad,
            layout: {
                default: [
                    "{backspace}",
                    "{numpad7} {numpad8} {numpad9}",
                    "{numpad4} {numpad5} {numpad6}",
                    "{numpad1} {numpad2} {numpad3}",
                    "{numpad0} {numpaddecimal}"
                ]
            },
            display: {
                '{backspace}': 'C'
            },
        });

        function onChangeNumpad(input) {
            // document.querySelector(selectedInput || ".order-item__quantity").value = 8;
            $(document).find(selectedInput).val(input)[0].dispatchEvent(new CustomEvent('input'));
            console.log("Input suka", selectedInput);
        }

        function onKeyPressNumpad(button) {
            console.log("Button pressed", button);
        }

        /* Numpad selected input */
        let selectedInput;
        let thisInput;
        let selectedInputPosition;
        let numPad = $('.simple-numPad');
        $(document).on('focusin', '.order-item__quantity', function (event) {
            thisInput = $( this );
            selectedInput = thisInput.change();
            selectedInputPosition = thisInput.offset();

            numPad.css({"top": selectedInputPosition['top'] + 32, "left": selectedInputPosition['left']});
            numPad.toggle();
            console.log(event.target.id);
            keyboardNumPad.setOptions({
                inputName: event.target.id
            });
        });

        $(document).on('focusout', '.order-item__quantity', function (event) {
            console.log('focused out');
            numPad.toggle();
        });

        $(document).on('input', '.order-item__quantity', function (event) {
            console.log(event.target.id);
            keyboardNumPad.setInput(event.target.value, event.target.id);
        });

        $(document).on('touchspin.on.stopspin', '.order-item__quantity', function() {
            $( this )[0].dispatchEvent(new CustomEvent('input'));
        });

        $(document).scannerDetection({
            timeBeforeScanTest: 200, // wait for the next character for upto 200ms
            startChar: [120], // Prefix character for the cabled scanner (OPL6845R)
            // endChar: [13], // be sure the scan is complete if key 13 (enter) is detected
            avgTimeByChar: 40, // it's not a barcode if a character takes longer than 40ms
            ignoreIfFocusOn: 'input',
            onComplete: function(barcode, qty){
                $.post({
                    url: 'get-product-by-barcode',
                    dataType: 'Json',
                    data: {
                        barcode: barcode
                    },
                    success: function(result) {
                        console.log(result);
                        orderListApp.setProduct(result);
                    },
                    error: function() {
                        console.log('Ошибка пойска!');
                    }
                });
            }
        });

        $(document).on('click', '#product-search__keyboard', function () {
            $('.simple-keyboard').toggle();
        });
        $(document).on('click', '#product-search__clear', () => {
            $('#product-search__input').val('');
            this.searchProduct();
        });
    }
});



/* Product cards grid system */
let masonryOptions = {
    // set itemSelector so .grid-sizer is not used in layout
    itemSelector: '.grid-item',
    // use element for option
    columnWidth: '.grid-sizer',
    percentPosition: true
};

$('#dynamic-form').on('beforeSubmit', function() {
    if (orderListApp.orders[this.currentOrder].products.length > 0) {
        return true;
    } else {
        noProductsAlert();
        return false;
    }
});

function noProductsAlert() {
    alert('Товар отсутвует, пожалуйста добавьте товар или обратитесь к администратору');
}

$(function(){
    //get the click of modal button to create / update item
    //we get the button by class not by ID because you can only have one id on a page and you can
    //have multiple classes therefore you can have multiple open modal buttons on a page all with or without
    //the same link.
//we use on so the dom element can be called again if they are nested, otherwise when we load the content once it kills the dom element and wont let you load anther modal on click without a page refresh
    $(document).on('click', '.showModalButton', function(){
        //check if the modal is open. if it's open just reload content not whole modal
        //also this allows you to nest buttons inside of modals to reload the content it is in
        //the if else are intentionally separated instead of put into a function to get the
        //button since it is using a class not an #id so there are many of them and we need
        //to ensure we get the right button and content.
        if ($('#modal').data('bs.modal').isShown) {
            $('#modal').find('#modalContent')
                .load($(this).attr('value'));
            //dynamiclly set the header for the modal
            document.getElementById('modalHeader').innerHTML = '<h4>' + $(this).attr('title') + '</h4>';
        } else {
            //if modal isn't open; open it and load content
            $('#modal').modal('show')
                .find('#modalContent')
                .load($(this).attr('value'));
            //dynamiclly set the header for the modal
            document.getElementById('modalHeader').innerHTML = '<h4>' + $(this).attr('title') + '</h4>';
        }
    });
});