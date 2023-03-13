
$(document).on('keydown', function(event){
    $(document).focus();
    if(event.key === 'Enter') {
      if(defaultModalActive && !isSimple) {
        modalSubmit();
      } else {
        //check Student
        let keyBufferString = keyBuffer.join('');
        let startPos = keyBufferString.indexOf('student');
        if(startPos >= 0) {        
          let tmpStudentArray = keyBufferString.substring(startPos).split(':')
          setUserbyId(tmpStudentArray[1], tmpStudentArray[2]);
        } else {
          //check ISBN
          let tmpIsbn = '';
          for(i=keyBuffer.length-1; i>=0; i--) {
            let tmpInt = parseInt(keyBuffer[i]);
            if(!isNaN(tmpInt)){
              tmpIsbn += keyBuffer[i];
              tmpIsbn = tmpIsbn.trim();
              if(tmpIsbn.length >= 13) break;
            } else if(keyBuffer[i] == '-' || keyBuffer[i] == ' '){
            } else {
              break;
            }
          }
          tmpIsbn = tmpIsbn.split('').reverse().join('');
          if(tmpIsbn.length == 13 && tmpIsbn.substring(0,3) == '978') {
            checkIsbnAction(tmpIsbn);
          } else {
            //console.log('tmpIsbn: ' + tmpIsbn);
            //playNotFound();
            //showErrorMsg(phpErrMsg[16]);
            checkIsbnAction(tmpIsbn);
          }
        }
        keyBuffer = new Array();
      }
    } else if(event.key == 'Escape' && !isSimple) {
      if(bookDetailsActive && !defaultModalActive) {
        closeBookDetails();
      }
      if(bookAddManualActive) {
        closeAddBookManually();
      }
    } else {
      if(event.key != 'Shift' && event.key != 'Alt' && event.key != 'Meta' && event.key != 'Tab' && event.key != 'CapsLock') {
        keyBuffer.push(event.key);
        if(keyBuffer.length > 32){
          keyBuffer.shift();
        }
      }
    }
  });
