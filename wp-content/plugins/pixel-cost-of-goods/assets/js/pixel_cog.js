// JavaScript Document

jQuery(function($){

	$( ".recalculate_button" ).on('click', function( e ) {
		var order_id = $(this).data('id');
		$(this).html("Processing...");
		$.ajax({
			type: "POST",
			url: PIXELCOG.ajaxUrl,
			data: {action: 'pixel_cog_order_recalculate_action', 'order_id': order_id},
			success: function (response) {
				//console.log(response);
				location.reload();
			},
			error: function (response) {
				console.log('ajax error');
			}
		});
	});

	$('.pixel_cog_tax_radio').on('change', function(){
		var val = $(this).val();
		$.ajax({
			type: "POST",
			url: PIXELCOG.ajaxUrl,
			data: {action: 'pixel_cog_update_tax_settings', tax:val},
			success: function (response) {
				console.log('Processing update_tax_settings, do not cancel');
			},
			error: function (response) {
				console.log('ERROR');
				return false;
			}
		});
		if(!$('*').is('.notice-tax')) {
			$(this).closest('table').after("<div class='notice notice-tax notice-warning'>\n" +
				"\t\t<p>You modified the way the profit is calculated, so we need to update your data. Please click on this button to run the script: <a href='#' class='button-primary' id='calculate_cost_btn_notice'>Save and Update</a></p>\n" +
				"\t</div>");
		}
	});

	// Export
	$('#product_csv_export').on('click', function (e) {
		e.preventDefault();
        pixel_export_cost_process();
	});

	function pixel_export_cost_process( limit,offset ) {
		if (typeof(offset) === 'undefined') offset = 0;
		if (typeof(limit) === 'undefined') limit = 'undefined';
		var post_data = {
			'limit': limit,
			'offset': offset,
			action: 'pixel_export_cost_product'
		};
		if (offset == 0)
			$('.pixel_cog_popup.popup').html('<div class="msg">Export processing...</div>').show();
		$.ajax({
			type: "POST",
			data: post_data,
			url: PIXELCOG.ajaxUrl,
			success: function (response) {
				console.log('Processing, do not cancel');
				if (response.loop == 1) {
                    pixel_export_cost_process(response.limit, response.offset);
					$('.pixel_cog_popup.popup').addClass('success').html('<div class="msg">exported '+response.offset+' products</div>');
				}
				if (response.loop == 0) {
					$('.pixel_cog_popup.popup').removeClass('success').hide();
					var valFileDownloadPath = response.file_url;
					window.open(valFileDownloadPath , '_blank');
				}
			},
			error: function (response) {
				console.log('ERROR');
				console.log(response);
				$('.pixel_cog_popup.popup').addClass('error').html('<div class="msg">Oops, something wrong</div>').show();
				setTimeout(function(){ $('.pixel_cog_popup.popup').removeClass('error').hide(); }, 1500);
			}
		});
	}

	$('#product_csv_export_cat').on('click', function (e) {
		e.preventDefault();
        pixel_export_cat_cost_process();
	});

	function pixel_export_cat_cost_process( limit,offset ) {

		if (typeof(offset) === 'undefined') offset = 0;
		if (typeof(limit) === 'undefined') limit = 'undefined';
		var post_data = {
			'limit': limit,
			'offset': offset,
			action: 'pixel_export_cat_cost_product'
		};
		if (offset == 0)
			$('.pixel_cog_popup.popup').html('<div class="msg">Export processing...</div>').show();
		$.ajax({
			type: "POST",
			data: post_data,
			url: PIXELCOG.ajaxUrl,
			success: function (response) {
				console.log('Processing, do not cancel');
				if (response.loop == 1) {
                    pixel_export_cat_cost_process(response.limit, response.offset);
					$('.pixel_cog_popup.popup').addClass('success').html('<div class="msg">exported '+response.offset+' categories</div>');
				}
				if (response.loop == 0) {
					$('.pixel_cog_popup.popup').removeClass('success').hide();
					var valFileDownloadPath = response.file_url;
					window.open(valFileDownloadPath , '_blank');
				}
			},
			error: function (response) {
				console.log("ERROR");
				console.log(response);
				$('.pixel_cog_popup.popup').addClass('error').html('<div class="msg">Oops, something wrong</div>').show();
				setTimeout(function(){ $('.pixel_cog_popup.popup').removeClass('error').hide(); }, 1500);
				return false;
			}
		});
	}

	// Import
	$('#product_csv_import').on('click', function (e) {
		e.preventDefault();
		$(this).closest('.setting_wrapper-row').append('<input type="file" id="product_csv_upload" style="display: none" />');
		$('#product_csv_upload').click();
	});

	$('#product_csv_import_cat').on('click', function (e) {
		e.preventDefault();
		$(this).closest('.setting_wrapper-row').append('<input type="file" id="product_cat_csv_upload" style="display: none" />');
		$('#product_cat_csv_upload').click();
	});

	var stepCountG = 0;
	$(document).on('change', '#product_csv_upload', function () {
		var file = $(this).prop('files')[0];
		$('.pixel_cog_popup.popup').html('<div class="msg">Import processing...</div>').show();
		var step = 0;
		var stepCount = 1;
		var temp = [];
		var items = [];
		var createStep = false;
		var itemsLength = 0;
		var counter = 0;
		console.log('start parse');
		Papa.parse(file, {
				header: true,
			complete: function(results, file) {
				var first_row_data = results.data[0];
				if ('item_ID' in first_row_data) {
					var resLength = results.data.length;
					for (var i = 0; i < resLength; i++) {
						createStep = false;
						step++;
						counter++;
						itemsLength += results.data[i].length;
						temp.push(results.data[i]);
						if(step == 100) {
							stepCount++;
							createStep = true;
							step = 0;
							items.push(temp);
							temp = [];
						}
					}
					if(!createStep) {
						items.push(temp);
					}
					stepCountG = stepCount;
					items.forEach(function(item, i, arr) {
						pixel_cog_import_process('products', item);
					});
				} else {
					$('.pixel_cog_popup.popup').addClass('error').html('<div class="msg">There were problems with your CSV, please make sure it\'s the right format</div>').show();
					setTimeout(function(){ $('.pixel_cog_popup.popup').removeClass('error').hide(); }, 1500);
				}
			}
		});
	});

	$(document).on('change', '#product_cat_csv_upload', function () {
		var file = $(this).prop('files')[0];
		$('.pixel_cog_popup.popup').html('<div class="msg">Import processing...</div>').show();
		var step = 0;
		var stepCount = 1;
		var temp = [];
		var items = [];
		var createStep = false;
		var itemsLength = 0;
		var counter = 0;
		console.log('start parse');
		Papa.parse(file, {
			header: true,
			complete: function(results) {
				var first_row_data = results.data[0];
				if ('category_ID' in first_row_data) {
					var resLength = results.data.length;
					for (var i = 0; i < resLength; i++) {
						createStep = false;
						step++;
						counter++;
						itemsLength += results.data[i].length;
						temp.push(results.data[i]);
						if(step == 100) {
							stepCount++;
							createStep = true;
							step = 0;
							items.push(temp);
							temp = [];
						}
					}
					if(!createStep) {
						items.push(temp);
					}
					stepCountG = stepCount;
					items.forEach(function(item, i, arr) {
						pixel_cog_import_process('categories', item);
					});
				} else {
					$('.pixel_cog_popup.popup').addClass('error').html('<div class="msg">There were problems with your CSV, please make sure it\'s the right format</div>').show();
					setTimeout(function(){ $('.pixel_cog_popup.popup').removeClass('error').hide(); }, 1500);
				}
			}
		});
	});

	function pixel_cog_import_process( type, csvdata) {
		var post_data = {
			dataType: 'JSON',
			'type': type,
			'csvdata': csvdata,
			action: 'pixel_import_cost_product',
		};
		$.ajax({
			type: "POST",
			data: post_data,
			url: PIXELCOG.ajaxUrl,
			async: true,
			success: function (response) {
				stepCountG--;
				if(stepCountG <= 0) {
					$('.pixel_cog_popup.popup').addClass('success').html('<div class="msg">Your CSV was imported successfully</div>').show();
					setTimeout(function(){ $('.pixel_cog_popup.popup').removeClass('success').hide(); }, 1500);
				}
			},
			error: function (response) {
				console.log("ERROR");
				$('.pixel_cog_popup.popup').addClass('error').html('<div class="msg">There were problems with your CSV, please make sure it\'s the right format</div>').show();
				setTimeout(function(){ $('.pixel_cog_popup.popup').removeClass('error').hide(); }, 1500);
				return false;
			}
		});
	}

	$('.product_cog_import_plugins').on('click', function (e) {
		e.preventDefault();
		var product_key = $(this).data('product-metakey');
		$('.pixel_cog_popup.popup').html('<div class="msg">The import process will overwrite all the data in the database! Be sure to back up the database before proceeding.<div class="buttons"><a href="#" class="button button-primary product_cog_import_plugins_yes" data-product-metakey="'+product_key+'">Yes i am sure</a><a href="#" class="button action product_cog_import_plugins_no">Cancel</a></div></div>').show();
	});

	$(document).on('click', '.product_cog_import_plugins_no', function () {
		$('.pixel_cog_popup.popup').html('').hide();
	});

	$(document).on('click', '.product_cog_import_plugins_yes', function () {
		$('.pixel_cog_popup.popup').html('<div class="msg">Import processing...</div>');
		var product_key = $(this).data('product-metakey');
		pixel_cog_import_plugins_process(product_key);
	});

	function pixel_cog_import_plugins_process( product_key, limit,offset ) {

		if (typeof(offset) === 'undefined') offset = 0;
		if (typeof(limit) === 'undefined') limit = 'undefined';
		var post_data = {
			'limit': limit,
			'offset': offset,
			'product_key': product_key,
			action: 'pixel_import_cost_product_plugins'
		};
		if (offset == 0)
			$('.pixel_cog_popup.popup').html('<div class="msg">Import processing...</div>').show();
		$.ajax({
			type: "POST",
			data: post_data,
			url: PIXELCOG.ajaxUrl,
			success: function (response) {
				console.log('Processing, do not cancel');
				if (response.loop == 1) {
					pixel_cog_import_plugins_process(product_key, response.limit, response.offset);
					$('.pixel_cog_popup.popup').addClass('success').html('<div class="msg">imported '+response.offset+' items</div>').show();
				}
				if (response.loop == 0) {
					$('.pixel_cog_popup.popup').addClass('success').html('<div class="msg">import complete</div>').show();
					setTimeout(function(){ $('.pixel_cog_popup.popup').removeClass('success').hide(); }, 1500);
				}
			},
			error: function (response) {
				console.log("ERROR");
				console.log(response);
				$('.pixel_cog_popup.popup').addClass('error').html('<div class="msg">Oops, something wrong</div>').show();
				setTimeout(function(){ $('.pixel_cog_popup.popup').removeClass('error').hide(); }, 1500);
				return false;
			}
		});
	}

	// license save
	$(document).on( 'click', '.wc-license-save', function(e) {
		var $button = $(this),
			data = {
				action: 'pixel_cog_toggle_license',
				key: $('#pixel_cog_license').val(),
				status: 'activate'
			};

		$button.addClass( 'btn-toggle--loading' );

		$.ajax( {
			url: PIXELCOG.ajaxUrl,
			data: data,
			dataType : 'json',
			type : 'POST',
			success:  function( response ) {
			 	if (response.data.license == 'valid'){
					location.reload();
				} else if (response.data.license == 'expired') {
					$('.pixel_cog_popup.popup').addClass('error').html('<div class="msg">Your license key expired</div>').show();
					setTimeout(function(){ $('.pixel_cog_popup.popup').removeClass('error').hide(); }, 1500);
				} else if (response.data.license == 'invalid' || response.data == 'site_inactive') {
					$('.pixel_cog_popup.popup').addClass('error').html('<div class="msg">Your license is not active for this URL.</div>').show();
					setTimeout(function(){ $('.pixel_cog_popup.popup').removeClass('error').hide(); }, 1500);
				} else if (response.data == 'missing') {
					$('.pixel_cog_popup.popup').addClass('error').html('<div class="msg">Invalid license.</div>').show();
					setTimeout(function(){ $('.pixel_cog_popup.popup').removeClass('error').hide(); }, 1500);
				} else if (response.data == 'key_mismatch') {
					$('.pixel_cog_popup.popup').addClass('error').html('<div class="msg">License keys don\'t match. Make sure you\'re using the correct license.</div>').show();
					setTimeout(function(){ $('.pixel_cog_popup.popup').removeClass('error').hide(); }, 1500);
				} else if (response.data == 'license_not_activable') {
					$('.pixel_cog_popup.popup').addClass('error').html('<div class="msg">If you have a bundle package, please use each individual license for your products.</div>').show();
					setTimeout(function(){ $('.pixel_cog_popup.popup').removeClass('error').hide(); }, 1500);
				} else if (response.data == 'revoked') {
					$('.pixel_cog_popup.popup').addClass('error').html('<div class="msg">Your license key has been disabled.</div>').show();
					setTimeout(function(){ $('.pixel_cog_popup.popup').removeClass('error').hide(); }, 1500);
				} else if (response.data == 'no_activations_left') {
					$('.pixel_cog_popup.popup').addClass('error').html('<div class="msg">Your license key has reached its activation limit.</div>').show();
					setTimeout(function(){ $('.pixel_cog_popup.popup').removeClass('error').hide(); }, 1500);
				} else if (response.data == 'invalid_item_id') {
					$('.pixel_cog_popup.popup').addClass('error').html('<div class="msg">Invalid item ID.</div>').show();
					setTimeout(function(){ $('.pixel_cog_popup.popup').removeClass('error').hide(); }, 1500);
				} else if (response.data == 'item_name_mismatch') {
					$('.pixel_cog_popup.popup').addClass('error').html('<div class="msg">This appears to be an invalid license key</div>').show();
					setTimeout(function(){ $('.pixel_cog_popup.popup').removeClass('error').hide(); }, 1500);
				} else if (response.data == 'inactive') {
					$('.pixel_cog_popup.popup').addClass('error').html('<div class="msg">This license is not active.</div>').show();
					setTimeout(function(){ $('.pixel_cog_popup.popup').removeClass('error').hide(); }, 1500);
				} else if (response.data == 'disabled') {
					$('.pixel_cog_popup.popup').addClass('error').html('<div class="msg">License key disabled.</div>').show();
					setTimeout(function(){ $('.pixel_cog_popup.popup').removeClass('error').hide(); }, 1500);
				} else {
					$('.pixel_cog_popup.popup').addClass('error').html('<div class="msg">An error occurred, please try again.</div>').show();
					setTimeout(function(){ $('.pixel_cog_popup.popup').removeClass('error').hide(); }, 1500);
				}
				},
			error: function (response) {
				console.log('ERROR');
				console.log(response);
				$('.pixel_cog_popup.popup').addClass('error').html('<div class="msg">Oops, something wrong</div>').show();
				setTimeout(function(){ $('.pixel_cog_popup.popup').removeClass('error').hide(); }, 1500);
			}
		} );

		return false;
	});

	$(document).on( 'click', '.wc-license-deactivate', function(e) {
		var $button = $(this),
			data = {
				action: 'pixel_cog_toggle_license',
				key: '',
				status: 'deactivate'
			};

		$button.addClass( 'btn-toggle--loading' );

		$.ajax( {
			url: PIXELCOG.ajaxUrl,
			data: data,
			dataType : 'json',
			type : 'POST',
			success:  function( response ) {
				location.reload();
				},
			error: function (response) {
				console.log('ERROR');
				console.log(response);
				$('.pixel_cog_popup.popup').addClass('error').html('<div class="msg">Oops, something wrong</div>').show();
				setTimeout(function(){ $('.pixel_cog_popup.popup').removeClass('error').hide(); }, 1500);
				return false;
			}
		} );

		return false;
	});

	$(document).on( 'click', '#calculate_cost_btn', function(e) {
		var $button = $(this);

		if ($button.hasClass('processing')) {
			e.preventDefault();
		} else {
			$button.addClass( 'processing' );
			$button.text('Calculate in process...');
			$.ajax({
				type: "POST",
				url: PIXELCOG.ajaxUrl,
				data: {action: 'pixel_cog_cron_order_recalculate_action'},
				success: function (response) {
					console.log('Processing, do not cancel');
					if (response.loop == 1) {
						pixel_cog_cron_order_recalculate(response.limit, response.offset, $button);
						$('.updated').html('Updated '+response.offset+' of '+response.total_orders_number+' orders');
					}
					if (response.loop == 0) {
						$button.text('Calculate complete');
						$('.updated').html('Updated '+response.total_orders_number+' of '+response.total_orders_number+' orders. Process completed.');
						setTimeout(function () {
							$button.text('Calculate');
							$button.removeClass( 'processing' );
						}, 1500)
					}
				},
				error: function (response) {
					console.log('ERROR');
					console.log(response);
					$button.text('Calculate');
					$button.removeClass( 'processing' );
					$('.pixel_cog_popup.popup').addClass('error').html('<div class="msg">Oops, something wrong</div>').show();
					setTimeout(function(){ $('.pixel_cog_popup.popup').removeClass('error').hide(); }, 1500);
					return false;
				}
			});
		}

		return false;
	});

	$(document).on( 'click', '#calculate_cost_btn_notice', function(e) {
		var $button = $(this);

		if ($button.hasClass('processing')) {
			e.preventDefault();
		} else {
			$button.addClass( 'processing' );
			$button.text('Calculate in process...');
			$.ajax({
				type: "POST",
				url: PIXELCOG.ajaxUrl,
				data: {action: 'pixel_cog_cron_order_recalculate_action'},
				success: function (response) {
					console.log('Processing, do not cancel');
					if (response.loop == 1) {
						pixel_cog_cron_order_recalculate(response.limit, response.offset, $button);
					}
					if (response.loop == 0) {
						$button.text('Calculate complete');
						setTimeout(function () {
							$button.closest('.notice-warning').remove();
						}, 1500)
					}
				},
				error: function (response) {
					console.log('ERROR');
					console.log(response);
					$button.text('Calculate');
					$button.removeClass( 'processing' );
					$('.pixel_cog_popup.popup').addClass('error').html('<div class="msg">Oops, something wrong</div>').show();
					setTimeout(function(){ $('.pixel_cog_popup.popup').removeClass('error').hide(); }, 1500);
					return false;
				}
			});
		}

		return false;
	});

	function pixel_cog_cron_order_recalculate( limit,offset,button ) {

		$button = button;

		if (typeof(offset) === 'undefined') offset = 0;
		if (typeof(limit) === 'undefined') limit = 'undefined';
		var post_data = {
			'limit': limit,
			'offset': offset,
			action: 'pixel_cog_cron_order_recalculate_action'
		};
		$.ajax({
			type: "POST",
			data: post_data,
			url: PIXELCOG.ajaxUrl,
			success: function (response) {
				console.log('Processing, do not cancel');
				if (response.loop == 1) {
					pixel_cog_cron_order_recalculate(response.limit, response.offset, $button);
                    $('.updated').html('Updated '+response.offset+' of '+response.total_orders_number+' orders');
				}
				if (response.loop == 0) {
					$button.text('Calculate complete');
					setTimeout(function () {
						$button.text('Calculate');
						$button.removeClass( 'processing' );
						$('.updated').html('Updated '+response.offset+' of '+response.total_orders_number+' orders. Process completed.');
                    }, 1500)
				}
			},
			error: function (response) {
				console.log('ERROR');
				console.log(response);
				$button.text('Calculate');
				$button.removeClass( 'processing' );
				$('.pixel_cog_popup.popup').addClass('error').html('<div class="msg">Oops, something wrong</div>').show();
				setTimeout(function(){ $('.pixel_cog_popup.popup').removeClass('error').hide(); }, 1500);
				return false;
			}
		});
	}

});