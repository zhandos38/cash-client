let menuApp = new Vue({
    el: '#menu-app',
    data: {
        cashBoxBalance: '',
        currentTime: '',
        shiftOpenModalActive: false,
        shiftCloseModalActive: false,
        openShiftActive: true
    },
    methods: {
        setBalance(value) {
            // this.order.takenCash = this.order.takenCash.toString();
            if (typeof value == 'number') {
                this.cashBoxBalance = parseFloat(this.cashBoxBalance === '' ? 0 : this.cashBoxBalance) + value;
                return true;
            }

            if ((this.cashBoxBalance === '' || this.cashBoxBalance.slice(-1) === '.') && value === '.')
                return true;

            if (this.cashBoxBalance === '' && value === '0')
                this.cashBoxBalance = '';
            else
                this.cashBoxBalance += value;
        },
        cleanBalance() {
            this.cashBoxBalance = '';
        },
        openShiftCloseModal() {
            this.shiftCloseModalActive = true;
        },
        closeShiftCloseModal() {
            this.shiftCloseModalActive = false;
        },
        closeShift() {
            $.post({
                url: '/order/close-shift',
                data: {sum: this.cashBoxBalance},
                success: () => {
                    this.cashBoxBalance = '';
                    this.openShiftActive = true;
                    this.shiftCloseModalActive = false;
                },
                error: function () {
                    alert('Ошибка закрытия смены, попробуйте снова или обратитесь к администратору');
                }
            });
        },
        openShiftOpenModal() {
            this.shiftOpenModalActive = true;
        },
        closeShiftOpenModal() {
            this.shiftOpenModalActive = false;
        },
        setShift() {
            $.post({
                url: '/order/set-shift',
                data: {sum: this.cashBoxBalance},
                success: () => {
                    this.openShiftActive = false;
                    this.shiftOpenModalActive = false;
                },
                error: function () {
                    alert('Ошибка закрытия смены, попробуйте снова или обратитесь к администратору');
                }
            });
        },
        checkShift() {
            $.get({
                url: '/order/check-shift',
                success: result => {
                    if (result) {
                        console.log('Open shift is disabled');
                        this.openShiftActive = false;
                    }
                    else {
                        console.log('Open shift is active');
                        this.openShiftActive = true;
                    }
                }
            });
        }
    },
    mounted() {
        this.checkShift();

        function display_time() {
            let refresh = 1000; // Refresh rate in milli seconds
            setTimeout(() => {
                menuApp.currentTime = new Date().toLocaleString();
                display_time();
            }, refresh);
        }
        display_time();
    }
});