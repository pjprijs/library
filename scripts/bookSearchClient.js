
/*

020 7401221
020 55 58282

hoofdingang olvg oost
p4



function addNewBook() {
    doLoad('addNewBook.php', {
        isbn: currentBookInfo.isbn,
        title: currentBookInfo.volumeInfo.title,
        subtitle: currentBookInfo.volumeInfo.subtitle,
        authors: currentBookInfo.volumeInfo.authors,
        publishedDate: currentBookInfo.volumeInfo.publishedDate,
        description: currentBookInfo.volumeInfo.description,
        pageCount: currentBookInfo.volumeInfo.pageCount
    }, function(success, data){
        if(success) {
            getISBNCover();
            showSuccessMsg(currentBookInfo.volumeInfo.title + ' ' + txtAddedToCollection + '.');
            getBooks();
        }
    });
}
*/

function getBookInfo(isbn, callback) {
    $.getJSON('https://www.googleapis.com/books/v1/volumes?q=isbn:' + isbn, function(data) {
        if(data.totalItems > 0) {
            currentBookInfo = data.items[0];
            currentBookInfo.isbn = isbn;
            callback();
        } else {
            //geen resultaten
            setModal(txtInfo, txtNoOnlineInformation + '.', '', false)
        }
    });
}

function getBookInfoWC(isbn, callback) {
    //https://www.worldcat.org/search?q=bn%3A9789024514281&qt=results_page
}

function getISBNCover() {
    loaderCount++;
    showWait();
    $.ajax({
        url: 'https://fbn.hostedwise.nl/cgi-bin/momredir.pl?size=300&isbn=' + currentBookInfo.isbn,
        cache:false,
        xhr:function(){
            var xhr = new XMLHttpRequest();
            xhr.responseType= 'blob'
            return xhr;
        },
        success: function(result){
            var data = new FormData();
            data.append('file', result);
            data.append('isbn', currentBookInfo.isbn);
            $.ajax({
                url :  "ajax/saveCover.php",
                type: 'POST',
                data: data,
                contentType: false,
                processData: false,
                success: function(data) {
                    loaderCount--;
                    if(loaderCount == 0) showWait(false);
                },    
                error: function() {
                    loaderCount--;
                    if(loaderCount == 0) showWait(false);
                }
            });
        },
        error: function() {
            loaderCount--;
            if(loaderCount == 0) showWait(false);
            showErrorMsg(txtCoverNotFound + ' ' + currentBookInfo.volumeInfo.title);
        }
    });
}
