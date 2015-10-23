
// namespace Site
//
var Posts = (function(document){

	// Checks if the post element has ALL of the filters
	function postHasEvery( tagElem, filters ) {
		var filtersPassed = 0;
		for( var f=0; f<filters.length; f+=1 ) {
			if( $( tagElem ).text().indexOf(filters[f]) >= 0 ) {
				filtersPassed ++;
			}
		}
		return filtersPassed == filters.length;
	}

	// Checks if the post element has any of the filters
	function postHasAny( tagElem, filters ) {
		var filtersPassed = 0;
		for( var f=0; f<filters.length; f+=1 ) {
			if( $( tagElem ).text().indexOf(filters[f]) >= 0 ) {
				return true;
			}
		}
		return false;
	}

	// This is the Site's API
	//
	return {

		// filters are ORed with each other and the sifted posts are shown.
		// all that don't meet muster are hidden
		filterOr : function ( filters ) {
			$('.post .tags').each(function(){
				if( postHasAny( this, filters )) {
					$(this).parent().show('fast');
				}
				else {
					$(this).parent().hide('fast');
				}
			});
		},

		// filters are ORed with each other and the sifted posts are shown.
		// all that don't meet muster are hidden
		filterAnd : function ( filters ) {
			$('.post .tags').each(function(){
				if( postHasEvery( this, filters )) {
					$(this).parent().show('fast');
				}
				else {
					$(this).parent().hide('fast');
				}
			});
		},

		// SHow all
		showAll : function () {
			$('.post').show('fast');
		},

	};
})(document);
