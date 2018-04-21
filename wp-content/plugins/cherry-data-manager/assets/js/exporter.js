/**
 * add JS functions for export handling
 */
jQuery(document).ready(function($) {

	jQuery('#export-content').on('click', post_export_content);

	function post_export_content(){

		var button = jQuery(this);

		button.off('click').addClass('not_active').next('.spinner').css({
			display:'inline-block',
			visibility: 'visible'
		});

		jQuery.ajax({
			url: ajaxurl,
			type: "post",
			dataType: "json",
			data: {
				action: 'cherry_data_manager_export'
			}
		}).done(function(response) {
			//console.log(response)
			window.location.href = response.file;
			button.removeClass('not_active').on('click', post_export_content).next('.spinner').css({'display':'none'});;
		}).error(function(response) {
			button.removeClass('not_active').on('click', post_export_content).next('.spinner').css({'display':'none'});;
		});

		return !1;
	}
});