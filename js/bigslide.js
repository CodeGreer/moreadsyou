/*! bigSlide - v0.12.0 - 2016-08-01
* http://ascott1.github.io/bigSlide.js/
* Copyright (c) 2016 Adam D. Scott; Licensed MIT */
!function(a){"use strict";"function"==typeof define&&define.amd?define(["jquery"],a):"object"==typeof exports?module.exports=a(require("jquery")):a(jQuery)}(function(a){"use strict";function b(a,b){for(var c,d=a.split(";"),e=b.split(" "),f="",g=0,h=d.length;h>g;g++){c=!0;for(var i=0,j=e.length;j>i;i++)(""===d[g]||-1!==d[g].indexOf(e[i]))&&(c=!1);c&&(f+=d[g]+"; ")}return f}a.fn.bigSlide=function(c){var d=this,e=a.extend({menu:"#menu",push:".push",shrink:".shrink",hiddenThin:".hiddenThin",side:"right",menuWidth:"15.625em",semiOpenMenuWidth:"4em",speed:"300",state:"closed",activeBtn:"active",easyClose:!1,saveState:!1,semiOpenStatus:!1,semiOpenScreenWidth:480,beforeOpen:function(){},afterOpen:function(){},beforeClose:function(){},afterClose:function(){}},c),f="transition -o-transition -ms-transition -moz-transitions webkit-transition "+e.side,g={menuCSSDictionary:f+" position top bottom height width",pushCSSDictionary:f,state:e.state},h={init:function(){i.init()},_destroy:function(){return i._destroy(),delete d.bigSlideAPI,d},changeState:function(){"closed"===g.state?g.state="open":g.state="closed"},setState:function(a){g.state=a},getState:function(){return g.state}},i={init:function(){this.$menu=a(e.menu),this.$push=a(e.push),this.$shrink=a(e.shrink),this.$hiddenThin=a(e.hiddenThin),this.width=e.menuWidth,this.semiOpenMenuWidth=e.semiOpenMenuWidth;var b={position:"fixed",top:"0",bottom:"0",height:"100%"},c={"-webkit-transition":e.side+" "+e.speed+"ms ease","-moz-transition":e.side+" "+e.speed+"ms ease","-ms-transition":e.side+" "+e.speed+"ms ease","-o-transition":e.side+" "+e.speed+"ms ease",transition:e.side+" "+e.speed+"ms ease"},f={"-webkit-transition":"all "+e.speed+"ms ease","-moz-transition":"all "+e.speed+"ms ease","-ms-transition":"all "+e.speed+"ms ease","-o-transition":"all "+e.speed+"ms ease",transition:"all "+e.speed+"ms ease"},g=!1;b[e.side]="-"+e.menuWidth,b.width=e.menuWidth;var j="closed";e.saveState?(j=localStorage.getItem("bigSlide-savedState"),j||(j=e.state)):j=e.state,h.setState(j),this.$menu.css(b);var k=a(window).width();"closed"===j?e.semiOpenStatus&&k>e.semiOpenScreenWidth?(this.$hiddenThin.hide(),this.$menu.css(e.side,"0"),this.$menu.css("width",this.semiOpenMenuWidth),this.$push.css(e.side,this.semiOpenMenuWidth),this.$shrink.css({width:"calc(100% - "+this.semiOpenMenuWidth+")"}),this.$menu.addClass("semiOpen")):this.$push.css(e.side,"0"):"open"===j&&(this.$menu.css(e.side,"0"),this.$push.css(e.side,this.width),this.$shrink.css({width:"calc(100% - "+this.width+")"}),d.addClass(e.activeBtn));var l=this;d.on("click.bigSlide touchstart.bigSlide",function(a){g||(l.$menu.css(c),l.$push.css(c),l.$shrink.css(f),g=!0),a.preventDefault(),"open"===h.getState()?i.toggleClose():i.toggleOpen()}),e.semiOpenStatus&&a(window).resize(function(){var b=a(window).width();b>e.semiOpenScreenWidth?"closed"===h.getState()&&(l.$hiddenThin.hide(),l.$menu.css({width:l.semiOpenMenuWidth}),l.$menu.css(e.side,"0"),l.$push.css(e.side,l.semiOpenMenuWidth),l.$shrink.css({width:"calc(100% - "+l.semiOpenMenuWidth+")"}),l.$menu.addClass("semiOpen")):(l.$menu.removeClass("semiOpen"),"closed"===h.getState()&&(l.$menu.css(e.side,"-"+l.width).css({width:l.width}),l.$push.css(e.side,"0"),l.$shrink.css("width","100%"),l.$hiddenThin.show()))}),e.easyClose&&a(document).on("click.bigSlide",function(b){a(b.target).parents().addBack().is(d)||a(b.target).closest(e.menu).length||"open"!==h.getState()||i.toggleClose()})},_destroy:function(){this.$menu.each(function(){var c=a(this);c.attr("style",b(c.attr("style"),g.menuCSSDictionary).trim())}),this.$push.each(function(){var c=a(this);c.attr("style",b(c.attr("style"),g.pushCSSDictionary).trim())}),this.$shrink.each(function(){var c=a(this);c.attr("style",b(c.attr("style"),g.pushCSSDictionary).trim())}),d.removeClass(e.activeBtn).off("click.bigSlide touchstart.bigSlide"),this.$menu=null,this.$push=null,this.$shrink=null,localStorage.removeItem("bigSlide-savedState")},toggleOpen:function(){e.beforeOpen(),h.changeState(),i.applyOpenStyles(),d.addClass(e.activeBtn),e.afterOpen(),e.saveState&&localStorage.setItem("bigSlide-savedState","open")},toggleClose:function(){e.beforeClose(),h.changeState(),i.applyClosedStyles(),d.removeClass(e.activeBtn),e.afterClose(),e.saveState&&localStorage.setItem("bigSlide-savedState","closed")},applyOpenStyles:function(){var b=a(window).width();e.semiOpenStatus&&b>e.semiOpenScreenWidth?(this.$hiddenThin.show(),this.$menu.animate({width:this.width},{duration:Math.abs(e.speed-100),easing:"linear"}),this.$push.css(e.side,this.width),this.$shrink.css({width:"calc(100% - "+this.width+")"}),this.$menu.removeClass("semiOpen")):(this.$menu.css(e.side,"0"),this.$push.css(e.side,this.width),this.$shrink.css({width:"calc(100% - "+this.width+")"}))},applyClosedStyles:function(){var b=a(window).width();e.semiOpenStatus&&b>e.semiOpenScreenWidth?(this.$hiddenThin.hide(),this.$menu.animate({width:this.semiOpenMenuWidth},{duration:Math.abs(e.speed-100),easing:"linear"}),this.$push.css(e.side,this.semiOpenMenuWidth),this.$shrink.css({width:"calc(100% - "+this.semiOpenMenuWidth+")"}),this.$menu.addClass("semiOpen")):(this.$menu.css(e.side,"-"+this.width),this.$push.css(e.side,"0"),this.$shrink.css("width","100%"))}};return h.init(),this.bigSlideAPI={settings:e,model:g,controller:h,view:i,destroy:h._destroy},this}});