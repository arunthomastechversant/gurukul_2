<?php


// global $_SERVER;
/** 
 * Get hearder Authorization
 * */
function getAuthorizationHeader(){
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    }
    else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        // print_r($requestHeaders);
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}
/**
* get access token from header
* */
function validateBearerToken($user = NULL) {
    $headers = getAuthorizationHeader();
    $roles = array('job_seeker');
    // HEADER: Get the access token from the header
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            $key = '/var/www/html/gurukul_2/local/epitome/public.key';
            $token = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $matches[1])[1]))));
            if(!empty($user) && !empty($token->data->user_id)){
                if(in_array($token->data->role,$roles) && $user == $token->data->user_id){
                    return true;
                }else{
                    return false;
                }
            }else{
                if(in_array($token->data->role,$roles)){
                    return true;
                }else{
                    return false;
                }
            }
        }
    }else{
        return false;
    }
}