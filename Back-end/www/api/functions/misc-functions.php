<?php

function cleanString($dirtyString){
  return ucfirst(strtolower(trim($dirtyString)));
}

/*
 * Description
 *
 * @param  text
 * @return text
 */
function getMessageCode($status)
{
$codes = Array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
      /*=============Customs==============*/
        600 => "Wasn't send 7 parameters. Expected : login + password + second_password + email + phone_number + firstname + name",
        601 => "No parameters given",
        610 => "The given Login is not valid",
        611 => "The given Login is already taken",
        620 => "The given Passwords do not match",
        621 => "The given Password have to be entered twice",
        630 => "The given Email adress is not valid",
        631 => "The given Email adress is  already registered",
        640 => "The given Phone number is not valid",
        641 => "The given Phone number is already registered",
        650 => "The given Firstname is not valid",
        660 => "The given name is not valid",
        670 => "The id is empty",
        671 => "The user id is empty",
        672 => "The social network id is empty",
        673 => "The id doesn't exist",
        680 => "The login given for the social network is empty",
        700 => "Could not get this user",
        710 => "Could not get the social networks list",
        886 => "Only GET/PUT/POST Request Accepted",
        887 => "Only GET Request accepted",
        888 => "Only POST/PUT/GET/DELETE Request accepted",
        889 => "SQL Error",
        890 => "Could not alter this element",
        891 => "Answer was Empty"
    );

    return (isset($codes[$status])) ? $codes[$status] : '';
}

/*
 * Description
 *
 * @param  text
 * @return text
 */
function sendResponse($code, $content_type = 'text/html', $result = null)
{
    $message =  utf8_encode(getMessageCode($code));
    $status_header = 'HTTP/1.1 ' . $code . ' ' . $message;
    error_log($status_header);
    header($status_header);
    header('Content-type: ' . $content_type);

    if ($result != null){
        $response['result'] = $result;
    }
    if($content_type == 'application/json; charset=utf-8'){
        $response['message'] = $message;
        echo json_encode($response);
     } else
        echo $response;
}
