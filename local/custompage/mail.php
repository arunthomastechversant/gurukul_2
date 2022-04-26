<?php
// $mailid ='arunthomas@techversantinfo.com';
// $subject ='Mail Server Working';
// $mailcontent ='Mail server working fine and mail send successfully at '.date('Y-m-d :H-s', time());

// $curl=curl_init();
// curl_setopt($curl,CURLOPT_URL,'https://qbpm.quest-global.com:8081/common-api/mail/send');
// curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,2);
// curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);


// // $headers = array("Authorization" => "Basic U2NoZWR1bGVyOjFRdUVTVGhkYiFAIzFRdUVTVA==", "Content-Type" => "application/json");
// $data = array("from" => "no-replay@quest-global.com", "to"=> $mailid, "subject" => $subject, "isHtml" => "true", "text" => $mailcontent, "isProd" => "true");
// curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic U2NoZWR1bGVyOjFRdUVTVGhkYiFAIzFRdUVTVA==','Content-Type: application/json'));
// curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
// curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
// $buffer = json_decode(curl_exec($curl), true);
// $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
// curl_close($curl);
//     if ($httpcode == 200){
//        print "your email has been sent sccessfully.<p>";
//     }else if ($httpcode == 401){
//        print "Unauthorized.<p>";
//     }else if ($httpcode == 404){
//        print "Not Found.<p>";
//     }else if ($httpcode == 500){
//        print "Something went wrong!.<p>";
//     }



// $html_brand = "www.google.com";
// $ch = curl_init();

// $options = array(
//     CURLOPT_URL            => $html_brand,
//     CURLOPT_RETURNTRANSFER => true,
//     CURLOPT_HEADER         => true,
//     CURLOPT_FOLLOWLOCATION => true,
//     CURLOPT_ENCODING       => "",
//     CURLOPT_AUTOREFERER    => true,
//     CURLOPT_CONNECTTIMEOUT => 120,
//     CURLOPT_TIMEOUT        => 120,
//     CURLOPT_MAXREDIRS      => 10,
// );
// curl_setopt_array( $ch, $options );
// $response = curl_exec($ch); 
// $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
// print_r($httpCode);exit();
// if ( $httpCode != 200 ){
//     echo "Return code is {$httpCode} \n"
//         .curl_error($ch);
// } else {
//     echo "<pre>".htmlspecialchars($response)."</pre>";
// }

// curl_close($ch);

// $to = "arunthomas@techversantinfo.com";
// $subject = "Mail Server Working";

// $message = "<b>Test Mail server Working.</b>";

// $msg = "First line of text\nSecond line of text";

// $header = "From:aruneathakattu@gmail.com \r\n";
// // $header .= "Cc:afgh@somedomain.com \r\n";
// $header .= "MIME-Version: 1.0\r\n";
// $header .= "Content-type: text/html\r\n";

// // $retval = mail($to,$subject,$message,$header);
// $retval = mail("arunthomas@techversantinfo.com","My subject",$msg);

// if($retval == true ) {
//    echo "Message sent successfully...";
// }else {
//    echo "Message could not be sent...";
// }

// $to = "arunthomas@techversantinfo.com";
// $subject = "HTML email";                                                                                                                                                                                 
// $headers = "MIME-Version: 1.0" . "\r\n";
// $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// $headers .= 'From: <aruneathakattu@gmail.com>' . "\r\n";
// $headers .= 'Cc: aruneathakattu@gmail.com' . "\r\n";

// mail($to,$subject,$message,$headers);


require_once('phpmailer/PHPMailerAutoload.php');

$subject = "Dummy";

$mail = new PHPMailer();
$mail->isSMTP();
$mail->SMTPAuth = true;
$mail->SMTPSecure = 'ssl';
$mail->Host = 'smtp.gmail.com';
$mail->Port = '465';
$mail->isHTML();
$mail->Username = 'aruneathakattu@gmail.com';
$mail->Password = 'eathakattu';
$mail->SetFrom('aruneathakattu@gmail.com');
$mail->Subject = 'Test Mail';
$mail->Body = 'This is a test mail';
$mail->AddAddress('arunthomas@techversantinfo.com');
// $mail->Send();
try {
    $mail->Send();
    throw new Exception("Mail Send Successfully");
  }
  catch(Exception $e) {
    echo $e->getMessage();
  }

?>
