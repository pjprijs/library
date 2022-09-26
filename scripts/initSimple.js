
let isSimple = true;
override = 'Simple';
let defaultModalActive = false;



$(function() {
    doLoad('init.php', {}, function(success, data){
      if(success) {
        //BUG FIX CHROMIUM MINIMUM WIDTH = 500px
        //if(window.screen.width == 480) 
        $('.modal-overlay').width(windowWidth);
        setDebug(debug.debug);
        //loadConfigItems();
        loanPageActive = true;
        loadPage(function(){
          //getLoan();        
          //getUsers();        
          //showLoan();  
          $('.page-default').show();
        });
        
      }
    }); 
  });
  
  