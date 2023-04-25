(function( $ ) {
    $(document).ready(function () {
        function firstToUpperCase( str ) {
            return str.substr(0, 1).toUpperCase() + str.substr(1);
        }
        function formatState (state) {
            if (!state.id) {
                return state.text;
            }
            var $state = $(
                '<div class="select_option_data">' +
                '<div class="select_option_icon"></div><div class="select_option_label" title="' + state.text + '">' + state.text + '</div>' +
                '</div>'
            );
            return $state;
        };
        $('select.boost-search-wc-products').select2({
            // placeholder: "Search for a WooCommerce Product",
            minimumInputLength: 3,
            multiple: true,
            theme: 'boost_multiple',
            closeOnSelect: false,
            templateResult: formatState,
            ajax: {
                url: BOOST_Ajax.ajaxurl,
                type: 'POST',
                dataType: 'json',
                delay: 250,
                quietMillis: 250,
                data: function (params, page) { // page is the one-based page number tracked by Select2
                    return {
                        // wp ajax action
                        action: 'boost_search_items_ajax',
                        search_string: params.term, //search term,
                        search_type: 'wc_products',
                        nonce: BOOST_Ajax.search_items_nonce
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: $.map(data.data.items, function (obj) {
                            return {id: obj.id.toString(), text: obj.post_title.toString()};
                        })
                    };
                }
            },
            cache: true
        });
        $('select.boost-search-wc-product-categories').select2({
            // placeholder: "Search for a WooCommerce Product Category",
            minimumInputLength: 3,
            multiple: true,
            theme: 'boost_multiple',
            closeOnSelect: false,
            templateResult: formatState,
            ajax: {
                url: BOOST_Ajax.ajaxurl,
                type: 'POST',
                dataType: 'json',
                delay: 250,
                quietMillis: 250,
                data: function (params, page) { // page is the one-based page number tracked by Select2
                    return {
                        // wp ajax action
                        action: 'boost_search_items_ajax',
                        search_string: params.term, //search term,
                        search_type: 'wc_product_categories',
                        nonce: BOOST_Ajax.search_items_nonce
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: $.map(data.data.items, function (obj) {
                            return {id: obj.term_id.toString(), text: obj.name.toString()};
                        })
                    };
                }
            },
            cache: true
        });
        $('select.boost-search-edd-downloads').select2({
            // placeholder: "Search for a WooCommerce Product",
            minimumInputLength: 3,
            multiple: true,
            theme: 'boost_multiple',
            closeOnSelect: false,
            templateResult: formatState,
            ajax: {
                url: BOOST_Ajax.ajaxurl,
                type: 'POST',
                dataType: 'json',
                delay: 250,
                quietMillis: 250,
                data: function (params, page) { // page is the one-based page number tracked by Select2
                    return {
                        // wp ajax action
                        action: 'boost_search_items_ajax',
                        search_string: params.term, //search term,
                        search_type: 'edd_downloads',
                        nonce: BOOST_Ajax.search_items_nonce
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: $.map(data.data.items, function (obj) {
                            return {id: obj.id.toString(), text: obj.post_title.toString()};
                        })
                    };
                }
            },
            cache: true
        });
        $('select.boost-search-edd-download-categories').select2({
            // placeholder: "Search for a WooCommerce Product Category",
            minimumInputLength: 3,
            multiple: true,
            theme: 'boost_multiple',
            closeOnSelect: false,
            templateResult: formatState,
            ajax: {
                url: BOOST_Ajax.ajaxurl,
                type: 'POST',
                dataType: 'json',
                delay: 250,
                quietMillis: 250,
                data: function (params, page) { // page is the one-based page number tracked by Select2
                    return {
                        // wp ajax action
                        action: 'boost_search_items_ajax',
                        search_string: params.term, //search term,
                        search_type: 'edd_download_categories',
                        nonce: BOOST_Ajax.search_items_nonce
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: $.map(data.data.items, function (obj) {
                            return {id: obj.term_id.toString(), text: obj.name.toString()};
                        })
                    };
                }
            },
            cache: true
        });
        $('select.boost-search-post-types').select2({
            // placeholder: "Search for a WooCommerce Product Category",
            minimumInputLength: 3,
            multiple: true,
            theme: 'boost_multiple',
            closeOnSelect: false,
            templateResult: formatState,
            ajax: {
                url: BOOST_Ajax.ajaxurl,
                type: 'POST',
                dataType: 'json',
                delay: 250,
                quietMillis: 250,
                data: function (params, page) { // page is the one-based page number tracked by Select2
                    return {
                        // wp ajax action
                        action: 'boost_search_items_ajax',
                        search_string: params.term, //search term,
                        search_type: 'post_types',
                        nonce: BOOST_Ajax.search_items_nonce
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: $.map(data.data.items, function (obj) {
                            return {id: obj.id.toString(), text: obj.name.toString()};
                        })
                    };
                }
            },
            cache: true
        });
        $('select.boost-search-specific-pages').select2({
            // placeholder: "Search for a WooCommerce Product",
            minimumInputLength: 3,
            multiple: true,
            theme: 'boost_multiple',
            closeOnSelect: false,
            templateResult: formatState,
            ajax: {
                url: BOOST_Ajax.ajaxurl,
                type: 'POST',
                dataType: 'json',
                delay: 250,
                quietMillis: 250,
                data: function (params, page) { // page is the one-based page number tracked by Select2
                    return {
                        // wp ajax action
                        action: 'boost_search_items_ajax',
                        search_string: params.term, //search term,
                        search_type: 'specific_pages',
                        nonce: BOOST_Ajax.search_items_nonce
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: $.map(data.data.items, function (obj) {
                            return {id: obj.id.toString(), text: obj.post_title.toString() + ' (' + firstToUpperCase(obj.post_type.toString().replace('_', ' ')) + ')'};
                        })
                    };
                }
            },
            cache: true
        });
        $('select.boost-search-taxonomies').select2({
            // placeholder: "Search for a WooCommerce Product",
            minimumInputLength: 3,
            multiple: true,
            theme: 'boost_multiple',
            closeOnSelect: false,
            templateResult: formatState,
            ajax: {
                url: BOOST_Ajax.ajaxurl,
                type: 'POST',
                dataType: 'json',
                delay: 250,
                quietMillis: 250,
                data: function (params, page) { // page is the one-based page number tracked by Select2
                    return {
                        // wp ajax action
                        action: 'boost_search_items_ajax',
                        search_string: params.term, //search term,
                        search_type: 'taxonomies',
                        nonce: BOOST_Ajax.search_items_nonce
                    };
                },
                processResults: function (data, params) {
                    var terms = $.map(data.data.items.terms, function (obj) {
                        return {
                            id: obj.term_id.toString(), text: obj.name.toString() + ' (' + firstToUpperCase(obj.taxonomy.toString().replace('_', ' ')) + ')'
                        };
                    });
                    var taxonomies = $.map(data.data.items.taxonomies, function (obj) {
                        return {
                            id: obj.name.toString(), text: obj.label.toString() + ' (All)'
                        };
                    });
                    return {results: terms.concat(taxonomies)};
                }
            },
            cache: true
        });

        $('.boost-items-list-table tbody tr th .boost-items-list-table-actions button[name="edit"]').on('click', function (event) {
            var html = '<span>\
                                <button type="button"\
                                    name="save"\
                                    value="save"\
                                    class="boost-button boost-button-small boost-button-action action-save">\
                                    <span class="boost-action-icon"></span>\
                                    <span>Save</span>\
                                </button>\
                                <button type="button"\
                                    name="cancel"\
                                    value="cancel"\
                                    class="boost-button boost-button-small boost-button-action action-cancel">\
                                    <span class="boost-action-icon"></span>\
                                    <span>Cancel</span>\
                                </button>\
                            </span>';
            
            var table_row = $(this).closest('tr');
            var table_row_copy = table_row.clone();
            table_row_copy.removeClass('boost-items-list-row');
            table_row_copy.addClass('boost-items-list-hidden-row');
            table_row_copy.find('.boost-items-list-table-actions').html(html);
            table_row_copy.find('.boost-items-list-changeable-cell').each(function(){
                var cell_id = $(this).attr('id');
                var cell_text = $(this).text();
                var new_cell_html = '<input type="text" name="'+cell_id+'" value="'+escapeHtml(cell_text)+'" class="boost-items-list-pages-input-style"/>'
                $(this).html(new_cell_html);
            });

            table_row.after(table_row_copy);
            table_row.hide();
            table_row.next().show();
        });

        $('.boost-items-list-table tbody').on('click', 'tr th .boost-items-list-table-actions button[name="save"]', function (event) {
            var table_row = $(this).closest('tr');

            var action_type = table_row.closest('table').attr('id');
            var item_id = table_row.attr('id');
            var item_data = {};
            table_row.find('.boost-items-list-changeable-cell').each(function () {
                var cell_id = $(this).attr('id');
                item_data[cell_id] = $(this).find('input[name="' + cell_id + '"]').val();
            });

            $('.boost-loading').show();
            $.post(
                BOOST_Ajax.ajaxurl,
                {
                    // wp ajax action
                    action: 'boost_edit_item_ajax',

                    // vars
                    action_type: action_type,
                    item_id: item_id,
                    item_data: item_data,

                    // send the nonce along with the request
                    nonce: BOOST_Ajax.edit_item_nonce
                },
                function (response) {
                    if ('Error' != response && response.success == true) {
                        var data = response.data;
                        var item_id = data.item_id;
                        var item_data = data.item_data;


                        var table_row = $('.boost-items-list-table tbody tr.boost-items-list-row#' + item_id + ' th .boost-items-list-table-actions').closest('tr');

                        table_row.find('.boost-items-list-changeable-cell').each(function () {
                            var cell_id = $(this).attr('id');
                            $(this).text(item_data[cell_id]);
                        });

                        table_row.show();
                        table_row.next().hide(0, function () {
                            $(this).remove();
                        });
                    }
                    $('.boost-loading').hide();
                }
            );
        });

        $('.boost-make-boost button[name="search_forms"]').on('click', function (event) {
            var capture_url = $('input[name="boost\[capture_url\]"]').val();
            var input_button_box = $(this).closest('.boost-input-button-box');
            var form_selector = $('select[id="boost\[form_selector\]"]');
            var capture_form_fields_select = $('select[id="capture_form_fields"]');
            var username_field_select = $('select[id="boost\[form_username_field\]"]');
            var surname_field_select = $('select[id="boost\[form_surname_field\]"]');
            var error_container = $('div.boost-make-boost-capture-url div.boost-make-boost-field-error-message');

            input_button_box.removeClass('boost-search-error');
            error_container.hide().text('');
            document.getElementById(form_selector.attr('id')).selectedIndex = -1;
            document.getElementById(username_field_select.attr('id')).selectedIndex = -1;
            document.getElementById(surname_field_select.attr('id')).selectedIndex = -1;
            $(form_selector.selector + ',' + username_field_select.selector + ',' + surname_field_select.selector).closest('.boost-make-boost-field').hide();
            $(form_selector.selector + ',' + capture_form_fields_select.selector + ',' + username_field_select.selector + ',' + surname_field_select.selector).empty();

            if(capture_url != '') {
                $('.boost-loading').show();
                $.post(
                    BOOST_Ajax.ajaxurl,
                    {
                        // wp ajax action
                        action: 'boost_search_forms_ajax',

                        // vars
                        capture_url: encodeURIComponent(capture_url),

                        // send the nonce along with the request
                        nonce: BOOST_Ajax.search_forms_nonce
                    },
                    function (response) {
                        if ('Error' != response && response.success == true) {
                            var data = response.data;
                            var forms_data = data.forms_data;

                            if ('error' in data) {
                                input_button_box.addClass('boost-search-error');
                                error_container.text(data['error']).show();
                            }
                            else {

                                if (forms_data.length > 0) {
                                    for (var i = 0; i < forms_data.length; i++) {
                                        var form_name = forms_data[i].id != '' ? 'form#' + forms_data[i].id : forms_data[i].selector;
                                        var form_id = forms_data[i].id != '' ? '#' + forms_data[i].id : forms_data[i].selector;
                                        if (form_name != '') {

                                            form_selector
                                                .append($("<option></option>")
                                                    .attr("value", form_id)
                                                    .text(form_name));


                                            for (var j = 0; j < forms_data[i].fields.length; j++) {

                                                capture_form_fields_select
                                                    .append($("<option></option>")
                                                        .attr("value", forms_data[i].fields[j].value)
                                                        .attr("data-form-id", form_id)
                                                        .text(forms_data[i].fields[j].name));

                                            }
                                        }
                                    }

                                    form_selector.change();
                                    $(form_selector.selector + ',' + username_field_select.selector + ',' + surname_field_select.selector).closest('.boost-make-boost-field').show();
                                }
                            }
                        }
                        $('.boost-loading').hide();
                    }
                );
            }

        });

        $('.boost-items-list-table tbody').on('click', 'tr th .boost-items-list-table-actions button[name="cancel"]', function (event) {

            var table_row = $(this).closest('tr').prev();
            table_row.next().hide(0, function () {
                $(this).remove();
            });
            table_row.show();

        });

        $('tbody').on('keydown', '.boost-items-list-hidden-row input', (function (event) {
                if (event.keyCode == 13) {
                    event.preventDefault();
                    $(this).closest('tr').find('button[name="save"]').click();
                    return false;
                }
            })
        );
    });

    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };

        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
})( jQuery );
