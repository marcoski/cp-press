
	/*jslint undef: false, browser: true, devel: false, eqeqeq: false, bitwise: false, white: false, plusplus: false, regexp: false */ 
	/*global jQuery,Image */
	
	// jquery 1.9.x fix start 
	var browser,matched;
	var $ = jQuery;
	
	function uaMatch(ua) {
		ua = ua.toLowerCase();

		var match = /(chrome)[ \/]([\w.]+)/.exec( ua ) ||
			/(webkit)[ \/]([\w.]+)/.exec( ua ) ||
			/(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) ||
			/(msie) ([\w.]+)/.exec( ua ) ||
		  ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
		  [];

		return {
			browser: match[ 1 ] || "",
			version: match[ 2 ] || "0"
		};
	}
	

	if ( !jQuery.browser ) {
		matched = uaMatch( navigator.userAgent );
		browser = {};

		if ( matched.browser ) {
			browser[ matched.browser ] = true;
			browser.version = matched.version;
		}

		// Chrome is Webkit, but Webkit is also Safari.
		if ( browser.chrome ) {
			browser.webkit = true;
		} else if ( browser.webkit ) {
			browser.safari = true;
		}

		jQuery.browser = browser;
	}
	
	var ua = navigator.userAgent.toLowerCase();
	var iDev = ua.match(/(iphone|ipod|ipad)/) !== null;
	var smartphone = ua.match(/(iphone|android)/) !== null;
	var android = !iDev && ua.match(/android ([^;]+)/);
	var webkit = ua.match("webkit") !== null;
	if (android) {
		android = android[1].split(/\./);
		android = parseFloat(android.shift() + "." + android.join(""));
	} else {
		android = false;
	}
	var mobile = (iDev || android || ua.match(/(android|blackberry|webOS|opera mobi)/) !== null);

	browserMatch = {
		iDev: iDev,
		android: android,
		mobile: mobile,
		webkit: webkit,
		smartphone: smartphone
	};
	var jhtml = $("html");
	var mobile =browserMatch.mobile;
	if (mobile) {
		jhtml.addClass("mobile").removeClass("desktop");
		if (browserMatch.android) {
			jhtml.addClass("android");
		} else if (browserMatch.iDev) {
			jhtml.addClass("ios");
		}
		if(browserMatch.smartphone){
			jhtml.addClass('smartphone');
		}
	} else {
		jhtml.addClass("desktop").removeClass("mobile");
	}
	
	


