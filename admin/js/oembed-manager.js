jQuery(document).ready( function($) {
	$( ".oemm-about-logo" ).css({opacity:1});

	$("#oemm_consumer_misc_disable_consumer").change(function(){
		var controls = $(".wrap :input[type!=hidden]").not(".button").not("#oemm_consumer_misc_disable_consumer");
		if ($("#oemm_consumer_misc_disable_consumer").prop("checked")) {controls.attr("disabled", "disabled");} else {controls.removeAttr("disabled");}
	});
	$("#oemm_consumer_misc_disable_consumer").change();
} );
