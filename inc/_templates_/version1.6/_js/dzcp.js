var doc=document,ie4=document.all,opera=window.opera;var innerLayer,layer,x,y,doWheel=false,offsetX=15,offsetY=5;var tickerc=0,mTimer=new Array,tickerTo=new Array,tickerSpeed=new Array;var isIE=false,isWin=false,isOpera=false;var DZCP={init:function(){doc.body.id="dzcp-engine-1.7";DZCP.DebugLogger("Load DZCP-Engine 1.7");isIE=navigator.appVersion.indexOf("MSIE")!=-1?true:false;isWin=navigator.appVersion.toLowerCase().indexOf("win")!=-1?true:false;isOpera=navigator.userAgent.indexOf("Opera")!=-1?true:false;$("body").append('<div id="infoDiv"></div>');layer=$("#infoDiv")[0];doc.body.onmousemove=DZCP.trackMouse;if(dzcp_config.shoutInterval>1){if($("#navShout")[0])window.setInterval("$('#navShout').load('../inc/ajax.php?i=shoutbox');",dzcp_config.shoutInterval)}DZCP.initLightbox();if(dzcp_config.slideshowInterval>1){$(".slidetabs").tabs(".images > div",{effect:"fade",rotate:true}).slideshow({autoplay:true,interval:dzcp_config.slideshowInterval})}else{$(".slidetabs").tabs(".images > div",{effect:"fade",rotate:true}).slideshow({autoplay:false,interval:6e3})}$(".tabs").tabs("> .switchs");$(".tabs2").tabs(".switchs2 > div",{effect:"fade",rotate:true})},initLightbox:function(){DZCP.DebugLogger("Initiation Lightbox");$("a[rel^=lightbox]").lightBox({fixedNavigation:true,overlayBgColor:"#000",overlayOpacity:.8,imageLoading:"../inc/images/lightbox/loading.gif",imageBtnClose:"../inc/images/lightbox/close.gif",imageBtnPrev:"../inc/images/lightbox/prevlabel.gif",imageBtnNext:"../inc/images/lightbox/nextlabel.gif",containerResizeSpeed:350,txtImage:dzcp_config.lng=="de"?"Bild":"Image",txtOf:dzcp_config.lng=="de"?"von":"of",maxHeight:screen.height*.9,maxWidth:screen.width*.9})},addEvent:function(e,t,n){if(e.addEventListener){e.addEventListener(t,n,false);return true}else if(e.attachEvent){var r=e.attachEvent("on"+t,n);return r}else return false},trackMouse:function(e){innerLayer=$("#infoInnerLayer")[0];if(typeof layer=="object"){var t=doc.all;var n=doc.getElementById&&!doc.all;var r=5;var i=-15;x=n?e.pageX-r:window.event.clientX+doc.documentElement.scrollLeft-r;y=n?e.pageY-i:window.event.clientY+doc.documentElement.scrollTop-i;if(innerLayer){var s=(t?innerLayer.offsetWidth:innerLayer.clientWidth)-3;var o=t?innerLayer.offsetHeight:innerLayer.clientHeight}else{var s=(t?layer.clientWidth:layer.offsetWidth)-3;var o=t?layer.clientHeight:layer.offsetHeight}var u=n?window.innerWidth+window.pageXOffset-12:doc.documentElement.clientWidth+doc.documentElement.scrollLeft;var a=n?window.innerHeight+window.pageYOffset:doc.documentElement.clientHeight+doc.documentElement.scrollTop;layer.style.left=(x+offsetX+s>=u-offsetX?x-(s+offsetX):x+offsetX)+"px";layer.style.top=y+offsetY+"px"}return true},popup:function(e,t,n){t=parseInt(t);n=parseInt(n)+50;popup=window.open(e,"Popup","width=1,height=1,location=0,scrollbars=0,resizable=1,status=0");popup.resizeTo(t,n);popup.moveTo((screen.width-t)/2,(screen.height-n)/2);popup.focus()},initGameServer:function(e){DZCP.initDynLoader("navGameServer_"+e,"server","&serverID="+e)},initTeamspeakServer:function(){DZCP.initDynLoader("navTeamspeakServer","teamspeak","")},initDynLoader:function(e,t,n){DZCP.DebugLogger("DynLoader -> Tag: '"+e+"' / URL: '"+"../inc/ajax.php?i="+t+n+"'");var r=$.ajax({url:"../inc/ajax.php?i="+t+n,type:"GET",data:{},cache:true,dataType:"html",contentType:"application/x-www-form-urlencoded;"});r.done(function(t){$("#"+e).html(t).hide().fadeIn("normal")})},initPageDynLoader:function(e,t){DZCP.DebugLogger("PageDynLoader -> Tag: '"+e+"' / URL: '"+t+"'");var n=$.ajax({url:t,type:"GET",data:{},cache:true,dataType:"html",contentType:"application/x-www-form-urlencoded;"});n.done(function(t){$("#"+e).html(t).hide().fadeIn("normal")})},initDynCaptcha:function(e,t,n,r,i,s,o){var u="../inc/ajax.php?i=securimage";if(t>1){u=u+"&height="+t}if(n>1){u=u+"&width="+n}if(r>=1){u=u+"&lines="+r}if(i.length>1){u=u+"&namespace="+i}if(s>=1){u=u+"&length="+s}if(o>0){u=u+"&sid="+o}else{u=u+"&sid="+Math.random()}DZCP.DebugLogger("DynCaptcha -> Tag: '"+e+"' / URL: '"+u+"'");var a=$.ajax({url:u,type:"GET",data:{},cache:false,dataType:"html",contentType:"application/x-www-form-urlencoded;"});a.done(function(t){$("#"+e).attr("src",t).hide().fadeIn("normal")})},EvalSound:function(e){DZCP.DebugLogger("EvalSound -> URL: '"+e+"'");var t=new Audio(e);t.play()},shoutSubmit:function(){$.post("../shout/index.php?ajax",$("#shoutForm").serialize(),function(e){if(e)alert(e.replace(/  /g," "));$("#navShout").load("../inc/ajax.php?i=shoutbox");if(!e)$("#shouteintrag").prop("value","")});return false},switchuser:function(){var e=doc.formChange.changeme.options[doc.formChange.changeme.selectedIndex].value;window.location.href=e},tempswitch:function(){var e=doc.form.tempswitch.options[doc.form.tempswitch.selectedIndex].value;if(e!="lazy")DZCP.goTo(e)},autocomplete:function(e,t){DZCP.DebugLogger("Autocomplete -> URL: '"+"../inc/ajax.php?i=autocomplete&type="+e+"&game="+n+"'");var n=$("#status :selected").val();$(document).load("../inc/ajax.php?i=autocomplete&type="+e+"&game="+n,function(e){var n=jQuery.parseJSON(e);if(n.qport!=""){if(t||$("#qport").val()==""){$("#qport").val(n.qport);$("#autochanged").show()}}})},goTo:function(e,t){if(t==1)window.open(e);else window.location.href=e},maxlength:function(e,t,n){if(e.value.length>n)e.value=e.value.substring(0,n);else t.value=n-e.value.length},showInfo:function(e,t,n,r,i,s){if(typeof layer=="object"){var o="";if(t&&n){var u=t.split(";");var a=n.split(";");var f="";for(var l=0;l<u.length;++l){f=f+"<tr><td>"+u[l]+"</td><td>"+a[l]+"</td></tr>"}o='<tr><td class="infoTop" colspan="2">'+e+"</td></tr>"+f+""}else if(t&&typeof n=="undefined"){o='<tr><td class="infoTop" colspan="2">'+e+"</td></tr><tr><td>"+t+"</td></tr>"}else{o="<tr><td>"+e+"</td></tr>"}var c="";if(r){c='<tr><td colspan=2 align=center><img src="'+r+'" width="'+i+'" height="'+s+'" alt="" /></td></tr>'}else{c=""}layer.innerHTML='<div id="hDiv">'+'  <table class="hperc" cellspacing="0" style="height:100%">'+"    <tr>"+'      <td style="vertical-align:middle">'+'        <div id="infoInnerLayer">'+'          <table class="hperc" cellspacing="0">'+"              "+o+""+"              "+c+""+"          </table>"+"        </div>"+"      </td>"+"    </tr>"+"  </table>"+"</div>";if(ie4&&!opera){layer.innerHTML+='<iframe id="ieFix" frameborder="0" width="'+$("#hDiv")[0].offsetWidth+'" height="'+$("#hDiv")[0].offsetHeight+'"></iframe>';layer.style.display="block"}else layer.style.display="block"}},showSteamBox:function(e,t,n,r,i){var s;switch(i){case 1:s="online";break;case 2:s="in-game";break;default:s="offline";break}if(typeof layer=="object"){layer.innerHTML='<div id="hDiv">'+'  <table class="hperc" cellspacing="0" style="height:100%">'+"    <tr>"+'      <td style="vertical-align:middle">'+'        <div id="infoInnerLayer">'+'             <table class="steam_box_bg" border="0" cellspacing="0" cellpadding="0">'+"              <tr>"+"                <td>"+'                   <div class="steam_box steam_box_user '+s+'">'+'                     <div class="steam_box_avatar '+s+'"> <img src="'+t+'" /></div>'+'                     <div class="steam_box_content">'+e+"<br />"+'                     <span class="friendSmallText">'+n+"<br>"+r+"</span></div>"+"                   </div>"+"                </td>"+"              </tr>"+"            </table>"+"        </div>"+"      </td>"+"    </tr>"+"  </table>"+"</div>";if(ie4&&!opera){layer.innerHTML+='<iframe id="ieFix" frameborder="0" width="'+$("#hDiv")[0].offsetWidth+'" height="'+$("#hDiv")[0].offsetHeight+'"></iframe>';layer.style.display="block"}else layer.style.display="block"}},hideInfo:function(){if(typeof layer=="object"){layer.innerHTML="";layer.style.display="none"}},toggle:function(e){if(e==0)return;if($("#more"+e).css("display")=="none"){$("#more"+e).fadeIn("normal");$("#img"+e).prop("src","../inc/images/collapse.gif")}else{$("#more"+e).fadeOut("normal");$("#img"+e).prop("src","../inc/images/expand.gif")}},fadetoggle:function(e){if(e==0)return;$("#more_"+e).fadeToggle("slow","swing");if($("#img_"+e).prop("alt")=="hidden")$("#img_"+e).prop({alt:"normal",src:"../inc/images/toggle_normal.png"});else $("#img_"+e).prop({alt:"hidden",src:"../inc/images/toggle_hidden.png"})},resizeImages:function(){for(var e=0;e<doc.images.length;e++){var t=doc.images[e];if(t.className=="content"){var n=t.width;var r=t.height;if(dzcp_config.maxW!=0&&n>dzcp_config.maxW){t.width=dzcp_config.maxW;t.height=Math.round(r*(dzcp_config.maxW/n));if(!DZCP.linkedImage(t)){var i=doc.createElement("span");var s=doc.createElement("a");i.appendChild(doc.createElement("br"));i.setAttribute("class","resized");i.appendChild(doc.createTextNode("auto resized to "+t.width+"x"+t.height+" px"));s.setAttribute("href",t.src);s.setAttribute("rel","lightbox");s.appendChild(t.cloneNode(true));t.parentNode.appendChild(i);t.parentNode.replaceChild(s,t);DZCP.initLightbox()}}}}},linkedImage:function(e){do{e=e.parentNode;if(e.nodeName=="A")return true}while(e.nodeName!="TD"&&e.nodeName!="BODY");return false},calSwitch:function(e,t){var n=$.ajax({url:"../inc/ajax.php?i=kalender&month="+e+"&year="+t,type:"GET",data:{},cache:false,dataType:"html",contentType:"application/x-www-form-urlencoded;"});n.done(function(e){$("#navKalender").html(e).hide().fadeIn("normal")})},teamSwitch:function(e){clearTimeout(mTimer[1]);$("#navTeam").load("../inc/ajax.php?i=teams&tID="+e,DZCP.initTicker("teams","h",60))},ajaxVote:function(e){DZCP.submitButton("contentSubmitVote");$.post("../votes/index.php?action=do&ajax=1&what=vote&id="+e,$("#navAjaxVote").serialize(),function(e){$("#navVote").html(e)});return false},ajaxFVote:function(e){DZCP.submitButton("contentSubmitFVote");$.post("../votes/index.php?action=do&fajax=1&what=fvote&id="+e,$("#navAjaxFVote").serialize(),function(e){$("#navFVote").html(e)});return false},ajaxPreview:function(e){DZCP.DebugLogger("Ajax Preview -> Tag: '"+e+"'");var t=doc.getElementsByTagName("textarea");for(var n=0;n<t.length;n++){var r=t[n].className;var i=t[n].id;if(r=="editorStyle"||r=="editorStyleWord"||r=="editorStyleNewsletter"){var s=tinyMCE.getInstanceById(i);$("#"+i).prop("value",s.getBody().innerHTML)}}$("#previewDIV").html('<div style="width:100%;text-align:center">'+' <img src="../inc/images/admin/loading.gif" alt="" />'+"</div>");var o="";if(e=="cwForm"){$("input[type=file]").each(function(){o=o+"&"+$(this).prop("name")+"="+$(this).prop("value")})}var u=prevURL;$.post(u,$("#"+e).serialize()+o,function(e){$("#previewDIV").html(e).hide().fadeIn("fast");DZCP.resizeImages()})},del:function(e){e=e.replace(/\+/g," ");e=e.replace(/oe/g,"�");return confirm(e+"?")},hideForumFirst:function(){$("#allkat").prop("checked",false)},hideForumAll:function(){for(var e=0;e<doc.forms["search"].elements.length;e++){var t=doc.forms["search"].elements[e];if(t.id.match(/k_/g))t.checked=false}},submitButton:function(e){submitID=e?e:"contentSubmit";$("#"+submitID).prop("disabled",true);$("#"+submitID).css("color","#909090");$("#"+submitID).css("cursor","default");return true},initTicker:function(e,t,n){DZCP.DebugLogger("Initiation Newticker");tickerTo[tickerc]=t=="h"||t=="v"?t:"v";tickerSpeed[tickerc]=parseInt(n)<=10?10:parseInt(n);var r=$("#"+e).html();var i='  <div id="scrollDiv'+tickerc+'" class="scrollDiv" style="position:relative;left:0;z-index:1">';i+='    <table id="scrollTable'+tickerc+'" class="scrolltable"  cellpadding="0" cellspacing="0">';i+="      <tr>";i+='        <td onmouseover="clearTimeout(mTimer['+tickerc+'])" onmouseout="DZCP.startTickerDiv('+tickerc+')">';for(var s=0;s<10;s++)i+=r;i+="        </td>";i+="      </tr>";i+="    </table>";i+="  </div>";$("#"+e).html(i);window.setTimeout("DZCP.startTickerDiv("+tickerc+");",1500);tickerc++},startTickerDiv:function(e){tableObj=$("#scrollTable"+e)[0];obj=tableObj.parentNode;objWidth=tickerTo[e]=="h"?tableObj.offsetWidth:tableObj.offsetHeight;newWidth=Math.floor(objWidth/2)*2+2;obj.style.width=newWidth;mTimer[e]=setInterval("DZCP.moveDiv('"+obj.id+"', "+newWidth+", "+e+");",tickerSpeed[e])},moveDiv:function(e,t,n){var r=$("#"+e)[0];if(tickerTo[n]=="h")r.style.left=parseInt(r.style.left)<=0-t/2+2?0:parseInt(r.style.left)-1+"px";else r.style.top=r.style.top==""||parseInt(r.style.top)<0-t/2+6?0:parseInt(r.style.top)-1+"px"},GoToAnchor:function(){if(!DZCP.empty(dzcp_config.AnchorMove)){DZCP.DebugLogger("GoToAnchor -> Tag: '"+dzcp_config.AnchorMove+"'");$("html, body").animate({scrollTop:$("#"+dzcp_config.AnchorMove).offset().top-12},"slow")}},empty:function(e){return e==null||$.noop(e)||!/\S/.test(e)},DebugLogger:function(e){if(dzcp_config.debug){console.info("DZCP Debug: "+e)}}};$(document).ready(function(){DZCP.init()});$(window).load(function(){DZCP.resizeImages();DZCP.GoToAnchor()})