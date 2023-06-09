(function( $ ) {
    $(document).ready(function () {
        var current_url = location.protocol + '//' + location.host + location.pathname;
        var leads_triggers = BOOST_public_Ajax.leads_triggers;
        for (var i in leads_triggers) {
            if (leads_triggers[i]['capture_url'] != '' && current_url.indexOf(leads_triggers[i]['capture_url']) == 0) {
                var selector = leads_triggers[i]['form_selector'] != '' ? leads_triggers[i]['form_selector'] : '#content form';
                if (selector.indexOf('nf-form') !== 1){
                    $(selector).on('submit', {boost_id: leads_triggers[i]['boost_id'], username_field: leads_triggers[i]['form_username_field'], surname_field: leads_triggers[i]['form_surname_field']}, function (event) {
                        var name = event.data.username_field != '' ? $(this).find('input[name="' + event.data.username_field + '"]').val() : '';
                        var surname = event.data.surname_field != '' ? $(this).find('input[name="' + event.data.surname_field + '"]').val() : '';
                        var user_name = '';
                        if (name == '') {
                            user_name = surname;
                        }
                        else {
                            user_name = name + (surname && surname != '' ? ' ' + surname[0] : '');
                        }

                        $.post(
                            BOOST_public_Ajax.ajaxurl,
                            {
                                // wp ajax action
                                action: 'boost_submit_form_ajax',

                                // vars
                                boost_id: event.data.boost_id,
                                user_name: user_name,

                                // send the nonce along with the request
                                nonce: BOOST_public_Ajax.submit_form_nonce
                            },
                            function (response) {
                                // response = $.parseJSON(response);
                                // var user_location = response.user_location;
                                // if (user_location){
                                // setCookie('BOOST_USER_LOCATION', user_location);
                                // document.cookie = 'BOOST_USER_LOCATION=' + user_location + ';' + 0 + ';path=/';
                                // }
                                console.log(response);
                            }
                        );
                    });
                }
                else {
                    $(document).on('nfFormSubmitResponse', {boost_id: leads_triggers[i]['boost_id'], username_field: leads_triggers[i]['form_username_field'], surname_field: leads_triggers[i]['form_surname_field'], nf_form_id: selector}, function (event, submitted_nf_form_data) {
                        nf_form_id = event.data.nf_form_id.replace(/[^0-9]/g, "");
                        if (nf_form_id != submitted_nf_form_data.id) {
                            return;
                        }
                        var name = event.data.username_field != '' ? submitted_nf_form_data.response.data.fields[event.data.username_field].value : '';
                        var surname = event.data.surname_field != '' ? submitted_nf_form_data.response.data.fields[event.data.surname_field].value : '';
                        var user_name = '';
                        if (name == '') {
                            user_name = surname;
                        }
                        else {
                            user_name = name + (surname && surname != '' ? ' ' + surname[0] : '');
                        }

                        $.post(
                            BOOST_public_Ajax.ajaxurl,
                            {
                                // wp ajax action
                                action: 'boost_submit_form_ajax',

                                // vars
                                boost_id: event.data.boost_id,
                                user_name: user_name,

                                // send the nonce along with the request
                                nonce: BOOST_public_Ajax.submit_form_nonce
                            },
                            function (response) {
                                // response = $.parseJSON(response);
                                // var user_location = response.user_location;
                                // if (user_location){
                                // setCookie('BOOST_USER_LOCATION', user_location);
                                // document.cookie = 'BOOST_USER_LOCATION=' + user_location + ';' + 0 + ';path=/';
                                // }
                                console.log(response);
                            }
                        );
                    });
                }
            }
        }
    });
})( jQuery );