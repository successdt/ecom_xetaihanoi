// JavaScript Document
jQuery(document).ready(function() {
	jQuery('.preview_gallery').click(function(){
		var url = jQuery(this).attr("href");
		var order = jQuery('#mainform').serialize();
		var height = 500;
		var gallery_height = jQuery('#mainform').find('#wc_dgallery_product_gallery_height').val();
		var navbar_height = jQuery('#mainform').find('#wc_dgallery_navbar_height').val();
		var thumb_height = jQuery('#mainform').find('#wc_dgallery_thumb_height').val();
		if ( thumb_height == '' || parseInt(thumb_height) <= 0 ) thumb_height = 75;
		height = parseInt(gallery_height) + parseInt(navbar_height) + parseInt(thumb_height) + 80;
		tb_show('Dynamic gallery preview', url+'&width=700&height='+height+'&action=woo_dynamic_gallery&KeepThis=false&'+order);
		return false;
	});
});	