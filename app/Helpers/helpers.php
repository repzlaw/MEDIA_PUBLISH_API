<?php

// use DateTime;
use App\Models\Team;
use App\Models\User;
use App\Models\Friend;
use App\Models\Player;
use Illuminate\Support\Str;
use App\Models\TeamFollower;
use App\Models\PlayerFollower;
use App\Models\CompetitionFollower;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

//process image function
function process_image($image)
{
    $random = Str::random(8);
    // Get filename with the extension
    $filenameWithExt = $image->getClientOriginalName();
    //get file name with the extension
    $filename = Hash::make(pathinfo($filenameWithExt, PATHINFO_FILENAME));
    //get just extension
    $extension = $image->getClientOriginalExtension();

    //filename to store
    $fileNameToStore = $random.'_'.time().'.'.$extension;

    return $fileNameToStore;
}

// fun to search user
function searchUser($query, $from)
{
        $q = $query;
        $output= ' ';
        if ($q != null) {
            $user = User::where('name', 'like', "%$q%")->limit(5)->get(['id','name']);
            if (count($user)) {
                if ($from === 'admin') {
                    foreach ($user as $key => $p) {
                        $output .= "
                        <li class='list-group-item' onclick='selectUser($p)'>".$p->name."</li>

                        ";
                        // <option value='$p->id'>$p->name </option>
                    }
                }elseif ($from === 'modal') {
                    foreach ($user as $key => $p) {
                        $output .= "
                        <li class='list-group-item' onclick='selectUserModal($p)'>".$p->name."</li>
                        ";
                    }
                }
            } else {
                $output = 'No Result';
            }
        }
        return $output;

}

//get word count from docx file
function docx2text($filename) {
    return readZippedXML($filename, "word/document.xml");
  }
 
 function readZippedXML($archiveFile, $dataFile) {
 // Create new ZIP archive
 $zip = new ZipArchive;
 
 // Open received archive file
 if (true === $zip->open($archiveFile)) {
     // If done, search for the data file in the archive
     if (($index = $zip->locateName($dataFile)) !== false) {
         // If found, read it to the string
         $data = $zip->getFromIndex($index);
         // Close archive file
         $zip->close();
         // Load XML from a string
         // Skip errors and warnings
         $xml = new DOMDocument();
     $xml->loadXML($data, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
         // Return data without XML formatting tags
         return strip_tags($xml->saveXML());
     }
     $zip->close();
 }
 
 // In case of failure return empty string
 return "";
 }


//func to get browser information
function getBrowser()
{
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }

    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    }
    elseif(preg_match('/Firefox/i',$u_agent))
    {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    }
    elseif(preg_match('/Chrome/i',$u_agent))
    {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    }
    elseif(preg_match('/Safari/i',$u_agent))
    {
        $bname = 'Apple Safari';
        $ub = "Safari";
    }
    elseif(preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Opera';
        $ub = "Opera";
    }
    elseif(preg_match('/Netscape/i',$u_agent))
    {
        $bname = 'Netscape';
        $ub = "Netscape";
    }

    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }

    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        }
        else {
            $version= $matches['version'][1];
        }
    }
    else {
        $version= $matches['version'][0];
    }

    // check if we have a number
    if ($version==null || $version=="") {$version="?";}

    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
}


//get active guard
function activeGuard(){
    foreach(array_keys(config('auth.guards')) as $guard){
        if(auth()->guard($guard)->check()) return $guard;
    }
    return null;
}

//get user account duration
function dateDuration($date){
    $datetime1 = new DateTime($date);
    $datetime2 = new DateTime(now());
    $interval = $datetime1->diff($datetime2);
    $days = $interval->format('%a');
        return $days;
}

//validate using recaptcha v3
function captchaV3Validation($recaptcha,$secret){
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $remoteip = $_SERVER['REMOTE_ADDR'];
    $data = [
        'secret' => $secret,
        'response' => $recaptcha,
        'remoteip' => $remoteip
    ];
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
            ]
        ];
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $resultJson = json_decode($result);

    if ($resultJson->success != true) {
        return false;
    }

    $result = ($resultJson->score >= 0.3 || $resultJson->success == true ) ? true : false;
    return $result;

}
