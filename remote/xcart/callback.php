<?php
/*
to be placed at: https://demoshop.pagamastarde.com/getfinancing/xcart/callback.php
*/
require_once ("config.php");
//recevice original pmt notification
$json = file_get_contents('php://input');
$temp = json_decode($json,true);

if ( $temp['updates']['status'] == "approved" || $temp['updates']['status'] == "preapproved"){

  //recover all saved data form the order
  $db = mysqli_init();
  $link = $db->real_connect ($db_host, $db_username,$db_password,$db_name);
  if (!$link)
  {
      throw new Exception('Connect error (' . mysqli_connect_errno() . '): ' . mysqli_connect_error() . "\n");      
  }
  $db->set_charset("utf8");
  $sql="SELECT * FROM `payments_gf` where trx_id = '".$temp['merchant_transaction_id']."' order by id desc limit 1";
  echo $sql;
  if ($result = $db->query($sql)) {
    if ($myrow = $result->fetch_array(MYSQLI_ASSOC)) {
        $data =$myrow;
    }
  }else{
    throw new Exception("SQL error ".$db->error);
  }
  if ($temp['updates']['status'] == "approved"){
    $x_result="completed";
  }else{
     $x_result="pending";
  }
  //send the callback to Shopty to confirm the purchase
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $data['callback_url']);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
  curl_setopt($curl, CURLOPT_TIMEOUT, 30);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
  curl_exec($curl);
  curl_close($curl);
}else{
  die("Status is not approved, aborting.");
}
?>

