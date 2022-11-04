//https://isbnsearch.org/isbn/0553293354
let currentBookInfo = {};
let currentBookStartPage = 0;
let currentHistoryBookStartPage = 0;
let bookSearchValue = null;
let bookDetailsActive = false;
let bookAddManualActive = false;
let currentSelectedBook = null;
let currentBookDetailsClose = undefined;
let currentSearchBookFunction = null;

function addBookManually() {
    bookAddManualActive = true;
    $('.book-add-new').show();
    $('.book-add-new-search').show();
    $('.book-add-new-manual').hide();
    $('.book-search').hide();
    $('.book-overview').hide();    
}

function closeAddBookManually() {
    bookAddManualActive = false;
    $('.book-add-new').hide();
    $('.book-search').show();
    $('.book-overview').show();
    $('.book-add-new-manual').hide();
    $('.book-isbn').val('');
    $('.book-title-input').val('');
    $('.book-subtitle-input').val('');
    $('.book-author-input').val('');
    $('.book-avi-input').val('');
}

function updateBookAmount(data) {
    if(data.amount == 0) {
        doLoad('updateBookAmount.php', {
            book: data.bookId
        }, function(success, data){
            if(success) {
                showSuccessMsg(data.title + ' ' + txtAddedToCollection + '.');
                getBooks();
            }
        });
    } else {
        let tmpText = txtCopy;
        if(data.amount > 1) tmpText = txtCopies;
        defaultModalData = {
            bookId: data.bookId
        }
        defaultModalFunction = function(data){
            doLoad('updateBookAmount.php', {
                book: data.bookId
            }, function(success, data){
                if(success) {
                    getBooks();
                }
            });
        };
        setModal(data.title, data.title + ' - ' + data.amount + ' ' + tmpText + ' ' + txtFound + '. ' + txtAddCopy + '?', 'Toevoegen', true)
    }
}

function manualFindNewBook() {    
    let isbn = $('.book-isbn').val();
    getIsbnBookId(isbn, function(data){
        if(data.bookId > 0) {
            updateBookAmount(data);
        } else {
            console.log("test");
            findNewBook(isbn, function(data2){
                console.log('a');
                if(Number.isInteger(data2.bookId)) {
                    showSuccessMsg(data2.title + ' ' + txtAddedToCollection + '.');
                    closeAddBookManually();
                    getBookDetails(data2.bookId);
                } else {
                    showAddBookManually();
                }
            });        
        }
    }); 
}

function manualAddNewBook() {
    let isbn = $('.book-isbn').val().trim();
    let title = $('.book-title-input').val().trim();
    let author = $('.book-author-input').val();
    if(isbn == '' || title == '' || author == '') {
        showErrorMsg(txtErrFieldsRequired + ': ' + txtTitle + '/' + txtAuthor + '/isbn');
    } else {
        let data = {
            isbn: isbn,
            title: title,
            subtitle: $('.book-subtitle-input').val().trim(),
            authors: author,
            avi: $('.book-avi-input').val(),
            manual: 1
        }
        doLoad('addNewBook.php', {
            data: data
        }, function(success, data){
            if(success) {
                showSuccessMsg(data.title + ' ' + txtAddedToCollection + '.');
                closeAddBookManually();
                getBookDetails(data.bookId);
            }
        });
    }

}

function showAddBookManually() {
    $('.book-add-new-search').hide();
    $('.book-add-new-manual').show();
    $('.book-author-input').select2({
        tags: true,
        tokenSeparators: [',', ';'],
        language: langCode,
        placeholder: txtFindAuthor,
        minimumInputLength: 2,
        ajax: {
            url: 'ajax/getAuthors.php',
            dataType: 'jsonp',
            delay: searchDelay,
            processResults: function (data) {
                return {
                    results: data.data.results
                };
            },
            data: function (params) {
                var query = {
                    search: params.term,
                    limit: 10,
                    page: params.page || 1
                }
                return query;
            }
        }
    }).on('select2:select', function (e) {
        var data = e.params.data;
        selectedUserId = data.id
    }).on('select2:open', function (e) {
        const evt = "scroll.select2";
        $(e.target).parents().off(evt);
        $(window).off(evt);
    });    
    $('.book-avi-input').html('');
    for(i in aviArray) {
        $('.book-avi-input').append($('<option value="' + aviArray[i].id + '">').append(aviArray[i].name));
    }
    showInfoMsg(txtNoOnlineInformation);
}

function findNewBook(isbn, callback) {
    doLoad('findNewBook.php', {
        isbn: isbn
    }, function(success, data){
        //if(success) {
            callback(data);
        //}
    });
}

function bookOverviewNext() {
    currentBookStartPage += maxItemsPerPage;
    $('.book-overview-next').blur();
    getBooks();
}

function bookOverviewPrevious() {
    currentBookStartPage -= maxItemsPerPage;
    if(currentBookStartPage < 0) currentBookStartPage = 0;
    $('.book-overview-previous').blur();
    getBooks();
}

function searchBook() {
    clearTimeout(currentSearchBookFunction);
    currentSearchBookFunction = setTimeout(function(){
        currentBookStartPage = 0;
        bookSearchValue = $('.book-search-input').val();
        getBooks();    
    }, searchDelay);
}

function getBooks() {
    let searchValue = bookSearchValue;
    if(searchValue != null) {
        if(searchValue.trim().length < 3) {
            searchValue = null;
        } else {
            $('.book-search-input-clear').removeClass('disabled');
        }
    }
    doLoad('getBooks.php', {
        search: searchValue,
        page: (currentBookStartPage/maxItemsPerPage)+1,
        limit: maxItemsPerPage
    }, function(success, data){
        if(success) {
            makeBookOverview(data);            
        }
    }, 1);
}

function emptyBookSearch() {
    currentBookStartPage = 0;
    bookSearchValue = '';
    $('.book-search-input').val('');
    $('.book-search-input-clear').addClass('disabled');
    $('.book-search-input-clear').blur();
    getBooks();
}

function formatAuthorList(authorArray) {
    let authorList = '';
    for(j=0; j<authorArray.length; j++) {
        if(authorList != '') authorList += '<br/>';
        authorList += authorArray[j].displayName;
    }
    return authorList;
}

function formatSerieList(serieArray) {
    let serieList = '';
    for(j=0; j<serieArray.length; j++) {
        if(serieList != '') serieList += '<br/>';
        serieList += serieArray[j].name;
        if(serieArray[j].number != 0) {
            serieList += ' - ' + txtPart + ' ' + serieArray[j].number;
        }
    }
    return serieList;
}

function makeBookOverview(allData) {
    data = allData.results;
    $('.books-list').html('');
    let pages = Math.ceil(allData.amount/maxItemsPerPage);
    if(allData.amount <= maxItemsPerPage) {
        $('.book-overview-buttons').hide();
        $('.book-overview-header').hide();
    } else {
        $('.book-overview-buttons').show();
        $('.book-overview-header').show();
        $('.book-overview-header').html(txtPage + ' ' + ((currentBookStartPage/maxItemsPerPage)+1) + ' ' + txtOf + ' ' + pages + ' (' + allData.amount + ' ' + txtBooks + ')');
        $('.book-overview-previous').addClass('disabled');
        $('.book-overview-next').addClass('disabled');
        if(currentBookStartPage > 0) {
            $('.book-overview-previous').removeClass('disabled');
        }
        if(allData.amount > (currentBookStartPage+maxItemsPerPage)) {
            $('.book-overview-next').removeClass('disabled');
        }
    }        

    for(i=0; i<data.length; i++) {
        let authorList = formatAuthorList(data[i].author);
        let serie = formatSerieList(data[i].serie);
        if(serie != '') serie = '<br/><span class="book-overview-serie">' + serie + '</span>';
        let subtitle = '';
        if(data[i].subtitle.trim() != '') subtitle = ' - ' + data[i].subtitle;
        $('.books-list').append(
            $('<tr>').append(
                $('<td>').append(
                    $('<span class="me-2">').append(
                        $('<input class="form-check-input book-overview-checkbox book-overview-checkbox-' + data[i].id + ' hidden" type="checkbox" onchange="checkBookOverviewRadio(' + data[i].id + ');" value="' + data[i].id + '"/>')
                    )
                ).append(
                    $('<span>').append(
                        $('<input class="form-check-input book-overview-radio book-overview-radio-' + data[i].id + ' hidden" type="radio" name="book-overview-radio" value="' + data[i].id + '"/>')
                    )
                )
            ).append(
                $('<td class="clickable" onclick="getBookDetails(' + data[i].id + ')">').append(
                    $('<span class="book-title-' + data[i].id + '">').append(data[i].title + subtitle + serie)
                )
            ).append(
                $('<td class="clickable" onclick="getBookDetails(' + data[i].id + ')">' + authorList + '</td>')
            ).append(
                $('<td>' + data[i].avi + '</td>')
            ).append(
                $('<td class="text-center">' + data[i].loaned + ' van ' + data[i].amount + '</td>')
            ).append(
                $('<td class="text-center"><i class="fas fa-pen clickable" onclick="editBook(' + data[i].id + ')" data-toggle="tooltip" title="' + txtEdit + '"></i></td>')
            ).append(
                $('<td class="text-center">' + (data[i].amount > 0 ? '<i class="fas fa-trash-can clickable" onclick="delBook(' + data[i].id + ', \'' + data[i].title + subtitle + '\', ' + data[i].loaned + ')" data-toggle="tooltip" title="' + txtDelete + '"></i>' : '') + '</td>')
        ));
        $('[data-toggle="tooltip"]').tooltip();
    }
}

function checkBookOverviewRadio(id) {
    if($('.book-overview-checkbox-' + id).prop('checked') == true) {
        $('.book-overview-radio-' + id).show();
        if($('.book-overview-radio:checked').val() === undefined) $('.book-overview-radio-' + id).prop('checked', true);
    } else {
        $('.book-overview-radio-' + id).hide();
        $('.book-overview-radio-' + id).prop('checked', false);
    }
}

function showCombineBooks(show) {    
    if(show) {
        $('.book-overview-checkbox').show();
        $('.book-overview-combine').hide();
        $('.book-overview-merge').show();
        $('.book-overview-combine-cancel').show();
    } else {
        $('.book-overview-checkbox').hide();
        $('.book-overview-radio').hide();
        $('.book-overview-combine').show();
        $('.book-overview-merge').hide();
        $('.book-overview-combine-cancel').hide();
    }
}

function mergeBooks() {
    defaultModalFunction = function(){
        let amountSelected = 0;
        let combineBookArray = new Array();
        $('.book-overview-checkbox:checked').each(function(){
            combineBookArray[combineBookArray.length] = $(this).val();
            amountSelected++;
        });
        if(amountSelected > 1) {
            if($('.book-overview-radio:checked').val() !== undefined) {
                doLoad('setCombineBook.php', {
                    combineBookArray: combineBookArray,
                    mainBookId: $('.book-overview-radio:checked').val()
                }, function(success, data){
                    if(success) {
                        getBooks();
                        showCombineBooks(false);
                    }
                });
            } else {
                showErrorMsg('Kies het boek waaronder de boeken samengevoegd moeten worden (rondje selecteren).');
            }
        } else {
            showErrorMsg('Kies 2 of meer boeken om samen te voegen (vinkje selecteren).');
        }            
    };
    //book-title-
    let bookList = '';
    $('.book-overview-checkbox:checked').each(function(){
        if(bookList != '') bookList += '<br/>';
        bookList += $('.book-title-' + $(this).val()).html();
    });
    setModal(txtMerge, 'Weet u zeker dat u deze boeken wilt samenvoegen tot <b>' + $('.book-title-' + $('.book-overview-radio:checked').val()).html() + '</b>?<br/><br/>' + bookList, txtMerge, true)
}

function delBook(bookId, bookTitle, loaned) {
    if(loaned > 0) {
        showErrorMsg(txtErrDelBooksLoaned);
    } else {
        defaultModalData = {
            bookId: bookId,
            bookTitle: bookTitle
        }
        defaultModalFunction = function(data){
            doLoad('delBook.php', {
                book: data.bookId,
                bookTitle: data.bookTitle
            }, function(success, data){
                if(success) {
                    getBooks();
                }
            });
        };
        setModal(txtDelete, '<b>' + bookTitle + '</b><br/>' + txtConfirmDelete, txtDelete, true)
    }
}

function editBook(bookId) {
    showInfoMsg('Work in progress :)');
}

function checkIsbnAction(isbn) {
    getIsbnBookId(isbn, function(data){
        if(data.bookId > 0) {
            //Book is in database
            if(data.amount == 0) {
                //update Amount
                playFound();                    
                updateBookAmount(data);
            } else {
                if(loanPageActive && selectedUserId !== null) {
                    checkLoanBookAction(data.bookId);
                } else if(bookPageActive ) {
                    playFound();                    
                    $('.book-search-input').blur();
                    $('.book-search-input').val(isbn);
                    searchBook();
                    getIsbnBookDetails(isbn);
                }
            }
        } else {
            //Book is not in database
            playNotFound();
            defaultModalData = {
                isbn: data.isbn
            }
            defaultModalFunction = function(data){
                findNewBook(data.isbn, function(data2){
                    if(Number.isInteger(data2.bookId)) {                        
                        showSuccessMsg(data2.title + ' ' + txtAddedToCollection + '.');
                        showBooks();
                        getBookDetails(data2.bookId);
                    } else {
                        //not found online
                        addBookManually();
                        showAddBookManually();
                    }    
                });
            };
            setModal(txtBook, txtNotInSystem, txtAdd, true)
        }
    });
}

function getIsbnBookId(isbn, callback) {
    doLoad('getIsbnBookId.php', {
        isbn: isbn
    }, function(success, data){
        if(success) {
            callback(data);
        }
    });
}

function getIsbnBookDetails(isbn) {
    getIsbnBookId(isbn, function(data){
        if(data.bookId > 0) {
            getBookDetails(data.bookId);
        }    
    });
}

function getBookDetails(bookId, closeFunction) {
    currentSelectedBook = bookId;
    currentBookDetailsClose = closeFunction;
    bookDetailsActive = true;
    $('.book-search').hide();
    $('.book-overview').hide();
    getBookHistory();
    doLoad('getBookDetails.php', {
        bookId: currentSelectedBook
    }, function(success, data){
        if(success) {
            showBookDetails(data);            
        }
    });    
}

function showBookDetails(data) {
    let isbn = '';
    let isbnImage = 'images/isbn/unknown.jpg';
    for(i=0; i<data.isbn.length; i++) {
        checkIsbnImage(data.id, data.isbn[i].isbn);
        if(isbn != '') isbn += '<br/>';
        isbn += '<a tabindex="0" class="book-details-isbn-link book-details-isbn-' + data.isbn[i].isbn + '">' + data.isbn[i].isbn + '</a>';
    }
    $('.book-details-title').html(data.title);
    $('.book-details-subtitle').html(data.subtitle);
    $('.book-details-description').html(data.description);
    let authorList = formatAuthorList(data.author);
    $('.book-details-author').html(authorList);
    let serieList = formatSerieList(data.serie);
    $('.book-details-serie').html('<b>' + serieList + '</b>');
    $('.book-details-isbn').html(isbn);
    $('.book-details-read-level').html(
        $('<span class="book-avi-label">').html(data.avi).append(' <i class="fas fa-pen clickable" onclick="bookEditAvi(true)" data-toggle="tooltip" title="' + txtEdit + '"></i>')
    ).append('<select class="form-select book-avi-edit hidden" onchange="bookUpdateAvi();">');
    $('.book-details-publication').html(data.published_date);
    $('.book-details-pages').html(data.pagecount);
    $('.book-details-image').html($('<img class="book-details-image-' + data.id + '" src="' + isbnImage + '" style="width: 150px;"/>'));  //   onerror="if(this.src != \'images/isbn/unknown.jpg\') this.src = \'images/isbn/unknown.jpg\';"
    $('.book-details').show();
    for(i in aviArray) {
        $('.book-avi-edit').append($('<option value="' + aviArray[i].id + '"' + (data.aviId == aviArray[i].id ? ' selected="selected"' : '') + '>').append(aviArray[i].name));
    }
}

function showBookDetailsTags() {
    $('.book-details-tags').select2({
        tags: true,
        tokenSeparators: [',', ';'],
        language: langCode,
        placeholder: txtFindAuthor,
        minimumInputLength: 2,
        ajax: {
            url: 'ajax/getAuthors.php',
            dataType: 'jsonp',
            delay: searchDelay,
            processResults: function (data) {
                return {
                    results: data.data.results
                };
            },
            data: function (params) {
                var query = {
                    search: params.term,
                    limit: 10,
                    page: params.page || 1
                }
                return query;
            }
        }
    }).on('select2:select', function (e) {
        var data = e.params.data;
        selectedUserId = data.id
    }).on('select2:open', function (e) {
        const evt = "scroll.select2";
        $(e.target).parents().off(evt);
        $(window).off(evt);
    });        
}

function bookEditAvi(show) {
    if(show) {
        $('.book-avi-label').hide();
        $('.book-avi-edit').show();
    } else {
        $('.book-avi-label').show();
        $('.book-avi-edit').hide();
    }
}

function bookUpdateAvi() {
    doLoad('setBookAvi.php', {
        bookId: currentSelectedBook,
        avi: $('.book-avi-edit option:selected').val()
    }, function(success, data){
        if(success) {            
            $('.book-avi-label').html($('.book-avi-edit option:selected').text()).append(' <i class="fas fa-pen clickable" onclick="bookEditAvi(true)" data-toggle="tooltip" title="' + txtEdit + '"></i>');
            bookEditAvi(false);
        }
    });
}

function closeBookDetails() {
    currentSelectedBook = null;
    bookDetailsActive = false;
    $('.book-details').hide();
    $('.book-search').show();
    $('.book-overview').show();
    if(currentBookDetailsClose !== undefined) currentBookDetailsClose();
}

function getBookHistory() {
    doLoad('getBookHistory.php', {
        bookId: currentSelectedBook,
        page: (currentHistoryBookStartPage/maxItemsPerPage)+1,
        limit: maxItemsPerPage
    }, function(success, data){
        if(success) {
            showBookHistory(data);            
        }
    });    
}

function showBookHistory(allData) {
    let data = allData.results;
    $('.books-details-history').html('');
    let pages = Math.ceil(allData.amount/maxItemsPerPage);
    if(allData.amount <= maxItemsPerPage) {
        $('.book-history-buttons').hide();
        $('.book-history-header').hide();
    } else {
        $('.book-history-buttons').show();
        $('.book-history-header').show();
        $('.book-history-header').html('<b>' + txtHistory + '</b> - ' + txtPage + ' ' + ((currentHistoryBookStartPage/maxItemsPerPage)+1) + ' ' + txtOf + ' ' + pages + ' (' + allData.amount + 'x ' + txtLent + ')');
        $('.book-history-previous').addClass('disabled');
        $('.book-history-next').addClass('disabled');
        if(currentHistoryBookStartPage > 0) {
            $('.book-history-previous').removeClass('disabled');
        }
        if(allData.amount > (currentHistoryBookStartPage+maxItemsPerPage)) {
            $('.book-history-next').removeClass('disabled');
        }
    }        
    if(data.length > 0) {
        for(i=0; i<data.length; i++) {
            let returnButton = missingButton = '';
            let endDate = data[i].end_date;
            if(data[i].enddate == '1111-11-11 11:11:11') endDate = txtMissing;
            if(data[i].enddate == '0000-00-00 00:00:00') {
                endDate = '-';
                returnButton = $('<button type="button" class="btn btn-outline-secondary" onclick="returnLoanedBook(' + data[i].id + ', \'' + $('.book-details-title').html() + '\', function(){ getBookHistory(); });">' + txtReturn + '</button>')
                missingButton = $('<button type="button" class="btn btn-outline-danger" onclick="missingLoanedBook(' + data[i].id + ', \'' + $('.book-details-title').html() + '\', function(){ getBookHistory(); });">' + txtMissing + '</button>');
            }
            $('.books-details-history').append($('<tr>').append(
                $('<td>').append($('<span>' + data[i].fullname + '</span>'))
            ).append(
                $('<td>').append($('<span>' + data[i].start_date + '</span>'))
            ).append(
                $('<td>').append($('<span>' + endDate + '</span>'))
            ).append(
                $('<td>').append(returnButton)
            ).append(
                $('<td>').append(missingButton)
            ));
        }
    }
}

function bookHistoryNext() {
    currentHistoryBookStartPage += maxItemsPerPage;
    $('.book-history-next').blur();
    getBookHistory();
}

function bookHistoryPrevious() {
    currentHistoryBookStartPage -= maxItemsPerPage;
    if(currentHistoryBookStartPage < 0) currentHistoryBookStartPage = 0;
    $('.book-history-previous').blur();
    getBookHistory();    
}

function checkIsbnImage(bookId, isbnId) {
    // book-details-isbn-XXX
    var img = new Image();
    var imageSrc = 'images/isbn/i' + isbnId + '.jpg'
    img.onload = function(){ 
        $('.book-details-image-' + bookId).attr('src', imageSrc);
        var popover = new bootstrap.Popover($('.book-details-isbn-' + isbnId), {
            content: '<img width="150" src="' + imageSrc + '" style="width: 150px"/>',
            trigger: 'focus',
            html: true
        });
        $('.book-details-isbn-' + isbnId).addClass('clickable');
    };
    img.onerror = function(){};
    img.src = imageSrc;
}