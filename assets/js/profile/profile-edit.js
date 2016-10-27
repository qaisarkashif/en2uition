var componentForm = {
    locality: 'long_name',//city
    administrative_area_level_1: 'long_name',//state
    country: 'long_name'//country
};
living_in = {
    country: '',
    administrative_area_level_1: '',
    locality: ''
};

function initialize() {
    var autocomplete = new google.maps.places.Autocomplete(
            /** @type {HTMLInputElement} */(document.getElementById('living')),
            {types: ['geocode']});
    google.maps.event.addListener(autocomplete, 'place_changed', function() {
        var place = autocomplete.getPlace();
        for (var component in componentForm) {
            document.getElementById(component).value = '';
            living_in[component] = '';
        }
        for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];
            if (componentForm[addressType]) {
                var val = place.address_components[i][componentForm[addressType]];
                document.getElementById(addressType).value = val;
                living_in[addressType] = val;
            }
        }
        var str = "";
        for (var i in living_in) {
           str += $.trim(living_in[i]) != '' ? living_in[i] + ", " : "";
        }
        $("#living").val(str.length > 2 ? str.substring(0, str.length - 2) : str);
    });
}

function clearAddress() {
    $("#living").val('');
    for (var component in living_in) {
        document.getElementById(component).value = '';
        living_in[component] = '';
    }
}

$(function() {
    initialize();
    
    $("#age").datepicker({viewMode: 'years', format: 'mm/dd/yyyy'});
    
    $("#frm-edit-pro").submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: '/profile/update',
            type: 'post',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(data) {
                if(data.errors !== undefined) {
                    if($.trim(data.errors) == "") {
                        var alert = '<div class="alert alert-success alert-dismissible" role="alert">';
                        alert += '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                        alert += '<strong>Profile has been successfully updated.</strong></div>';
                        var username = $.trim($("#username").val());
                        if(username == '') {
                            username = '<span>&nbsp;</span>';
                        }
                        $("#hmpg-username", window.parent.document).html(username);
                        var info_block = $("ul.pro-info", window.parent.document);
                        if(info_block.length !== 0) {
                            info_block.find("li:eq(0) span").text($("#living").val());
                            info_block.find("li:eq(1) span").text($("#age").val());
                            info_block.find("li:eq(2) span").text($("#education option:selected").text());
                            info_block.find("li:eq(3) span").text($("#gender option:selected").text());
                            info_block.find("li:eq(4) span").text($("#sexual_ori option:selected").text());
                        }
                        if(data.color !== undefined && $(".profile-shape > img", window.parent.document).length !== 0) {
                            $(".profile-shape > img", window.parent.document).attr('class', 'shape-'+data.color);
                        }
                    } else {
                        var alert = '<div class="alert alert-danger alert-dismissible" role="alert">';
                        alert += '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                        alert += data.errors + '</div>';
                    }
                    $(".frm-edit-info ul").before(alert);
                }
            },
            error: function (xhr, text_status, error_thrown) { if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } }
        });
    });
});