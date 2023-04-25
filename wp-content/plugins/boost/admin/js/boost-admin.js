(function( $ ) {
    $(document).ready(function () {

        /* DESKTOP POSITION */
        $('.boost-position').on('click', '.position-place:not(.disabled)', function(){
            var obj = this;
            if (!$(obj).hasClass('active')) {
                $(obj).parent().children('.position-place').removeClass('active');
                var new_position = $(obj).attr('data-position');
                $(obj).parent().find('input.boost-position').val(new_position);
                $(obj).addClass('active');
            }
        });

        /* SPINNER */

        $('.spinner-control').on('click', function(){
            if ($(this).hasClass('spinner-down')) {
                $(this).parent().parent().find('input[type="number"]')[0].stepDown();
            }
            else {
                $(this).parent().parent().find('input[type="number"]')[0].stepUp();
            }
        });

        $('.boost-page-tab-list').on('change', 'input[type="radio"]', function(){
            var obj = this;
            if ($(obj).prop('checked')) {
                var tab = $(obj).val();

                $.when($('.boost-tabs-container>*.boost-tab').not('.boost-tab-' + tab).hide('fast', function () {
                    $('.boost-tabs-container>.boost-tab-' + tab).show('fast', function () {})
                })
                ).then(function(){
                });
            }
        });

        $('.boost-dropdown>.boost-dropdown-toggle').on('click', function () {
            $(this).closest('.boost-dropdown').children('.boost-dropdown-content').toggle("fast", function () {
                $(this).closest('.boost-dropdown').toggleClass('boost-dropdown-active');
            });
        });

        $(document).mouseup(function (e) {
            var dropdown_menu = $(".boost-dropdown.boost-dropdown-active.boost-dropdown-menu");
            if (dropdown_menu.has(e.target).length === 0){
                dropdown_menu.children('.boost-dropdown-toggle').click();
            }
        });

        $('.boost-make-boost-subtype select').on('change', function(){
            var selectedSubtype = $(this).val();
            var containersBoxId = $(this).attr('data-containers-box-id');
            var containerId = 'boost-make-boost-subtype-container-' + selectedSubtype;

            $(this).closest('.boost-make-boost-field').find('.boost-make-boost-subtype-description.boost-active').removeClass('boost-active');

            $(this).closest('.boost-make-boost-step-container').find('.boost-make-boost-fake-block').hide();

            show_container(containersBoxId, containerId);

            $(this).closest('.boost-make-boost-field').find('.boost-make-boost-subtype-description.boost-make-boost-subtype-'+selectedSubtype).addClass('boost-active');

            if (selectedSubtype != 'stock_messages') {
                $(this).closest('.boost-make-boost-step-container').find('.boost-make-boost-fake-block').show();
            }
        });

        function show_container(containersBoxId, containerId){
            $.when($('#'+containersBoxId + '>*.boost-active').hide('fast', function(){
                $(this).removeClass('boost-active');
            })).then(function(){
                $('#'+containersBoxId + ' #'+containerId).show('fast', function(){
                    $(this).addClass('boost-active');
                });
            });

        }

        String.prototype.replaceAll = function(search, replacement) {
            var target = this;
            return target.split(search).join(replacement);
        };

        String.prototype.replaceArray = function(find, replace) {
            var replaceString = this;
            for (var i = 0; i < find.length; i++) {
                replaceString = replaceString.replaceAll(find[i], replace[i]);
            }
            return replaceString;
        };

        var boost_find = [
            '[name]',
            '[time]',
            '[town]',
            '[state]',
            '[country]',
            '[product_name]',
            '[product_with_link]',
            '[stock]'
        ];

        var boost_replace = [
            'Name',
            'Time',
            'Town',
            'State',
            'Country',
            'Product name',
            'Product with link',
            'Stock'
        ];
        $('.boost-make-boost-field input[type="text"][name$="[top_message]"]').on('change', function(){
            var boost_top_message = $(this).val();
            boost_top_message = boost_top_message.replaceArray(boost_find, boost_replace);
            $('.boost-make-boost-field-preview-data .boost-notification-message-top').html(boost_top_message);
        });
        $('.boost-make-boost-field input[type="text"][name$="[message]"]').on('change', function(){
            var boost_message = $(this).val();
            boost_message = boost_message.replaceArray(boost_find, boost_replace);
            $('.boost-make-boost-field-preview-data .boost-notification-message-bottom').html(boost_message);
        });

        $('.boost-make-boost-field select[name$="[notification_template]"]').on('change', function(){
            var boost_notification_template = $(this).val();
            $('.boost-notification').removeClass('boost-notification-square boost-notification-round').addClass('boost-notification-' + boost_notification_template);
        });

        $('.boost-page form').on('submit', function(){
            $('.boost-loading').show();
        });

        function resizeInput() {
            $(this).attr('size', $(this).val().length);
        }

        $('input.boost-resizable-input').each(resizeInput);


        $('select[id="boost\[form_selector\]"]').on('change', function() {

            var selected_form = $(this).val();
            var username_field_select = $('select[id="boost\[form_username_field\]"]');
            var surname_field_select = $('select[id="boost\[form_surname_field\]"]');

            $(username_field_select.selector + ',' + surname_field_select.selector).empty();

            $('#capture_form_fields option[data-form-id="'+selected_form+'"]').each(function(){
                var option = $(this);
                $(username_field_select.selector + ',' +
                    surname_field_select.selector)
                    .append(option.clone());
            });

            select_default_capture_form_fields();
        });

        function select_default_capture_form_fields(){
            var name_templates = ['name', 'firstname', 'first_name', 'first-name', 'username', 'user_name', 'user-name', 'your-name'];
            var surname_templates = ['surname', 'lastname', 'last_name', 'last-name', 'secondname', 'second_name', 'second-name', 'familyname', 'family_name', 'family-name'];

            var username_field_select = $('select[id="boost\[form_username_field\]"]');
            var surname_field_select = $('select[id="boost\[form_surname_field\]"]');

            var comparisons = ['=', '^=', '*='];
            for (var i=0; i < comparisons.length; i++){
                if(select_default_capture_form_field(username_field_select, name_templates, comparisons[i])){
                    break;
                }
            }
            for (i=0; i < comparisons.length; i++){
                if(select_default_capture_form_field(surname_field_select, surname_templates, comparisons[i])){
                    break;
                }
            }
        }

        function select_default_capture_form_field(select, field_name_templates, compare){
            var selected = false;
            for(var i=0; i < field_name_templates.length; i++) {
                if (select.find('option[value'+compare+'"' + field_name_templates[i] + '"]').length > 0) {
                    select.find('option[value'+compare+'"' + field_name_templates[i] + '"]').attr('selected', 'selected');
                    selected = true;
                    break;
                }
            }
            return selected;
        }

        $('input[id="boost\[capture_url\]"]').on('change paste input', function(){

            var form_selector = $('select[id="boost\[form_selector\]"]');
            var capture_form_fields_select = $('select[id="capture_form_fields"]');
            var username_field_select = $('select[id="boost\[form_username_field\]"]');
            var surname_field_select = $('select[id="boost\[form_surname_field\]"]');
            var error_container = $('div.boost-make-boost-capture-url div.boost-make-boost-field-error-message');
            var input_button_box = $(this).closest('.boost-input-button-box');

            input_button_box.removeClass('boost-search-error');
            error_container.hide().text('');
            document.getElementById(form_selector.attr('id')).selectedIndex = -1;
            document.getElementById(username_field_select.attr('id')).selectedIndex = -1;
            document.getElementById(surname_field_select.attr('id')).selectedIndex = -1;
            $(form_selector.selector + ',' + username_field_select.selector + ',' + surname_field_select.selector).closest('.boost-make-boost-field').hide();
            $(form_selector.selector + ',' + capture_form_fields_select.selector + ',' + username_field_select.selector + ',' + surname_field_select.selector).empty();
        });


        var countries_for_fakes_select = $('select.boost-countries-for-fakes[id="boost\[countries_for_fakes\]"]').select2({
            // placeholder: "",
            // minimumInputLength: 3,
            multiple: true,
            cache: true
        });

        $(".boost-countries-for-fakes-container .select2-select-all").on("click", function () {
            var options = countries_for_fakes_select[0].options;
            for (var i = 0;  i < options.length;  i++)
                options[i].selected = true;

            countries_for_fakes_select.trigger("change");
        });

        $(".boost-countries-for-fakes-container .select2-clear").on("click", function () {
            countries_for_fakes_select.val(null).trigger("change");
        });

        /* RANGE ELEMENT */
        function init_range_elements(){
            $('.range').each(function(){
                var rangeLabels = $(this).find('ul.range-labels li');
                var rangeLabelWidth = 100/rangeLabels.length;
                $(this).find('.range-labels').css('margin', '0 '+2*rangeLabelWidth+'% 0 '+rangeLabelWidth/2+'%');
                $(rangeLabels).css('width', rangeLabelWidth+'%');
            });
        }
        init_range_elements();

        var setActiveRange = function (el) {
            var curVal = parseInt(el.value);
            var rangeContainer = $(el).closest('.range');

            $(rangeContainer).find('.range-labels li').removeClass('active');

            $(rangeContainer).find('ul.range-labels li:nth-child(' + curVal + ')').addClass('active');
        }

        $('.range input').on('input', function () {
            setActiveRange(this);
        });
        
        $('.range-labels li').on('click', function () {
            var index = $(this).index();

            $(this).closest('.range').find('input[type="range"]').val(index+1).trigger('input');

        });
        /* END RANGE ELEMENT */

        /* CUSTOM SELECT2 STYLE */
        $('.select2-simple').select2({
            minimumResultsForSearch: -1,
            theme: 'boost_standard_new',
            // containerCssClass: 'test1',
            // dropdownCssClass: 'test2',
            // adaptContainerCssClass: '',
            // adaptDropdownCssClass: ''
        });

        function init_select2_show_urls_block() {
            $('.select2-standard-simple').select2({
                minimumResultsForSearch: -1,
                theme: 'boost_standard_simple',
                data: [
                    {
                        id: 'contains',
                        text: 'Contains'
                    },
                    {
                        id: 'equals',
                        text: 'Equals'
                    }
                ]
            });
        }
        init_select2_show_urls_block();

        function formatState (state) {
            if (!state.id) {
                return state.text;
            }
            var $state = $(
                '<div class="select_option_data">' +
                    '<div class="select_option_icon"></div><div class="select_option_label">' + state.text + '</div>' +
                '</div>'
            );
            return $state;
        };

        $('.select2-multiple').select2({
            minimumResultsForSearch: 1,
            theme: 'boost_multiple',
            closeOnSelect: false,
            templateResult: formatState,
            tags: true
        });

        $('.select2-standard').select2({
            minimumResultsForSearch: -1,
            theme: 'boost_standard_new'
        });

        $('select.select2-standard').on('change', function(){
            // console.log($(this).find('option:selected').attr('data-my-attr'));
        });




        /* DISPLAY CHECKBOXES */
        $('.boost-display-checkbox .boost-checkbox').on('change', function(){
            var description = '';
            if ($(this).is(':checked')) {
                description = $(this).attr('data-checked-label');
                $(this).closest('.boost-make-boost-position-field').addClass('position-active');
                $(this).closest('.boost-make-boost-position-field').find('.boost-make-boost-field-description').text(description);
                $(this).closest('.boost-make-boost-position-field').find('.position-place ').removeClass('disabled');
            }
            else {
                description = $(this).attr('data-unchecked-label');
                $(this).closest('.boost-make-boost-position-field').removeClass('position-active');
                $(this).closest('.boost-make-boost-position-field').find('.boost-make-boost-field-description').text(description);
                $(this).closest('.boost-make-boost-position-field').find('.position-place ').addClass('disabled');
            }
        });
        /* END DISPLAY CHECKBOXES */

        /* SHOW/HIDE SWITCHER */
        $('.boost-show-hide-switcher').on('change', function(){
            var relative_box_id = $(this).attr('data-relative-box-id');
            if ($(this).is(':checked')) {
                $('#'+relative_box_id).show('fast');
            }
            else {
                $('#'+relative_box_id).hide('fast');
            }
        });
        /* END SHOW/HIDE SWITCHER */

        /* SWITCH FIELD CHANGE */

        /* END SWITCH FIELD CHANGE */

        /* DRAG & DROP BLOCKS */
        $('input[name="boost[message]"]').on('change', function(){
            var message_length = $(this).val().length;
            $('.boost-make-boost-message-length').text(message_length);
        });

        $('.boost-message-editor').on('change keyup mouseup DOMSubtreeModified', function(){
            var message = $(this).text();
            var max_len = parseInt($(this).attr('data-max-len'));
            if (max_len > 0 && message.length > max_len) {
                message = message.substr(0, max_len);
                $(this).html($(this).data('html'));
                $(this).closest('.boost-message-editor-data').find('input[type="text"]').change();
            }
            $(this).closest('.boost-message-editor-data').find('input[type="text"]').val(message).change();
            $(this).data('html', $(this).html());
        });

        $(document).on('focusin', '.boost-message-editor', function(){
            $(this).data('html', $(this).html());
        });

        $(".draggable").draggable({
            grid: [ 20, 20 ],
            // appendTo: ".boost-message-editor",
            containment: "window",
            cursor: 'move',
            revertDuration: 100,
            revert: 'invalid',
            helper: 'clone'
        });

        $(".boost-message-editor").droppable({
            accept: ".draggable",
            drop: function (event, ui) {
                var tag_text = $.trim(ui.draggable.text());
                tag_text = tag_text.replaceAll(' ','<span class="boost-message-tag-hidden-char">_</span>');
                var tag =   ' ' + '<span contenteditable="false" class="boost-message-tag">' +
                                '<span class="boost-message-tag-hidden-char">[</span>' + tag_text.toLowerCase() +
                                '<span class="boost-message-tag-hidden-char">]</span>' +
                                '<span class="boost-message-tag-delete"></span>' +
                            '</span>' + '';

                var html = $.trim($(this).html());
                var html_new = html + tag;
                var max_len = parseInt($(this).attr('data-max-len'));
                if(max_len > 0 && ($.trim($(this).text()).length+$.trim(ui.draggable.text()).length) > max_len) {
                    return false;
                }
                $(this).html(html_new);
            }
        });

        $('div.boost-message-editor').on('click', '.boost-message-tag .boost-message-tag-delete', function(){
            $(this).closest('.boost-message-tag').remove();
        });
        /* END DRAG & DROP BLOCKS */

        $('.boost-make-boost-show-add-url').on('click', function(){
            var html = '<div class="boost-make-boost-show-url boost-make-boost-url">' +
                            '<select name="boost[dc_urls][url_type][]" class="select2-standard-simple boost-input-style"></select>' +
                            '<input name="boost[dc_urls][url][]" type="text" class="boost-input-style" placeholder="Type here…">' +
                            '<span class="boost-url-delete"></span>' +
                        '</div>';
            $(this).closest('.boost-make-show-urls-block-urls').find('.boost-make-boost-show-urls').append(html);
            init_select2_show_urls_block();
        });
        $('.boost-make-boost-hide-add-url').on('click', function(){
            var html = '<div class="boost-make-boost-hide-url boost-make-boost-url">' +
                            '<select name="boost[de_urls][url_type][]" class="select2-standard-simple boost-input-style"></select>' +
                            '<input name="boost[de_urls][url][]" type="text" class="boost-input-style" placeholder="Type here…">' +
                            '<span class="boost-url-delete"></span>' +
                        '</div>';
            $(this).closest('.boost-make-hide-urls-block-urls').find('.boost-make-boost-hide-urls').append(html);
            init_select2_show_urls_block();
        });

        $('div.boost-make-boost-urls').on('click', '.boost-url-delete', function(){
            if ($(this).closest('.boost-make-boost-urls').find('.boost-make-boost-url').length > 1) {
                $(this).closest('.boost-make-boost-url').remove();
            }
        });

        $('.boost-table thead .boost-select-boost-all input[type="checkbox"]').on('change', function(){
            var status = this.checked;
            $(this).closest('table').find('.boost-select-boost input[type="checkbox"]').each(function(){
                $(this).attr('checked', status).change();
            });
        });

        $('.boost-table tbody .boost-select-boost input[type="checkbox"]').on('change', function(){
            if(!this.checked) {
                $(this).closest('table').find('input[type="checkbox"][name="select_all_boost"]').attr('checked', false);
                $(this).closest('tr').removeClass('boost-bold');
            }
            else {
                $(this).closest('tr').addClass('boost-bold');
            }

            if ($(this).closest('.boost-page').find('.boost-select-boost input[type="checkbox"]:checked').length > 0){
                $('.boost-batch-actions').removeClass('boost-hidden');
            }
            else {
                $('.boost-batch-actions').addClass('boost-hidden');
            }
        });

        $('button[data-need-confirm="true"]').on('click', function(e){
            e.preventDefault();
            var button = $(this).clone().removeAttr('data-need-confirm').text('Yes').attr('class','boost-button boost-button-primary');
            var html='<div class="boost-confirm-window">' +
                        '<div class="boost-confirm-window-data">' +
                            '<div class="boost-confirm-window-message">Are you sure?</div>' +
                            '<div class="boost-confirm-window-buttons">' +
                                '<div class="boost-confirmation-buttons-block">' +
                                    $(button).prop('outerHTML') +
                                    '<button class="boost-button boost-button-cancel boost-confirm-window-button-cancel">' +
                                        '<span>No</span>' +
                                    '</button>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                     '</div>';
            $(this).closest('form').append(html);
        });

        $('.boost-page').on('click', '.boost-confirm-window-button-cancel', function(){
            $(this).closest('.boost-confirm-window').remove();
        });

        $('input[type="checkbox"][name*="notification_style_"]').on('change', function(){
            if (!$(this).is(':checked') && !$(this).closest('.boost-make-boost-notification-style-templates').find('input[type="checkbox"][name*="notification_style_"]:checked').length) {
                $(this).prop('checked', true);
            }
        });

        $('.boost-make-boost-select-notification-image-type-container input[type="checkbox"][name*="boost["]').on('change', function(){
            if (!$(this).is(':checked') && !$('.boost-make-boost-select-notification-image-type-container input[type="checkbox"][name*="boost["]:checked').length) {
                $(this).prop('checked', true);
            }
        });

    });
})( jQuery );
