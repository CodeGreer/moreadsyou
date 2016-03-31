<!doctype html>
<html class="no-js" lang="en">
<head>
	<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Author Page</title>
    
    
    
    
<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="css/reset.css" />
    <link rel="stylesheet" type="text/css" href="css/mo_style.css" />
    <link rel="stylesheet" type="text/css" href="css/mo_responsive.css" />


<!-- Google Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Judson%7CRaleway' rel='stylesheet' type='text/css'>
    
<!-- Javascript -->  
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="js/modernizr.custom.js"></script>
    
</head>
  
<body class="site"> 
    
<!-- start my code -->
    
<!-- Borders -->     
<div id="left"></div>
<div id="right"></div>
    
<!-- Container -->     
<div id="container">
        
<!-- Mobile Header -->
    <div id="mobile_header_text" class="push">
        <h1 class="title">Mo Reads You</h1>
            <h1 class="description">Editing &#38; Proofreading Services</h1>
        <h3 class="menu_link_mobile"><a href="#menu" class="hamburger"> &#9776;</a></h3>
    </div> 
    
    <div id="mobile_header_two" class="push">
        <h1 class="tagline">Helping You Be a Better Writer</h1>  
    </div>
<!-- End Mobile Header -->     
        
<!-- Header -->   
    <div id="header" class="push" >  
        
<!-- Menu -->  
        <h3 class="menu_link">Go To <a href="#menu" class="hamburger"> &#9776;</a></h3>

        <nav id="menu" class="panel">
            <ul class="menu_links">
                <li><a href="mo_index.php">Home</a></li>
                <li><a href="#">Authors</a></li>
                <li><a href="bloggers.php">Bloggers</a></li>
                <li><a href="business.php">Business</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
<!-- End Menu -->               
        <img class="tree" alt="Tree branch with green leaves." src="images/tree.jpg">
        
        <div id="title_div">
            <h1 class="title">Mo Reads You</h1>
            <h1 class="description">Editing &#38; Proofreading Services</h1>
        </div> 
        
        <div id="tagline_div">
            <h1 class="tagline">Helping You Be a Better Writer</h1>   
        </div> 
        
        <div id="profile_image">
            <img alt="Picture of Monique" src="images/mo.jpg">
        </div>
        
        <div id="page_header_text">
            <p class="first_line purple_italic">It’s not just your Manuscript.</p>
            <p class="second_line purple_italic">It’s your Reader’s Bliss.</p>
        </div>
     </div>     
<!-- End Header -->   
                    
<!-- Main -->     
    <div id="content">
        <img alt="A woman sitting on a bed in a hotel room using her laptop" class="content_image" src="images/hotel.jpg">
        
        <p>You’ve mastered your story. You’ve developed deep, rich, compelling characters. Created the perfect setting. Crafted your plot to perfection.</p>
  
        
<!-- Contact Button Form PHP -->       
<?php
// OPTIONS - PLEASE CONFIGURE THESE BEFORE USE!

$yourEmail = "tjed.greer@gmail.com"; // the email address you wish to receive these mails through
$yourWebsite = "Mo Reads You"; // the name of your website
$thanksPage = ''; // URL to 'thanks for sending mail' page; leave empty to keep message on the same page 
$maxPoints = 4; // max points a person can hit before it refuses to submit - recommend 4
$requiredFields = "name,email,comments"; // names of the fields you'd like to be required as a minimum, separate each field with a comma


// DO NOT EDIT BELOW HERE
$error_msg = array();
$result = null;

$requiredFields = explode(",", $requiredFields);

function clean($data) {
	$data = trim(stripslashes(strip_tags($data)));
	return $data;
}
function isBot() {
	$bots = array("Indy", "Blaiz", "Java", "libwww-perl", "Python", "OutfoxBot", "User-Agent", "PycURL", "AlphaServer", "T8Abot", "Syntryx", "WinHttp", "WebBandit", "nicebot", "Teoma", "alexa", "froogle", "inktomi", "looksmart", "URL_Spider_SQL", "Firefly", "NationalDirectory", "Ask Jeeves", "TECNOSEEK", "InfoSeek", "WebFindBot", "girafabot", "crawler", "www.galaxy.com", "Googlebot", "Scooter", "Slurp", "appie", "FAST", "WebBug", "Spade", "ZyBorg", "rabaz");

	foreach ($bots as $bot)
		if (stripos($_SERVER['HTTP_USER_AGENT'], $bot) !== false)
			return true;

	if (empty($_SERVER['HTTP_USER_AGENT']) || $_SERVER['HTTP_USER_AGENT'] == " ")
		return true;
	
	return false;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	if (isBot() !== false)
		$error_msg[] = "No bots please! UA reported as: ".$_SERVER['HTTP_USER_AGENT'];
		
	// lets check a few things - not enough to trigger an error on their own, but worth assigning a spam score.. 
	// score quickly adds up therefore allowing genuine users with 'accidental' score through but cutting out real spam :)
	$points = (int)0;
	
	$badwords = array("adult", "beastial", "bestial", "blowjob", "clit", "cum", "cunilingus", "cunillingus", "cunnilingus", "cunt", "ejaculate", "fag", "felatio", "fellatio", "fuck", "fuk", "fuks", "gangbang", "gangbanged", "gangbangs", "hotsex", "hardcode", "jism", "jiz", "orgasim", "orgasims", "orgasm", "orgasms", "phonesex", "phuk", "phuq", "pussies", "pussy", "spunk", "xxx", "viagra", "phentermine", "tramadol", "adipex", "advai", "alprazolam", "ambien", "ambian", "amoxicillin", "antivert", "blackjack", "backgammon", "texas", "holdem", "poker", "carisoprodol", "ciara", "ciprofloxacin", "debt", "dating", "porn", "link=", "voyeur", "content-type", "bcc:", "cc:", "document.cookie", "onclick", "onload", "javascript");

	foreach ($badwords as $word)
		if (
			strpos(strtolower($_POST['comments']), $word) !== false || 
			strpos(strtolower($_POST['name']), $word) !== false
		)
			$points += 2;
	
	if (strpos($_POST['comments'], "http://") !== false || strpos($_POST['comments'], "www.") !== false)
		$points += 2;
	if (isset($_POST['nojs']))
		$points += 1;
	if (preg_match("/(<.*>)/i", $_POST['comments']))
		$points += 2;
	if (strlen($_POST['name']) < 3)
		$points += 1;
	if (strlen($_POST['comments']) < 15 || strlen($_POST['comments'] > 1500))
		$points += 2;
	if (preg_match("/[bcdfghjklmnpqrstvwxyz]{7,}/i", $_POST['comments']))
		$points += 1;
	// end score assignments

	foreach($requiredFields as $field) {
		trim($_POST[$field]);
		
		if (!isset($_POST[$field]) || empty($_POST[$field]) && array_pop($error_msg) != "Please fill in all the required fields and submit again.\r\n")
			$error_msg[] = "Please fill in all the required fields and submit again.";
	}

	if (!empty($_POST['name']) && !preg_match("/^[a-zA-Z-'\s]*$/", stripslashes($_POST['name'])))
		$error_msg[] = "The name field must not contain special characters.\r\n";
	if (!empty($_POST['email']) && !preg_match('/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i', strtolower($_POST['email'])))
		$error_msg[] = "That is not a valid e-mail address.\r\n";
	if (!empty($_POST['url']) && !preg_match('/^(http|https):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?\/?/i', $_POST['url']))
		$error_msg[] = "Invalid website url.\r\n";
	
	if ($error_msg == NULL && $points <= $maxPoints) {
		$subject = "MRY Contact Button Email";
		
		$message = "You received this e-mail message through your website: \n\n";
		foreach ($_POST as $key => $val) {
			if (is_array($val)) {
				foreach ($val as $subval) {
					$message .= ucwords($key) . ": " . clean($subval) . "\r\n";
				}
			} else {
				$message .= ucwords($key) . ": " . clean($val) . "\r\n";
			}
		}
		$message .= "\r\n";
		$message .= 'IP: '.$_SERVER['REMOTE_ADDR']."\r\n";
		$message .= 'Browser: '.$_SERVER['HTTP_USER_AGENT']."\r\n";
		$message .= 'Points: '.$points;

		if (strstr($_SERVER['SERVER_SOFTWARE'], "Win")) {
			$headers   = "From: $yourEmail\r\n";
		} else {
			$headers   = "From: $yourWebsite <$yourEmail>\r\n";	
		}
		$headers  .= "Reply-To: {$_POST['email']}\r\n";

		if (mail($yourEmail,$subject,$message,$headers)) {
			if (!empty($thanksPage)) {
				header("Location: $thanksPage");
				exit;
			} else {
				$result = '<div id="success">Your message was successfully sent.</div>';
				$disable = true;
			}
		} else {
			$error_msg[] = '<div id="fail">Your message could not be sent this time.</div>';
		}
	} else {
		if (empty($error_msg))
			$error_msg[] = '<div id="fail">Your message looks too<br> much like spam, and could<br> not be sent at this time.</div>';
	}
}
function get_data($var) {
	if (isset($_POST[$var]))
		echo htmlspecialchars($_POST[$var]);
}
?>

<!--
	Free PHP Mail Form v2.4.4 - Secure single-page PHP mail form for your website
	Copyright (c) Jem Turner 2007-2014
	http://jemsmailform.com/

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	To read the GNU General Public License, see http://www.gnu.org/licenses/.
-->

<?php
if (!empty($error_msg)) {
	echo '<p class="error">ERROR: '. implode("<br />", $error_msg) . "</p>";
}
if ($result != NULL) {
	echo '<p class="success">'. $result . "</p>";
}
?>
<!-- End PHP -->  
        
<!-- Contact Button -->        
        <div id="contact_popup" class="popup_button popup_button_modal popup_button_modal3 popup_button_fixed">
            <button type="button" class="button button_contact">Let's get started!</button> 
            <div class="popup_content">
				<div>
				    <div>
				        <h3 class="close icon_close">Close</h3>
				        <h2 class="popup_title">Do you want a title?</h2>
				        <form action="<?php echo basename(__FILE__); ?>" method="post">
                            <label for="name">Your name:</label> 
		                    <input type="text" name="name" id="name" value="<?php get_data("name"); ?>" required/>
				            <label for="email">Your email:</label> 
		                    <input type="text" name="email" id="email" value="<?php get_data("email"); ?>" required />
	                        <label for="comments">What can I do for you?</label><br>
		                    <textarea name="comments" id="comments"><?php get_data("comments"); ?></textarea>  
                            <input type="submit" name="submit" id="submit" class="button button_submit" value="Send" <?php if (isset($disable) && $disable === true) echo ' disabled="disabled"'; ?> />   
				        </form>
				    </div>
				</div>
            </div>
        </div>
<!-- End Contact Button -->
        
        <p>You’ve written. And rewritten. And edited. And proofread. And rewritten. And re-edited. And re-proofread. And had your mom, your best friend, and your cat read it.</p>
        
        <p>It’s perfect!</p>
        
        <p>Now what?</p>
        
        <p>Now you send it to me. <span class="purple_italic">I’ll help you make it even better.</span></p>

        <p>Because, let’s face it, you’ve been staring at this thing for a really long time. Reading it over and over and over. You’ve memorized entire passages, and rewritten others so many times you’re not sure which version you kept. You’re pretty sure moving those four scenes around didn’t mess with your timeline. And your cat ran it through spellcheck at least three times.</p> 
        
        <p>But . . .</p>

        <p>You need another set of eyes. You need someone to read your masterpiece <span class="purple_italic">like your reader will.</span></p>

        <p>And, that’s what I do. I read your manuscript with <span class="purple_italic">the eyes of your reader</span>, and I tell you what your reader thinks of it. Before your reader gets ahold of it. Before you get that first bad review on Amazon.</p>

        <p>And, I do all the other stuff you expect an editor to do. Like checking grammar and spelling. And making sure your main character’s hair doesn’t inexplicably turn from red to blonde in chapter three, and that it didn’t take four days to drive ten miles.</p>

        <p>Because readers notice these things. And it bugs them. <span class="purple_italic">And you don’t want to bug your readers, do you?</span></p>

        <p>But, you’re nervous. You’ve slaved over this book, and you’ve agonized over how to tell your story in your own voice. And you don’t want some <span class="maroon_italic">Evil Editor</span> with a <span class="maroon_italic">Big Fat Red Sharpie</span> slashing away at your manuscript.</p>
        
        <!-- <div id="download">
            <button type="button" class="button button_download">Downloadable</button> 
            <button type="button" class="button button_download">Downloadable</button> 
        </div> -->

        <p>Don’t worry. I won’t do that. I don’t even own a red Sharpie. I’ll make suggestions. I’ll point out possible improvements. I’ll highlight the things that are great. And, yes, I’ll fix spelling and grammar and do all of that other stuff that makes writing more readable, while keeping it distinctly “you.”</p>
        
        <p>So reading your book is a pleasure for your reader. Because that’s what you want, right? For your book to be <span class="purple_italic">Your Reader’s Bliss.</span></p>

        <p>So, <a class="contact" href="contact.php">get in touch with me</a>, tell me about your project, and let’s talk about it.</p>

        <p>Chat soon – Mo</p>

        <p>P.S. If you want an idea what it will cost, my rates are on my <a class="contact" href="contact.php">Contact page</a>.</p>
    </div>   
<!-- End Main --> 
</div>
<!-- End Container -->    
    
<div id="push"></div>
 
        
<!-- Footer -->      
<div id="footer">
     <ul class="social">
        <li><a href="https://twitter.com/MoReadsYou"><img alt="Twitter" src="images/twitter.jpg"></a></li>
        <li><a href=""><img alt="Facebook" src="images/facebook.jpg"></a></li>
        <li><a href="https://www.linkedin.com/in/moniquehuenergardt"><img alt="LinkedIn" src="images/linkedin.jpg"></a></li>
     </ul>
    
    <ul class="credits">
        <li>Site Coded by <a class="contact" href="http://codegreer.com/">Code Greer</a></li>
        <li><a class="contact" href="contact.php">Full Site Credits</a></li>
    </ul>
    <ul class="copyright">
        <li>&#169; Monique Huenergardt</li>
    </ul>   
</div>  
<!-- End Footer -->  
    
<!-- Javascript --> 
    <script src="js/bigslide.js"></script>
    <script src="js/classie.js"></script>
    <script src="js/uiMorphingButton_fixed.js"></script>
    <script>
        (function() {
            var docElem = window.document.documentElement, didScroll, scrollPosition;

            // trick to prevent scrolling when opening/closing button
            function noScrollFn() {
				window.scrollTo( scrollPosition ? scrollPosition.x : 0, scrollPosition ? scrollPosition.y : 0 );
            }

            function noScroll() {
				window.removeEventListener( 'scroll', scrollHandler );
				window.addEventListener( 'scroll', noScrollFn );
            }

            function scrollFn() {
				window.addEventListener( 'scroll', scrollHandler );
            }

            function canScroll() {
				window.removeEventListener( 'scroll', noScrollFn );
				scrollFn();
            }

            function scrollHandler() {
				if( !didScroll ) {
				    didScroll = true;
				    setTimeout( function() { scrollPage(); }, 60 );
				}
            };

            function scrollPage() {
				scrollPosition = { x : window.pageXOffset || docElem.scrollLeft, y : window.pageYOffset || docElem.scrollTop };
				didScroll = false;
            };

            scrollFn();

            [].slice.call( document.querySelectorAll( '.popup_button' ) ).forEach( function( bttn ) {
				new UIMorphingButton( bttn, {
				    closeEl : '.icon_close',
				    onBeforeOpen : function() {
				        // don't allow to scroll
                        noScroll();
				},
				    onAfterOpen : function() {
				        // can scroll again
				        canScroll();
				},
				    onBeforeClose : function() {
				        // don't allow to scroll
				        noScroll();
				},
				    onAfterClose : function() {
				        // can scroll again
				        canScroll();
				}
                } );
            } );

            // the selector is generic when no linkable button present
            [].slice.call( document.querySelectorAll( 'form button' ) ).forEach( function( bttn ) { 
				bttn.addEventListener( 'click', function( ev ) { ev.preventDefault(); } );
            } );
        })();
    </script>
    
<!-- Big Slide -->
    <script>
        $(document).ready(function() {
        $('.menu_link, .menu_link_mobile').bigSlide();
        });
    </script>           
<!-- End Javascript -->          
    
</body> 
</html>