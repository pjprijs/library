
$(function() {
    doLoad('init.php', {}, function(success, data){
      if(success) {
        setDebug(debug.debug);
        loadConfigItems();
        loadPage(function(){
          getLoan();        
          getUsers();        
          showLoan();  
        });
        $(document).on('select2:open', () => {
          if (!event.target.multiple) { 
            $('.select2-container--open .select2-search--dropdown .select2-search__field').last()[0].focus() 
          }
        });
      }
    }); 
  });
  
  