//main
let langCode = 'nl';
let navbarIcon = '<i class="fas fa-book"></i>&nbsp;';
let txtBrand = 'Gouwzee Bieb';

//menu
let menuButtonLoan = 'Uitlenen';
let menuButtonBooks = 'Boeken';
let menuButtonUsers = 'Leerlingen';
let menuButtonStats = 'Test Stats';
let menuButtonConfig = 'Configuratie';

//general items
let txtSave = 'opslaan';
let txtCancel = 'annuleren';
let txtClose = 'sluiten';
let txtCheck = 'controleer';
let txtHistory = 'geschiedenis';
let txtSearch = 'zoeken';
let txtClear = 'legen';
let txtPrevious = 'vorige';
let txtNext = 'volgende';
let txtPage = 'pagina';
let txtPages = 'pagina\'s';
let txtOf = 'van';
let txtInfo = 'info';
let txtInfoLong = 'informatie';
let txtWarning = 'waarschuwing';
let txtError = 'fout';
let txtSuccess = 'ok';
let txtDelete = 'verwijderen';
let txtCopy = 'exemplaar';
let txtCopies = 'exemplaren';
let txtFound = 'gevonden';
let txtAdd = 'toevoegen';
let txtEdit = 'bewerken';
let txtMerge = 'samenvoegen'
let txtNotFound = 'niet gevonden';
let txtConfig = 'configuratie';
let txtErrFieldsRequired = 'de volgende velden zijn verplicht'

//loan
let txtFindStudent = 'zoek een leerling';
let txtFindBook = 'zoek een boek';
let txtFindAuthor = 'zoek een auteur';
let txtReturn = 'inleveren';
let txtReturned = 'ingeleverd';
let txtLoan = 'uitlenen';
let txtLent = 'uitgeleend';
let txtMissing = 'vermist';
let txtAllCopiesLent = 'Alle exemplaren zijn al uitgeleend';
let txtMissingLoanedBook = 'Wilt u dit boek als vermist opgeven?<br/>Er wordt dan 1 versie verwijderd uit de database.';

//books
let txtTitle = 'titel';
let txtSubtitle = 'bijtitel'
let txtAuthor = 'auteur';
let txtBook = 'boek';
let txtBooks = 'boeken';
let txtDays = 'dagen';
let txtPart = 'deel';
let txtReadLevel = 'AVI';
let txtAmount = 'aantal';
let txtPublication = 'uitgave';
let txtNoOnlineInformation = 'Geen informatie over dit boek gevonden online';
let txtAddedToCollection = 'is toegevoegd aan de collectie';
let txtAddCopy = 'Wilt u een exemplaar toevoegen';
let txtErrDelBooksLoaned = 'Er zijn nog boeken uitgeleeend.<br/>Alle boeken moeten ingenomen zijn voor het boek verwijderd kan worden.';
let txtConfirmDelete = 'Weet u zeker dat u dit wilt verwijderen?'
let txtNotInSystem = 'Dit boek staat nog niet in het systeem. Wilt u deze toevoegen?';
let txtCoverNotFound = 'Omslag niet gevonden voor';
let txtDecrease = 'verlagen';
let txtIncrease = 'verhogen';
let txtNotNegative = 'aantal kan niet negatief zijn';

//users
let txtUser = 'leerling';
let txtUsers = 'leerlingen';
let txtName = 'naam';
let txtFirstname = 'voornaam';
let txtLastname = 'achternaam';
let txtPrefix = 'tussenvoegsel';
let txtGroup = 'groep';
let txtAllGroups = 'Alle groepen';

//php err msg
let phpErrMsg = new Array();
phpErrMsg[1] = 'PHP sessie niet gevonden';
phpErrMsg[5] = 'Probleem met ophalen van gegevens';
phpErrMsg[6] = 'Probleem met opslaan van gegevens';
phpErrMsg[11] = 'Dit boek is al uitgeleend aan deze leerling';
phpErrMsg[12] = 'Fout bij het innemen van het boek';
phpErrMsg[13] = 'Verwijderen van boek mislukt';
phpErrMsg[14] = 'Bijwerken van aantal boeken mislukt';
phpErrMsg[15] = 'Bijwerken van vermist boek mislukt';
phpErrMsg[16] = 'Geen geldige QR- of streepjescode';


$(function() {
    // SET VALUES
    $('.navbar-brand').html(navbarIcon + txtBrand);
    $('.menu-button-loan').html(menuButtonLoan);
    $('.menu-button-books').html(menuButtonBooks);
    $('.menu-button-users').html(menuButtonUsers);
    $('.menu-button-stats').html(menuButtonStats);
    $('.menu-button-config').html(menuButtonConfig);

    $('.txt-user').html(txtUser);
    $('.txt-cancel').html(txtCancel);
    $('.txt-close').html(txtClose);
    $('.txt-check').html(txtCheck);
    $('.txt-clear').html(txtClear);
    $('.txt-info').html(txtInfo);
    $('.txt-info-long').html(txtInfoLong);
    $('.txt-config').html(txtConfig);
    $('.txt-add').html(txtAdd);

    $('.txt-title').html(txtTitle);
    $('.txt-subtitle').html(txtSubtitle);
    $('.txt-pages').html(txtPages);
    $('.txt-author').html(txtAuthor);
    $('.txt-history').html(txtHistory);
    $('.txt-previous').html(txtPrevious);
    $('.txt-next').html(txtNext);
    $('.txt-merge').html(txtMerge);
    $('.txt-book').html(txtBook);
    $('.txt-books').html(txtBooks);
    $('.txt-days').html(txtDays);
    $('.txt-publication').html(txtPublication);

    $('.txt-loan').html(txtLoan);
    $('.txt-lent').html(txtLent);
    $('.txt-returned').html(txtReturned);
    $('.txt-return').html(txtReturn);
    $('.txt-missing').html(txtMissing);
    $('.txt-all-copies-lent').html(txtAllCopiesLent);

    $('.txt-search').html(txtSearch);
    $('.book-search-input').attr('placeholder', txtTitle + '/' + txtAuthor + '/isbn...');
    $('.txt-read-level').html(txtReadLevel);
    $('.txt-amount').html(txtAmount);
    $('.txt-add-book').html(txtBook + ' ' + txtAdd);
    $('.txt-add-read-level').html(txtReadLevel + ' ' + txtAdd);

    $('.user-search-input').attr('placeholder', txtFirstname + '/' + txtLastname + '...');
    $('.txt-name').html(txtName);
    $('.txt-group').html(txtGroup);
});
