<?php
/*
to be placed at: https://demoshop.pagamastarde.com/getfinancing/xcart/redirect.php
*/

require('config.php');
$callback_url = $_POST['callback_url'];
$success_url= $_POST['success_url'];
$failure_url = $_POST['failure_url'];
$form_url = $_POST['form_url'];
$trx_id = $_POST['trx_id'];

if (empty($form_url)){
  header("Location: $failure_url");
}

//in order to send proper values of the callback, we need to save values in database to grab it on return.
$db = mysqli_init();
$link = $db->real_connect ($db_host, $db_username,$db_password,$db_name);
if (!$link)
{
    throw new Exception ('Connect error (' . mysqli_connect_errno() . '): ' . mysqli_connect_error() . "\n");
}
$db->set_charset("utf8");
$sql="INSERT INTO `xcart_gf`.`payments_gf` (`ID`, `insert_date`, `callback_url`, `success_url`, `failure_url`, `form_url`, `trx_id` ) VALUES
(NULL,
CURRENT_TIMESTAMP,
'".$callback_url."',
'".$success_url."',
'".$failure_url."',
'".$form_url."',
'".$trx_id."')";
if ($db->query($sql) === TRUE) {

}else{
  throw new Exception("SQL error ".$db->error);
}


?>
<!-- automatically redirect to pmt -->
Thank you for your order! Follow the GetFinancing process to finish the payment.
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js"></script>
<script src="https://partner.getfinancing.com/libs/1.0/getfinancing.js"></script>

<script>
var onComplete = function() {
    // this is called when the user finishes all the steps and
     window.location.href='<?php echo $success_url; ?>';
};

var onAbort = function() {
    // this is called when the user closes the lightbox before
     window.location.href='<?php echo $success_url; ?>';
};

setTimeout(function(){
        new GetFinancing( '<?php echo $form_url; ?>' , onComplete, onAbort);
},2000);  // 2000 is the delay in milliseconds



</script>


