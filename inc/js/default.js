function bc(id,color){$("#"+id).css("border","1px solid "+color)}
function recaptcha(){$("#rcpt").html('<img src="index.html?captchaimage'+'&'+Math.random()+'" style="float:right;margin-left:2px">')}
function varl(id){value = $("#"+id).val();return $("#"+id).val().length}
function c4e(id){return $("#"+id).val()!=0?true:false}
function isemail(email){var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);return pattern.test(email)}
function ce(id){if(!c4e(id)){bc(id,"red");return false}else{bc(id,"#0073ea");return true}}
function bcc(id){$("#"+id).css("border-color","#CCCCCC #999 #999 #CCCCCC")}
function toPersianNumber(num){var persianNumberArray=new Array('۰','۱','۲','۳','۴','۵','۶','۷','۸','۹');var res='';for(;num/10>0;){n=num%10;num=parseInt(num/10);res=persianNumberArray[n]+res}return res}
function loadanim(id){$("#"+id).html('<img src="/images/wait.gif">')}
jQuery.fn.exists = function(){return this.length>0}
jQuery.fn.center = function(){this.css("position","absolute");this.css("top","50%");this.css("left","50%");this.css("margin-top","-"+(this.height()/2)+"px");this.css("margin-left","-"+(this.width()/2)+"px");return this}
function lrfa(e){if(e=='404.html'){location.href="/user";}else{return true}}
function changetitle(title){$(document).attr('title',title)}
