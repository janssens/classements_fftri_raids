function defer(method) {
    if (window.jQuery) {
        method();
    } else {
        setTimeout(function() { defer(method) }, 50);
    }
}

function getRegistrationInfo(value,fct){
    $.ajax({
        'data': {id:value},
        'method': 'POST',
        'url': '/registration/',
        'format': 'json',
        'complete': function(jqXHR, textStatus){
            if (textStatus == 'success'){
                //console.log(jqXHR.responseJSON);
                fct(jqXHR.responseJSON);
            }
        }
    });
}
function getRegistrationInfoFromNumber(value,fct){
    $.ajax({
        'data': {number:value},
        'method': 'POST',
        'url': '/registration/from_number',
        'format': 'json',
        'complete': function(jqXHR, textStatus){
            if (textStatus == 'success'){
                //console.log(jqXHR.responseJSON);
                fct(jqXHR.responseJSON);
            }
        }
    });
}