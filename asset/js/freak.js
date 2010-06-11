var AUTORELOAD=300000;
var AUTODELAY=60000;
var AUTOHIDE=2500;
var arelc=0;
var alive;
var timer;
var watch;

function reloadList() {
	stopAlive();
	jQuery.ajax({
		url: RELOAD_URI,
		type: "get",
		dataType: "html",
		success: function(html){
			$("#dlist").empty();
			$("#dlist").html(html);
			$('#dlist *[title]').wTip();
			$('a.ajax').makeajax();
			$('#dlist table thead tr th').makeClickable();
		    $('#dlist table tbody tr td').makeClickable();
		    startAlive(AUTORELOAD);
		}
   });
}
/*
function reloadTimers(id) {
	jQuery.ajax({
		url: URI+"timer/main/id/"+id+"/ajax/1",
		type: "get",
		dataType: "html",
		success: function(html){
			$('a.ajax').makeajax();
		}
	});
}
*/
function checkAll(id) {
	$('#'+id+' input:checkbox').attr("checked", "checked");
}

function timertostring(t) {
	h = Math.floor(t / 3600);
	m = Math.floor(t / 60) - (h*60);
	s = t - (h*3600 + m*60);
	d = (h<10)?('0'+h):h;
	d += ':'+((m<10)?('0'+m):m);
	d += ':'+((s<10)?('0'+s):s);
	return d;
}

function ticking() {
	$('#dtimer').html(timertostring(++timer));
}

function clockstart(id) {
	if (id) {
		// starting a task from list
		timer = 0;
		el = $('#tr_'+id);
		if (el[0]) {
			$('#dlist table tbody tr').removeClass('current');
			el.addClass('current');
			$('#sts_'+id+' span').html(LANGRUNNING);
		}
	} else {
		timer = $('#i_timer').val();
	}
	watch = window.setInterval('ticking()',1000);
	clockstatus('running');
}

function clockstop() {
	clearInterval(watch);
}
function clockreport(id) {
	clearInterval(watch);
	$('#sts_'+id+' span').html(timertostring(timer));
}
function clockstatus(cl) {
	$('#drun').removeClass('running paused');
	$('#drun').addClass(cl);
}
$(document).ready(function(){

	$('*[title]').wTip();
	$('#drun button').wTip();
	bindAjax();
	
	$('input:first').focus();
	$('.focus:first').focus();
	if ($('#drun.running').length) {
		clockstart();
	}
 
    // bind form using 'ajaxForm' 
    $('#drun').ajaxForm({
    	target:	'#drun',
    	data: {'ajax':'1'},
    	beforeSubmit:  function() {
    		clockstop();
    		$("p#vtip").remove();
    	},
    	complete: function() {
    		$('#drun button').wTip();
    		$('#drun input').focus();
    	}
    });
    
    // bind ajax links
    $('a.ajax').makeajax();

    // bind rows/td clicks
    $('#dlist table thead tr th').makeClickable();
    $('#dlist table tbody tr td').makeClickable();

    // focus on first field when opening a cbox
    $(document).bind('cbox_complete', function(){
		$('#cboxLoadedContent :input').not(':hidden').first().focus();
	});
	
	// get reload URI
	el = $('.reload').first();
	RELOAD_URI = el.attr('href');
	el.attr('href','javascript:reloadList()');
	
	// enable user menu on hover
	$('#duser p a').mouseenter(function() {
		$('#dmenu').slideDown(100);
		$('#duser').mouseleave(function() {
			$('#dmenu').slideUp(100);
			$(this).unbind('mouseleave');
		});
	});
    
    // reload task list every 5 minutes
    startAlive(AUTORELOAD);
    
    // check server action report, and clean within a few seconds
    if ($('#message.message')[0] || $('#message.error')[0]) {
    	window.setTimeout('cleanMessage()',AUTOHIDE);
    }
});

function cleanMessage() {
	$('#message').fadeOut('slow', function() {
    	$(this).removeClass('message error').html('...').fadeIn();
    });
}

function showmenu() {
	$('#dmenu').slideToggle(100);
}

function startAlive(d) {
	if ($('#dlist.tasks')[0]) {
		// console.log('start alive (delay = '+d+')');
		alive = window.setTimeout('stayingAlive()',d);
	}
}
function stopAlive() {
	// console.log('clear alive !');
	clearTimeout(alive);
}

function stayingAlive() {
	/*
	var currentTime = new Date();
	console.log('staying alive @ '+currentTime.getHours()+':'+currentTime.getMinutes()+':'+currentTime.getSeconds());
	*/
	el = $('#colorbox');
	if (el[0] && el.css('display') == 'block') {
		// something is being done, 
		startAlive(AUTODELAY);
	} else {
		if (arelc++ > 100) {
			// full reload to prevent script oveclocking
			// it seems that after a few calls some browser start to shake
			window.location.reload();
		} else {
			reloadList();
		}
	}
}

jQuery.fn.makeClickable = function() {
	return this.each(function() {
		$(this).click(function(e) {
			if (e.target.nodeName == 'A') {
				if (!e.target.href.match(/(ajax)/)) {
					window.location.href = e.target.href;
				}
				return true;
			}
			el = $(this);
			//first try to find a clickme link
			ei = el.find('a.clickme');
			if (ei[0]) {
				ei.click();
				return true;
			}
			$(this).find('a:visible').first().click();
		})
	});	
}

function bindAjax() {
	$('#timerstatus').ajaxStart(function() {
		// window.clearInterval(alive);
		$(this).addClass('loading');
	});
	$('#timerstatus').ajaxStop(function(){
    	$(this).removeClass('loading');
		// alive = window.setInterval('stayingalive()',10000);
    });
    // $(document).ajaxComplete(bindAjax);
}

jQuery.fn.makeajax = function(boxed) {
	return this.each(function() {
		if (!this.href.match(/(ajax)/)) {
			if (URLMODREWRITE) {
				this.href = this.href+'/ajax/1';
			} else if (this.href.match(/\?/)) {
				this.href = this.href+'&ajax=1';
			} else {
				this.href = this.href+'?ajax=1';
			}
			el = $(this);
			if (el.hasClass('bigbox')) {
				el.colorbox({title:'...',width:"700", height:"400"});
			} else if (el.hasClass('box')) {
				el.colorbox({title:'...',width:"400", height:"400"});
			} else {
				el.ajaxify();
			}
		}
	});
};

jQuery.fn.ajaxify = function(target) {
	return this.each(function() {
		$(this).click(function(e) {
			e.preventDefault();
			el = $(this);
			if (el.hasClass('confirm')) {
				if (!confirm('Really delete this item ?')) { // -TODO-TRANSLATE-
					return false;
				}
			}
			rel = el.attr('rel');
			jQuery.ajax({
				url: this.href,
				type: "get",
				dataType: "html",
				success: function(html){
					if (rel) {
						$('#'+rel).html(html);
					} else {
						$("#dlist").append(html);
					}
				}
		   });
		});
	});
}
/* jQuery Form Plugin */
;(function($){$.fn.ajaxSubmit=function(options){if(!this.length){log('ajaxSubmit: skipping submit process - no element selected');return this}if(typeof options=='function')options={success:options};var url=$.trim(this.attr('action'));if(url){url=(url.match(/^([^#]+)/)||[])[1]}url=url||window.location.href||'';options=$.extend({url:url,type:this.attr('method')||'GET',iframeSrc:/^https/i.test(window.location.href||'')?'javascript:false':'about:blank'},options||{});var veto={};this.trigger('form-pre-serialize',[this,options,veto]);if(veto.veto){log('ajaxSubmit: submit vetoed via form-pre-serialize trigger');return this}if(options.beforeSerialize&&options.beforeSerialize(this,options)===false){log('ajaxSubmit: submit aborted via beforeSerialize callback');return this}var a=this.formToArray(options.semantic);if(options.data){options.extraData=options.data;for(var n in options.data){if(options.data[n]instanceof Array){for(var k in options.data[n])a.push({name:n,value:options.data[n][k]})}else a.push({name:n,value:options.data[n]})}}if(options.beforeSubmit&&options.beforeSubmit(a,this,options)===false){log('ajaxSubmit: submit aborted via beforeSubmit callback');return this}this.trigger('form-submit-validate',[a,this,options,veto]);if(veto.veto){log('ajaxSubmit: submit vetoed via form-submit-validate trigger');return this}var q=$.param(a);if(options.type.toUpperCase()=='GET'){options.url+=(options.url.indexOf('?')>=0?'&':'?')+q;options.data=null}else options.data=q;var $form=this,callbacks=[];if(options.resetForm)callbacks.push(function(){$form.resetForm()});if(options.clearForm)callbacks.push(function(){$form.clearForm()});if(!options.dataType&&options.target){var oldSuccess=options.success||function(){};callbacks.push(function(data){var fn=options.replaceTarget?'replaceWith':'html';$(options.target)[fn](data).each(oldSuccess,arguments)})}else if(options.success)callbacks.push(options.success);options.success=function(data,status,xhr){for(var i=0,max=callbacks.length;i<max;i++)callbacks[i].apply(options,[data,status,xhr||$form,$form])};var files=$('input:file',this).fieldValue();var found=false;for(var j=0;j<files.length;j++)if(files[j])found=true;var multipart=false;if((files.length&&options.iframe!==false)||options.iframe||found||multipart){if(options.closeKeepAlive)$.get(options.closeKeepAlive,fileUpload);else fileUpload()}else $.ajax(options);this.trigger('form-submit-notify',[this,options]);return this;function fileUpload(){var form=$form[0];if($(':input[name=submit]',form).length){alert('Error: Form elements must not be named "submit".');return}var opts=$.extend({},$.ajaxSettings,options);var s=$.extend(true,{},$.extend(true,{},$.ajaxSettings),opts);var id='jqFormIO'+(new Date().getTime());var $io=$('<iframe id="'+id+'" name="'+id+'" src="'+opts.iframeSrc+'" onload="(jQuery(this).data(\'form-plugin-onload\'))()" />');var io=$io[0];$io.css({position:'absolute',top:'-1000px',left:'-1000px'});var xhr={aborted:0,responseText:null,responseXML:null,status:0,statusText:'n/a',getAllResponseHeaders:function(){},getResponseHeader:function(){},setRequestHeader:function(){},abort:function(){this.aborted=1;$io.attr('src',opts.iframeSrc)}};var g=opts.global;if(g&&!$.active++)$.event.trigger("ajaxStart");if(g)$.event.trigger("ajaxSend",[xhr,opts]);if(s.beforeSend&&s.beforeSend(xhr,s)===false){s.global&&$.active--;return}if(xhr.aborted)return;var cbInvoked=false;var timedOut=0;var sub=form.clk;if(sub){var n=sub.name;if(n&&!sub.disabled){opts.extraData=opts.extraData||{};opts.extraData[n]=sub.value;if(sub.type=="image"){opts.extraData[n+'.x']=form.clk_x;opts.extraData[n+'.y']=form.clk_y}}}function doSubmit(){var t=$form.attr('target'),a=$form.attr('action');form.setAttribute('target',id);if(form.getAttribute('method')!='POST')form.setAttribute('method','POST');if(form.getAttribute('action')!=opts.url)form.setAttribute('action',opts.url);if(!opts.skipEncodingOverride){$form.attr({encoding:'multipart/form-data',enctype:'multipart/form-data'})}if(opts.timeout)setTimeout(function(){timedOut=true;cb()},opts.timeout);var extraInputs=[];try{if(opts.extraData)for(var n in opts.extraData)extraInputs.push($('<input type="hidden" name="'+n+'" value="'+opts.extraData[n]+'" />').appendTo(form)[0]);$io.appendTo('body');$io.data('form-plugin-onload',cb);form.submit()}finally{form.setAttribute('action',a);t?form.setAttribute('target',t):$form.removeAttr('target');$(extraInputs).remove()}};if(opts.forceSync)doSubmit();else setTimeout(doSubmit,10);var domCheckCount=100;function cb(){if(cbInvoked)return;var ok=true;try{if(timedOut)throw'timeout';var data,doc;doc=io.contentWindow?io.contentWindow.document:io.contentDocument?io.contentDocument:io.document;var isXml=opts.dataType=='xml'||doc.XMLDocument||$.isXMLDoc(doc);log('isXml='+isXml);if(!isXml&&(doc.body==null||doc.body.innerHTML=='')){if(--domCheckCount){log('requeing onLoad callback, DOM not available');setTimeout(cb,250);return}log('Could not access iframe DOM after 100 tries.');return}log('response detected');cbInvoked=true;xhr.responseText=doc.body?doc.body.innerHTML:null;xhr.responseXML=doc.XMLDocument?doc.XMLDocument:doc;xhr.getResponseHeader=function(header){var headers={'content-type':opts.dataType};return headers[header]};if(opts.dataType=='json'||opts.dataType=='script'){var ta=doc.getElementsByTagName('textarea')[0];if(ta)xhr.responseText=ta.value;else{var pre=doc.getElementsByTagName('pre')[0];if(pre)xhr.responseText=pre.innerHTML}}else if(opts.dataType=='xml'&&!xhr.responseXML&&xhr.responseText!=null){xhr.responseXML=toXml(xhr.responseText)}data=$.httpData(xhr,opts.dataType)}catch(e){log('error caught:',e);ok=false;xhr.error=e;$.handleError(opts,xhr,'error',e)}if(ok){opts.success(data,'success');if(g)$.event.trigger("ajaxSuccess",[xhr,opts])}if(g)$.event.trigger("ajaxComplete",[xhr,opts]);if(g&&!--$.active)$.event.trigger("ajaxStop");if(opts.complete)opts.complete(xhr,ok?'success':'error');setTimeout(function(){$io.removeData('form-plugin-onload');$io.remove();xhr.responseXML=null},100)};function toXml(s,doc){if(window.ActiveXObject){doc=new ActiveXObject('Microsoft.XMLDOM');doc.async='false';doc.loadXML(s)}else doc=(new DOMParser()).parseFromString(s,'text/xml');return(doc&&doc.documentElement&&doc.documentElement.tagName!='parsererror')?doc:null}}};$.fn.ajaxForm=function(options){return this.ajaxFormUnbind().bind('submit.form-plugin',function(e){e.preventDefault();$(this).ajaxSubmit(options)}).bind('click.form-plugin',function(e){var target=e.target;var $el=$(target);if(!($el.is(":submit,input:image"))){var t=$el.closest(':submit');if(t.length==0)return;target=t[0]}var form=this;form.clk=target;if(target.type=='image'){if(e.offsetX!=undefined){form.clk_x=e.offsetX;form.clk_y=e.offsetY}else if(typeof $.fn.offset=='function'){var offset=$el.offset();form.clk_x=e.pageX-offset.left;form.clk_y=e.pageY-offset.top}else{form.clk_x=e.pageX-target.offsetLeft;form.clk_y=e.pageY-target.offsetTop}}setTimeout(function(){form.clk=form.clk_x=form.clk_y=null},100)})};$.fn.ajaxFormUnbind=function(){return this.unbind('submit.form-plugin click.form-plugin')};$.fn.formToArray=function(semantic){var a=[];if(this.length==0)return a;var form=this[0];var els=semantic?form.getElementsByTagName('*'):form.elements;if(!els)return a;for(var i=0,max=els.length;i<max;i++){var el=els[i];var n=el.name;if(!n)continue;if(semantic&&form.clk&&el.type=="image"){if(!el.disabled&&form.clk==el){a.push({name:n,value:$(el).val()});a.push({name:n+'.x',value:form.clk_x},{name:n+'.y',value:form.clk_y})}continue}var v=$.fieldValue(el,true);if(v&&v.constructor==Array){for(var j=0,jmax=v.length;j<jmax;j++)a.push({name:n,value:v[j]})}else if(v!==null&&typeof v!='undefined')a.push({name:n,value:v})}if(!semantic&&form.clk){var $input=$(form.clk),input=$input[0],n=input.name;if(n&&!input.disabled&&input.type=='image'){a.push({name:n,value:$input.val()});a.push({name:n+'.x',value:form.clk_x},{name:n+'.y',value:form.clk_y})}}return a};$.fn.formSerialize=function(semantic){return $.param(this.formToArray(semantic))};$.fn.fieldSerialize=function(successful){var a=[];this.each(function(){var n=this.name;if(!n)return;var v=$.fieldValue(this,successful);if(v&&v.constructor==Array){for(var i=0,max=v.length;i<max;i++)a.push({name:n,value:v[i]})}else if(v!==null&&typeof v!='undefined')a.push({name:this.name,value:v})});return $.param(a)};$.fn.fieldValue=function(successful){for(var val=[],i=0,max=this.length;i<max;i++){var el=this[i];var v=$.fieldValue(el,successful);if(v===null||typeof v=='undefined'||(v.constructor==Array&&!v.length))continue;v.constructor==Array?$.merge(val,v):val.push(v)}return val};$.fieldValue=function(el,successful){var n=el.name,t=el.type,tag=el.tagName.toLowerCase();if(typeof successful=='undefined')successful=true;if(successful&&(!n||el.disabled||t=='reset'||t=='button'||(t=='checkbox'||t=='radio')&&!el.checked||(t=='submit'||t=='image')&&el.form&&el.form.clk!=el||tag=='select'&&el.selectedIndex==-1))return null;if(tag=='select'){var index=el.selectedIndex;if(index<0)return null;var a=[],ops=el.options;var one=(t=='select-one');var max=(one?index+1:ops.length);for(var i=(one?index:0);i<max;i++){var op=ops[i];if(op.selected){var v=op.value;if(!v)v=(op.attributes&&op.attributes['value']&&!(op.attributes['value'].specified))?op.text:op.value;if(one)return v;a.push(v)}}return a}return el.value};$.fn.clearForm=function(){return this.each(function(){$('input,select,textarea',this).clearFields()})};$.fn.clearFields=$.fn.clearInputs=function(){return this.each(function(){var t=this.type,tag=this.tagName.toLowerCase();if(t=='text'||t=='password'||tag=='textarea')this.value='';else if(t=='checkbox'||t=='radio')this.checked=false;else if(tag=='select')this.selectedIndex=-1})};$.fn.resetForm=function(){return this.each(function(){if(typeof this.reset=='function'||(typeof this.reset=='object'&&!this.reset.nodeType))this.reset()})};$.fn.enable=function(b){if(b==undefined)b=true;return this.each(function(){this.disabled=!b})};$.fn.selected=function(select){if(select==undefined)select=true;return this.each(function(){var t=this.type;if(t=='checkbox'||t=='radio')this.checked=select;else if(this.tagName.toLowerCase()=='option'){var $sel=$(this).parent('select');if(select&&$sel[0]&&$sel[0].type=='select-one'){$sel.find('option').selected(false)}this.selected=select}})};function log(){if($.fn.ajaxSubmit.debug){var msg='[jquery.form] '+Array.prototype.join.call(arguments,'');if(window.console&&window.console.log)window.console.log(msg);else if(window.opera&&window.opera.postError)window.opera.postError(msg)}}})(jQuery);

/*	ColorBox v1.3.6 from http://colorpowered.com/colorbox/ */
(function(c){function r(b,d){d=d==="x"?m.width():m.height();return typeof b==="string"?Math.round(b.match(/%/)?d/100*parseInt(b,10):parseInt(b,10)):b}function M(b){b=c.isFunction(b)?b.call(i):b;return a.photo||b.match(/\.(gif|png|jpg|jpeg|bmp)(?:\?([^#]*))?(?:#(\.*))?$/i)}function Y(){for(var b in a)if(c.isFunction(a[b])&&b.substring(0,2)!=="on")a[b]=a[b].call(i);a.rel=a.rel||i.rel;a.href=a.href||i.href;a.title=a.title||i.title}function Z(b){i=b;a=c.extend({},c(i).data(q));Y();if(a.rel&&a.rel!=="nofollow"){g= c(".cboxElement").filter(function(){return(c(this).data(q).rel||this.rel)===a.rel});j=g.index(i);if(j<0){g=g.add(i);j=g.length-1}}else{g=c(i);j=0}if(!B){C=B=n;N=i;N.blur();c(document).bind("keydown.cbox_close",function(d){if(d.keyCode===27){d.preventDefault();e.close()}}).bind("keydown.cbox_arrows",function(d){if(g.length>1)if(d.keyCode===37){d.preventDefault();D.click()}else if(d.keyCode===39){d.preventDefault();E.click()}});a.overlayClose&&s.css({cursor:"pointer"}).one("click",e.close);c.event.trigger(aa); a.onOpen&&a.onOpen.call(i);s.css({opacity:a.opacity}).show();a.w=r(a.initialWidth,"x");a.h=r(a.initialHeight,"y");e.position(0);O&&m.bind("resize.cboxie6 scroll.cboxie6",function(){s.css({width:m.width(),height:m.height(),top:m.scrollTop(),left:m.scrollLeft()})}).trigger("scroll.cboxie6")}P.add(D).add(E).add(t).add(Q).hide();R.html(a.close).show();e.slideshow();e.load()}var q="colorbox",F="hover",n=true,e,x=c.browser.msie&&!c.support.opacity,O=x&&c.browser.version<7,aa="cbox_open",H="cbox_load",S= "cbox_complete",T="resize.cbox_resize",s,k,u,p,U,V,W,X,g,m,l,I,J,K,Q,P,t,E,D,R,y,z,v,w,i,N,j,a,B,C,$={transition:"elastic",speed:350,width:false,height:false,innerWidth:false,innerHeight:false,initialWidth:"400",initialHeight:"400",maxWidth:false,maxHeight:false,scalePhotos:n,scrolling:n,inline:false,html:false,iframe:false,photo:false,href:false,title:false,rel:false,opacity:0.9,preloading:n,current:"image {current} of {total}",previous:"previous",next:"next",close:"close",open:false,overlayClose:n, slideshow:false,slideshowAuto:n,slideshowSpeed:2500,slideshowStart:"start slideshow",slideshowStop:"stop slideshow",onOpen:false,onLoad:false,onComplete:false,onCleanup:false,onClosed:false};e=c.fn.colorbox=function(b,d){var h=this;if(!h.length)if(h.selector===""){h=c("<a/>");b.open=n}else return this;h.each(function(){var f=c.extend({},c(this).data(q)?c(this).data(q):$,b);c(this).data(q,f).addClass("cboxElement");if(d)c(this).data(q).onComplete=d});b&&b.open&&Z(h);return this};e.init=function(){function b(d){return c('<div id="cbox'+ d+'"/>')}m=c(window);k=c('<div id="colorbox"/>');s=b("Overlay").hide();u=b("Wrapper");p=b("Content").append(l=b("LoadedContent").css({width:0,height:0}),J=b("LoadingOverlay"),K=b("LoadingGraphic"),Q=b("Title"),P=b("Current"),t=b("Slideshow"),E=b("Next"),D=b("Previous"),R=b("Close"));u.append(c("<div/>").append(b("TopLeft"),U=b("TopCenter"),b("TopRight")),c("<div/>").append(V=b("MiddleLeft"),p,W=b("MiddleRight")),c("<div/>").append(b("BottomLeft"),X=b("BottomCenter"),b("BottomRight"))).children().children().css({"float":"left"}); I=c("<div style='position:absolute; top:0; left:0; width:9999px; height:0;'/>");c("body").prepend(s,k.append(u,I));if(x){k.addClass("cboxIE");O&&s.css("position","absolute")}p.children().bind("mouseover mouseout",function(){c(this).toggleClass(F)}).addClass(F);y=U.height()+X.height()+p.outerHeight(n)-p.height();z=V.width()+W.width()+p.outerWidth(n)-p.width();v=l.outerHeight(n);w=l.outerWidth(n);k.css({"padding-bottom":y,"padding-right":z}).hide();E.click(e.next);D.click(e.prev);R.click(e.close);p.children().removeClass(F); c(".cboxElement").live("click",function(d){if(d.button!==0&&typeof d.button!=="undefined")return n;else{Z(this);return false}})};e.position=function(b,d){function h(A){U[0].style.width=X[0].style.width=p[0].style.width=A.style.width;K[0].style.height=J[0].style.height=p[0].style.height=V[0].style.height=W[0].style.height=A.style.height}var f=m.height();f=Math.max(f-a.h-v-y,0)/2+m.scrollTop();var o=Math.max(document.documentElement.clientWidth-a.w-w-z,0)/2+m.scrollLeft();b=k.width()===a.w+w&&k.height()=== a.h+v?0:b;u[0].style.width=u[0].style.height="9999px";k.dequeue().animate({width:a.w+w,height:a.h+v,top:f,left:o},{duration:b,complete:function(){h(this);C=false;u[0].style.width=a.w+w+z+"px";u[0].style.height=a.h+v+y+"px";d&&d()},step:function(){h(this)}})};e.resize=function(b){function d(){a.w=a.w||l.width();a.w=a.mw&&a.mw<a.w?a.mw:a.w;return a.w}function h(){a.h=a.h||l.height();a.h=a.mh&&a.mh<a.h?a.mh:a.h;return a.h}function f(G){e.position(G,function(){if(B){if(x){A&&l.fadeIn(100);k[0].style.removeAttribute("filter")}if(a.iframe)l.append("<iframe id='cboxIframe'"+ (a.scrolling?" ":"scrolling='no'")+" name='iframe_"+(new Date).getTime()+"' frameborder=0 src='"+a.href+"' "+(x?"allowtransparency='true'":"")+" />");l.show();Q.show().html(a.title);if(g.length>1){P.html(a.current.replace(/\{current\}/,j+1).replace(/\{total\}/,g.length)).show();E.html(a.next).show();D.html(a.previous).show();a.slideshow&&t.show()}J.hide();K.hide();c.event.trigger(S);a.onComplete&&a.onComplete.call(i);a.transition==="fade"&&k.fadeTo(L,1,function(){x&&k[0].style.removeAttribute("filter")}); m.bind(T,function(){e.position(0)})}})}if(B){var o,A,L=a.transition==="none"?0:a.speed;m.unbind(T);if(b){l.remove();l=c('<div id="cboxLoadedContent"/>').html(b);l.hide().appendTo(I).css({width:d(),overflow:a.scrolling?"auto":"hidden"}).css({height:h()}).prependTo(p);c("#cboxPhoto").css({cssFloat:"none"});O&&c("select:not(#colorbox select)").filter(function(){return this.style.visibility!=="hidden"}).css({visibility:"hidden"}).one("cbox_cleanup",function(){this.style.visibility="inherit"});a.transition=== "fade"&&k.fadeTo(L,0,function(){f(0)})||f(L);if(a.preloading&&g.length>1){b=j>0?g[j-1]:g[g.length-1];o=j<g.length-1?g[j+1]:g[0];o=c(o).data(q).href||o.href;b=c(b).data(q).href||b.href;M(o)&&c("<img />").attr("src",o);M(b)&&c("<img />").attr("src",b)}}else setTimeout(function(){var G=l.wrapInner("<div style='overflow:auto'></div>").children();a.h=G.height();l.css({height:a.h});G.replaceWith(G.children());e.position(L)},1)}};e.load=function(){var b,d,h,f=e.resize;C=n;i=g[j];a=c.extend({},c(i).data(q)); Y();c.event.trigger(H);a.onLoad&&a.onLoad.call(i);a.h=a.height?r(a.height,"y")-v-y:a.innerHeight?r(a.innerHeight,"y"):false;a.w=a.width?r(a.width,"x")-w-z:a.innerWidth?r(a.innerWidth,"x"):false;a.mw=a.w;a.mh=a.h;if(a.maxWidth){a.mw=r(a.maxWidth,"x")-w-z;a.mw=a.w&&a.w<a.mw?a.w:a.mw}if(a.maxHeight){a.mh=r(a.maxHeight,"y")-v-y;a.mh=a.h&&a.h<a.mh?a.h:a.mh}b=a.href;J.show();K.show();if(a.inline){c('<div id="cboxInlineTemp" />').hide().insertBefore(c(b)[0]).bind(H+" cbox_cleanup",function(){c(this).replaceWith(l.children())}); f(c(b))}else if(a.iframe)f(" ");else if(a.html)f(a.html);else if(M(b)){d=new Image;d.onload=function(){var o;d.onload=null;d.id="cboxPhoto";c(d).css({margin:"auto",border:"none",display:"block",cssFloat:"left"});if(a.scalePhotos){h=function(){d.height-=d.height*o;d.width-=d.width*o};if(a.mw&&d.width>a.mw){o=(d.width-a.mw)/d.width;h()}if(a.mh&&d.height>a.mh){o=(d.height-a.mh)/d.height;h()}}if(a.h)d.style.marginTop=Math.max(a.h-d.height,0)/2+"px";f(d);g.length>1&&c(d).css({cursor:"pointer"}).click(e.next); if(x)d.style.msInterpolationMode="bicubic"};d.src=b}else c("<div />").appendTo(I).load(b,function(o,A){A==="success"?f(this):f(c("<p>Request unsuccessful.</p>"))})};e.next=function(){if(!C){j=j<g.length-1?j+1:0;e.load()}};e.prev=function(){if(!C){j=j>0?j-1:g.length-1;e.load()}};e.slideshow=function(){function b(){t.text(a.slideshowStop).bind(S,function(){h=setTimeout(e.next,a.slideshowSpeed)}).bind(H,function(){clearTimeout(h)}).one("click",function(){d();c(this).removeClass(F)});k.removeClass(f+ "off").addClass(f+"on")}var d,h,f="cboxSlideshow_";t.bind("cbox_closed",function(){t.unbind();clearTimeout(h);k.removeClass(f+"off "+f+"on")});d=function(){clearTimeout(h);t.text(a.slideshowStart).unbind(S+" "+H).one("click",function(){b();h=setTimeout(e.next,a.slideshowSpeed);c(this).removeClass(F)});k.removeClass(f+"on").addClass(f+"off")};if(a.slideshow&&g.length>1)a.slideshowAuto?b():d()};e.close=function(){c.event.trigger("cbox_cleanup");a.onCleanup&&a.onCleanup.call(i);B=false;c(document).unbind("keydown.cbox_close keydown.cbox_arrows"); m.unbind(T+" resize.cboxie6 scroll.cboxie6");s.css({cursor:"auto"}).fadeOut("fast");k.stop(n,false).fadeOut("fast",function(){c("#colorbox iframe").attr("src","about:blank");l.remove();k.css({opacity:1});try{N.focus()}catch(b){}c.event.trigger("cbox_closed");a.onClosed&&a.onClosed.call(i)})};e.element=function(){return c(i)};e.settings=$;c(e.init)})(jQuery);

/* Yetii - Yet (E)Another Tab Interface Implementation,version 1.6,http://www.kminek.pl/lab/yetii/,
Copyright (c) Grzegorz Wojcik,Code licensed under the BSD License: http://www.kminek.pl/bsdlicense.txt */
function Yetii(){this.defaults={id:null,active:1,interval:null,wait:null,persist:null,tabclass:'tab',activeclass:'active',callback:null,leavecallback:null};this.activebackup=null;for(var n in arguments[0]){this.defaults[n]=arguments[0][n]};this.getTabs=function(){var a=[];var b=document.getElementById(this.defaults.id).getElementsByTagName('*');var c=new RegExp("(^|\\s)"+this.defaults.tabclass.replace(/\-/g,"\\-")+"(\\s|$)");for(var i=0;i<b.length;i++){if(c.test(b[i].className))a.push(b[i])}return a};this.links=document.getElementById(this.defaults.id+'-nav').getElementsByTagName('a');this.listitems=document.getElementById(this.defaults.id+'-nav').getElementsByTagName('li');this.show=function(a){for(var i=0;i<this.tabs.length;i++){this.tabs[i].style.display=((i+1)==a)?'block':'none';if((i+1)==a){this.addClass(this.links[i],this.defaults.activeclass);this.addClass(this.listitems[i],this.defaults.activeclass+'li')}else{this.removeClass(this.links[i],this.defaults.activeclass);this.removeClass(this.listitems[i],this.defaults.activeclass+'li')}}if(this.defaults.leavecallback&&(a!=this.activebackup))this.defaults.leavecallback(this.defaults.active);this.activebackup=a;this.defaults.active=a;if(this.defaults.callback)this.defaults.callback(a)};this.rotate=function(a){this.show(this.defaults.active);this.defaults.active++;if(this.defaults.active>this.tabs.length)this.defaults.active=1;var b=this;if(this.defaults.wait)clearTimeout(this.timer2);this.timer1=setTimeout(function(){b.rotate(a)},a*1000)};this.next=function(){var a=(this.defaults.active+1>this.tabs.length)?1:this.defaults.active+1;this.show(a);this.defaults.active=a};this.previous=function(){var a=((this.defaults.active-1)==0)?this.tabs.length:this.defaults.active-1;this.show(a);this.defaults.active=a};this.previous=function(){this.defaults.active--;if(!this.defaults.active)this.defaults.active=this.tabs.length;this.show(this.defaults.active)};this.gup=function(a){a=a.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");var b="[\\?&]"+a+"=([^&#]*)";var c=new RegExp(b);var d=c.exec(window.location.href);if(d==null)return null;else return d[1]};this.parseurl=function(a){var b=this.gup(a);if(b==null)return null;if(parseInt(b))return parseInt(b);if(document.getElementById(b)){for(var i=0;i<this.tabs.length;i++){if(this.tabs[i].id==b)return(i+1)}}return null};this.createCookie=function(a,b,c){if(c){var d=new Date();d.setTime(d.getTime()+(c*24*60*60*1000));var e="; expires="+d.toGMTString()}else var e="";document.cookie=a+"="+b+e+"; path=/"};this.readCookie=function(a){var b=a+"=";var d=document.cookie.split(';');for(var i=0;i<d.length;i++){var c=d[i];while(c.charAt(0)==' ')c=c.substring(1,c.length);if(c.indexOf(b)==0)return c.substring(b.length,c.length)}return null};this.contains=function(a,b,c){return a.indexOf(b,c)!=-1};this.hasClass=function(a,b){return this.contains(a.className,b,' ')};this.addClass=function(a,b){if(!this.hasClass(a,b))a.className=(a.className+' '+b).replace(/\s{2,}/g,' ').replace(/^\s+|\s+$/g,'')};this.removeClass=function(a,b){a.className=a.className.replace(new RegExp('(^|\\s)'+b+'(?:\\s|$)'),'$1');a.className.replace(/\s{2,}/g,' ').replace(/^\s+|\s+$/g,'')};this.tabs=this.getTabs();this.defaults.active=(this.parseurl(this.defaults.id))?this.parseurl(this.defaults.id):this.defaults.active;if(this.defaults.persist&&this.readCookie(this.defaults.id))this.defaults.active=this.readCookie(this.defaults.id);this.activebackup=this.defaults.active;this.show(this.defaults.active);var f=this;for(var i=0;i<this.links.length;i++){this.links[i].customindex=i+1;this.links[i].onclick=function(){if(f.timer1)clearTimeout(f.timer1);if(f.timer2)clearTimeout(f.timer2);f.show(this.customindex);if(f.defaults.persist)f.createCookie(f.defaults.id,this.customindex,0);if(f.defaults.wait)f.timer2=setTimeout(function(){f.rotate(f.defaults.interval)},f.defaults.wait*1000);return false}}if(this.defaults.interval)this.rotate(this.defaults.interval)};

/* wTip plugin based on Vertigo Tip by www.vertigo-project.com */
jQuery.fn.wTip = function() {
	var xOffset = -10;
	var xOffinv = -50;
	var yOffset = 15;
	return this.each(function() {
		$(this).unbind().hover(    
	        function(e) {
	            this.t = this.title;
	            this.title = '';
	            if (!this.t) {
	            	this.t = $(this).html();
	            }
	            var inv = $(this).hasClass('inv');
	            this.top = (e.pageY + yOffset);
	            if (inv) {
	            	this.left = (e.pageX + xOffinv);
	           	} else {
	    	        this.left = (e.pageX + xOffset);
	    	    }
	            $("body").append( '<p id="vtip"'+((inv)?' class="inv"':'')+'><span id="vtipArrow">&nbsp;</span>' + this.t + '</p>' );           	            
	            // $('p#vtip #vtipArrow').attr("src", '/asset/img/vtip_arrow.png');
	            $('p#vtip').css("top", this.top+"px").css("left", this.left+"px").fadeIn("slow");
	        },
	        function() {
	            this.title = this.t;
	            $("p#vtip").fadeOut("slow").remove();
	        }
	    ).mousemove(
	        function(e) {
	        	el = $("p#vtip");
	        	if (el.hasClass('inv')) {
	        		this.left = (e.pageX + xOffinv);
	        	} else {
	        		this.left = (e.pageX + xOffset);
	        	}
	            this.top = (e.pageY + yOffset);
	            el.css("top", this.top+"px").css("left", this.left+"px");
	        }
	    );
	});
};