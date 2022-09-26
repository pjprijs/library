let debug = {}
debug.debug = false;
cookieDebug = getCookie('debug');
if(cookieDebug != '') debug.debug = (cookieDebug === 'true');

let searchDelay = 250;
let maxItemsPerPage = 10;
let msgCounter = 0;
let loaderCount = 0;
let keyBuffer = new Array();
let aviArray = new Array();
//let aviLevelsOrder = new Array();
let currentRequest = new Array();

//CHROMIUM minimum width fix
let windowWidth = $(window).width();
if(window.screen.width <= 500) windowWidth = window.screen.width;

$(window).resize(function() {
  alignLayers();
});

function playFound() {
  var audio = new Audio('audio/found.wav');
  audio.play();    
}

function playNotFound() {
  var audio = new Audio('audio/notfound.wav');
  audio.play();    
}

function alignLayers() {
  positionWait();
  resizePortal();
  positionMsgLayer();
}

function loadPage(callback) {
  alignLayers();
  $('.btn').tooltip();
  callback();
}


function showErrorMsg(msg) {
  showError(msg,10);
}

function showInfoMsg(msg) {
  $('.msgBox alert-info').remove();
  showMsg(msg, 'alert alert-info', txtInfo+':', 'fas fa-circle-info');
}

function showWarningMsg(msg) {
  $('.msgBox alert-warning').remove();
  showMsg(msg, 'alert alert-warning', txtWarning+':', 'fas fa-circle-exclamation');
}

function showSuccessMsg(msg) {
  $('.msgBox alert-success').remove();
  showMsg(msg, 'alert alert-success', txtSuccess+':', 'fas fa-circle-check');
}

function showError(msg, code) {
  if(code == 100) showHome();
  $('.msgBox alert-danger').remove();
  showMsg(msg, 'alert alert-danger', txtError+':', 'fas fa-circle-xmark');
}


function showMsg(msg, className, title, icon, timeout) {
  if(timeout === undefined) timeout = 5000;
  $('.msgContainer').append($('<div id="msgBox' + msgCounter + '" class="' + className + ' msgBox" style="display: none"></div>'));
  if(icon !== undefined) $('#msgBox' + msgCounter).append('<span class="' + icon + '"></span>&nbsp;');
  if(title !== undefined) $('#msgBox' + msgCounter).append('<b class="msgTitle">' + title + '</b>&nbsp;');
  $('#msgBox' + msgCounter).append(msg);
  $('#msgBox' + msgCounter).fadeIn();
  positionMsgLayer();
  setTimeout(function(tmpCount) { return function() { $('#msgBox' + tmpCount).fadeOut(400,function(){$(this).remove(); positionMsgLayer();}); }}(msgCounter),timeout);
  msgCounter++;
}


function showWait(enabled) {
  if(enabled === undefined) enabled = true;
  if(enabled) {
    $('#wait').show();
  } else {
    $('#wait').hide();
  }
}
function positionWait() {
  $('#wait').css('left', (windowWidth-32)/2);
  $('#wait').css('top', ($(window).height()-32)/2);
}

function positionMsgLayer() {
  $('.msgContainer').finish();
  $('.msgContainer').css({
    left: (windowWidth-$('.msgContainer').width())/2 + 'px',
    top: ($(window).height()-$('.msgContainer').height())/2 + 'px'
  });
  if(windowWidth < 500) {
    $('.msgContainer').css({
      left: '0px',
      width: '100%'
    });
  }
}

function resizePortal() {
  $('.page-container').height($(window).height()-70);
}


function doLoad(url, postData, callback, abortPreviousRequest) {  
  loaderCount++;
  if(abortPreviousRequest === undefined) abortPreviousRequest = 0;
  if(postData === undefined) postData = {};
  showWait();
  currentRequest[abortPreviousRequest] = $.ajax({
    url: 'ajax/' + url + '?_=' + new Date().getTime(),
    dataType: 'jsonp',
    type: 'POST',
    data: postData,
    beforeSend : function() {
      if(abortPreviousRequest != 0) {
        if(currentRequest[abortPreviousRequest] != null) {
          currentRequest[abortPreviousRequest].abort('Aborting previous run');
        }
      }
    }
  }).done(function(result) {
    loaderCount--;
    if(loaderCount <= 0) showWait(false);
    ajaxReply(result, callback);
  }).fail(function(jqXHR, textStatus, error) {
    loaderCount--;
    if(loaderCount <= 0) showWait(false);
  });
}

function doLoadHtml(url, postData, callback) {
  loaderCount++;
  if(postData === undefined) postData = {};
  showWait();
  $.ajax({
    url: url,
    dataType: 'html',
    type: 'GET'
  }).done(function(result) {
    loaderCount--;
    if(loaderCount == 0) showWait(false);
    callback(result);
  });
}

function ajaxReply(result, callback) {
  if(result.error > 0) {
    showError(phpErrMsg[result.error] + (debug ? '<br/><b>debug:</b> ' + result.errorMsg : ''), result.error);      
    if(callback !== undefined) callback(false, result.data, result.debug);
  } else {
    if(callback !== undefined) callback(true, result.data, result.debug);
  }  
}

function setCookie(cname, cvalue, exdays) {
  const d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  let expires = "expires="+ d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

debug.log = function(msg) {
  if(debug.debug) console.log(msg);
}

function getCookie(cname) {
  let name = cname + "=";
  let decodedCookie = decodeURIComponent(document.cookie);
  let ca = decodedCookie.split(';');
  for(let i = 0; i <ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}
