<!DOCTYPE html>
<html lang="en">
  <head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="Rich text paste to Mediawiki markup">
    <meta name="author" content="Tony Landa">
	<meta name="robots" content="noindex, nofollow" />
    <link rel="icon" href="/favicon.ico">

    <title>Rich text paste to Mediawiki markup</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/jumbotron.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Landa Enterprises</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
        </div><!--/.navbar-collapse -->
      </div>
    </nav>

    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron">
      <div class="container">
        <h1>Paste something</h1>
        <p>Hit paste, and we'll handle the rest</p>
        <!--
        <p><a class="btn btn-primary btn-lg" href="#" role="button">Learn more &raquo;</a></p>
        -->
      </div>
    </div>

    <div class="container">
      <div class="row">

        <div class="col-md-4">
          <h2>Input:</h2>
          <div style="min-height:32px"><input type="checkbox" name="date_translate" id="option_date_translate" /> Translate dates to YYYY-MM-DD</div>
          <div id="paste-input" contenteditable="true" style="border: solid 1px #000;min-height: 32px; margin-bottom: 10px;" onpaste="handlePaste(this, event)"></div>
          <p>
            <a class="btn btn-default" href="" role="button" onclick="event.preventDefault();clearBoxes()">Clear</a>
            <a class="btn btn-default" href="" role="button" onclick="event.preventDefault();processInput()">Again</a>
          </p>
        </div>

        <div class="col-md-4">
          <h2>HTML Output:</h2>
          <div id="paste-output" style="border: solid 1px #ccc;min-height: 32px; margin-bottom: 10px"></div>
          <p><a class="btn btn-default" href="" role="button" onclick="clearBoxes()">Clear</a></p>
        </div>

        <div class="col-md-4">
          <h2>Mediawiki:</h2>
          <textarea id="paste-mediawiki" style="border: solid 1px #ccc;min-height: 32px; margin-bottom: 10px; width: 100%; height: 300px;"></textarea>
          <p><a class="btn btn-default" href="" role="button" onclick="clearBoxes()">Clear</a></p>
        </div>

      </div>

      <hr>

      <footer>
        <p>&copy; Landa Enterprises 2016</p>
        <p><a href="/debug/error-log/">Error log</a></p>
      </footer>
    </div> <!-- /container -->

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <!-- script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script -->
    <script src="js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
    <script src="js/paste.js"></script>
  </body>
</html>
