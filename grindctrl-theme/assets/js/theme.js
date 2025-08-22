(function(){
	"use strict";
	var yearEl=document.querySelector('.grindctrl-year');
	if(yearEl){yearEl.textContent=String(new Date().getFullYear());}
	if('serviceWorker' in navigator){
		navigator.serviceWorker.register('/wp-content/themes/grindctrl-theme/assets/js/sw.js').catch(function(){/* noop */});
	}
})();
