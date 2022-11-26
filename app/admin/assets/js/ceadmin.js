$(document).ready(function() {

    $('#datepicker').datepicker({
        format: "dd-mm-yyyy",
        orientation: "bottom auto"
    });
    $('#datepicker2').datepicker({
        format: "dd-mm-yyyy",
        orientation: "bottom auto"
    });

    $.fn.extend({
        check : function()  {
           return this.filter(":radio, :checkbox").attr("checked", true);
        },
        uncheck : function()  {
           return this.filter(":radio, :checkbox").removeAttr("checked");
        }
     });
});

function CEA_ShowRate(fee) {
    var from = $("#gateway_from").val();
    var to = $("#gateway_to").val();
    var is_allowed = $("#percentage_fee").val();
    if(is_allowed == "allow") {
        var data_url = "./requests/load.php?a=rate&from="+from+"&to="+to+"&fee="+fee;
        $.ajax({
            type: "GET",
            url: data_url,
            dataType: "json",
            success: function (data) {
                if(data.status == "success") {
                    var exchange_rate = data.rate_from+' '+data.currency_from+' = '+data.rate_to+' '+data.currency_to;
                    var reserve = data.reserve+' '+data.currency_to;
                    $("#rate_status").show();
                    $("#exchange_rate").val(exchange_rate);
                }
            }
        });
    }
}

function CEA_LoadFields(value) {
    var data_url = "./requests/fields.php?gateway="+value;
    $.ajax({
        type: "GET",
        url: data_url,
        dataType: "html",
        success: function (data) {
            $("#account_fields").html(data);
        }
    });
}

function CEA_LoadCryptoFields(merchant) {
    var data_url = "requests/crypto_fields.php?merchant="+merchant;
    $.ajax({
        type: "GET",
        url: data_url,
        dataType: "html",
        success: function (data) {
            $("#merchant_fields").html(data);
        }
    });
}

function CEA_ShowField(num) {
    if(num == "4") {
        $("#txfield").show();
    } else {
        $("#txfield").hide();
    }
}