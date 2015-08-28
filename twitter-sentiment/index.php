<?php
require_once 'twitteroauth.php';
require_once 'alchemy.php';

define('CONSUMER_KEY', '');
define('CONSUMER_SECRET', '');
define('ACCESS_TOKEN', '');
define('ACCESS_TOKEN_SECRET', '');

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Twitter Sentiment</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	</head>
	
	<body>
      <nav class="navbar navbar-inverse navbar-fixed-top">
         <div class="container">
            <div class="navbar-header"><a class="navbar-brand" href="#">Twitter Sentiment</a></div>
         </div>
      </nav>
      
      <div class="jumbotron">
         <div class="container">
            <h2>Welcome to Twitter Sentiment!</h2>
         </div>
      </div>
      
      <div class="container">
         <p>This application looks for mentions on Twitter containing the term informed. 
            The the application captures the sentiment of the tweet consulting Alchemy API.</p>
         <p><strong>Alchemy API allows only 1000 requests per day</strong>.
            Also it's <strong>required</strong> you have your own 
            <a href="https://apps.twitter.com/"><strong>Twitter app</strong></a>
            with <strong><em>Consumer Key</em></strong>, <strong><em>Consumer Secret</em></strong>, 
            <strong><em>Access Token</em></strong> and <strong><em>Acess Token Secret</em></strong>.
            All this information should be inputed <strong><em>in the code</em></strong>.</p>
            <p><span class="label label-danger">Important: </span> AlchemyAPI is not very accurate 
            regardless the language.</p>
      </div>

      
      <?php
      // handle posted data - this application does not use MVC
      $data = array_merge($_GET, $_POST);
      if ($data['sent'] == md5('sentForm') && isset($data['inputTerm']) && isset($data['inputLang'])) 
      {
         $twitter = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
         $query = array( "q" => "#{$data['inputTerm']}",
                         "count" => 10,
                         "lang" => $data['inputLang']);
 
         $results = $twitter->get('search/tweets', $query);
         ?>
         <div class="container">
            <h3>Sentiment Analysis for term <em><?=$data['inputTerm']?></em> (retrieving last 10 tweets):</h3>
            <?php
            if (count($results)) {
               foreach ($results->statuses as $result) {
                  $tweet = "https://twitter.com/{$result->user->screen_name}/status/{$result->id_str}";
                  $alchemtAPI = new AlchemyAPI();
                  $sentiment = $alchemtAPI->analyse($tweet);
                  $alert = ($sentiment == 'positive' ? 'success' : ($sentiment == 'negative' ? 'danger' : 'warning'));
                  ?>
                  <div class="alert alert-<?=$alert?>" role="alert">
                     <strong><?=$sentiment?></strong> <?=$result->text?>
                  </div>
                  <?php
                  sleep(1);
               }
            } else {
               ?>
               <div class="alert alert-danger" role="alert"><strong>Term Not found!</strong></div>
               <?php
            }
            ?>
            </div><hr/>
            <?php
      }
      ?>
      
      <div class="container">
         <form class="form-signin" method="get">
            <h2 class="form-signin-heading">Enter one or more terms</h2>
            <label for="inputEmail" class="sr-only">Term(s)</label>
            <input type="text" id="inputTerm" name="inputTerm" class="form-control" placeholder="Term(s)" required />
            <div class="checkbox"></div>
            <select id="inputLang" name="inputLang" class="form-control">
               <option value="en">English</option>
               <option value="pt">Portuguese</option>
            </select>
            <div class="checkbox"></div>
            <button class="btn btn-lg btn-primary btn-block" type="submit">Capture Sentiment</button>
            <input type="hidden" name="sent" value="<?=md5('sentForm')?>"/>
         </form>
      </div>

      <hr/>
      <footer><p> - By Felipe Morcelli</p></footer>
    </div>
	</body>
</html>
