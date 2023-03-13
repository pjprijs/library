<?php
$currentStyle = "sandstone";
$navbarStyle = "navbar-light bg-light";
if(isset($_COOKIE['selectedStyle'])) $currentStyle = $_COOKIE['selectedStyle'];
if(isset($_COOKIE['navbarStyle'])) $navbarStyle = $_COOKIE['navbarStyle'];
?>
<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Bibliotheek Gouwzee</title>
    <link rel="apple-touch-icon" sizes="180x180" href="images/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon/favicon-16x16.png">
    <link rel="manifest" href="images/favicon/site.webmanifest">
    <meta name="viewport" content="width=device-width">
    <!--<link href="libs/bootstrap-5.1.3-dist/css/bootstrap.min.css" rel="stylesheet">-->
    <link href="libs/bootstrap-5.1.3-dist/themes/<?php echo $currentStyle; ?>.min.css" rel="stylesheet">
    <link href="libs/select2/select2.min.css" rel="stylesheet" />

    <script type='text/javascript' src="libs/jquery/jquery-3.6.0.min.js"></script>
    <script type='text/javascript' src="libs/bootstrap-5.1.3-dist/js/bootstrap.bundle.min.js"></script>
    <script type='text/javascript' src='libs/qrcode/qrcode.min.js'></script>
    <script type='text/javascript' src="libs/sortable/Sortable.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-sortablejs@latest/jquery-sortable.js"></script>
    <script type='text/javascript' src="libs/select2/select2.full.min.js"></script>
    <script type='text/javascript' src="libs/select2/i18n/nl.js"></script>
    <script type='text/javascript' src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js" integrity="sha512-6PM0qYu5KExuNcKt5bURAoT6KCThUmHRewN3zUFNaoI6Di7XJPTMoT6K0nsagZKk2OB4L7E3q1uQKHNHd4stIQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script type='text/javascript' src='scripts/init.js?rnd=<?php echo rand();?>'></script>
    <script type='text/javascript' src='scripts/initDefault.js?rnd=<?php echo rand();?>'></script>
    <script type='text/javascript' src='scripts/scanner.js?rnd=<?php echo rand();?>'></script>
    <script type='text/javascript' src='scripts/modal.js?rnd=<?php echo rand();?>'></script>
    <script type='text/javascript' src='scripts/contentvalues.js?rnd=<?php echo rand();?>'></script>
    <script type='text/javascript' src='scripts/menu.js?rnd=<?php echo rand();?>'></script>
    <script type='text/javascript' src='scripts/loan.js?rnd=<?php echo rand();?>'></script>
    <script type='text/javascript' src='scripts/books.js?rnd=<?php echo rand();?>'></script>
    <script type='text/javascript' src='scripts/users.js?rnd=<?php echo rand();?>'></script>
    <script type='text/javascript' src='scripts/reports.js?rnd=<?php echo rand();?>'></script>
    <script type='text/javascript' src='scripts/config.js?rnd=<?php echo rand();?>'></script>

    <link rel=stylesheet type='text/css' href='css/main.css?rnd=<?php echo rand();?>' title='style'/>
    <link rel=stylesheet type='text/css' href='css/books.css' title='style'/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
  </head>
  <body>
    <nav class="navbar navbar-expand-lg <?php echo $navbarStyle; ?>"><!--  navbar-light bg-light -->
      <div class="container-fluid">
        <a class="navbar-brand d-none d-sm-block" href="javascript://;"></a>
        <a class="navbar-brand d-block d-sm-none" href="javascript://;" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="nav navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item debug">
              <a class="nav-link menu-button menu-button-test" href="#" onClick="showTest();" data-bs-toggle="collapse" data-bs-target=".navbar-collapse.show">Test</a>
            </li>
            <li class="nav-item">
              <a class="nav-link menu-button menu-button-loan" href="#" onClick="showLoan();" data-bs-toggle="collapse" data-bs-target=".navbar-collapse.show">Loan</a>
            </li>
            <li class="nav-item">
              <a class="nav-link menu-button menu-button-books" href="#" onClick="showBooks();" data-bs-toggle="collapse" data-bs-target=".navbar-collapse.show">Books</a>
            </li>
            <li class="nav-item">
              <a class="nav-link menu-button menu-button-users" href="#" onClick="showUsers();" data-bs-toggle="collapse" data-bs-target=".navbar-collapse.show">Users</a>
            </li>
            <li class="nav-item">
              <a class="nav-link menu-button menu-button-reports" href="#" onClick="showReports();" data-bs-toggle="collapse" data-bs-target=".navbar-collapse.show">Reports</a>
            </li>
            <li class="nav-item debug">
              <a class="nav-link menu-button menu-button-stats" href="#" onClick="showStats();" data-bs-toggle="collapse" data-bs-target=".navbar-collapse.show">Stats</a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link menu-button menu-button-config dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Config</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item txt-read-level" href="#" onClick="showConfig('avi');">avi</a></li>
                <li><a class="dropdown-item txt-group" href="#" onClick="showConfig('group');">group</a></li>
                <li><a class="dropdown-item txt-author" href="#" onClick="showConfig('author');">author</a></li>
                <li><a class="dropdown-item txt-scanner" href="#" onClick="showConfig('scanner');">scanner</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><i class="fas fa-palette"></i> style</li>
                <li><a class="dropdown-item menu-style" href="#" onClick="toggleStyle('sandstone');">standaard</a></li>
                <li><a class="dropdown-item menu-style" href="#" onClick="toggleStyle('minty');">mint</a></li>
                <li><a class="dropdown-item menu-style" href="#" onClick="toggleStyle('spacelab');">blauw</a></li>
                <li><a class="dropdown-item menu-style" href="#" onClick="toggleStyle('sketchy');">getekend</a></li>
                <li><a class="dropdown-item menu-style" href="#" onClick="toggleStyle('solar');">donker</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item menu-debug" href="#" onClick="toggleDebug();">debug is off</a></li>
              </ul>
            </li>
          </ul>
          <i class="fas fa-bug debug hidden"></i>
        </div>
      </div>
    </nav>

    <div class="page-container">
      <div class="container">
          <br/><br/>
          <div class="page page-test">
            <div class="row">
              <div class="col-sm-3">
                <div class="card mb-3">
                  <div class="card-header">
                    <span class="title">QR Pieter Jan</span>
                  </div>
                  <div class="card-body padded">
                    <div id="qrcode1"></div>
                    <script type="text/javascript">
                      var qrcode = new QRCode(document.getElementById("qrcode1"), {
                        text: "student:1:Pieter Jan",
                        width: 128,
                        height: 128,
                        colorDark : "#5868bf",
                        colorLight : "#ffffff",
                        correctLevel : QRCode.CorrectLevel.H
                      });
                    </script>
                  </div>
                </div><!-- /card -->
              </div><!-- /col-sm-3 -->
            
              <div class="col-sm-3">
                <div class="card mb-3">
                  <div class="card-header">
                    <span class="title">QR Leerling</span>
                  </div>
                  <div class="card-body padded">
                    <div id="qrcode2"></div>
                    <script type="text/javascript">
                      var qrcode = new QRCode(document.getElementById("qrcode2"), {
                        text: "student:3:YYY",
                        width: 128,
                        height: 128,
                        colorDark : "#CCAA00",
                        colorLight : "#ffffff",
                        correctLevel : QRCode.CorrectLevel.H
                      });
                    </script>
                  </div>
                </div><!-- /card -->
              </div><!-- /col-sm-3 -->

              <div class="col-sm-3">
                <div class="card mb-3">
                  <div class="card-header">
                    <span class="title">QR Leerling</span>
                  </div>
                  <div class="card-body padded">
                    <div id="qrcode3"></div>
                    <script type="text/javascript">
                      var qrcode = new QRCode(document.getElementById("qrcode3"), {
                        text: "student:6:XXX",
                        width: 128,
                        height: 128,
                        colorDark : "#FFAA00",
                        colorLight : "#ffffff",
                        correctLevel : QRCode.CorrectLevel.H
                      });
                    </script>
                  </div>
                </div><!-- /card -->
              </div><!-- /col-sm-3 -->

              <div class="col-sm-3">
                <div class="card mb-3">
                  <div class="card-header">
                    <span class="title">QR Leerling Onbekend</span>
                  </div>
                  <div class="card-body padded">
                    <div id="qrcode33"></div>
                    <script type="text/javascript">
                      var qrcode = new QRCode(document.getElementById("qrcode33"), {
                        text: "student:666:John Doe",
                        width: 128,
                        height: 128,
                        colorDark : "#AA0000",
                        colorLight : "#ffffff",
                        correctLevel : QRCode.CorrectLevel.H
                      });
                    </script>
                  </div>
                </div><!-- /card -->
              </div><!-- /col-sm-3 -->

            </div><!-- /row -->

            <div class="row">
              <div class="col-sm-3">
                <div class="card mb-3">
                  <div class="card-header">
                    <span class="title">QR Boek</span>
                  </div>
                  <div class="card-body padded">
                    <div id="qrcode4"></div>
                    <script type="text/javascript">
                      var qrcode = new QRCode(document.getElementById("qrcode4"), {
                        text: "9789025846336",
                        width: 128,
                        height: 128,
                        correctLevel : QRCode.CorrectLevel.H
                      });
                    </script>
                  </div>
                </div><!-- /card -->
              </div><!-- /col-sm-3 -->
            
              <div class="col-sm-3">
                <div class="card mb-3">
                  <div class="card-header">
                    <span class="title">QR Boek</span>
                  </div>
                  <div class="card-body padded">
                    <div id="qrcode5"></div>
                    <script type="text/javascript">
                      var qrcode = new QRCode(document.getElementById("qrcode5"), {
                        text: "9789025841744",
                        width: 128,
                        height: 128,
                        correctLevel : QRCode.CorrectLevel.H
                      });
                    </script>
                  </div>
                </div><!-- /card -->
              </div><!-- /col-sm-3 -->

              <div class="col-sm-3">
                <div class="card mb-3">
                  <div class="card-header">
                    <span class="title">QR Boek</span>
                  </div>
                  <div class="card-body padded">
                    <div id="qrcode6"></div>
                    <script type="text/javascript">
                      var qrcode = new QRCode(document.getElementById("qrcode6"), {
                        text: "9789021021041",
                        width: 128,
                        height: 128,
                        correctLevel : QRCode.CorrectLevel.H
                      });
                    </script>
                  </div>
                </div><!-- /card -->
              </div><!-- /col-sm-3 -->
            </div><!-- /row -->            
          </div><!-- / page test -->

          <div class="page page-loan">
            <?php include("html/loan.html"); ?>
          </div><!-- / page loan -->

          <div class="page page-books">
            <?php include("html/books.html"); ?>
          </div><!-- / page books -->

          <div class="page page-users">
            <?php include("html/users.html"); ?>
          </div><!-- / page users -->

          <div class="page page-stats">
            <?php include("html/stats.html"); ?>
          </div><!-- / page stats -->

          <div class="page page-config">
            <?php include("html/config.html"); ?>
          </div><!-- / page config -->

          <div class="page page-reports">
            <?php include("html/reports.html"); ?>
          </div><!-- / page reports -->

          <br/>

      </div>
    </div>

    <div class="msgContainer"></div> <!-- error/info messages -->
    <div id="wait" class="hidden"><img src="images/wait.gif"/></div> <!-- loading spinner -->
    <br/>
    <div class="footer">
      <div class="pull-left">&copy; 2022<?php echo (date("Y") > 2022 ? " - " . date("Y") : ""); ?> - Pieter Jan Prijs</div>
      <div class="footer-info pull-right"></div>
    </div><!-- /footer -->

    <!-- Modal -->
    <div class="modal fade" id="defaultModal" tabindex="-1" aria-labelledby="defaultModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="defaultModalLabel">Titel</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary txt-close" data-bs-dismiss="modal">close</button>
            <button type="button" class="btn btn-primary modal-dialog-button-ok" onClick="modalSubmit();">ok</button>
          </div>
        </div>
      </div>
    </div>
    <!-- /modal -->

  </body>
</html>