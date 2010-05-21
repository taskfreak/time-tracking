/*
Yetii - Yet (E)Another Tab Interface Implementation
version 1.6
http://www.kminek.pl/lab/yetii/
Copyright (c) Grzegorz Wojcik
Code licensed under the BSD License:
http://www.kminek.pl/bsdlicense.txt
*/

function Yetii() {

	this.defaults = {
		
		id: null,
		active: 1,
		interval: null,
		wait: null,
		persist: null,
		tabclass: 'tab',
		activeclass: 'active',
		callback: null,
		leavecallback: null
	
	};
	
	this.activebackup = null;
	
	for (var n in arguments[0]) { this.defaults[n]=arguments[0][n]; };	
	
	this.getTabs = function() {
        	
        var retnode = [];
        var elem = document.getElementById(this.defaults.id).getElementsByTagName('*');
		
		var regexp = new RegExp("(^|\\s)" + this.defaults.tabclass.replace(/\-/g, "\\-") + "(\\s|$)");
	
        for (var i = 0; i < elem.length; i++) {
			if (regexp.test(elem[i].className)) retnode.push(elem[i]);
        }
    
        return retnode;
    
    };
	
	this.links = document.getElementById(this.defaults.id + '-nav').getElementsByTagName('a');
	this.listitems = document.getElementById(this.defaults.id + '-nav').getElementsByTagName('li');
	
	this.show = function(number) {
        
        for (var i = 0; i < this.tabs.length; i++) {
			
			this.tabs[i].style.display = ((i+1)==number) ? 'block' : 'none';
				
			if ((i+1)==number) {
				this.addClass(this.links[i], this.defaults.activeclass);
				this.addClass(this.listitems[i], this.defaults.activeclass + 'li');
			} else {
				this.removeClass(this.links[i], this.defaults.activeclass);
				this.removeClass(this.listitems[i], this.defaults.activeclass + 'li');
			}
		
		}
		
		
		if (this.defaults.leavecallback && (number != this.activebackup)) this.defaults.leavecallback(this.defaults.active);
		
		this.activebackup = number;
		
		
		this.defaults.active = number;
		
		if (this.defaults.callback) this.defaults.callback(number);
		
    
    };
	
	this.rotate = function(interval) {
    
        this.show(this.defaults.active);
        this.defaults.active++;
    
        if (this.defaults.active > this.tabs.length) this.defaults.active = 1;
    
	
        var self = this;
		
		if (this.defaults.wait) clearTimeout(this.timer2);
		 
        this.timer1 = setTimeout(function(){self.rotate(interval);}, interval*1000);
    
    };
	
	this.next = function() {

        var _target = (this.defaults.active + 1 > this.tabs.length) ? 1 : this.defaults.active + 1;
        this.show(_target);
        this.defaults.active = _target;

    };
	
	this.previous = function() {

        var _target = ((this.defaults.active - 1) == 0) ? this.tabs.length : this.defaults.active - 1;
        this.show(_target);
        this.defaults.active = _target; 

    };
	
	this.previous = function() {
		
		this.defaults.active--;
    	if(!this.defaults.active) this.defaults.active = this.tabs.length;
		this.show(this.defaults.active);
	
	};
	
	this.gup = function(name) {
		name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
		var regexS = "[\\?&]"+name+"=([^&#]*)";
		var regex = new RegExp( regexS );
		var results = regex.exec( window.location.href );
		if (results == null) return null;
		else return results[1];
	};
	
	this.parseurl = function(tabinterfaceid) {
		
		var result = this.gup(tabinterfaceid);
		
		if (result==null) return null;
		if (parseInt(result)) return parseInt(result); 
		if (document.getElementById(result)) {	
			for (var i=0;i<this.tabs.length;i++) {
				if (this.tabs[i].id == result) return (i+1);
			}
		}
		
		return null;
		
	};

	this.createCookie = function(name,value,days) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		}
		else var expires = "";
		document.cookie = name+"="+value+expires+"; path=/";
	};
	
	this.readCookie = function(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	};
	
	this.contains = function(el, item, from) {
		return el.indexOf(item, from) != -1;
	};
	
	this.hasClass = function(el, className){
		return this.contains(el.className, className, ' ');
	};
	
	this.addClass = function(el, className){
		if (!this.hasClass(el, className)) el.className = (el.className + ' ' + className).replace(/\s{2,}/g, ' ').replace(/^\s+|\s+$/g, '');
	};
	
	this.removeClass = function(el, className){
		el.className = el.className.replace(new RegExp('(^|\\s)' + className + '(?:\\s|$)'), '$1');
		el.className.replace(/\s{2,}/g, ' ').replace(/^\s+|\s+$/g, '');
	};


	this.tabs = this.getTabs();
	this.defaults.active = (this.parseurl(this.defaults.id)) ? this.parseurl(this.defaults.id) : this.defaults.active;
	if (this.defaults.persist && this.readCookie(this.defaults.id)) this.defaults.active = this.readCookie(this.defaults.id);  
	this.activebackup = this.defaults.active;
	this.show(this.defaults.active);
	
	var self = this;
	for (var i = 0; i < this.links.length; i++) {
	this.links[i].customindex = i+1;
	this.links[i].onclick = function(){ 
		
		if (self.timer1) clearTimeout(self.timer1);
		if (self.timer2) clearTimeout(self.timer2); 
		
		self.show(this.customindex);
		if (self.defaults.persist) self.createCookie(self.defaults.id, this.customindex, 0);
		
		if (self.defaults.wait) self.timer2 = setTimeout(function(){self.rotate(self.defaults.interval);}, self.defaults.wait*1000);
		
		return false;
	};
    }
	
	if (this.defaults.interval) this.rotate(this.defaults.interval);
	
};