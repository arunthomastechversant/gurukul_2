<?php

    $mailid ='arunthomas@techversantinfo.com';
    $subject ='Cron Running';
    $mailcontent ='Cron Run Successfully On '.date('Y-m-d :H-s', time());
    //print_r($mailcontent);exit();
    $curl=curl_init();
    curl_setopt($curl,CURLOPT_URL,'https://qbpm.quest-global.com:8081/common-api/mail/send');
    curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,2);
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
    $headers = array("Authorization" => "Basic U2NoZWR1bGVyOjFRdUVTVGhkYiFAIzFRdUVTVA==", "Content-Type" => "application/json");
    $data = array("from" => "no-replay@quest-global.com", "to"=> $mailid, "subject" => $subject, "isHtml" => "true", "text" => $mailcontent, "isProd" => "true");
    //curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic U2NoZWR1bGVyOjFRdUVTVGhkYiFAIzFRdUVTVA==','Content-Type: application/json'));
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    $buffer = json_decode(curl_exec($curl), true);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    print_r($httpcode);exit();

    if ($httpcode == 200){
       print "your email has been sent sccessfully.<p>";
    }else if ($httpcode == 401){
       print "Unauthorized.<p>";
    }else if ($httpcode == 404){
       print "Not Found.<p>";
    }else if ($httpcode == 500){
       print "Something went wrong!.<p>";
    }

?>
