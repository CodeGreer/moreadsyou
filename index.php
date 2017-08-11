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
		$subject = "Home Contact Button - ". $yourWebsite;
		
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
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
    
    <title>Editing and Proofreading Service for Authors, Bloggers, and Business | Atlanta - Gwinnett | Mo Reads You</title>

    <link rel="canonical" href="http://www.moreadsyou.com/">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="editing, copy editor, proofreading service, writing, author, blogger, blog content, business content, Atlanta, Gwinnett" >
    <meta name="description" content="What have you got to say for yourself? I can help you say it better. Editing and proofreading services for authors, content producers, and everyone else.">

<!-- Facebook -->
    <meta property="og:title" content="Mo Reads You | Editing, proofreading, and writing services." >
    <meta property="og:site_name" content="Mo Reads You">
    <meta property="og:url" content="http://www.moreadsyou.com/" >
    <meta property="og:description" content="What have you got to say for yourself? I can help you say it better. Editing and proofreading services for authors, content producers, and everyone else." >
    <meta property="og:image" content="http://www.moreadsyou.com/images/mo.jpg" >
    <meta property="og:type" content="website" >
    <meta property="og:locale" content="en_US" >

<!--Twitter-->
    <meta property="twitter:card" content="summary" >
    <meta property="twitter:title" content="Mo Reads You | Editing, proofreading, and writing services." >
    <meta property="twitter:description" content="What have you got to say for yourself? I can help you say it better. Editing and proofreading services for authors, content producers, and everyone else." >
    <meta property="twitter:creator" content="@moreadsyou" >
    <meta property="twitter:url" content="http://www.moreadsyou.com/" >
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
    <link href='https://fonts.googleapis.com/css?family=Judson%7CRaleway:300,700' rel='stylesheet' type='text/css'>
    
<!-- Javascript -->  
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="js/modernizr.custom.js"></script>
    
</head>
  
<body class="site"> 
    
<!-- start my code -->
    
<!-- Borders -->   
<div id="left"></div>
<div id="right"></div> 
    
<!-- Mobile Header -->
    <header id="mobile_header_text" class="push">
        <h1 class="title"><a href="#">Mo Reads You</a></h1>
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
                <li><a href="#">Home | About</a></li>
                <li><a href="authors.php"><span class="bold_purple">A</span>uthors</a></li><li><a href="business.php"><span class="bold_purple">B</span>usinesses</a></li>
                <li><a href="bloggers.php"><span class="bold_purple">C</span>ontent Producers</a></li>
                <li><a href="contact.php">Contact | Rates</a></li>
            </ul>
        </nav>
<!-- End Menu -->             
        
        <img class="tree" alt="Tree branch with green leaves." src="images/tree.jpg">
        
        <div id="title_div">
            <h1 class="title"><a href="#">Mo Reads You</a></h1>
            <h1 class="description">Editing &#38; Proofreading Services</h1>
        </div> 
        
        <div id="tagline_div">
            <h1 class="tagline">Helping You Be a Better Writer</h1>   
        </div> 
        
        <div id="profile_image">
            <img alt="Picture of Monique" src="images/mo.jpg">
        </div>
        
        <div id="page_header_text">
            <p class="first_line purple_italic">So, what have you got to say for yourself?</p>
            <p class="second_line purple_italic">I can help you say it better.</p>
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
        
        <div id="left_button">
            <form method="get" action="authors.php">
                <button class="button button_index">You’re writing the next Great American Novel and need someone to help massage it into shape. And to check your grammar.<span class="more">More...</span></button>
            </form>
        </div>
        
        <div id="middle_button">
            <form method="get" action="bloggers.php">
                <button class="button button_index">You have Brilliant Ideas that will Change the Internet and need to be sure typos don’t make you look like an amateur. Before you hit the publish button.<span class="more">More...</span></button>
            </form>    
        </div>
        
        <div id="right_button">
            <form method="get" action="business.php">
                <button class="button button_index">You’ve got something to say and need help using your words. Without being lectured to by your 5th grade English teacher.<span class="more">More...</span></button>
            </form>    
        </div>
        
<!-- Contact Button -->        
        <div id="contact_popup_index" class="popup_button popup_button_modal popup_button_modal3 popup_button_fixed hide">
            <button type="button" class="button button_contact">Get in touch!</button> 
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
        
<!-- Contact Button Mobile -->        
        <div class="contact_popup_index2 show">
            <form method="get" action="contact.php#contact_right">
                <button class="button button_contact">Get in touch!</button>
            </form>    
        </div>
<!-- End Contact Button Mobile -->  
        <br>
        
        <p>Hey, I'm Monique. I'm that annoying person who notices the one tiny error in just about anything, and points it out. I've always been an avid reader, and will generally take a shot at reading just about anything a friend recommends. I love being pulled into someone else's mind, their ideas, their world. I love a lovely turn of phrase, a delightful word, a perfectly expressed sentiment. I hate garden path sentences, confusing punctuation, and anything else that interrupts absolute immersion in the reading experience. I channel that passion into helping other people express themselves in writing as clearly and seamlessly as possible. I help them be better writers. I'd like to help you, too.</p>
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
            <li>Site Coded by <a class="contact" href="http://codegreer.com/">CodeGreer</a></li>
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

            // causing issues with linkable buttons so used div selector
            [].slice.call( document.querySelectorAll( '#contact_button_index' ) ).forEach( function( bttn ) { 
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