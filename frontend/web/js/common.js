$('.form-password__input_btn').on('click', function(){
    var inputType = $('.input-password').attr('type');
    if (inputType == 'password') {
        $('.input-password').attr('type', 'text');
        $('#eye').attr('class', 'fa fa-eye-slash');
    }else{
        $('.input-password').attr('type', 'password');
        $('#eye').attr('class', 'fa fa-eye');
    }
});

$('.form-password-repeat__input_btn').on('click', function(){
    var inputType = $('.input-password-repeat').attr('type');
    if (inputType == 'password') {
        $('.input-password-repeat').attr('type', 'text');
        $('#eye-slash').attr('class', 'fa fa-eye-slash');
    }else{
        $('.input-password-repeat').attr('type', 'password');
        $('#eye-slash').attr('class', 'fa fa-eye');
    }
});


$("#username_input").keyup(function(e){
    this.value = this.value.replace(/[^0-9\.]/g, '');
});

$("#password_input").keyup(function(e){
    this.value = this.value.replace(/[а-яА-ЯёЁ]/g, '');
});

$("#password_repeat_input").keyup(function(e){
    this.value = this.value.replace(/[а-яА-ЯёЁ]/g, '');
});

