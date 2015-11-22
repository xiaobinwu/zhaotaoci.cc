/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
function fontZoom(z, i) {
	var i = i ? i : 'content';
	var size = $('#'+i).css('font-size');
	var new_size = Number(size.replace('px', ''));
	new_size += z == '+' ? 1 : -1;
	if(new_size < 12) new_size = 12;
	$('#'+i).css('font-size', new_size+'px');
	$('#'+i+' *').css('font-size', new_size+'px');
}
function ImgZoom(i, m) {
	var m = m ? m : 550; var w = i.width;
	if(w < m) {
		//i.className = '';
	} else {
		var h = i.height;
		i.height = parseInt(h*m/w);
		i.width = m;
		i.title = L['click_open'];
		i.onclick = function (e) {window.open(DTPath+'api/view.php?img='+i.src);}
	}
}
window.onload = function() {
	try {
	var Imgs = content_id ? Dd(content_id).getElementsByTagName("img") : $('img');
	for(var i=0;i<Imgs.length;i++) {ImgZoom(Imgs[i], img_max_width);}
	var Links = Dd(content_id).getElementsByTagName("a");
	for(var i=0;i<Links.length;i++)	{if(Links[i].target != '_blank') {if(document.domain && Links[i].href.indexOf(document.domain) == -1) Links[i].target = '_blank';}}
	} catch(e) {}
}