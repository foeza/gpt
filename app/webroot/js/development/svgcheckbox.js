if( document.createElement('svg').getAttributeNS ) {

	var checkbxsCheckmark = Array.prototype.slice.call( document.querySelectorAll( 'div.cb-checkmark input[type="checkbox"]' ) ),
		pathDefs = {
			checkmark : ['M43.339,104.883c-2.064,0-4.001-1.973-5.319-3.556L1.614,57.135c-1.189-1.422-1.752-3.49-1.585-5.337s1.044-3.649,2.469-4.838c1.25-1.04,2.83-1.678,4.451-1.678c2.071,0,4.017,0.88,5.337,2.472l31.09,37.272l69.366-82.548C114.072,0.902,116.01,0,118.06,0c1.628,0,3.212,0.574,4.462,1.621c2.936,2.469,3.316,6.855,0.852,9.787l-74.703,89.945c-1.331,1.576-3.267,3.53-5.317,3.53H43.339z'],
		},
		animDefs = {
			checkmark : { speed : .2, easing : 'ease-in-out' },
		};

	function createSVGEl( def ) {
		var svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
		if( def ) {
			svg.setAttributeNS( null, 'viewBox', def.viewBox );
			svg.setAttributeNS( null, 'preserveAspectRatio', def.preserveAspectRatio );
		}
		else {
			svg.setAttributeNS( null, 'viewBox', '0 0 100 100' );
		}
		svg.setAttribute( 'xmlns', 'http://www.w3.org/2000/svg' );
		return svg;
	}

	function controlCheckbox( el, type, svgDef ) {
		var svg = createSVGEl( svgDef );
		el.parentNode.appendChild( svg );
		
		el.addEventListener( 'change', function() {
			if( el.checked ) {
				draw( el, type );
			}
			else {
				reset( el );
			}
		} );
	}
	checkbxsCheckmark.forEach( function( el, i ) { controlCheckbox( el, 'checkmark' ); } );

	function draw( el, type ) {
		var paths = [], pathDef, 
			animDef,
			svg = el.parentNode.querySelector( 'svg' );

		switch( type ) {
			case 'fill': pathDef = pathDefs.fill; animDef = animDefs.fill; break;
			case 'checkmark': pathDef = pathDefs.checkmark; animDef = animDefs.checkmark; break;
		};
		
		paths.push( document.createElementNS('http://www.w3.org/2000/svg', 'path' ) );

		if( type === 'cross' || type === 'list' ) {
			paths.push( document.createElementNS('http://www.w3.org/2000/svg', 'path' ) );
		}
		
		for( var i = 0, len = paths.length; i < len; ++i ) {
			var path = paths[i];
			svg.appendChild( path );

			path.setAttributeNS( null, 'd', pathDef[i] );

			var length = path.getTotalLength();
			// Clear any previous transition
			//path.style.transition = path.style.WebkitTransition = path.style.MozTransition = 'none';
			// Set up the starting positions
			path.style.strokeDasharray = length + ' ' + length;
			if( i === 0 ) {
				path.style.strokeDashoffset = Math.floor( length ) - 1;
			}
			else path.style.strokeDashoffset = length;
			// Trigger a layout so styles are calculated & the browser
			// picks up the starting position before animating
			path.getBoundingClientRect();
			// Define our transition
			path.style.transition = path.style.WebkitTransition = path.style.MozTransition  = 'stroke-dashoffset ' + animDef.speed + 's ' + animDef.easing + ' ' + i * animDef.speed + 's';
			// Go!
			path.style.strokeDashoffset = '0';
		}
	}

	function reset( el ) {
		Array.prototype.slice.call( el.parentNode.querySelectorAll( 'svg > path' ) ).forEach( function( el ) { el.parentNode.removeChild( el ); } );
	}
	function resetRadio( el ) {
		Array.prototype.slice.call( document.querySelectorAll( 'input[type="radio"][name="' + el.getAttribute( 'name' ) + '"]' ) ).forEach( function( el ) { 
			var path = el.parentNode.querySelector( 'svg > path' );
			if( path ) {
				path.parentNode.removeChild( path );
			}
		} );
	}
}