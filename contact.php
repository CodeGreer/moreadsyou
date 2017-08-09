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
		$subject = "Contact Page - ". $yourWebsite;
		
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
    
    <link rel="canonical" href="http://www.moreadsyou.com/contact.php">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Me for Editing and Proofreading Service | Atlanta - Gwinnett | Mo Reads You</title>
    
    <meta name="keywords" content="editing, copy editor, proofreading services, Atlanta, Gwinnett" >
    <meta name="description" content="What have you got to say for yourself? I can help you say it better. Contact me for editing and proofreading services.">

<!-- Facebook -->
    <meta property="og:title" content="Contact | Mo Reads You | Editing and Proofreading" >
    <meta property="og:site_name" content="Mo Reads You">
    <meta property="og:url" content="http://www.moreadsyou.com/contact.php" >
    <meta property="og:description" content="What have you got to say for yourself? I can help you say it better. Contact me for editing and proofreading services." >
    <meta property="og:image" content="http://www.moreadsyou.com/images/mo.jpg" >
    <meta property="og:type" content="website" >
    <meta property="og:locale" content="en_US" >

<!--Twitter-->
    <meta property="twitter:card" content="summary" >
    <meta property="twitter:title" content="Contact | Mo Reads You | Editing and Proofreading" >
    <meta property="twitter:description" content="What have you got to say for yourself? I can help you say it better. Contact me for editing and proofreading services." >
    <meta property="twitter:creator" content="@moreadsyou" >
    <meta property="twitter:url" content="http://www.moreadsyou.com/contact.php" >
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
                <li><a href="bloggers.php"><span class="bold_purple">C</span>ontent Producers</a></li>
                <li><a href="#">Contact | Rates</a></li>
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
            <p class="first_line purple_italic">We need to talk!</p>
            <p class="second_line purple_italic">Don't be shy, tell me who you are.</p>
        </div>
    </header>     
<!-- End Header -->      
              

<!-- Main --> 
    <main>
        
        <div class="contact_flex">
            <div id="contact_left">
                <p>You may not be sure exactly what kind of help you need with your document, and that’s fine. The best way to figure it out is for me to take a look so we can talk about it. You can email your document as a Word or Adobe file to <a class="contact" href="mailto:moreadsyou@outlook.com">MoReadsYou@outlook.com</a> and let me know what services you’d like. I won’t share your document or your contact information with anyone. Promise. You don’t even have to give me your phone number, but a name would be helpful. Or a nickname. Even an alias would do.</p>

                <br><br>
                <p class="hide">Want an instant estimate? Sorry, but I don’t do robo-quotes. You and your document are important to me. So let’s talk. M’kay?</p>
            </div>
            

            <div id="contact_right">
                <div id="contactpage_form">
                    <?php
                    if ( !empty( $error_msg ) ) {
                        echo '<p class="fail">ERROR: '. implode( "<br>", $error_msg ) . "</p>";
                    }
                    if ( $result != NULL ) {
                        echo '<p id="success">'. $result . "</p>";
                    }
                    ?>
                    <form action="<?php echo basename( __FILE__ ); ?>" method="post">
                        <noscript>
                                <p><input type="hidden" name="nojs" id="nojs" /></p>
                        </noscript>

                        <input type="hidden" name="token" value="<?php if ( is_array( $_SESSION['nonce'] ) ) echo key( $_SESSION['nonce'] ); ?>">

                        <label for="name">Your name:</label> 
                        <input type="text" name="name" id="name" value="<?php get_data("name"); ?>" required>

                        <label for="email">Your email:</label> 
                        <input type="email" name="email" id="email" value="<?php get_data("email"); ?>" required>
                        
                        <label for="url">Your phone number:</label> 
                        <input type="text" name="phone" id="phone" value="<?php get_data("phone"); ?>" required>
                        
                        <label for="url">Your website (optional):</label> 
                        <input type="text" name="url" placeholder="Please include the http://" id="url" value="<?php get_data("url"); ?>" />

                        <label for="comments">What can I do for you?</label><br>
                        <textarea name="comments" id="comments"><?php get_data("comments"); ?></textarea>

                        <input type="submit" name="submit" id="submit" class="button button_submit" value="Send" <?php if ( isset( $disable ) && $disable === true ) echo ' disabled="disabled"'; ?> />

                    </form>
                </div>
            </div>
        </div>

        <div class="contact_flex">
            <div id="contact_left2">


                <p class="show"><br><br>Want an instant estimate? Sorry, but I don’t do robo-quotes. You and your document are important to me. So let’s talk. M’kay?</p>
                <br><br>

                <div id="green_box">
                    <div id="green_box_top">
                        <p>You might not be sure what services you need, but here are some numbers if you want to go shopping.</p>
                    </div>
                    <br>
                    <ul>
                        <li>Proofreading</li>
                        <li>2 pennies per word</li>
                    </ul>

                    <ul>
                        <li>Copy Editing</li>
                        <li>3 pennies per word</li>
                    </ul>

                    <ul>
                        <li>Developmental Editing</li>
                        <li>3&#189; pennies per word</li>
                    </ul>

                    <ul>
                        <li>Please just help me write this</li>
                        <li>4 pennies per word</li>
                    </ul>

                    <ul>
                        <li>Beta Reading</li>
                        <li>Let's talk. It's usually free.</li>
                    </ul>

                    <ul>
                        <li>TERMS:</li>
                        <li>50% to start, balance due within 10 days of delivery</li>
                    </ul>
                </div>
            </div>


            <div id="contact_right2">
                <br>
                <p class="mobile_center">Site Credits:</p>
                <ul class="mobile_center">
                    <li>Coding by <a href="http://codegreer.com/" target="_blank">CodeGreer</a></li>

                    <li>Design by Monique Huenergardt and <a href="http://codegreer.com/" target="_blank">CodeGreer</a></li>

                    <li>Written content by Monique Huenergardt</li>

                    <li>Banner photo by Monique Huenergardt</li>

                    <li>Profile photo by Thomas Huenergardt</li>

                    <li>Other photos by <a href="https://www.pexels.com/search/laptop%20woman%20bed/" target="_blank">Pexels</a></li>

                    <li>Contact form by <a href="http://jemsmailform.com/" target="_blank">Jem's PHP Mail Form</a></li>

                    <li>Social media icons by <a href="http://www.graphicsfuel.com/2013/06/simple-flat-social-media-icons-psd-png/" target="_blank">GraphicsFuel</a></li>

                    <li>Clip art image by <a href="http://cliparts.co" target="_blank">Cliparts.co</a></li>

                    <li>Javascript and buttons by <a href="http://tympanus.net/codrops/" target="_blank">Codrops</a> and <a href="http://ascott1.github.io/bigSlide.js/" target="_blank">Big Slide</a></li>
                </ul>
                <br><br>
            </div>
        </div> 
         <br><br>
        
        <div class="contact_flex">
            
            <div id="contact_left">
                <p><span class="bold">All right, who told you about me?</span><br>
                I'd like to thank them in a way that really shows my appreciation...by sending them a check. If you and I end up working together, I'll send your referring friend a check equal to ten percent (10%) of what you pay me for our first project together. But, you have to tell me about your friend <span class="purple_italic">before</span> you and I start conversing about your project. So, fill out this form <span class="purple_italic">right now</span> so I can thank your friend later. </p>

                <br><br>
                <p>I PROMISE I won't spam you or your friend, and I won't sell or otherwise pass along your information to anyone. Scout's honor.</p>
            </div>
            

            <div id="contact_right">
                <div id="contactpage_form">
                    <?php
                    if ( !empty( $error_msg ) ) {
                        echo '<p class="fail">ERROR: '. implode( "<br>", $error_msg ) . "</p>";
                    }
                    if ( $result != NULL ) {
                        echo '<p id="success">'. $result . "</p>";
                    }
                    ?>
                    <form action="<?php echo basename( __FILE__ ); ?>" method="post">
                        <noscript>
                                <p><input type="hidden" name="nojs" id="nojs" /></p>
                        </noscript>

                        <input type="hidden" name="token" value="<?php if ( is_array( $_SESSION['nonce'] ) ) echo key( $_SESSION['nonce'] ); ?>">

                        <label for="name">My Name Is:</label> 
                        <input type="text" name="name" id="name" value="<?php get_data("name"); ?>" required>

                        <label for="email">My Email Address:</label> 
                        <input type="email" name="email" id="email" value="<?php get_data("email"); ?>" required>

                        <label for="url">My Phone Number:</label> 
                        <input type="text" name="phone" id="phone" value="<?php get_data("phone"); ?>" required>
                        <br><br>

                        <label for="name">My Friend's Name Is:</label> 
                        <input type="text" name="name" id="name" value="<?php get_data("name"); ?>" required>

                        <label for="email">My Friend's Email Address Is:</label> 
                        <input type="email" name="email" id="email" value="<?php get_data("email"); ?>" required>

                        <label for="url">My Friend's Phone Number Is:</label> 
                        <input type="text" name="phone" id="phone" value="<?php get_data("phone"); ?>" required>
                        <br><br>
                        
                        <label for="comments">Tell me a little bit about your project.</label><br>
                        <textarea name="comments" id="comments"><?php get_data("comments"); ?></textarea>

                        <input type="submit" name="submit" id="submit" class="button button_submit" value="Send" <?php if ( isset( $disable ) && $disable === true ) echo ' disabled="disabled"'; ?> />

                    </form>
                </div>
            </div>
        </div>
        
        
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
            <li><a class="contact" href="contact.php">Full Site Credits</a></li>
            <li>Site Coded by <a class="contact" href="http://codegreer.com/">CodeGreer</a></li>
        </ul>
    </footer>  
<!-- End Footer -->  
    
<!-- Javascript -->  
    <script src="js/bigslide.js"></script>
    <script src="js/classie.js"></script>
    
<!-- Big Slide -->
    <script>
        $(document).ready(function() {
        $('.menu_link, .menu_link_mobile').bigSlide();
        });
    </script>  
<!-- End Javascript -->                   
    
</body> 
</html>