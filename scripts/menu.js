let loanPageActive = false;
let bookPageActive = false;

function showTest() {
  if($('.page-test').is(':visible')) {
  } else {
    resetMenu();
    $('.menu-button-test').addClass('active');
    $('.page-test').show();
  }
}

function showLoan() {
  if($('.page-loan').is(':visible')) {
  } else {
    resetMenu();
    $('.menu-button-loan').addClass('active');
    $('.page-loan').show();
    loanPageActive = true;
  }
}

function showBooks() {
  closeBookDetails();
  closeAddBookManually();
  if($('.page-books').is(':visible')) {
  } else {
    getBooks();
    resetMenu();
    $('.menu-button-books').addClass('active');
    $('.page-books').show();
    bookPageActive = true;
  }
}

function showUsers() {
  if($('.page-users').is(':visible')) {
  } else {
    resetMenu();
    $('.menu-button-users').addClass('active');
    $('.page-users').show();
  }
}

function showReports() {
  if($('.page-reports').is(':visible')) {
  } else {
    resetMenu();
    $('.menu-button-reports').addClass('active');
    initReports();
    $('.page-reports').show();
  }
}

function showStats() {
  if($('.page-stats').is(':visible')) {
  } else {
    resetMenu();
    $('.menu-button-stats').addClass('active');
    $('.page-stats').show();
  }
}

function showConfig(item) {
  $('.page-config-subpage').hide();
  $('.page-config-' + item).show();
  if($('.page-config').is(':visible')) {
  } else {
    resetMenu();
    $('.menu-button-config').addClass('active');
    $('.page-config').show();
  }
}

function resetMenu() {
  $('.page').hide();
  $('.menu-button').removeClass('active');  
  $('.menu-button').blur(); 
  loanPageActive = false;
  bookPageActive = false;
  bookDetailsActive = false;
}
