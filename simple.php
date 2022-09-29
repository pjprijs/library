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
    <script type='text/javascript' src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js" integrity="sha512-6PM0qYu5KExuNcKt5bURAoT6KCThUmHRewN3zUFNaoI6Di7XJPTMoT6K0nsagZKk2OB4L7E3q1uQKHNHd4stIQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script type='text/javascript' src='scripts/init.js?rnd=<?php echo rand();?>'></script>
    <script type='text/javascript' src='scripts/initSimple.js?rnd=<?php echo rand();?>'></script>
    <script type='text/javascript' src='scripts/scanner.js?rnd=<?php echo rand();?>'></script>
    <script type='text/javascript' src='scripts/contentvalues.js?rnd=<?php echo rand();?>'></script>
    <script type='text/javascript' src='scripts/menu.js?rnd=<?php echo rand();?>'></script>
    <script type='text/javascript' src='scripts/loan.js?rnd=<?php echo rand();?>'></script>
    <script type='text/javascript' src='scripts/books.js?rnd=<?php echo rand();?>'></script>
    <script type='text/javascript' src='scripts/users.js?rnd=<?php echo rand();?>'></script>
    <script type='text/javascript' src='scripts/config.js?rnd=<?php echo rand();?>'></script>

    <script type='text/javascript' src='scripts/overrideSimple.js?rnd=<?php echo rand();?>'></script>

    <link rel=stylesheet type='text/css' href='css/main.css?rnd=<?php echo rand();?>' title='style'/>
    <link rel=stylesheet type='text/css' href='css/books.css' title='style'/>
    <link rel=stylesheet type='text/css' href='css/simple.css' title='style'/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
  </head>
  <body>

    <div class="page-container">
      <div class="container">
          <br/><br/>
          <div class="page page-default">
            <div class="loan-display-user title-simple"></div>    
            
            <div class="table-responsive loan-user-books hidden">
              <table class="table table-striped table-hover">
                  <thead>
                      <tr>
                          <th style="min-width:250px" class="txt-title">title</th>
                          <th style="min-width:30px" class="txt-lent">lent</th>
                      </tr>
                  </thead>
                  <tbody class="loan-user-books-table"></tbody>
              </table>        
            </div>

          </div><!-- / page test -->

          <br/>

      </div>
    </div>

    <div class="modal-overlay hidden">
      <br/>
      <div class="modal-body text-center">Boek titel naam hier te staan uitlenen?</div><br/>
      <center><table width="90%"><tr>
        <td width="45%">
          <div class="d-grid gap-2">
            <button type="button" onclick="defaultModalExitFunction(); $('.modal-overlay').hide();" class="btn btn-danger btn-lg btn-block px-0 py-3">
              <span style="font-size: 2em">Nee</span>
            </button>
          </div>
        </td><td></td><td width="45%">
          <div class="d-grid gap-2">
            <button type="button" onclick="defaultModalFunction(defaultModalData); $('.modal-overlay').hide();" class="btn btn-success btn-lg btn-block px-0 py-3">
              <span style="font-size: 2em">Ja</span>
            </button>
          </div>
        </td>
      </tr></table></center>
    </div>
    <div class="msgContainer"></div> <!-- error/info messages -->
    <div id="wait" class="hidden"><img src="images/wait.gif"/></div> <!-- loading spinner -->
    <br/>
    <div class="footer">
      <div class="pull-left">&copy; 2022<?php echo (date("Y") > 2022 ? " - " . date("Y") : ""); ?> - Running at: <?php echo shell_exec("/sbin/ifconfig wlan0 | grep 'inet ' | cut -f2 | awk '{print $2}'") ?></div>
      <div class="footer-info pull-right"></div>
    </div><!-- /footer -->

  </body>
</html>