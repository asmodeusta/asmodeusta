/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* Global */
var dom=function(v,i,n,c,e){
    n=function(a,b){
        return new c(a,b);
    };
    c=function(a,b){
        i.push.apply(this,
        a?
        a.nodeType||a===window?
        [a]
        :""+a===a?
        /</.test(a)?
        ((e=v.createElement(b||"q")).innerHtml=a,e.children)
        :(b&&n(b)[0]||v).querySelectorAll(a)
        :/f/.test(typeof a)?
        /c/.test(v.readyState)?
        a()
        :v.addEventListener("DOMContentLoaded",a)
        :a
        :i);
    };
    n.fn=c.prototype=i;
    n.one=function(a,b){return n(a,b)[0]||null;};
    return n;
}(document,[]);
var nc=new Array();
var nh=new Array();
var rc={};
var rs=false;
var cp,pp,ready=false;

function ajax(o){
    var a={
        m:'method'in o?o.method:'POST',
        u:'url'in o?o.url:window.location.pathname,
        p:'params'in o?o.params:{},
        h:'headers'in o?o.headers:{},
        e:'element'in o?o.element:this,
        bs:'beforeSend'in o?o.beforeSend:function(e){},
        ol:'onload'in o?o.onload:function(r){},
        os:'onsuccess'in o?o.onsuccess:function(r){},
        oe:'onerror'in o?o.onerror:function(r){},
        od:'ondone'in o?o.ondone:function(r){},
        send:function(){
            r=new XMLHttpRequest();
            r.onreadystatechange=function(){
                if(r.readyState===4){
                    var rt=r.responseText;
                    a.ol(rt);
                    if(r.status === 200){
                        a.os(rt);
                    }else{
                        a.oe(rt);
                    }
                    a.od(rt);
                }
            };
            r.open(a.m,a.u);
            r.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
            r.setRequestHeader("X-Requested-With","XMLHttpRequest");
            for(var n in a.h){r.setRequestHeader(n,a.h[n]);};
            var p='ajaxCall=1';for(var n in a.p){p+='&'+n+'='+a.p[n];}
            a.bs(a.e);
            r.send(p);
            return a;
        }
    };
    return a.send();
};
function page(o){
    var p={
        path:'path'in o?o.path:window.location.pathname,
        title:'title'in o?o.title:'',
        css:'css'in o?o.css:{},
        body:'body'in o?o.body:{},
        js:'js'in o?o.js:{},
        setTitle:function(t){this.title=t;},
        addCss:function(id,t,s){(t='text'?act(s,id):ac(s,id));},
        addJs:function(id,t,s){(t='text'?ast(s,id):as(s,id));},
        cb:'cb'in o?o.cb:false,
        ac:function(u,id){
            var d=dom.one("link#"+(id||"dyn_css"));
            if(d!==null){dom.one("head").removeChild(d);}
            var l=document.createElement("link");
            l.rel="stylesheet";
            l.type="text/css";
            l.href=u;
            l.id=id;
            l.nav="1";
            dom.one("head").appendChild(l);
        },
        act:function(t,id){
            var d=dom.one("style#"+(id||"dyn_css_text"));
            if(d!==null){dom.one("head")[0].removeChild(d);}
            var l=document.createElement("style");
            l.type="text/css";
            l.id=id;
            l.nav="1";
            l.innerHTML=t;
            dom.one("head").appendChild(l);
        },
        as:function(u,id){
            var d=dom.one("script#"+(id||"dyn_script"));
            if(d!==null){dom.one("body").removeChild(d);}
            var s=document.createElement("script");
            s.src=u;
            s.type="text/javascript";
            s.id=id;
            s.nav="1";
            dom.one("body").appendChild(s);
        },
        ast:function(t,id){
            var d=dom.one("script#"+(id||"dyn_script"));
            if(d!==null){dom.one("body").removeChild(d);}
            var s=document.createElement("script");
            s.type="text/javascript";
            s.id=id;
            s.nav="1";
            s.innerHTML=t;
            dom.one("body").appendChild(s);
        },
        clean:function(){(this.cb?this.cleanBody():'');this.cleanCss();this.cleanJs();},
        cleanCss:function(){this.css.forEach(function(e){dom.one('head').removeChild(e);});},
        cleanBody:function(){},
        cleanJs:function(){this.js.forEach(function(e){dom.one('body').removeChild(e);});},
        this:function(){
            p=this;
            p.title=document.title;
            p.body={navigation:{load:dom.one("#navigation").innerHTML},
                actions:{load:dom.one("#actions").innerHTML},
                content:{load:dom.one("#content").innerHTML},
                sidebar:{load:dom.one("#sidebar").innerHTML}};
            dom('script[nav="1"]').forEach(function(e){
                if(e.hasAttribute('id')&&e.hasAttribute('src')){
                    p.js[e.getAttribute('id')]={url:e.getAttribute('src')};
                }else if(e.hasAttribute('id')){
                    p.js[e.getAttribute('id')]={text:e.innerHTML};
                }});
            dom('link[nav="1"]').forEach(function(e){
                if(e.hasAttribute('id')&&e.getAttribute('rel')==="stylesheet"){
                    p.css[e.getAttribute('id')]={url:e.getAttribute('href')};
                }});
            dom('style[nav="1"]').forEach(function(e){
                if(e.hasAttribute('id')){
                    p.css[e.getAttribute('id')]={text:e.innerHTML};
                }});
            return p;
        },
        load:function(){},
        page:function(a){page(a);}
    };
    return p;
}
function load(o){ready=true;for(var d in o){o[d]();};};
window.onload=function(){
    history.pushState({page:window.location.pathname,type: "page"},document.title,window.location.pathname);
    if(history.pushState){
        window.onpopstate=function(event){
            if(event.state!==null){
                if(event.state.type.length>0){lp(nc[event.state.page],event.state.page);nh.pop();}
            }
        };
    }
    cp=page({}).this();
    nc[window.location.pathname]=cp;
    rc.sideMenuLoad=function(){dom('.side-menu>.more').forEach(function(e){
        e.onclick=function(){
            var p=this.parentNode;
            if(!p.classList.contains('active')){
                var ne=preloader(p);
                setTimeout(function(){
                    p.classList.toggle('active',true);
                    ne.remove();
                },2000);
            }else{
                p.classList.toggle('active',false);
            }
        };
    });};
    dom('[class*="css-icon"]').forEach(function(e){
        e.addEventListener('click',function(){this.classList.toggle('active');});
    });
    dom.one('#main-menu-toggle>a').addEventListener('click',function(){
        dom.one('#main-menu').classList.toggle('active');
    });
    dom.one('#search>.toggle').addEventListener('click',function(){
        this.parentNode.classList.toggle('active');
    });
    /////////
    load(rc);
};
function lp(r,p){
    var o={r:r};
    getJSON(o);
    o=o.r;
    pp=cp;cp=o;
    if(typeof(pp)==='object'){clearss(pp);}
    if(typeof(o)==='object'){
        if('title'in o){document.title=o.title;}
        if('css'in o){for(var id in o.css){
            if('text'in o.css[id]){act(o.css[id].text,id);}
            else if('url'in o.css[id]){ac(o.css[id].url,id);}
        }}
        if('body'in o){for(var id in o.body){
            if('add'in o.body[id]){ba(o.body[id].add,id);}
            else if('load'in o.body[id]){bl(o.body[id].load,id);}
            else if('remove'in o.body[id]){br(o.body[id].remove,id);}
        }}
        if('js'in o){for(var id in o.js){
            if('text'in o.js[id]){ast(o.js[id].text,id);}
            else if('url'in o.js[id]){as(o.js[id].url,id);}
        }}
    }else{dom.one('#content').innerHTML=o;}
    nc[p]=o;
    load(rc);
}
function clearss(o){
    if('css'in o){for(var id in o.css){dom.one("head").removeChild(dom.one("#"+id));}}
    if('js'in o){for(var id in o.js){dom.one("body").removeChild(dom.one("script#"+id));}}
}
function ac(u,id){
    var d=dom.one("link#"+(id||"dyn_css"));
    if(d!==null){dom.one("head").removeChild(d);}
    var l=document.createElement("link");
    l.rel="stylesheet";
    l.type="text/css";
    l.href=u;
    l.id=id;
    l.nav="1";
    dom.one("head").appendChild(l);
}
function act(t,id){
    d=dom.one("style#"+(id||"dyn_css_text"));
    if(d!==null){dom.one("head")[0].removeChild(d);}
    var l=document.createElement("style");
    l.type="text/css";
    l.id=id;
    l.nav="1";
    l.innerHTML=t;
    dom.one("head").appendChild(l);
}
function as(u,id){
    var d=dom.one("script#"+(id||"dyn_script"));
    if(d!==null){dom.one("body").removeChild(d);}
    var s=document.createElement("script");
    s.src=u;
    s.type="text/javascript";
    s.id=id;
    s.nav="1";
    dom.one("body").appendChild(s);
}
function ast(t,id){
    var d=dom.one("script#"+(id||"dyn_script"));
    if(d!==null){dom.one("body").removeChild(d);}
    var s=document.createElement("script");
    s.type="text/javascript";
    s.id=id;
    s.nav="1";
    s.innerHTML=t;
    dom.one("body").appendChild(s);
}
function ba(t,id){
    var e=document.createElement('div');
    e.innerHTML=t;
    dom.one('#'+id).appendChild(e);
}
function bl(t,id){
    dom.one('#'+id).innerHTML=t;
}
function br(t,id){
    dom.one('#'+id).innerHTML='';
}

function getJSON(o){try{o.r=JSON.parse(o.r);return true;}catch(e){return false;}}
function goPage(p){
    ajax({
        url:p,
        onsuccess:function(r){lp(r,p);},
        onerror:function(r){dom.one('#content').innerHTML=r;nc[p]=r;},
        ondone:function(){nh[nh.length]=p;;history.pushState({page:p,type:"page"},document.title,p);}
    });
}

function preloader(p){
    var ne=document.createElement('div');
    ne.className='loader';
    p.insertBefore(ne,p.firstChild);
    ne.style.position='absolute';
    ne.style.top=p.offsetTop+'px';
    ne.style.left=p.offsetLeft+'px';
    ne.style.height=p.clientHeight+'px';
    ne.style.width=p.clientWidth+'px';
    return {p:p,o:ne,remove:function(){p.removeChild(ne);}};
}