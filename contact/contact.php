<?php

// Set email variables
$email_to = 'paulmarkbunker@gmail.com';
$email_subject = 'Fumbalinas Form Submission';

// Set required fields
$required_fields = array('fullname','email','comment');

// set error messages
$error_messages = array(
	'fullname' => 'Please enter your Name.',
	'email' => 'Please enter a valid Email Address.',
	'comment' => 'Please enter your Message.'
);

// Set form status
$form_complete = FALSE;

// configure validation array
$validation = array();

// check form submittal
if(!empty($_POST)) {
	// Sanitise POST array
	foreach($_POST as $key => $value) $_POST[$key] = remove_email_injection(trim($value));
	
	// Loop into required fields and make sure they match our needs
	foreach($required_fields as $field) {		
		// the field has been submitted?
		if(!array_key_exists($field, $_POST)) array_push($validation, $field);
		
		// check there is information in the field?
		if($_POST[$field] == '') array_push($validation, $field);
		
		if($field == 'fullname')if($_POST[$field] == 'Full Name') array_push($validation, $field);
		if($field == 'comment')if($_POST[$field] == 'Your Message') array_push($validation, $field);
		
		// validate the email address supplied
		if($field == 'email') if(!validate_email_address($_POST[$field])) array_push($validation, $field);
	}
	
	// basic validation result
	if(count($validation) == 0) {
		// Prepare our content string
		$email_content = 'New Website Comment: ' . "\n\n";
		
		// simple email content
		foreach($_POST as $key => $value) {
			if($key != 'submit') $email_content .= $key . ': ' . $value . "\n";
		}
		
		// if validation passed ok then send the email
		mail($email_to, $email_subject, $email_content);
		
		// Update form switch
		$form_complete = TRUE;
	}
}

function validate_email_address($email = FALSE) {
	return (preg_match('/^[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}$/i', $email))? TRUE : FALSE;
}

function remove_email_injection($field = FALSE) {
   return (str_ireplace(array("\r", "\n", "%0a", "%0d", "Content-Type:", "bcc:","to:","cc:"), '', $field));
}

?>
<!doctype html>

<head>
<meta charset="utf-8">
<title>Fumbalinas - Contact Form</title>
<link href="contact.css" rel="stylesheet" type="text/css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.js"></script>
<script src="jquery.validate.min.js"></script>
<script>

$(document).ready(function()
{
    $(".defaultText").focus(function(srcc)
    {
        if ($(this).val() == $(this)[0].title)
        {
            $(this).removeClass("error defaultTextActive");
            $(this).val("");
        }
    });
    
    $(".defaultText").blur(function()
    {
        if ($(this).val() == "")
        {
            $(this).addClass("defaultTextActive");
            $(this).val($(this)[0].title);
        }
    });
    
    $(".defaultText").blur();  
	
	$("form").submit(function() {
		$(".defaultText").each(function() {
			if($(this).val() == this.title) {
				$(this).val("");
			}
		});
	});
	
	$("form").validate({
		 focusInvalid: false,
		 onkeyup: false,
   		 onclick: false,
		 errorPlacement: function(error,element) {
                $(".defaultText").blur(); 
         },
		 rules: {
			fullname:{required:true},
			email:{
				required: true,
				//regex: /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/,
				email:true
				},
			comment:{required:true}
		}
	});
	
	      
});
<?php if($form_complete === TRUE): ?>

setTimeout('ourRedirect()', 5000)
		function ourRedirect(){
			
			parent.$.fancybox.close();
			}
<?php endif; ?>
</script>
</head>
<body>
<div id="formWrap">
	<div id="form">
        <?php if($form_complete === FALSE): ?>
    <form name="contact" action="contact.php" method="post" id="contactForm" >
    
     <div class="input">
		<input type="text" id="fullname" class="detail defaultText" name="fullname" title="Full Name" value="<?php echo isset($_POST['fullname'])? $_POST['fullname'] : '';?>" />
				<?php if(in_array('fullname', $validation)): ?><span class="error"><?php echo $error_messages['fullname']; ?></span><?php endif; ?>
     </div><!--end .input-->  
     <div class="input">         
        <input type="text" id="email" class="detail defaultText" name="email" title="Email Address" value="<?php echo isset($_POST['email'])? $_POST['email'] : ''; ?>" />
				<?php if(in_array('email', $validation)): ?><span class="error"><?php echo $error_messages['email']; ?></span><?php endif; ?>
     </div><!--end .input-->
     <div class="input">           
        <textarea id="comment" name="comment" class="mess defaultText" title="Your Message" ><?php echo isset($_POST['comment'])? $_POST['comment'] : ''; ?></textarea>
				<?php if(in_array('comment', $validation)): ?><span class="error"><?php echo $error_messages['comment']; ?></span><?php endif; ?>
     </div><!--end .input--> 
     <div class="submit">          
        <input type="submit" name="submit" id="submit" value="Send Message" />
	</div><!--end .submit-->

	 </form>
        <?php else: ?>
        <p style="font-size:36px; color:#255e67; margin-left:25px;">Thank you for your Message!</p>
		<?php endif; ?>
	</div><!--end #form-->
</div><!-- end #formWrap-->

</body>
</html>
