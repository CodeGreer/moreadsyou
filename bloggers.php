<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 

	Jem's PHP Mail Form Premium v2.1.1
	Secure single-page PHP mail form for your website
	Copyright (c) Jem Turner 2014-2017
	http://jemsmailform.com/

* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

// OPTIONS - PLEASE CONFIGURE THESE BEFORE USE!

// the email address you wish to receive these mails through
$primary_recipient = "moreadsyou@gmail.com"; 

// additional recipients to be "CC"ed into the email (the email address of these 
// recipients will be visible in the mail); separate each email with a comma
$cc_recipients = ""; 

// additional recipients to be "BCC"ed into the email (the email address of these 
// recipients will NOT be visible in the mail); separate each email with a comma
$bcc_recipients = ""; 

$yourWebsite = "Mo Reads You"; // the name of your website
$thanksPage = 'thanks.html'; // URL to 'thanks for sending mail' page; leave empty to keep message on the same page 
$maxPoints = 4; // max points a person can hit before it refuses to submit - recommend 4
$requiredFields = "name,email,comments"; // names of the fields you'd like to be required as a minimum, separate each field with a comma
$prevent_repeats = true; // prevent rapid submits (submissions less than 60 seconds apart)


// DO NOT EDIT BELOW HERE
session_start();

function generate_nonce( $length = 32 ) {
	if ( function_exists( 'openssl_random_pseudo_bytes' ) ) {
		# we have php5 :)
		return substr( base64_encode( openssl_random_pseudo_bytes( 1000 ) ), 0, $length );
	} else {
		# php4 makes babies cry :(
		return substr( base64_encode( rand( 0, 1000 ) ), 0, $length );
	}
}
function destroy_nonce() {
	unset( $_SESSION['nonce'] );
}

if ( !isset( $_SESSION['nonce'] ) ) {
	$token = generate_nonce();
	$_SESSION['nonce'][$token] = strtotime( "+1 hour" );
}

$error_msg = array();
$result = null;

$requiredFields = explode( ",", $requiredFields );

function clean($data) {
	$data = trim( stripslashes( strip_tags( $data ) ) );
	return $data;
}
function is_bot() {
	$bots = array( "Indy", "Blaiz", "Java", "libwww-perl", "Python", "OutfoxBot", "User-Agent", "PycURL", "AlphaServer", "T8Abot", "Syntryx", "WinHttp", "WebBandit", "nicebot", "Teoma", "alexa", "froogle", "inktomi", "looksmart", "URL_Spider_SQL", "Firefly", "NationalDirectory", "Ask Jeeves", "TECNOSEEK", "InfoSeek", "WebFindBot", "girafabot", "crawler", "www.galaxy.com", "Googlebot", "Scooter", "Slurp", "appie", "FAST", "WebBug", "Spade", "ZyBorg", "rabaz" );

	foreach ( $bots as $bot )
		if ( stripos( $_SERVER['HTTP_USER_AGENT'], $bot ) !== false )
			return true;

	if ( empty( $_SERVER['HTTP_USER_AGENT'] ) || $_SERVER['HTTP_USER_AGENT'] == " " )
		return true;
	
	return false;
}

function is_valid_email( $email_address ) {
	if ( function_exists( 'filter_var' ) ) {
		# we have php5 :)
		if ( filter_var( $email_address, FILTER_VALIDATE_EMAIL ) !== false )
			return true;
		
		return false;
	} else {
		# php4 makes babies cry :(
		if ( preg_match( '/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])(([a-z0-9-])*([a-z0-9]))+(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i', strtolower( $email_address ) ) )
			return true;
		
		return false;
	}
}
function is_valid_url( $web_address ) {
	if ( function_exists( 'filter_var' ) ) {
		# we have php5 :)
		if ( filter_var( $web_address, FILTER_VALIDATE_URL ) !== false )
			return true;
		
		return false;
	} else {
		# php4 makes babies cry :(
		if ( preg_match( '/^(http|https):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?\/?/i', $web_address ) )
			return true;
		
		return false;
	}
}
function validate_nonce( $token ) {
	if ( !isset( $token ) || !isset( $_SESSION['nonce'][$token] ) )
		return false; # token or session missing

	if ( time() > $_SESSION['nonce'][$token] ) {
		destroy_nonce();
		return false; # expired
	}

	if ( $token != key( $_SESSION['nonce'] ) ) {
		destroy_nonce();
		return false; # submitted token doesn't match session
	}
	
	return true;
}

if ( $_SERVER['REQUEST_METHOD'] == "POST" ) {
	if ( validate_nonce( $_POST['token'] ) !== true ) {
		$error_msg[] = "Invalid submission";
		destroy_nonce();
	}
	
	if ( is_bot() !== false )
		$error_msg[] = "No bots please! UA reported as: ". $_SERVER['HTTP_USER_AGENT'];
		
	// lets check a few things - not enough to trigger an error on their own, but worth assigning a spam score.. 
	// score quickly adds up therefore allowing genuine users with 'accidental' score through but cutting out real spam :)
	$points = (int)0;
	
	if ( isset( $_SESSION['last_submit'] ) ) {
		if ( time()-$_SESSION['last_submit'] > 60 && time()-$_SESSION['last_submit'] < 360 )
			$points += 2;
		
		if ( true == $prevent_repeats && time()-$_SESSION['last_submit'] < 60 ) {
			$error_msg[] = "You have only just filled in the form; please do not send multiple form submissions.";
		}
	} else {
		$_SESSION['last_submit'] = time();
	}
	
	$badwords = array("adult", "beastial", "bestial", "blowjob", "clit", "cum", "cunilingus", "cunillingus", "cunnilingus", "cunt", "ejaculate", "fag", "felatio", "fellatio", "fuck", "fuk", "fuks", "gangbang", "gangbanged", "gangbangs", "hotsex", "hardcode", "jism", "jiz", "orgasim", "orgasims", "orgasm", "orgasms", "phonesex", "phuk", "phuq", "pussies", "pussy", "spunk", "xxx", "viagra", "phentermine", "tramadol", "adipex", "advai", "alprazolam", "ambien", "ambian", "amoxicillin", "antivert", "blackjack", "backgammon", "texas", "holdem", "poker", "carisoprodol", "ciara", "ciprofloxacin", "debt", "dating", "porn", "link=", "voyeur", "content-type", "bcc:", "cc:", "document.cookie", "onclick", "onload", "javascript");

	foreach ( $badwords as $word )
		if (
			strpos( strtolower( $_POST['comments'] ), $word ) !== false || 
			strpos( strtolower( $_POST['name'] ), $word ) !== false
		)
			$points += 2;
	
	if ( strpos( $_POST['comments'], "http://" ) !== false || strpos( $_POST['comments'], "www." ) !== false )
		$points += 2;
	if ( isset( $_POST['nojs'] ) )
		$points += 1;
	if ( preg_match( "/(<.*>)/i", $_POST['comments'] ) )
		$points += 2;
	if ( strlen( $_POST['name']) < 3 )
		$points += 1;
	if ( strlen( $_POST['comments'] ) < 15 || strlen( $_POST['comments'] > 1500 ) )
		$points += 2;
	if ( preg_match( "/[bcdfghjklmnpqrstvwxyz]{7,}/i", $_POST['comments'] ) )
		$points += 1;
	// end score assignments

	foreach($requiredFields as $field) {
		trim( $_POST[$field] );
		
		if ( !isset( $_POST[$field] ) || empty( $_POST[$field] ) )
			$error_msg['empty_fields'] = "Please fill in all required fields and submit again.";
	}

	// updated regex from http://stackoverflow.com/questions/5963228/regex-for-names-with-special-characters-unicode
	if ( !empty( $_POST['name'] ) && !preg_match( "~^(?:[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+\s?[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+\s?)+$~u", stripslashes( $_POST['name'] ) ) )
		$error_msg[] = "The name field must not contain special characters.\r\n";
	if ( !empty( $_POST['email'] ) && !is_valid_email( $_POST['email'] ) )
		$error_msg[] = "That is not a valid e-mail address.\r\n";
	if ( !empty( $_POST['url'] ) && !is_valid_url( $_POST['url'] ) )
		$error_msg[] = "Invalid website url.\r\n";
	
	if ( $error_msg == NULL && $points <= $maxPoints ) {
		$subject = "Bloggers Contact Button - ". $yourWebsite;
		
		$message = "You received this e-mail message through your website: \n\n";
		foreach ( $_POST as $key => $val ) {
			if ( $key == 'token' || $key == 'submit' )
				continue; // we don't need these in the email
			
			if ( is_array( $val ) ) {
				foreach ( $val as $subval ) {
					$message .= ucwords( $key ) . ": " . clean( $subval ) . "\r\n";
				}
			} else {
				$message .= ucwords( $key ) . ": " . clean( $val ) . "\r\n";
			}
		}
		$message .= "\r\n";
		$message .= 'IP: '. $_SERVER['REMOTE_ADDR']."\r\n";
		$message .= 'Browser: '. $_SERVER['HTTP_USER_AGENT']."\r\n";
		$message .= 'Points: '. $points;

		if ( strstr( $_SERVER['SERVER_SOFTWARE'], "Win" ) ) {
			$headers   = "From: $primary_recipient\r\n";
		} else {
			$headers   = "From: $yourWebsite <$primary_recipient>\r\n";	
		}
		$headers  .= "Reply-To: {$_POST['email']}\r\n";

		if ( '' != $cc_recipients ) {
			$headers .= "CC: ". $cc_recipients;
		}		
		if ( '' != $bcc_recipients ) {
			$headers .= "BCC: ". $bcc_recipients;
		}
		
		$headers .= "Content-Transfer-Encoding: 8bit\r\n";
		$headers .= "Content-type: text/plain; charset=UTF-8\r\n";
		

		if ( mail( $primary_recipient, $subject, $message, $headers ) ) {
			destroy_nonce();
			
			if ( !empty( $thanksPage ) ) {
				header( "Location: $thanksPage" );
				exit;
			} else {
				$result = 'Your message was successfully sent.';
				$disable = true;
			}
		} else {
			destory_nonce();
			$error_msg[] = 'Your message could not be sent this time. ['.$points.']';
		}
	} else {
		if ( empty( $error_msg ) ) {
			// error message is empty so it must be a points problem
			$error_msg[] = 'Your message looks too much like spam, and could not be sent at this time. ['.$points.']';
		} else {
			// ooops, someone made an error - let's remove the last submission time so they don't get peed off
			unset( $_SESSION['last_submit'] );
		}
	}
}
function get_data( $var ) {
	if ( isset( $_POST[$var] ) )
		echo htmlspecialchars( $_POST[$var] );
}
?>
<!-- End PHP -->  

<!doctype html>
<html class="no-js" lang="en">
<head>
	<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Editing and Proofreading Service for Bloggers | Atlanta - Gwinnett | Mo Reads You</title>
    
    <meta name="keywords" content="content editing, blog copy editor, blog proofreading services, cotent copy editor, blog editor, blog proofreader, blogger, Atlanta, Gwinnett" >
    <meta name="description" content="What have you got to say for yourself? I can help you say it better. Editing and proofreading services for bloggers.">

<!-- Facebook -->
    <meta property="og:title" content="Mo Reads You | Bloggers - Editing and Proofreading" >
    <meta property="og:site_name" content="Mo Reads You">
    <meta property="og:url" content="http://www.moreadsyou.com/bloggers.php" >
    <meta property="og:description" content="What have you got to say for yourself? I can help you say it better. Editing and proofreading services for bloggers." >
    <meta property="og:image" content="http://www.moreadsyou.com/images/mo.jpg" >
    <meta property="og:type" content="website" >
    <meta property="og:locale" content="en_US" >

<!--Twitter-->
    <meta property="twitter:card" content="summary" >
    <meta property="twitter:title" content="Mo Reads You | Bloggers - Editing and Proofreading" >
    <meta property="twitter:description" content="What have you got to say for yourself? I can help you say it better. Editing and proofreading services for bloggers." >
    <meta property="twitter:creator" content="@moreadsyou" >
    <meta property="twitter:url" content="http://www.moreadsyou.com/bloggers.php" >
    <meta property="twitter:image" content="http://www.moreadsyou.com/images/mo.jpg" >

    
<!-- Favicon -->
    <link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/apple-touch-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/apple-touch-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/apple-touch-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon-180x180.png">
    <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="/favicon-194x194.png" sizes="194x194">
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="/android-chrome-192x192.png" sizes="192x192">
    <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="/manifest.json">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#b10909">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-TileImage" content="/mstile-144x144.png">
    <meta name="theme-color" content="#ffffff">
    
<!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/style.min.css" />

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
        <?php
        if ( !empty( $error_msg ) ) {
            echo '<p class="fail">ERROR: '. implode( "<br>", $error_msg ) . "</p>";
        }
        if ( $result != NULL ) {
            echo '<p id="success">'. $result . "</p>";
        }
        ?>
        
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
                            <form action="<?php echo basename( __FILE__ ); ?>" method="post">
                                <noscript>
                                        <p><input type="hidden" name="nojs" id="nojs" /></p>
                                </noscript>

                                <input type="hidden" name="token" value="<?php if ( is_array( $_SESSION['nonce'] ) ) echo key( $_SESSION['nonce'] ); ?>">

                                <label for="name">Your name:</label> 
                                <input type="text" name="name" id="name" value="<?php get_data("name"); ?>" required>

                                <label for="email">Your email:</label> 
                                <input type="email" name="email" id="email" value="<?php get_data("email"); ?>" required>

                                <label for="url">Your phone number (optional):</label> 
                                <input type="text" name="phone" id="phone" value="<?php get_data("phone"); ?>">

                                <label for="comments">What can I do for you?</label><br>
                                <textarea name="comments" id="comments"><?php get_data("comments"); ?></textarea>

                                <input type="submit" name="submit" id="submit" class="button button_submit" value="Send" <?php if ( isset( $disable ) && $disable === true ) echo ' disabled="disabled"'; ?> />

                            </form>
                        </div>
                    </div>
                </div>
            </div>
<!-- End Contact Button -->
            <br>

<!-- Download Text and Button -->    
            <p class="bold center hide_mobile">Why You Need an Ideal Visitor Avatar</p>

            <p class="hide_mobile">You might have an idea of who’ll be visiting your site or reading your newsletter. But your Editor will need to know some specifics to ensure the reading experience is appropriate and enjoyable for your visitor. <span class="bold purple">Click the green button to download my free Ideal Visitor Avatar template.</span></p>
            
            <div id="download">                
                <form method="get" action="ideal_visitor_blogger.docx">
                    <button class="button button_download">Free Visitor Avatar</button>
                </form> 
            </div> 
<!-- End Download Text and Button --> 
            
<!-- Mobile Downloadables -->
            <hr class="show_mobile">
            
            <p class="show_mobile">Do you have an Ideal Visitor Profile? Please visit my website from your desktop or laptop to find out what it is and why you need one. You can download my free templates from there.<br><br></p>                   
            
<!-- Testimonial --> 
            <img alt="Clickable heart with the words 'Cal says...'" class="heart expander" data-expander-target=".testimonial" src="images/Cal.png">

            <div class="testimonial">
                <p>Mo is a hard working and detailed proofreader / editor. I am a private golf instructor and Mo assisted in proofreading and editing all my pictures, videos and submissions for my new website and blogs. Mo is not afraid to make suggestions or be critical about the content of the work she is proofreading or editing. All is done with a professional attitude. I would not have any other person do it other than Mo.</p>
                
                <p>- <a class="contact" href="http://teeitupforefun.com/">Cal Vollmer</a>, Tee It Up Fore Fun</p>
            </div>
<!-- End Testimonial -->     
            
<!-- Mobile Testimonial -->  
            <img alt="Clickable heart with the words 'Cal says...'" class="heart_mobile" src="images/Cal.png">

            <div class="testimonial_mobile">
                 <p>Mo is a hard working and detailed proofreader / editor. I am a private golf instructor and Mo assisted in proofreading and editing all my pictures, videos and submissions for my new website and blogs. Mo is not afraid to make suggestions or be critical about the content of the work she is proofreading or editing. All is done with a professional attitude. I would not have any other person do it other than Mo.</p>
                
                <p>- <a class="contact" href="http://teeitupforefun.com/">Cal Vollmer</a>, Tee It Up Fore Fun</p>
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
            [].slice.call( document.querySelectorAll( '#contact_button' ) ).forEach( function( bttn ) { 
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