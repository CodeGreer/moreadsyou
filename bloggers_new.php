<?php
// OPTIONS - PLEASE CONFIGURE THESE BEFORE USE!

$yourEmail = "moreadsyou@gmail.com"; // the email address you wish to receive these mails through
$yourWebsite = "Mo Reads You"; // the name of your website
$thanksPage = 'thanks.html'; // URL to 'thanks for sending mail' page; leave empty to keep message on the same page 
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
		$subject = "MRY Contact Button - Bloggers";
		
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
			$error_msg[] = '<div class="fail">Your message could not be sent this time.</div>';
		}
	} else {
		if (empty($error_msg))
			$error_msg[] = '<div class="fail">Your message looks too<br> much like spam, and could<br> not be sent at this time.</div>';
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

<!doctype html>
<html class="no-js" lang="en">
<head>
	<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Bloggers | Mo Reads You</title>
    
<!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/reset.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <link rel="stylesheet" type="text/css" href="css/responsive.css" />

<!-- Google Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Judson%7CRaleway' rel='stylesheet' type='text/css'>
    
<!-- Javascript -->  
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script type="text/javascript" src="js/simple-expand.js"></script>
    <script src="js/modernizr.custom.js"></script>
    
</head>
  
<body class="site"> 
    
<!-- start my code -->
    
<!-- Borders -->     
<div id="left"></div>
<div id="right"></div>
    
<!-- Mobile Header -->
    <header id="mobile_header_text" class="push">
        <h1 class="title"><a href="index.php">Mo Reads You</a></h1>
            <h1 class="description">Editing &#38; Proofreading Services</h1>
        <h3 class="menu_link_mobile"><a href="#menu" class="hamburger"> &#9776;</a></h3>
    </header> 
    
    <header id="mobile_header_two" class="push">
        <h1 class="tagline">Helping You Be a Better Writer</h1>  
    </header>
<!-- End Mobile Header -->    
        
<!-- Header -->  
    <header id="header" class="push">  
        
<!-- Menu -->  
        <h3 class="menu_link">Go To <a href="#menu" class="hamburger"> &#9776;</a></h3>

        <nav id="menu" class="panel">
            <ul class="menu_links">
                <li><a href="index.php">Home | About</a></li>
                <li><a href="authors.php"><span class="bold_purple">A</span>uthors</a></li><li><a href="business.php"><span class="bold_purple">B</span>usinesses</a></li>
                <li><a href="#"><span class="bold_purple">C</span>ontent Producers</a></li>
                <li><a href="contact.php">Contact | Rates</a></li>
            </ul>
        </nav>
<!-- End Menu -->              
        
        <img class="tree" alt="Tree branch with green leaves." src="images/tree.jpg">
        
        <div id="title_div">
            <h1 class="title"><a href="index.php">Mo Reads You</a></h1>
            <h1 class="description">Editing &#38; Proofreading Services</h1>
        </div> 
        
        <div id="tagline_div">
            <h1 class="tagline">Helping You Be a Better Writer</h1>   
        </div> 
        
        <div id="profile_image">
            <a href="index.php"><img alt="Picture of Monique" src="images/mo.jpg"></a>
        </div>
        
        <div id="page_header_text">
            <p class="first_line purple_italic">Web-based content producers.</p>
            <p class="second_line purple_italic">You know who you are.</p>
        </div>            
    </header>     
<!-- End Header -->   
                    
<!-- Main -->  
    <main>
        <section id="content">
            <p>You’re an ideal person. An information lizard. A mustard of quality content production.</p>

            <p>See what I did there? Even just one wrong letter can skew your meaning.</p>                      

            <p><span class="purple_italic">Errors kill your credibility.</span> They make your reader want to stop reading. Opt out of your newsletter. Or even (gasp) stop visiting your blog.</p>

            <p><span class="purple_italic">You know your stuff.</span> You know the importance of your message to your audience. And sometimes in the flush of creativity, in the rush of getting just the perfect combination of words onto the screen, <span class="purple_italic">mistakes creep in.</span> Even when you proofread your own work, they manage to elude you.</p>

            <p>You need another set of eyes. You need a <span class="purple_italic">blunder buster</span> to catch those pesky errors before they jump off the screen and confuse your audience. Or worse, turn them off entirely. Because once they’re gone, <span class="purple_italic">they don’t come back</span>.</p>

            <p>Don’t think you need another set of eyes? The proof is in the pudding, as they say, so put me to the test. Send me something you’ve written &#8212; up to 500 words &#8212; and I’ll give you <span class="purple_italic">a free sample edit.</span> I’ll point out places where you might not be quite as clear as you think you are. Spots where your reader may need to back up and take another run before they understand your meaning.</p> 

            <p>I’ll even tell you what it would cost if you were paying me (not as much as you think). And if I can’t improve your writing by even one word, I’ll pour blessings upon your head and send you on your merry way with my best wishes for success.</p>

            <p>But, if you agree that I made it better, even by just one “oh, yeah, I see what you did there,” then seriously think about bringing me on as <span class="purple_italic">your Private Consulting Word Guru.</span></p>

            <p><span class="purple_italic">Because you’re a pro.</span> And you need to sound like it.</p>

            <p>So, <a class="contact" href="contact.php">get in touch with me</a>, tell me about your project, and let’s get started.</p>

            <p>Chat soon ~ Mo</p>

            <p>P.S. If you want an idea what it will cost, my rates are on my <a class="contact" href="contact.php#contact_right2">Contact page</a>.</p>
        </section>
        
        <section id="sidebar">
            <img alt="A woman sitting at a table with a cup of tea and a book while working on her laptop" class="content_image" src="images/laptop.jpg">
            
<!-- Contact Button -->        
            <div id="contact_popup" class="popup_button popup_button_modal popup_button_modal3 popup_button_fixed">
                <button type="button" class="button button_contact">Let's Get Started!</button> 
                <div class="popup_content">
                    <div>
                        <div>
                            <h3 class="close icon_close">Close</h3>
                            <!-- <h2 class="popup_title">Do you want a title?</h2> -->
                            <form action="<?php echo basename(__FILE__); ?>" method="post">
                                <label for="name">Your name:</label> 
                                <input type="text" name="name" id="name" value="<?php get_data("name"); ?>" required/>
                                <label for="email">Your email:</label> 
                                <input type="text" name="email" id="email" value="<?php get_data("email"); ?>" required />
                                <label for="phone">Your phone number (optional):</label> 
                                <input type="text" name="phone" id="phone" value="<?php get_data("phone"); ?>" />
                                <label for="comments">What can I do for you?</label><br>
                                <textarea name="comments" id="comments"><?php get_data("comments"); ?></textarea>  
                                <input type="submit" name="submit" id="submit" class="button button_submit" value="Send" <?php if (isset($disable) && $disable === true) echo ' disabled="disabled"'; ?> />   
                            </form>
                        </div>
                    </div>
                </div>
            </div>
<!-- End Contact Button -->
            <br>

<!-- Download Text and Button     
            <p class="bold center">Why You Need an Ideal Reader Profile</p>

            <p>You’ve got a story, but who are you telling it to? You might have an idea of who’s going to read your book, but the clearer you are about who they are, the more enjoyable the reading experience will be for them. Also, your Editor will want one. <span class="bold maroon">Click the green button to download my free Ideal Reader Profile template.</span></p>
            
            <div id="download">                
                <form method="get" action="Ideal_Reader_Profile.docx">
                    <button class="button button_download">Free Profile Template</button>
                </form> 
            </div>
            
            <br>
            
            <p class="bold center">Why You Need a Style Sheet</p>

            <p>You’ve put in agonizing hours deciding on every detail of your story. And your Editor is going to make a thousand tiny decisions as she works through your manuscript. How’s she going to keep track of all that? Take a look at my Style Sheet template to find out why Editors use them, and why you should be using one while you write. <span class="bold maroon">Click the green button to download my free Style Sheet template.</span></p>
            
            <div id="download">                
                <form method="get" action="Style_Sheet.docx">
                    <button class="button button_download">Free Style Template</button>
                </form> 
            </div>
<!-- End Download Text and Button --> 
            <br><br>
            
<!-- Testimonial -->            
            <img alt="Clickable heart with the words 'Donna says...'" class="heart expander" data-expander-target=".donna" src="images/Donna.png">

            <div class="donna">
                <p>I’m so happy I was able to find Monique to help me write my online estate sale courses. It has been a long time dream of mine to start a national organization of estate sale agents and offer ongoing training and education. Monique was able to take my years of experience in the business and my rough notes and turn them into exactly the quality of training course I’d hoped for. And her help developing content for my website was invaluable. No matter what your project is if it involves expressing yourself in words Mo can make it better.</p>
                
                <p>- <a class="contact" href="https://naoel.com/about-us/">Donna Davis</a>, Founder and Lead Instructor, NAOEL</p>
            </div>
<!-- End Testimonial -->     
            
<!-- Mobile Testimonial -->            
            <img alt="Clickable heart with the words 'Donna says...'" class="heart_mobile" src="images/Donna.png">

            <div id="testimonial_mobile">
               <p>I’m so happy I was able to find Monique to help me write my online estate sale courses. It has been a long time dream of mine to start a national organization of estate sale agents and offer ongoing training and education. Monique was able to take my years of experience in the business and my rough notes and turn them into exactly the quality of training course I’d hoped for. And her help developing content for my website was invaluable. No matter what your project is if it involves expressing yourself in words Mo can make it better.</p>
                
                <p>- <a class="contact" href="https://naoel.com/about-us/">Donna Davis</a>, Founder and Lead Instructor, NAOEL</p>
            </div>
<!-- End Mobile Testimonial -->                 
            
        </section>
    </main>
<!-- End Main -->     
    
    <div id="push"></div>
 
        
<!-- Footer -->      
    <footer>
         <ul class="social">
            <li><a href="https://twitter.com/MoReadsYou" target="_blank"><img alt="Twitter" src="images/twitter.jpg"></a></li>
            <li><a href="https://www.facebook.com/monique.huenergardt" target="_blank"><img alt="Facebook" src="images/facebook.jpg"></a></li>
            <li><a href="https://www.linkedin.com/in/moniquehuenergardt" target="_blank"><img alt="LinkedIn" src="images/linkedin.jpg"></a></li>
         </ul>

        <ul class="copyright">
            <li>&#169; Monique Huenergardt</li>
            <li><a class="contact" href="mailto:moreadsyou@outlook.com">MoReadsYou@outlook.com</a></li>
            <li>Lawrenceville, GA, USA</li>
        </ul>   

        <ul class="credits">
            <li><a class="contact" href="contact.php#contact_right2">Full Site Credits</a></li>
            <li>Site Coded by <a class="contact" href="http://codegreer.com/">Code Greer</a></li>
        </ul>
    </footer>  
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

            // for demo purposes only
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
    
<!-- Simple Expand Script -->   
    
    <script>
         $(function () {
            $('.expander').simpleexpand();
        });
    </script>          
<!-- End Javascript -->                
    
</body> 
</html>