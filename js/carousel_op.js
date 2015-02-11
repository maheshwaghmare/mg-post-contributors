jQuery(document).ready(function() {

	
	jQuery("#mgpc").each(function(){

		var Items, SlideSpeed, AutoPlay, StopOnHover, Navigation, Pagination, Responsive;

		if(jQuery(this).attr('data-items')) { Items = 1 } else { Items = 5; }
		if(jQuery(this).attr('data-slidespeed')) { SlideSpeed = 200 } else { SlideSpeed = 200; }
		if(jQuery(this).attr('data-autoplay')) { AutoPlay = true; } else { AutoPlay = false; }
		if(jQuery(this).attr('data-stoponhover')) { StopOnHover = true; } else { StopOnHover = false; }
		if(jQuery(this).attr('data-navigation')) { Navigation = true; } else { Navigation = false; }
		if(jQuery(this).attr('data-pagination')) { Pagination = false; } else { Pagination = true; }
		if(jQuery(this).attr('data-responsive')) { Responsive = false; } else { Responsive = true; }
	});

	jQuery("#mgpc-list-carousel").owlCarousel({
/*			autoPlay: 3000,
			items : 1,
			itemsDesktop : [1199,1],
			itemsDesktopSmall : [979,1]
*/
			items : "'"+Items+"'",
			slideSpeed : "'"+SlideSpeed+"'",
			autoPlay : "'"+AutoPlay+"'",
			stopOnHover : "'"+StopOnHover+"'",
			navigation : "'"+Navigation+"'",
			responsive : "'"+Responsive+"'",

			pagination : "'"+Pagination+"'",
//			pagination: false,

	});
});