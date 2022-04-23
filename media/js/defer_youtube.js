/**
 * @component     CG Parallax
 * Version			: 1.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2022 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
 * Updated on       : January, 2021
**/
function init_you() { 
	var vidDefer = document.getElementsByTagName('iframe'); 
	for (var i=0; i<vidDefer.length; i++) { 
		if(vidDefer[i].getAttribute('yousrc')) { 
			vidDefer[i].setAttribute('src',vidDefer[i].getAttribute('yousrc')); 
		} 
	} 
} 
window.onload = init_you;
