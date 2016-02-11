<?php

/*********************************************************\
| Deal with feedback addon                                |
| ~~~~~~~~~~~~~~~~~~~~~~~~                                |
\*********************************************************/
if (!defined('IN_FS')) {
    die('Do not access this file directly.');
}
if (!Post::has('feedback')) {
    die("Parameter feedback has not been sent");
}

if ($user->isAnon() && !$fs->prefs['enable_anon_feedback']){
    die("Anonymous user can not create tickets");
}

$feedback = Post::val('feedback');

function recursivePrintValue(array $PostArray){
    $sReturnValue = "";
    if(is_array($PostArray)){
        foreach($PostArray as $PostKey => $PostValue){
            if(is_array($PostValue)){
                $sReturnValue.= "** ".$PostKey.": ** ". recursivePrintValue($PostValue);
                continue;
            }
            $sReturnValue.= "** ".$PostKey.": ** ".$PostValue."
";
        }
    }
    return $sReturnValue;
}

$task = array(
    'project_id' => $fs->prefs['def_feedback_proj'],
    'status' => 1, // unconfirmed
    'item_summary' => strlen($feedback['note']) > 97 ? substr($feedback['note'], 0, 97).'...' : $feedback['note'], // tytul
    'detailed_desc' =>  '** Wiadomość: ** '.$feedback['note'] . '
** URL: ** [[' . $feedback['url'] . '|' . $feedback['url'] . "]]
** Cookie: ** ".$feedback['Cookie']."
** Browser: ** " . recursivePrintValue($feedback['browser']), // opis
    'product_category' => '',
);

if ($user->isAnon()){
    $task['anon_email'] = 'krzysztof.blachut@egm.pl'; // wysylac na domyslny? pytac? cokolwiek? ;)
}

list($task_id, $token) = Backend::create_task($task);

$fname = substr($task_id . '_' . md5(uniqid(mt_rand(), true)), 0, 30);
$path = BASEDIR .'/attachments/'. $fname;
file_put_contents($path, base64_decode(str_replace('data:image/png;base64,', '', $feedback['img'])));
@chmod($path, 0644);

$db->Query("INSERT INTO  {attachments}
                         ( task_id, comment_id, file_name,
                           file_type, file_size, orig_name,
                           added_by, date_added)
                 VALUES  (?, ?, ?, ?, ?, ?, ?, ?)",
    array($task_id, 0, $fname, 'image/png', filesize($path), "screen.png", $user->id, time()));
$attid = $db->Insert_ID();
Flyspray::logEvent($task_id, 7, $attid, "screen.png");
die('1');