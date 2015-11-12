<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!--  Lindat styles  -->
  <link rel="stylesheet" href="branding/public/css/lindat.css" type="text/css" />
 
  <!--Google Fonts  -->
  <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Inconsolata" />
  
  <!--jQuery -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

  <!-- Bootstrap   -->
  <link href="dist/css/bootstrap.css" rel="stylesheet" media="screen" type="text/css" />
  <script type="text/javascript" src="dist/js/bootstrap.js"></script>

  <!-- Prettify   -->
  <script src="https://google-code-prettify.googlecode.com/svn/loader/run_prettify.js"></script>




</head>

<body id="lindat-services">

  <?php
  include("branding/header.htm");
  ?>



  <div id="lindat-services" class="lindat-container">

    <?php
	# local variables
	$max_char = 5000;
	$encoded = '';

	# input 
	$text = '';
	
	# output 
	$output_decoded->{"result"} = '';
	$output_decoded->{"input"} = '';

	# URL
	$base_url = 'http://quest.ms.mff.cuni.cz:8280/cesilko' ;
	$translate_api =  $base_url . '/translate?';


	if ((isset($_REQUEST['text'])) && (trim($_REQUEST['text']) != '')) {
	    $text = $_REQUEST['text'];
	    $text = trim($text);	
	    # input text size should not exceed $max_char
	    $input_length = strlen($text);
	    if ( $input_length > $max_char ) {
	      $tmptext = substr($text, 0, $max_char);
	      $text = $tmptext;
	    }
	    $text_iso = iconv('utf-8', 'iso-8859-2//TRANSLIT', $text);
	    #$text_iso = iconv('iso-8859-2', 'utf-8//IGNORE', $text_iso);
	    $encoded = urlencode('data') . '=' . urlencode($text_iso);
	    $ch = curl_init($translate_api);
	    curl_setopt($ch, CURLOPT_POSTFIELDS,  $encoded);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_HTTPHEADER , array('Content-Type: application/x-www-form-urlencoded; charset=iso-8859-2'));
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $output = curl_exec($ch);
	    $output_decoded = json_decode($output);
	    $output_utf8 = iconv("iso-8859-2", "utf-8//TRANSLIT", $output_decoded->{'result'});
	    curl_close($ch);
	}
    ?>

<!--    Each service contains the following
    1. Description
    2. Service Demo
    3. Developer's Guide
-->

    <br/>
    <div id="service_title">
      <h2>Cesilko</h2>
    </div>
    <br/>

            <ul id="serviceTab" class="nav nav-tabs nav-justified">
              <li><a href="#description" data-toggle="tab">Software Information</a></li>
              <li class="active"><a href="#servicedemo" data-toggle="tab">Software Demo</a></li>
              <li><a href="#apidocumentation" data-toggle="tab">Developer's Guide</a></li>
            </ul>

            <div id="serviceTabContent" class="tab-content">
	      <!-- Description -->
              <div class="tab-pane fade" id="description">
		<div class="container">
		<br />

<!--		    <div class="panel panel-primary">
		      <div class="panel-heading">
			<h3 class="panel-title">Cesilko</h3>
		      </div>
		      <div class="panel-body">-->

			<p>Česílko is a tool enabling the fast and efficient translation from one source language into many target languages, which are mutually related. </p>
			
			  <table class='table-' cellpadding="4">
<!--			    <tr>
				<th>Description</th> 
				<th>Value</th>
			    </tr>-->
<!--			    <tr>
				<th>Software Description</th>
				<td>Česílko is a tool enabling the fast and efficient translation from one source language into many target languages, which are mutually related. </td>
			    </tr>-->
			    <tr>
				<th>Authors</th>
				<td>Jan Hajič, Vladislav Kuboň, Petr Homola</td>
			    </tr>

			    <tr>
				<th>Homepage</th>
				<td><a href="http://ufal-point.mff.cuni.cz/services/cesilko/">http://ufal-point.mff.cuni.cz/services/cesilko/</a></td>
			    </tr>

			    <tr>
				<th>Tutorials</th>
				<td><a href="http://ufal-point.mff.cuni.cz/services/cesilko/">Demo</a></td>
			    </tr>

			    <tr>
				<th>Status</th>
				<td>NA</td>
			    </tr>

			    <tr>
				<th>OS</th>
				<td>Linux</td>
			    </tr>

			    <tr>
				<th>License</th>
				<td><a href="http://creativecommons.org/licenses/by-nc-nd/3.0/">Attribution-NonCommercial-NoDerivs 3.0 Unported (CC BY-NC-ND 3.0)</a></td>
			    </tr>
			  </table>
<!--		      </div>
		    </div>-->




		</div>

              </div>

	      <!-- Service Demo -->
              <div class="tab-pane fade in active" id="servicedemo">


			<h2 style="text-align:center">Translation Demo</h2>
<p>
  Welcome to the demonstration of the <span lang="cs">Česílko</span> project which has been developed
on the <a href="http://ufal.mff.cuni.cz">Institute of Formal and Applied Linguistics</a>.</p>

<p>The system <span lang="cs">Česílko</span> was designed as a tool enabling the fast and efficient translation from one source language into many target languages,
which are mutually related. The system receives as its input a high quality human translation of the original into Czech (from any language).
It translates the Czech input into a number of languages related to Czech. The system contains at the moment 5 language pairs, 4 of them only
as experiments, namely Czech into Polish, Lithuanian, Macedonian and Lower Sorbian. Unfortunately, the system cannot be tested on arbitrary
texts for these language pairs due to a small size of all dictionaries. The only working language pair (and at the same time also exploitable
outside of the above mentioned setup) is the fifth one, Czech to Slovak. Similarly to other MT systems, <span lang="cs">Česílko</span>
requires human post-editing. The system is being developed since 1998.</p>

<p>The demo is freely available for testing. An explicit written permission of the authors is required for any commercial exploitation of the system.
If you run the demo, you agree that data obtained during testing can be used for further improvements of the systems at UFAL. All comments and reactions are welcome.</p>

<h2 style="text-align:center">Write Your Text</h2>

		<h3>Czech Input (CZ)</h3>

		<form action="index.php" method="post">
		  <fieldset style="border: 0px">
		    <label>Input text in the Czech language (up to <?php echo
		(string)$max_char; ?> characters, additional will be truncated)</label>
		    <br />
		    <textarea id="text" name="text" lang="cs" rows="4" cols="30" style="width:
		100%" maxlength="<?php echo (string)$max_char; ?>" required><?php
		echo htmlspecialchars($text);
		?></textarea>
		    <br />
		    <button type="submit" class="btn btn-primary">Translate</button>
		    <br />
		  </fieldset>
		</form>


		<h3>Slovak Output (SK)</h3>

<!--		<p style="background-color: #fff6d9; border: #ffedb2 1px solid; padding: 0.4em; " lang="sk">-->
		<p style="background-color: #dddddd; min-height: 100px; border: #AAAAAA 1px solid; padding: 0.4em; " lang="sk">

		    <?php echo htmlspecialchars($output_decoded->{'result'}); ?>
		</p>
		<br />
		<br />
              </div>

	      <!-- Developer's Guide  -->
              <div class="tab-pane fade" id="apidocumentation">

		<br />

		<div class='dotted_border'>
		<ul>
		      <li><a href=#api_introduction>Introduction</a></li>
		      <li><a href="#api_summary">API Reference</a></li>
		      <li><a href="#rest_ex">Complete example: Calling REST via PHP script</a></li>		      
		</ul>
		</div>

		<br />

<!-- 		<h3>Cesilko via REST API</h3> -->

		<h4><a id='api_introduction'>Introduction</a></h4>

		<p>This section serves as a reference to developers who would like to use Cesilko
		translation API in their web applications. The Cesilko API is available via REST.
		The Cesilko REST API can be accessed directly or via any other web programming
		tools that support standard HTTP request methods and JSON for output handling.
		The following sections explain the invocation of Cesilko REST API with examples.</p>

		<p>
		<div class="alert alert-info">At the moment, for all the API requests, the response format is <a href="http://en.wikipedia.org/wiki/JSON">JSON</a>.</div>
		</p>
		

		<h4><a id='api_summary'>API Reference</a></h4>

		<table border='1' class='table table-bordered'>
		<tr>
		    <th>#</th>
		    <th>Service Request</th> 
		    <th>Description</th>
		    <th>HTTP Method</th>
		</tr>
		<tr>
		    <td>1</td>
		    <td><span class="inconsolata_f"><a href="#api_translate">translate</a></span></td>
		    <td>translates text in Czech (CZ) to Slovak (SK)</td>
		    <td>GET</td>
		</tr>
		<tr>
		    <td>2</td>
		    <td><span class="inconsolata_f"><a href="#api_version">version</a></span></td>
		    <td>returns the version number of Cesilko system</td>
		    <td>GET</td>
		</tr>

		<tr>
		    <td>3</td>
		    <td><span class="inconsolata_f"><a href="#api_author">author</a></span></td>
		    <td>returns the author of the Cesilko system</td>
		    <td>GET</td>
		</tr>

		</table>

		<h4><span class="inconsolata_f"><a id='api_translate'>1. translate</a></span></h4>

		<p>The <span class="inconsolata_f">translate</span> is the main operation/service offered 
		by the REST API. This service translates the given text in Czech language into Slovak. The 
		possible parameters for the <span class="inconsolata_f">translate</span> service are described 
		in the following table.</p>

		<table border='1' class='table table-bordered'>
		<tr align="left">
		    <th>#</th>
		    <th>Parameter</th>
		    <th>Mandatory</th>
		    <th>Data type</th>
		    <th>Description</th>
		</tr>
		<tr align="left">
		    <td>1</td>
		    <td><span class="inconsolata_f">data</span></td>
		    <td>yes</td>
		    <td><span class="inconsolata_f">string</span></td>
		    <td>Czech input text in <b>UTF-8</b></td>
		<tr>
		</table>

		<div class="panel panel-success">
		  <div class="panel-heading">
		    <h3 class="panel-title">Example #1:</h3>
		  </div>
		  <div class="panel-body">
Browser Example
<pre class="prettyprint">
http://quest.ms.mff.cuni.cz:8280/cesilko/translate?data={Czech text}
http://quest.ms.mff.cuni.cz:8280/cesilko/translate?data=Já mám hlad
</pre>
<div class="row">
<div class="col-md-1 col-md-offset-11"><button type="button" class="btn btn-success btn-xs" onclick="tryMyService('translate_demo')" align="right">try this</button></div>
</div>
<br />
CURL Example
<pre class="prettyprint">
curl -X POST --data-urlencode "data=Já mám hlad" http://quest.ms.mff.cuni.cz:8280/cesilko/translate
</pre>
		  </div>
		</div>

		<div class="panel panel-success">
		  <div class="panel-heading">
		    <h3 class="panel-title">JSON Response</h3>
		  </div>
		  <div class="panel-body">
<pre class="prettyprint">
{
  "input": "'J\u00e1 m\u00e1m hlad'", 
  "result": " 'Ja m\u00e1m hlad' \n\n"
}
</pre>
		  </div>
		</div>



		<h4><span class="inconsolata_f"><a id="api_version">2. version</a></span></h4>

		<p>This service returns the version number of the Cesilko system</p>

		<div class="panel panel-success">
		  <div class="panel-heading">
		    <h3 class="panel-title">Browser Example</h3>
		  </div>
		  <div class="panel-body">
<pre class="prettyprint">
http://quest.ms.mff.cuni.cz:8280/cesilko/version
</pre>
		      <div class="row">
		      <div class="col-md-1 col-md-offset-11"><button type="button" class="btn btn-success btn-xs" onclick="tryMyService('version_demo')" align="right">try this</button></div>
		      </div>
<br />
CURL Example
<pre class="prettyprint">
curl http://quest.ms.mff.cuni.cz:8280/cesilko/version
</pre>
		  </div>
		</div>

		<div class="panel panel-success">
		  <div class="panel-heading">
		    <h3 class="panel-title">JSON Response</h3>
		  </div>
		  <div class="panel-body">
<pre class="prettyprint">
{
  "version": "v1.0"
}
</pre>
		  </div>
		</div>


		<h4><span class="inconsolata_f"><a id="api_author">3. author</a></span></h4>

		<p>This service returns the author of the Cesilko system</p>


		<div class="panel panel-success">
		  <div class="panel-heading">
		    <h3 class="panel-title">Browser Example</h3>
		  </div>
		  <div class="panel-body">
<pre class="prettyprint">
http://quest.ms.mff.cuni.cz:8280/cesilko/author
</pre>
		      <div class="row">
		      <div class="col-md-1 col-md-offset-11"><button type="button" class="btn btn-success btn-xs" onclick="tryMyService('author_demo')" align="right">try this</button></div>
		      </div>
<br />
CURL Example
<pre class="prettyprint">
curl http://quest.ms.mff.cuni.cz:8280/cesilko/author
</pre>
		  </div>
		</div>

		<div class="panel panel-success">
		  <div class="panel-heading">
		    <h3 class="panel-title">JSON Response</h3>
		  </div>
		  <div class="panel-body">
		  </div>
		</div>



	      </div>
            </div>

  </div>

  <?php
  include("branding/footer.htm");
  ?>
  
  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script src="//code.jquery.com/jquery.js"></script>
  <!-- Include all compiled plugins (below), or include individual files as needed -->
  <script src="dist/js/bootstrap.js"></script>

  <script>
  function tryMyService(demoId) 
  {
      var translate_demo = "http://quest.ms.mff.cuni.cz:8280/cesilko/translate?data=Já mám hlad";
      var version_demo = "http://quest.ms.mff.cuni.cz:8280/cesilko/version";
      var author_demo = "http://quest.ms.mff.cuni.cz:8280/cesilko/author";

      if (demoId == "translate_demo") {
	window.open(translate_demo);
      }
      else if (demoId == "version_demo") {
	// TODO: yet to be implemented
	window.open(version_demo);
      }
  }
  </script>
</body>
</html>

