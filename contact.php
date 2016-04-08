<!doctype html>
<html class="no-js" lang="en">
<head>
	<meta charset="utf-8" />
    
    <title>Contact | Mo Reads You</title>

    <link rel="canonical" href="http://www.moreadsyou.com/">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="keyword, keyword, keyword, keyword, keyword, keyword" >
    <meta name="description" content="Sentence for Google to index with your link.">

<!-- Facebook -->
    

<!-- Twitter -->
    

<!-- Favicon -->
    
    
<!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/style.min.css" />

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
        <h1 class="title"><a href="index.php">Mo Reads You</a></h1>
            <h1 class="description">Editing &#38; Proofreading Services</h1>
        <h3 class="menu_link_mobile"><a href="#menu" class="hamburger"> &#9776;</a></h3>
    </div> 
    
    <div id="mobile_header_two" class="push">
        <h1 class="tagline">Helping You Be a Better Writer</h1>  
    </div>
<!-- End Mobile Header -->      
        
<!-- Header -->  
    <div id="header" class="push">  
        
<!-- Menu -->  
        <h3 class="menu_link">Go To <a href="#menu" class="hamburger"> &#9776;</a></h3>

        <nav id="menu" class="panel">
            <ul class="menu_links">
                <li><a href="index.php">Home | About</a></li>
                <li><a href="authors.php">Authors</a></li>
                <li><a href="bloggers.php">Bloggers</a></li>
                <li><a href="business.php">Businesses</a></li>
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
     </div>     
<!-- End Header -->      
                    
<!-- Main -->     
    <div id="content">
        <div class="contact_flex">
            <div id="contact_left">
                <p>You may not be sure exactly what kind of help you need with your document, and that’s fine. The best way to figure it out is for me to take a look so we can talk about it. You can email your document as a Word or Adobe file to <a class="contact" href="mailto:moreadsyou@outlook.com">MoReadsYou@outlook.com</a> and let me know what services you’d like. I won’t share your document or your contact information with anyone. Promise. You don’t even have to give me your phone number, but a name would be helpful. Or a nickname. Even an alias would do.</p>
                
                <br><br>
                <p class="hide">Want an instant estimate? Sorry, but I don’t do robo-quotes. You and your document are important to me. So let’s talk. M’kay?</p>
            </div>
            
            <div id="contact_right">
                <div id="contactpage_form">
                    <?php
    // OPTIONS - PLEASE CONFIGURE THESE BEFORE USE!

    $yourEmail = "Moreadsyou@outlook.com"; // the email address you wish to receive these mails through
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
            $subject = "MRY Contact Button - Contact";

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
			$error_msg[] = '<div class="fail">Your message could not be sent at this time.</div>';
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
        echo '<p class="error"> ERROR: '. implode("<br />", $error_msg) . "</p>";
    }
    if ($result != NULL) {
        echo '<p class="success">'. $result . "</p>";
    }
    ?>

                    <form action="<?php echo basename(__FILE__); ?>" method="post">
                        <label for="name">Your name:</label> 
                        <input type="text" name="name" id="name" value="<?php get_data("name"); ?>" required/>

                        <label for="email">Your email:</label> 
                        <input type="text" name="email" id="email" value="<?php get_data("email"); ?>" required />

                        <label for="url">Your website (optional):</label> 
                        <input type="text" name="url" placeholder="Please include the http://" id="url" value="<?php get_data("url"); ?>" />

                        <label for="phone">Your phone number (optional):</label> 
                        <input type="text" name="phone" id="phone" value="<?php get_data("phone"); ?>" />

                        <label for="comments">What can I do for you?</label><br>
                        <textarea name="comments" id="comments"><?php get_data("comments"); ?></textarea>  

                        <input type="submit" name="submit" id="submit" class="button button_submit" value="Send" <?php if (isset($disable) && $disable === true) echo ' disabled="disabled"'; ?> />   
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
                        <li>One penny per word</li>
                    </ul>

                    <ul>
                        <li>Copy Editing</li>
                        <li>1&#189; pennies per word</li>
                    </ul>

                    <ul>
                        <li>Developmental Editing</li>
                        <li>1&#189; pennies per word</li>
                    </ul>

                    <ul>
                        <li>Please just help me write this</li>
                        <li>Four pennies per word</li>
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
                    <li>Coding by <a href="http://codegreer.com/" target="_blank">Code Greer</a></li>

                    <li>Design by Monique Huenergardt and <a href="http://codegreer.com/" target="_blank">Code Greer</a></li>

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
    </div>   
<!-- End Main --> 
</div>
<!-- End Container -->    
    
<div id="push"></div>
     
<!-- Footer -->      
<div id="footer">
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
        <li>Site Coded by <a class="contact" href="http://codegreer.com/">Code Greer</a></li>
    </ul>
</div>  
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