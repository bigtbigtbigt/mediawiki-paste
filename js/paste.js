var inputBox = jQuery('#paste-input');
var outputBox = jQuery('#paste-output');
var mediawikiBox = jQuery('#paste-mediawiki');
var conversionScriptURL = "lib/html_to_mediawiki.php";

function handlePaste (elem, e) {
  var savedcontent = elem.innerHTML;
  if (e && e.clipboardData && e.clipboardData.getData) {// Webkit - get data from clipboard, put into editdiv, cleanup, then cancel event
    if (/text\/html/.test(e.clipboardData.types)) {
      elem.innerHTML = e.clipboardData.getData('text/html');
    }
    else if (/text\/plain/.test(e.clipboardData.types)) {
      elem.innerHTML = e.clipboardData.getData('text/plain');
    }
    else {
      elem.innerHTML = "";
    }
    waitForPasteData(elem, savedcontent);
    if (e.preventDefault) {
      e.stopPropagation();
      e.preventDefault();
    }
    return false;
  }
  else {// Everything else - empty editdiv and allow browser to paste content into it, then cleanup
    elem.innerHTML = "";
    waitForPasteData(elem, savedcontent);
    return true;
  }
}

function waitForPasteData (elem, savedcontent) {
  if (elem.childNodes && elem.childNodes.length > 0) {
    processPaste(elem, savedcontent);
  }
  else {
    that = {
      e: elem,
      s: savedcontent
    }
    that.callself = function () {
      waitForPasteData(that.e, that.s)
    }
    setTimeout(that.callself,20);
  }
}

function processPaste (elem, savedcontent) {
  // Fill the input box with the HTML of what was pasted

  pasteddata = elem.innerHTML;
  //^^Alternatively loop through dom (elem.childNodes or elem.getElementsByTagName) here
  
  elem.innerHTML = savedcontent;
  
  inputBox.html(pasteddata);

  processInput();
}

function processInput () {
  // For HTML, just display the innerHTML
  outputBox.text(inputBox.html());
  
  // For MediaWiki, take what's in the input box and send it to the conversion script
  mediawikiBox.text('Processing...');

  jQuery.post( conversionScriptURL, { 
      html: inputBox.html(),
      date_translate: jQuery('#option_date_translate').is(':checked')
    } )
    .done(function( data ) {
      populateMediawikiBox( data.mediawiki );
  });
}

function populateMediawikiBox( markup ) {
  mediawikiBox.text(markup);
  mediawikiBox.height('0px');
  mediawikiBox.height(mediawikiBox.prop('scrollHeight'));
}

function clearBoxes() {
  inputBox.html('');
  outputBox.html('');
  mediawikiBox.html('');
}
