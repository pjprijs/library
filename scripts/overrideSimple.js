let defaultModalFunction = function(){};
let defaultModalExitFunction = function(){};
let defaultModalData = {};

showLoan = function() {}

showNewLoanBook = function() {}

showLoanedBooks = function (allData) {
    let data = allData.results;
    $('.loan-user-books').show();
    $('.loan-user-books-table').html('');
    loanedBooksArray = new Array();
    if(data.length > 0) {
        $('.loan-user-books').show();        
        for(i=0; i<data.length; i++) {
            loanedBooksArray[i] = parseInt(data[i].id);
            $('.loan-user-books-table').append($('<tr>').append(
                $('<td class="loan-user-books-id-' + data[i].id + '" loanId="' + data[i].loan + '">').append($('<span>' + data[i].title + '</span>'))
            ).append(
                $('<td>').append($('<span>' + data[i].start_date + '</span>'))
            ));
        }
    } else {
        $('.loan-user-books').hide();
    }
}

setModal = function(titleText, bodyText, buttonText, buttonVisible){
    if(buttonVisible === undefined) buttonVisible = true;
    $('.modal-title').html(titleText);
    $('.modal-body').html(bodyText);
    $('.modal-dialog-button-ok').html(buttonText);
    $('.modal-dialog-button-ok').hide();
    if(buttonVisible) $('.modal-dialog-button-ok').show();
    //defaultModal.show();
    //defaultModalActive = true;
    // if ok -> defaultModalFunction
    // if not -> defaultModalExitFunction
    $('.modal-overlay').show();
}

function showBooks() {};
function getBookDetails(a) {};
function addBookManually() {};
function showAddBookManually() {
    //errmsg not available
    showErrorMsg(txtNoOnlineInformation);
};

getLoanedBooksHistory = function(){};