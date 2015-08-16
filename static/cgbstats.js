CGBStats = {};

CGBStats.mobileAndTabletCheck = function() {
  var check = false;
  (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);
  return check;
}

CGBStats.gui = {};
CGBStats.gui.headerScroll = {};
CGBStats.gui.headerScroll.update = function(){
	if(CGBStats.mobileAndTabletCheck()) return;
	var totalWidth = 100;
	$(".tab-header-inner").children().each(function(){
		totalWidth += $(this).outerWidth();
	});
	
	$(".tab-header-inner").width(totalWidth);
	
	if($(document).width() < $(".tab-header-inner").width())
		$(".tab-header .scrollbar-thumb").show().width($(document).width() * $(document).width() / $(".tab-header-inner").width());
	else
		$(".tab-header .scrollbar-thumb").hide();
};

CGBStats.gui.headerScroll.init = function(){
	$(".tab-header .scrollbar-thumb").hide();
	
	if(CGBStats.mobileAndTabletCheck()){
		$(".tab-header").css("overflow-x", "auto");
	}
	
	var isDragging = false;
	var ox, omx;
	$(".tab-header .scrollbar-thumb").mousedown(function(e) {
		isDragging = true;
		ox = $(".tab-header .scrollbar-thumb").position().left;
		omx = e.pageX;
	});
	$(document).mouseup(function(e) {
		isDragging = false;
	});
	
	$(document).mousemove(function(e) {
		if(!isDragging) return;
		
		e.preventDefault();
		var pos = e.pageX - omx + ox;
		if(pos > $(document).width() - $(".tab-header .scrollbar-thumb").width())
			pos = $(document).width() - $(".tab-header .scrollbar-thumb").width();
		if(pos < 0)
			pos = 0;
		$(".tab-header .scrollbar-thumb").css("left", pos + "px");
		
		$(".tab-header").scrollLeft($(".tab-header-inner").width() * pos / $(document).width());
	})
	
	CGBStats.gui.headerScroll.update();
	
	$(window).resize(CGBStats.gui.headerScroll.update);
};

CGBStats.gui.headerScroll.init();

CGBStats.checkCompatibility = function(){
	var hasLocalStorage = (typeof window['localStorage'] !== 'undefined');
	var hasPushState = (typeof history['pushState'] !== 'undefined');
	
	var hasXHR = (typeof window.XMLHttpRequest !== 'undefined');
	// skip fancy stuff, a simple alert should work
	// not like this should really ever happen anyway
	if(!hasXHR) alert("You are using an unsupported browser! Upgrade to the latest version to use CGBStats.");
	
	return {
		localStorage: hasLocalStorage,
		pushState: hasPushState
	};
};

CGBStats.compat = CGBStats.checkCompatibility();

CGBStats.nav = {};

CGBStats.nav._shutdownHooks = [];
CGBStats.nav.addShutdownHook = function(cb){
	CGBStats.nav._shutdownHooks.push(cb);
};

CGBStats.nav.getPage = function(){
	var pathname = location.pathname;
	if(pathname == "" || pathname == "/" || pathname == "index.html") return "/global";
	else return pathname;
};

CGBStats.nav.getQuery = function(){
	return location.search;
};

CGBStats.nav.onPageLoaded = function(){
	$(".tab-content").scrollTop(0);
	$("[data-href]").off().on("click", function(e){
		e.preventDefault();
		CGBStats.nav.setLocation($(this).attr("data-href"));
		$(".tab, .tab-header .header").removeClass("selected");
		$(this).addClass("selected");
		return false;
	});
	
	if(location.pathname != CGBStats.nav.getPage())
		history.replaceState({}, "", CGBStats.nav.getPage());
	
	$(".tab, .tab-header .header").removeClass("selected");
	$(".tab[data-href='" + CGBStats.nav.getPage() + CGBStats.nav.getQuery() + "']").addClass("selected");
	if(CGBStats.nav.getPage() == "/about") $(".tab-header .header").addClass("selected");
};

CGBStats.nav.loadPage = function(){
	if(typeof CGBStats.nav.xhr !== 'undefined') CGBStats.nav.xhr.abort();
	
	while(CGBStats.nav._shutdownHooks.length > 0){
		(CGBStats.nav._shutdownHooks.pop())();
	}
	
	if(CGBStats.nav.getPage() !== "/about" && CGBStats.nav.getPage() !== "/logout" && CGBStats.nav.getPage() !== "/join" && $(".tab[data-href='" + CGBStats.nav.getPage() + CGBStats.nav.getQuery() + "']").length == 0){
		CGBStats.persist.addTab(CGBStats.nav.getPage() + CGBStats.nav.getQuery(), "CGBStats");
		
		$('<a class="tab" href="javascript:void(0)"></a>').attr("data-href", CGBStats.nav.getPage() + CGBStats.nav.getQuery()).append($("<span>").addClass("tab-name").text(CGBStats.nav.getPage().substring(1) + " ")).append($("<a>").attr("href", "javascript:void(0)").text("X").click(function(){
			var href = $(this).parent().attr("data-href");
			CGBStats.persist.removeTab(href);
			$(this).parent().remove();
			if(CGBStats.nav.getPage() + CGBStats.nav.getQuery() == href) CGBStats.nav.setLocation("/global");
			CGBStats.gui.headerScroll.update();
		})).appendTo($(".tab-header-inner"));
		CGBStats.gui.headerScroll.update();
	}
	
	var timeout = setTimeout(function(){
		$(".tab-content").html("").addClass("loading");
	}, 500);
	CGBStats.nav.xhr = $.get("/templates" + CGBStats.nav.getPage() + ".php" + location.search, function(data){
		$(".tab-content").html(data).removeClass("loading");
	}).fail(function(xhr){
		var header = "Something bad happened";
		var content = "The page could not be loaded";
		var status = xhr.status;
		if(status == 404){
			header = "Page not found";
			content = "That page does not exist";
		} else if(status == 401 || status == 403){
			header = "Access denied";
			content = "You are not allowed to go here";
		} else if(status == 400 || status == 405){
			header = "Client error";
			content = "Your browser sent a bad request";
		}
		$(".tab-content").html("<div class='container'><h1>" + header + "</h1><p>" + content + ".<br/><span style='font-size: 0.8em; color: gray;'>HTTP code " + status + "</span></p></div>").removeClass("loading");
	}).always(function(){
		clearTimeout(timeout);
		CGBStats.nav.onPageLoaded();
	});
};

CGBStats.nav.setLocation = function(newLocation){
	if(CGBStats.compat.pushState) history.pushState({}, "", newLocation);
	else location.href = newLocation;
	
	CGBStats.nav.loadPage();
};

CGBStats.nav.reload = function(){
	CGBStats.nav.setLocation(CGBStats.nav.getPage() + CGBStats.nav.getQuery());
};

CGBStats.nav.onPopState = function(evt){
	CGBStats.nav.loadPage();
};
window.onpopstate = CGBStats.nav.onPopState;

CGBStats.nav.openTab = function(loc, name){
	if($(".tab[data-href='" + loc + "']").length > 0){
		$(".tab[data-href='" + loc + "']").trigger("click");
		return;
	}
	
	CGBStats.persist.addTab(loc, name);
	
	$('<a class="tab" href="javascript:void(0)"></a>').attr("data-href", loc).append($("<span>").addClass("tab-name").text(name + " ")).append($("<a>").attr("href", "javascript:void(0)").text("X").click(function(){
		var href = $(this).parent().attr("data-href");
		CGBStats.persist.removeTab(href);
		$(this).parent().remove();
		if(CGBStats.nav.getPage() + CGBStats.nav.getQuery() == href)CGBStats.nav.setLocation("/global");
		CGBStats.gui.headerScroll.update();
	})).appendTo($(".tab-header-inner"));
	CGBStats.gui.headerScroll.update();
};

CGBStats.account = {};
CGBStats.account.getUserId = function(callback){
	$.get("/api/getuserid.php", function(data){
		if(data.logged_in) callback(data.userid);
		else callback(false);
	}).fail(function(){
		callback(false);
	});
};

CGBStats.account.getUserId(function(userid){
	if(userid === false) return;
	CGBStats.account.userid = userid;
	$(".account-tab").attr("data-href", "/logout").text("Log Out");
});

CGBStats.persist = {};
CGBStats.persist.addTab = function(loc, name){
	if(!CGBStats.compat.localStorage) return;
	if(typeof localStorage.CGBStatsTabs === 'undefined') localStorage.CGBStatsTabs = "{}";
	var tabs = JSON.parse(localStorage.CGBStatsTabs);
	tabs[loc] = {name: name};
	localStorage.CGBStatsTabs = JSON.stringify(tabs);
};

CGBStats.persist.removeTab = function(loc){
	if(!CGBStats.compat.localStorage) return;
	if(typeof localStorage.CGBStatsTabs === 'undefined') localStorage.CGBStatsTabs = "{}";
	var tabs = JSON.parse(localStorage.CGBStatsTabs);
	delete tabs[loc];
	localStorage.CGBStatsTabs = JSON.stringify(tabs);
};

CGBStats.persist.openTabs = function(){
	if(!CGBStats.compat.localStorage) return;
	if(typeof localStorage.CGBStatsTabs === 'undefined') localStorage.CGBStatsTabs = "{}";
	var tabs = JSON.parse(localStorage.CGBStatsTabs);
	for(loc in tabs){
		if(!tabs.hasOwnProperty(loc)) continue;
		CGBStats.nav.openTab(loc, tabs[loc].name);
	}
};

CGBStats.persist.openTabs();

if(CGBStats.nav.getPage() !== "/about" && CGBStats.nav.getPage() !== "/logout" && CGBStats.nav.getPage() !== "/join" && $(".tab[data-href='" + CGBStats.nav.getPage() + CGBStats.nav.getQuery() + "']").length == 0){
	CGBStats.persist.addTab(CGBStats.nav.getPage() + CGBStats.nav.getQuery(), "CGBStats");
	
	$('<a class="tab" href="javascript:void(0)"></a>').attr("data-href", CGBStats.nav.getPage() + CGBStats.nav.getQuery()).append($("<span>").addClass("tab-name").text(CGBStats.nav.getPage().substring(1) + " ")).append($("<a>").attr("href", "javascript:void(0)").text("X").click(function(){
		var href = $(this).parent().attr("data-href");
		CGBStats.persist.removeTab(href);
		$(this).parent().remove();
		if(CGBStats.nav.getPage() + CGBStats.nav.getQuery() == href) CGBStats.nav.setLocation("/global");
		CGBStats.gui.headerScroll.update();
	})).appendTo($(".tab-header-inner"));
	CGBStats.gui.headerScroll.update();
}

CGBStats.nav.onPageLoaded();

$(".cookie-banner-dismiss").click(function(){
	$(".cookie-banner").fadeOut();
	document.cookie = "CGBStatsCookieBanner=dismissed; path=/; expires=Sat Nov 20 2286 12:46:39 GMT-0500 (Eastern Standard Time)";
});

if (!Date.now) {
    Date.now = function() { return new Date().getTime(); }
}

jQuery.fn.selectText = function(){
    var doc = document;
    var element = this[0];
    console.log(this, element);
    if (doc.body.createTextRange) {
        var range = document.body.createTextRange();
        range.moveToElementText(element);
        range.select();
    } else if (window.getSelection) {
        var selection = window.getSelection();        
        var range = document.createRange();
        range.selectNodeContents(element);
        selection.removeAllRanges();
        selection.addRange(range);
    }
};