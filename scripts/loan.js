let selectedUserId = null;
let selectedBookId = null;
let loanedBooksArray = new Array();
let currentOpenLoanStartPage = 0;
let currentHistoryLoanStartPage = 0;

function setUserbyId(id, name) {
    doLoad('getUserById.php', {
        userId: id
    }, function(success, data){
        if(success) {
            if(data != null) {
                var audio = new Audio('audio/found.wav');
                audio.play();
                $('.loan-select-user-div').hide();
                $('.loan-display-user-div').show();
                $('.loan-display-user').html(data.fullname + '<span class="user-details"> (' + txtGroup + ' ' + (data.groupname != '' ? data.groupname + ' - ' + data.schoolyear : data.schoolyear) + ')</span>');
                showLoan();
                selectedUserId = data.id
                showNewLoanBook();
                getLoanedBooks();
            } else {
                var audio = new Audio('audio/notfound.wav');
                audio.play();
                showErrorMsg(txtUser + ' ' + name + ' ' + txtNotFound);
            }
        }
    });    
}

function formatGetUsers(usersArray) {
    for(i=0; i<usersArray.length; i++) {
        usersArray[i].text = usersArray[i].fullname + ' (' + txtGroup + ' ' + (usersArray[i].groupname != '' ? usersArray[i].groupname + ' - ' + usersArray[i].schoolyear : usersArray[i].schoolyear) + ')';
    }
    return usersArray;
}
function formatGetBooks(booksArray) {
    let returnArray = new Array();
    for(i=0; i<booksArray.length; i++) {
        returnArray[i] = new Object();
        returnArray[i].id = booksArray[i].id
        returnArray[i].text = booksArray[i].fulltitle;
    }
    return returnArray;
}

function getLoan() {    
    $('.loan-select-user').select2({
        language: langCode,
        placeholder: txtFindStudent,
        minimumInputLength: 2,
        ajax: {
            url: 'ajax/getUsers.php',
            dataType: 'jsonp',
            delay: searchDelay,
            processResults: function (data) {
                return {
                    results: formatGetUsers(data.data.results),
                    pagination: data.data.pagination
                };
            },
            data: function (params) {
                var query = {
                    search: params.term,
                    limit: 10,
                    orderField: 'u.surname',
                    orderDir: 0,
                    page: params.page || 1
                }
                return query;
            }
        }
    }).on('select2:select', function (e) {
        var data = e.params.data;
        selectedUserId = data.id
        showNewLoanBook();
        getLoanedBooks();
    }).on('select2:open', function (e) {
        const evt = "scroll.select2";
        $(e.target).parents().off(evt);
        $(window).off(evt);
    });

    $('.loan-find-book-select').select2({
        language: langCode,
        placeholder: txtFindBook,
        minimumInputLength: 2,
        ajax: {
            url: 'ajax/getBooks.php',
            dataType: 'jsonp',
            delay: searchDelay,
            processResults: function (data) {
                return {
                    results: formatGetBooks(data.data.results),
                    pagination: data.data.pagination
                };
            },
            data: function (params) {
                var query = {
                    search: params.term,
                    page: params.page || 1,
                    limit: 10
                }
                return query;
            }
        }
    }).on('select2:select', function (e) {
        var data = e.params.data;
        selectedBookId = data.id
        loanCheckAvailableBook();        
    });;
}

function showNewLoanUser() {
    selectedUserId = null;
    $('.loan-select-user-div').show();
    $('.loan-display-user-div').hide();
    $('.loan-display-user').html('');
    $('.loan-select-user').val(null).trigger('change');
    $('.loan-find-book').hide();
    $('.loan-user-books').hide();        
    $('.loan-user-books-history').hide();        
}

function showNewLoanBook() {
    $('.loan-find-book').show();
    selectedBookId = null;
    loanedBooksArray = new Array();
    $('.loan-find-book-select').val(null).trigger('change');
    $('.loan-find-book-button').hide();
    $('.loan-no-book').hide();
}


function getLoanedBooks() {
    getLoanedBooksOpen();
    getLoanedBooksHistory();
}

function getLoanedBooksOpen() {
    doLoad('getLoanedBooks.php', {
        userId: selectedUserId,
        open: true,
        page: (currentOpenLoanStartPage/maxItemsPerPage)+1,
        limit: maxItemsPerPage
    }, function(success, data){
        if(success) {
            showLoanedBooks(data);
        }
    });
}

function getLoanedBooksHistory() {
    doLoad('getLoanedBooks.php', {
        userId: selectedUserId,
        open: false,
        page: (currentHistoryLoanStartPage/maxItemsPerPage)+1,
        limit: maxItemsPerPage
    }, function(success, data){
        if(success) {
            showLoanedBooksHistory(data);
        }
    });
}

function showLoanedBooks(allData) {
    let data = allData.results;
    let pages = Math.ceil(allData.amount/maxItemsPerPage);
    if(allData.amount <= maxItemsPerPage) {
        $('.loan-open-buttons').hide();
        $('.loan-open-title').hide();
    } else {
        $('.loan-open-buttons').show();
        $('.loan-open-title').show();
        $('.loan-open-title').html(txtPage + ' ' + ((currentOpenLoanStartPage/maxItemsPerPage)+1) + ' ' + txtOf + ' ' + pages + ' (' + allData.amount + ' ' + txtBooks + ')');
        $('.loan-open-previous').addClass('disabled');
        $('.loan-open-next').addClass('disabled');
        if(currentOpenLoanStartPage > 0) {
            $('.loan-open-previous').removeClass('disabled');
        }
        if(allData.amount > (currentOpenLoanStartPage+maxItemsPerPage)) {
            $('.loan-open-next').removeClass('disabled');
        }
    }        
    $('.loan-user-books-table').html('');
    loanedBooksArray = new Array();
    let isbnImage = 'images/isbn/unknown.jpg';
    if(data.length > 0) {
        $('.loan-user-books').show();        
        for(i=0; i<data.length; i++) {
            loanedBooksArray[i] = parseInt(data[i].id);
            $('.loan-user-books-table').append($('<tr>').append(
                $('<td>').append(
                    $('<img class="loan-book-image-' + data[i].id + '" src="' + isbnImage + '" align="left" width="75"/>')
                )
            ).append(
                $('<td class="loan-user-books-id-' + data[i].id + ' clickable" startDate="' + data[i].startdate + '" loanId="' + data[i].loan + '" onclick="showBooks(); getBookDetails(' + data[i].id + ', function(){ showLoan(); })">').append($('<span>' + data[i].fulltitle + '</span>'))
            ).append(
                $('<td>').append($('<span>' + data[i].start_date + '</span>'))
            ).append(
                $('<td width="100">').append($('<button type="button" class="btn btn-outline-secondary" onclick="returnLoanedBook(' + data[i].loan + ',' + data[i].id + ', \'' + data[i].title.replaceAll('\'', '\\\'') + '\', function(){ getLoanedBooks(); });">' + txtReturn + '</button>'))
            ).append(
                $('<td width="100">').append($('<button type="button" class="btn btn-outline-danger" onclick="missingLoanedBook(' + data[i].loan + ', \'' + data[i].title.replaceAll('\'', '\\\'') + '\', function(){ getLoanedBooks(); });">' + txtMissing + '</button>'))
            ));
            loadIsbnImage(data[i].isbn, $('.loan-book-image-' + data[i].id), 0); 
        }
    } else {
        $('.loan-user-books').hide();
    }
}

function showLoanedBooksHistory(allData) {
    let data = allData.results;
    let pages = Math.ceil(allData.amount/maxItemsPerPage);
    if(allData.amount <= maxItemsPerPage) {
        $('.loan-history-buttons').hide();
        $('.loan-history-title').hide();
    } else {
        $('.loan-history-buttons').show();
        $('.loan-history-title').show();
        $('.loan-history-title').html(txtPage + ' ' + ((currentHistoryLoanStartPage/maxItemsPerPage)+1) + ' ' + txtOf + ' ' + pages + ' (' + allData.amount + ' ' + txtBooks + ')');
        $('.loan-history-previous').addClass('disabled');
        $('.loan-history-next').addClass('disabled');
        if(currentHistoryLoanStartPage > 0) {
            $('.loan-history-previous').removeClass('disabled');
        }
        if(allData.amount > (currentHistoryLoanStartPage+maxItemsPerPage)) {
            $('.loan-history-next').removeClass('disabled');
        }
    }    
    $('.loan-user-books-history-table').html('');
    if(data.length > 0) {
        $('.loan-user-books-history').show();        
        for(i=0; i<data.length; i++) {
            let endDate = data[i].end_date;
            if(data[i].enddate == '1111-11-11 11:11:11') endDate = txtMissing;
            $('.loan-user-books-history-table').append($('<tr>').append(
                $('<td class="clickable" onclick="showBooks(); getBookDetails(' + data[i].id + ', function(){ showLoan(); })">').append($('<span>' + data[i].fulltitle + '</span>'))
            ).append(
                $('<td>').append($('<span>' + data[i].start_date + '</span>'))
            ).append(
                $('<td>').append($('<span>' + endDate + '</span>'))
            ));
        }
    } else {
        $('.loan-user-books-history').hide();
    }
}

function setLoanedBook(title) {
    doLoad('setLoanedBook.php', {
        userId: selectedUserId,
        bookId: selectedBookId
    }, function(success, data){
        if(success) {
            let tmpTitle = '';
            if(title != undefined) tmpTitle = title + ' ';
            showSuccessMsg(tmpTitle + txtLent + '.');
            showNewLoanBook();
            getLoanedBooksOpen();
        }
    });
}

function returnLoanedBook(loanId, book, title, callback) {
    const d = new Date();
    let day = d.getDate();
    let month = d.getMonth()+1;
    if(day < 10) day = '0' + day;
    if(month < 10) month = '0' + month;
    let datestring = d.getFullYear()  + "-" + month + "-" + day;
    if(datestring == $('.loan-user-books-id-' + book).attr('startDate')) {
        if(!confirm(title + ' ' + txtReturn + '?')) {
            return false;
        }
    }
    //loanId = $('.loan-user-books-id-' + book).attr('loanId');

    doLoad('returnLoanedBook.php', {
        loanId: loanId
    }, function(success, data){
        if(success) {
            showSuccessMsg(title + ' ' + txtReturned + '.');
            callback();
        }
    });
}

function missingLoanedBook(loanId, bookTitle, callback) {
    defaultModalData = {
        loanId: loanId,
        bookTitle: bookTitle
    }
    defaultModalFunction = function(data){
        doLoad('setMissingBook.php', {
            loanId: data.loanId,
            bookTitle: data.bookTitle
        }, function(success, data){
            if(success) {
                callback();
            }
        });
    };
    setModal(txtBook, txtMissingLoanedBook, txtDelete, true)
}

function loanCheckAvailableBook() {
    doLoad('getLoanAvailableBook.php', {
        bookId: selectedBookId
    }, function(success, data){
        if(success) {
            if(data.loaned >= data.total) {
                // no book available
                $('.loan-no-book').show();
                $('.loan-find-book-button').hide();
            } else {
                $('.loan-no-book').hide();
                $('.loan-find-book-button').show();
            }
        }
    });
}

function getBookTitle(bookId, callback) {
    doLoad('getBookTitle.php', {
        bookId: bookId
    }, function(success, data){
        if(success) {
            callback(data);
        }
    });
}

function checkBookAvailable(bookId, callback) {
    doLoad('getLoanAvailableBook.php', {
        bookId: bookId
    }, function(success, data){
        if(success) {
            callback(data);
        }
    });
}
function checkLoanBookAction(bookId) {  
    getBookTitle(bookId, function(data){
        if(loanedBooksArray.includes(parseInt(data.bookId))) {
            //Book is returned
            let loanId = $('.loan-user-books-id-' + book).attr('loanId');
            returnLoanedBook(loanId, data.bookId, data.title, function(){
                playFound();
                getLoanedBooks();
            });    
        } else {
            selectedBookId = data.bookId;
            checkBookAvailable(data.bookId, function(amountData){
                playFound();
                if(amountData.loaned < amountData.total) {
                    setLoanedBook(data.title);
                } else {
                    playNotFound();
                    //No copies available
                    selectedBookId = null;
                    showErrorMsg(data.title + ' - ' + txtAllCopiesLent);
                }
            });
        }
    });  
}

function loadIsbnImage(isbnArray, elem, counter) { 
    if(counter >= isbnArray.length) return;
    let img = new Image();
    let imageSrc = 'images/isbn/i' + isbnArray[counter].isbn + '.jpg';
    img.onload = function(){ 
        elem.attr('src', imageSrc);
    }
    img.onerror = function(){
        let newCounter = counter + 1;
        loadIsbnImage(isbnArray, elem, newCounter);
    };
    img.src = imageSrc;   
}

function loanOpenNext() {
    currentOpenLoanStartPage += maxItemsPerPage;
    $('.loan-open-next').blur();
    getLoanedBooksOpen();
}

function loanOpenPrevious() {
    currentOpenLoanStartPage -= maxItemsPerPage;
    if(currentOpenLoanStartPage < 0) currentOpenLoanStartPage = 0;
    $('.loan-open-previous').blur();
    getLoanedBooksOpen();    
}

function loanHistoryNext() {
    currentHistoryLoanStartPage += maxItemsPerPage;
    $('.loan-history-next').blur();
    getLoanedBooksHistory();
}

function loanHistoryPrevious() {
    currentHistoryLoanStartPage -= maxItemsPerPage;
    if(currentHistoryLoanStartPage < 0) currentHistoryLoanStartPage = 0;
    $('.loan-history-previous').blur();
    getLoanedBooksHistory();    
}
