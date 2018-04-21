/**
 * add JS file uploader handler
 */
jQuery(document).ready(function() {

	// process handle upload from local drive
	if (typeof(window.FileReader) == 'undefined' && window.location.search.indexOf('not_supported=true')==-1) {
		window.location.search = '?page=import-page&not_supported=true';
	}else{
		var $upload_files_html5   = jQuery('#upload_files_html5'),
			$upload_button        = jQuery('.wizard-upload-files'),
			$drop_zone            = jQuery('#area-drag-drop, #file_list_holder'),
			$upload_table         = jQuery('#file_list'),
			$upload_form          = jQuery('form#upload_files'),
			$continue_install     = jQuery('.next_step'),
			$info_holder          = jQuery('#info_holder'),
			$import_step_2        = jQuery('#import_step_2'),
			$import_step_3        = jQuery('#import_step_3'),
			$upload_status        = jQuery('#upload_status'),
			$not_load_file        = jQuery('#not_load_file'),
			$return_to_switcher   = jQuery('.dm-return-to-switcher'),
			$import_type_selector = jQuery('.dm-import-type-selector'),
			files_array           = new Array(),
			_auto_load_last_file  = 0,
			row_class             = 'alternate',
			last_add_file,
			drop_file_list,
			loaded_XML            = false;
			//loaded_JSON = false;

		$drop_zone.on('dragover', function() {
			$drop_zone.parent().addClass('hover');
			return false;
		}).on('dragleave', function() {
			$drop_zone.parent().removeClass('hover');
			return false;
		}).on('drop', function(event) {
			get_file_list(event.originalEvent.dataTransfer.files);
			return false;
		});
		$upload_button.on('click', add_more_files);
		$upload_files_html5.on('change', function(){
			get_file_list(jQuery(this)[0].files);
		});
		$upload_form.on('mouseenter', function(){
			$drop_zone.removeClass('pointer_events');
		});
	}

	// add more files from local drive
	function add_more_files(){
		$drop_zone.addClass('pointer_events');
		$upload_files_html5.click();
		return !1;
	}

	// get uploaded files list
	function get_file_list(file_list){
		$upload_button.off();

		drop_file_list = file_list;
		last_add_file = 0;

		jQuery('.loader_bar span b', $upload_status).css({'width':'0%'});
		$drop_zone.parent().removeClass('hover');
		$upload_form.removeClass('add_files');
		$upload_form.addClass('hidden_');
		$import_step_2.removeClass('hidden_');

		add_file(drop_file_list[last_add_file]);
	}

	// add file to upload request
	function add_file(file){
		var file_name = file.name;

		last_add_file++;

		if(!in_array(files_array, file_name)){
			var upload_file_num = files_array.length-1,
				file_size = file.size,
				file_size_type = ['B', 'KB', 'MB', 'GB'];

			files_array.push(file_name);
			row_class = row_class == 'alternate' ? '' : 'alternate' ;

			if(file.type == 'text/xml') loaded_XML = file_name ;
			//if(file_name.indexOf('.json') !=-1) loaded_JSON = file_name ;

			for (var i = 0; file_size > 1024 && i < file_size_type.length - 1; i++ ) {
				file_size /= 1024;
			};
			jQuery('#file_list_body', $upload_table).prepend('<div id="file_status_'+upload_file_num+'" class="row '+row_class+'" ><div class="column_1">'+file_name+'</div><div class="column_2">'+file_size.toFixed(2)+' '+file_size_type[i]+'</div><div class="column_3"><span class="file_progress_bar"></span><span class="file_progress_text">' + cherry_dm_import_texts['upload'] + ' <span class="load_percent">0</span> %</span></div></div>');

			if ( file.size > parseInt( cherry_dm_import_vars.max_file_size ) ) {
				jQuery('#file_status_'+upload_file_num).addClass('error_file').find('.file_progress_text').html(cherry_dm_import_texts['error_size']);
				jQuery('#error_counter b').html(parseInt(jQuery('#error_counter b').text())+1);
				switch_file(last_add_file);
			} else if(file.name.indexOf('.') == -1 && file.type == "" ) {
				jQuery('#file_status_'+upload_file_num).addClass('error_file').find('.file_progress_text').html(cherry_dm_import_texts['error_folder']);
				jQuery('#error_counter b').html(parseInt(jQuery('#error_counter b').text())+1);
				switch_file(last_add_file);
			}else{
				var form_data = new FormData();
				form_data.append('file', file);
				send_file(form_data, upload_file_num);
			}
		}else{
			switch_file(last_add_file);
		}
	}

	// send file to upload
	function send_file(file_to_send, file_num){
		var xhr = new XMLHttpRequest();
		xhr.onload = function(data){
			var file_status_row =  jQuery('#file_status_' + file_num),
				loader_bar = jQuery('.file_progress_bar', file_status_row);

			jQuery('.load_percent', file_status_row).text('100');
			loader_bar.css({'width':'100%'});
			setTimeout(function(){
				loader_bar.addClass('transition').css({'opacity':0});
			},500);

			switch_file(last_add_file);
		};
		xhr.upload.onprogress = function(event){
			upload_progress(event, file_num);
		};
		xhr.open('POST', cherry_dm_import_vars.action_url);
		xhr.setRequestHeader('X-FILE-NAME', file_num);
		xhr.send(file_to_send);
	}

	// calc upload progress
	function upload_progress(event, file_num) {
		var percent = parseInt(event.loaded / event.total * 100);
		jQuery('.load_percent', '#file_status_' + file_num).text(percent);
		jQuery('.file_progress_bar', '#file_status_' + file_num).css({'width':percent + '%'});
	}

	// switch files
	function switch_file(file_num){
		var percent = parseInt(file_num / drop_file_list.length * 100);
		jQuery('.loader_bar span', $upload_status).css({'width':percent+'%'});
		jQuery('.progress-bar-counter_ span', $upload_status).html(percent);
		jQuery('#upload_counter b').html(parseInt(jQuery('#upload_counter b').text())+1);

		if (drop_file_list[file_num]) {
			add_file(drop_file_list[file_num]);
		} else {
			setTimeout(function(){
				load_all_content();
			}, 1000);
		}
	}

	function load_all_content(){
		$info_holder.removeClass('hidden_');
		$upload_button.on('click', add_more_files);
		$continue_install.off();
		if(loaded_XML /*&& loaded_JSON*/) {
			jQuery('p .upload_status_text', $info_holder).html(cherry_dm_import_texts['uploaded_status_text_1']);
			$not_load_file.addClass('hidden_');
			$upload_status.addClass('upload_done');
			$continue_install.removeClass('not_active').on('click', function(event) {

				event.preventDefault();

				cherry_dm_prepare_to_install();

			});
		} else {
			$continue_install.on('click', function(event) {
				event.preventDefault();
				$not_load_file.removeClass('hidden_');
			});
		}
	}

	function cherry_dm_prepare_to_install() {

		var event = jQuery.Event('cherry_data_manager_start_install');
		jQuery(document).trigger( event );

		// disable file uploading
		$drop_zone.off();
		$upload_button.off();
		$upload_files_html5.off();
		// hide not necessary blocks
		$return_to_switcher.addClass('hidden_');
		$upload_status.addClass('hidden_');
		$upload_form.addClass('hidden_');
		$info_holder.find('.wizard-upload-files').addClass('hidden_');
		$not_load_file.addClass('hidden_');
		jQuery('#file_list_holder').addClass('hidden_');
		jQuery('#importing_warning').addClass('hidden_');
		$continue_install.off('click').addClass('hidden_');
		// show needed blocks
		jQuery("#import_data").removeClass('hidden_');
		jQuery('#import_xml_status').removeClass('hidden_');
		// add import start status text
		$info_holder.find('.upload_status_text').html(cherry_dm_import_texts['uploaded_status_text']);
		// send first AJAX request
		ajax_post('import_xml');
	}

	function ajax_post(action, file){
		var data = {
			action: action,
			file:   file != 0 ? file : 0,
			type:   cherry_dm_import_vars.type,
			nonce:  cherry_data_manager_ajax.nonce
		};

		if ( cherry_dm_import_texts[action] != undefined ) {
			add_text_status(action);
			jQuery.ajax({
				url: ajaxurl,
				data: data,
				type:'POST',
				success:function(response) {
					if(response == "error"){
						error_status();
					} else if(loaded_XML) {
						switch_ajax_post(response);
					} else {
						//import complete
						add_text_status('import_complete');
					}
				},
				error:function(response) {
					error_status();
				},
				timeout: 900000
			});
		} else {
			error_status();
		}
	}

	/**
	 * Add auto uploder table item by file name
	 */
	function cherry_dm_row_loader( file ) {
		if ( cherry_dm_import_row == null || cherry_dm_import_row == undefined ) {
			return false;
		}

		var result = cherry_dm_import_row.replace( /%file%/g, file );

		return result;
	}

	/**
	 * process automatic upload
	 */
	function cherry_wizard_auto_load() {

		row_class = row_class == 'alternate' ? '' : 'alternate';
		var _file = 'sample_data.zip',
			_row  = cherry_dm_row_loader(_file);

		$import_step_2.removeClass('hidden_');
		jQuery('#file_list_body', $upload_table).prepend(_row).find('.row:first').addClass(row_class);

		jQuery.ajax({
			url: ajaxurl,
			type: "post",
			dataType: "json",
			data: {
				action: 'cherry_dm_content_auto_load',
				file:   _file,
				nonce:  cherry_dm_import_vars.nonce
			},
			success: function(responce) {

				jQuery('#upload_counter b').html('1');
				jQuery('.loader_bar span', $upload_status).css({ 'width': '100%' });
				jQuery('.progress-bar-counter_ span', $upload_status).html('100');
				jQuery('.row.row-preload').removeClass('row-preload').addClass('row-success');

				$import_step_2.removeClass('hidden_');
				$info_holder.removeClass('hidden_');

				if ( responce.type == 'success' ) {
					loaded_XML = 'yes';
					$continue_install.removeClass('not_active').addClass('ready_to_install');
					$upload_status.addClass('upload_done');
				} else {
					$not_load_file.removeClass('hidden_');
				}

			},
			error: function( error ) {

				jQuery('#upload_counter b').html('1');
				jQuery('.loader_bar span', $upload_status).css({ 'width': '100%' });
				jQuery('.progress-bar-counter_ span', $upload_status).html('100');
				jQuery('.row.row-preload').removeClass('row-preload').addClass('row-error');

			}
		})

	}

	// run automatic uploader
	jQuery(document).on('click', '#cherry-dm-remote-import', function(event) {

		event.preventDefault();

		if ( ! window.confirm( window.cherry_dm_import_texts.confirm_load ) ) {
			return ! 1;
		}

		/*if ( jQuery(this).hasClass('disabled') ) {
			return;
		}

		if ( cherry_dm_import_trigger.autoload != 'yes' ) {
			return;
		}*/

		$import_type_selector.css('display', 'none');

		$upload_form.addClass('hidden_');

		cherry_wizard_auto_load();

		$continue_install.on('click', function(event){

			if ( jQuery(this).hasClass('ready_to_install') ) {

				event.preventDefault();
				cherry_dm_prepare_to_install();
			}
		});
	});

	// run local uploader
	jQuery(document).on('click', '#cherry-dm-local-import', function(event) {
		event.preventDefault();

		if ( ! window.confirm( window.cherry_dm_import_texts.confirm_load ) ) {
			return ! 1;
		}

		$import_type_selector.css('display', 'none');
		$return_to_switcher.removeClass('hidden_');
	});

	// return to upload switcher
	jQuery(document).on('click', '#cherry-dm-return-to-switcher', function(event) {
		event.preventDefault();
		$import_type_selector.css('display', 'block');
		$return_to_switcher.addClass('hidden_');
	});

	function switch_ajax_post(response){

		var import_iterations = 23;

		switch (response) {
			case '0':
				error_status();
			break;
			case 'error':
				error_status();
			break;
			case 'undefined':
				error_status();
			break;
			case 'import_end':
				add_text_status('import_complete');
			break;
			/* case 'import_json':
				if(loaded_JSON){
					ajax_post(response, loaded_JSON);
				}else{
					add_text_status('import_complete');
				}
			break*/
			default:
				var load_bar_percent = jQuery('#import_data .loader_bar span').width()/jQuery('#import_data .loader_bar').width()*100,
					load_percent_count = parseFloat( load_bar_percent + (100/import_iterations) );

				jQuery('#import_data .loader_bar span').css({'width':(load_bar_percent + (100/import_iterations) ) +"%"});
				jQuery('#import_data .progress-bar-counter_ span').html( toFixed(load_percent_count, 2) );
				ajax_post(response, 0);
			break;
		}
	}

	function toFixed(value, precision) {
		var precision = precision || 0,
			power = Math.pow(10, precision),
			absValue = Math.abs(Math.round(value * power)),
			result = (value < 0 ? '-' : '') + String(Math.floor(absValue / power));

		if (precision > 0) {
			var fraction = String(absValue % power),
				padding = new Array(Math.max(precision - fraction.length, 0) + 1).join('0');
			result += '.' + padding + fraction;
		}
		return result;
	}

	function add_text_status(text_index){
		jQuery('#status_log .dm-install-item:last-child').addClass('item-success');
		if(text_index == 'import_complete'){
			jQuery('#status_log').append( '<div class="dm-install-item item-success">' + cherry_dm_import_texts['import_complete'] + '</div>' );
			instal_content_done();
		}else{
			jQuery('#status_log').append('<div class="dm-install-item">' + cherry_dm_import_texts[text_index] + '</div>');
		}
	}

	function instal_content_done(){
		jQuery('#import_data .loader_bar span').css({'width':'100%'});
		jQuery('#import_data .progress-bar-counter_ span').html(100);

		setTimeout(function(){

			var event = jQuery.Event('cherry_data_manager_import_end');

			jQuery(document).trigger( event );

			if ( event.isDefaultPrevented() ) {
				return;
			}

			$import_step_2.addClass('hidden_');
			$import_step_3.removeClass('hidden_');
			$upload_form.addClass('hidden_');

		}, 2000);
	}

	function error_status(){
		jQuery('#status_log .dm-install-item:last-child').addClass('item-error').append('<span class="dm-install-item-message">' + cherry_dm_import_texts["instal_error"] + '</span>');
		jQuery('#import_data .loader_bar span').css({'width':'100%', 'background':'red'});
		jQuery('#import_data .progress-bar-counter_ span').html( 100 );
	}

	function in_array(array, value) {
		for(var i=0; i<array.length; i++) {
			if (array[i] == value) return true;
		}
		return false;
	}

});