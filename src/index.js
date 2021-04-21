document.querySelectorAll('.recaptcha-module').forEach(function(el){
    el.closest('form').classList.add('with-recaptcha');


    var el2 = document.createElement('input');
    el2.type = "hidden";
    el2.name = 'recaptcha_response';
    el2.classList.add('recaptcha_response');
    el2.style.display = 'none';

    el.closest('form').appendChild(el2);

    el.closest('form').addEventListener('click',function(){

        let activeForm = document.querySelector('.with-recaptcha.active');

        if( activeForm ){
            activeForm.classList.remove('active');
        }

        this.classList.add('active');
    });

});

