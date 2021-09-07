$("#password").on("keyup", function() {
    var password = document.getElementById("password").value;
    console.log(password.length);
    var regex = /^[\w(!@#$%^&*()_+\-={};':"|,.<>?)][^\s(àáãạảăắằẳẵặâấầẩẫậèéẹẻẽêềếểễệđìíĩỉịòóõọỏôốồổỗộơớờởỡợùúũụủưứừửữựỳỵỷỹýÀÁÃẠẢĂẮẰẲẴẶÂẤẦẨẪẬÈÉẸẺẼÊỀẾỂỄỆĐÌÍĨỈỊÒÓÕỌỎÔỐỒỔỖỘƠỚỜỞỠỢÙÚŨỤỦƯỨỪỬỮỰỲỴỶỸÝ)]{7,}$/;
    if (regex.test(password)) {
        document.getElementById("password").style.border = "1px solid Gainsboro";
        document.getElementById("errPass").style.display = "none";
        return true;
    } else {
        document.getElementById("password").style.border = "1px solid red";
        document.getElementById("errPass").style.display = "";
        return false;
    }
})

function validate(evt) {
    var theEvent = evt || window.event;

    // Handle paste
    if (theEvent.type === 'paste') {
        key = event.clipboardData.getData('text/plain');
    } else {
        // Handle key press
        var key = theEvent.keyCode || theEvent.which;
        key = String.fromCharCode(key);
    }
    var regex = /[0-9]|\./;
    if (!regex.test(key)) {
        theEvent.returnValue = false;
        if (theEvent.preventDefault) theEvent.preventDefault();
    }
}

$("#mobile").on("keyup", function() {
    var mobile = $('#mobile').val();
    var regex = /^[0-9]{9}$/;
    if(mobile == ""){
        document.getElementById("mobile").style.border = "1px solid Gainsboro";
    }else{
        if (!regex.test(mobile)) {
            document.getElementById("mobile").style.border = "1px solid red";
            document.getElementById("errMobile").style.display = "";
            return false;
        } else {
            document.getElementById("mobile").style.border = "1px solid Gainsboro";
            document.getElementById("errMobile").style.display = "none";
            return true;
        }
    }
 
})
$("#id_no").on("keyup", function() {
    var id_no = $('#id_no').val();
    var regex = /^[0-9]{9,12}$/;
    if(id_no == ""){
        document.getElementById("id_no").style.border = "1px solid Gainsboro";
    }else{
        if (!regex.test(id_no)) {
            document.getElementById("id_no").style.border = "1px solid red";
            document.getElementById("errIdNo").style.display = "";
            return false;
        } else {
            document.getElementById("id_no").style.border = "1px solid Gainsboro";
            document.getElementById("errIdNo").style.display = "none";
            return true;
        }
    }
   
})
$("#prob_salary").on("keyup", function() {
    var prob_salary = $('#prob_salary').val();
    var regex = /^[0-9]{1,}$/;
    if(prob_salary == ""){
        document.getElementById("prob_salary").style.border = "1px solid Gainsboro";
    }else{
        if (!regex.test(prob_salary)) {
        document.getElementById("prob_salary").style.border = "1px solid red";
        return false;
        } else {
            document.getElementById("prob_salary").style.border = "1px solid Gainsboro";
            return true;
        }
    }
})
$("#office_salary").on("keyup", function() {
    var office_salary = $('#office_salary').val();
    var regex = /^[0-9]{1,}$/;
    if(office_salary == ""){
        document.getElementById("office_salary").style.border = "1px solid Gainsboro";
    }else{
        if (!regex.test(office_salary)) {
        document.getElementById("office_salary").style.border = "1px solid red";
        return false;
        } else {
            document.getElementById("office_salary").style.border = "1px solid Gainsboro";
            return true;
        }
    }
   
})
$("#account_number").on("keyup", function() {
    var account_number = $('#account_number').val();
    var regex = /^[0-9]{1,}$/;
    if(account_number == ""){
        document.getElementById("account_number").style.border = "1px solid Gainsboro";
    }else{
        if (!regex.test(account_number)) {
            document.getElementById("account_number").style.border = "1px solid red";
            return false;
        } else {
            document.getElementById("account_number").style.border = "1px solid Gainsboro";
            return true;
        }
    }
    
})

$("#email").on("keyup", function() {
    var email = document.getElementById("email").value;
    var regex =
        /(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/;
    if (!regex.test(email)) {
        document.getElementById("email").style.border = "1px solid red";
        document.getElementById("errEmail").style.display = "";
        return false;
    } else {
        document.getElementById("email").style.border = "1px solid Gainsboro";
        document.getElementById("errEmail").style.display = "none";
        return true;
    }
})
