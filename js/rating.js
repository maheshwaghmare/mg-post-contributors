jQuery(window).load(function(){
   jQuery(".star").hover( function(){

   		var found = "Thanks! :)";
   		if( jQuery( this ).hasClass('star-one') ) {
            found = ":C";
         } 
   		if( jQuery( this ).hasClass('star-two') ) {
   			found = ":(";
   		}
   		if( jQuery( this ).hasClass('star-three') ) {
   			found = ":|";
   		}
   		if( jQuery( this ).hasClass('star-four') ) {
   			found = ":)";
   		}
   		if( jQuery( this ).hasClass('star-five') ) {
   			found = ":D";
   		}

   		//var file = jQuery(this).css("background-image");
   		jQuery(".result").html(found);
   });
});