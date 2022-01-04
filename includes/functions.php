<?php
class Format {
    function linkslash($str){
	global $ost_wpdb;
	$str = preg_replace("~[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]~", "", $str);
	//remove backslashes
	while(strchr($str,'\\')) {
        $str = stripslashes($str);
	}
	return $str;
	}
    function stripslashes($str){
	global $ost_wpdb;
	//remove backslashes
	while(strchr($str,'\\')) {
        $str = stripslashes($str);
	}
	return $str;
	} 
}
function key4ce_generateID(){
	$id=mt_rand(100000, 999999);	
	$config = get_option('os_ticket_config');
	extract($config);
	$ost_wpdb = new wpdb($username, $password, $database, $host);	
	$count_no=$ost_wpdb->get_var("SELECT count(*) as count from ".$keyost_prefix."ticket WHERE number = '$id'");
	if($count_no > 0){
	return key4ce_generateID();
	}
	return $id;
}
function key4ce_truncate($string, $max = 50, $replacement = ''){
    if (strlen($string) <= $max){
        return $string;
    }
    $leave = $max - strlen ($replacement);
    return substr_replace($string, $replacement, $leave);
}
function key4ce_getKeyValue($key){	
	$config = get_option('os_ticket_config');
	extract($config);
	$ost_wpdb = new wpdb($username, $password, $database, $host);	
	$getKeyvalue=$ost_wpdb->get_var("SELECT value FROM ".$keyost_prefix."config WHERE `key` LIKE '$key'");
	return $getKeyvalue;
}

function key4ce_getPluginValue($plugin){	
	$config = get_option('os_ticket_config');
	extract($config);
	$ost_wpdb = new wpdb($username, $password, $database, $host);	
	$getPluginValue=$ost_wpdb->get_var("SELECT isactive FROM ".$keyost_prefix."plugin WHERE `name` = '$plugin' AND isphar='1'");
	return $getPluginValue;
}
function key4ce_wpetss_forum_text($text){
// convert links
    $text = " ".$text;
    $text = preg_replace('#(((f|ht){1}tps?://)[-a-zA-Z0-9@:;%_\+.~\#?&//=\[\]]+)#i', '<a href="\\1" target=_blank>\\1</a>', $text);
    $text = preg_replace('#([[:space:]()[{}])(www.[-a-zA-Z0-9@:;%_\+.~\#?&//=]+)#i', '\\1<a href="http://\\2" target=_blank>\\2</a>', $text);
    $text = preg_replace('#([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})#i', '<a href="mailto:\\1" target=_blank>\\1</a>', $text);
    $text = ltrim($text);
    $print_text = '';
    
    foreach(explode("\n",$text) as $line){
	$line = rtrim($line);
	$line = preg_replace("/\t/","&nbsp;&nbsp;&nbsp;",$line);
	if(preg_match('/^(\s+)/',$line,$matches)){
		$line = str_repeat("&nbsp;",strlen($matches[1])) . ltrim($line);
	}
	$print_text .= $line . "<br/>\n";
	}
	return $print_text;
}
function key4ce_generateHashKey($length = 10) {
    $characters = '-0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}
function key4ce_generateHashSignature($length = 10) {
    $characters = '-0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}
function key4ce_getUserEmail($id){
	$config = get_option('os_ticket_config');
	extract($config);
	$ost_wpdb = new wpdb($username, $password, $database, $host);	
	$getUserEmail=$ost_wpdb->get_var("SELECT address FROM ".$keyost_prefix."user_email WHERE `id` = '$id'");
	return $getUserEmail;
}
//1.9.5.1 Functions
function key4ce_FileConfigValue(){
	$config = get_option('os_ticket_config');
	extract($config);
	$ost_wpdb = new wpdb($username, $password, $database, $host);	
	$getConfigValue=$ost_wpdb->get_var("SELECT configuration FROM ".$keyost_prefix."form_field WHERE `type` = 'thread' AND name='message'");
	return $getConfigValue;	
}
function getOriginalMessage($ticketid)
{
	$config = get_option('os_ticket_config');
	extract($config);
	$ost_wpdb = new wpdb($username, $password, $database, $host);	
	$getOriginalMessage=$ost_wpdb->get_var("SELECT body FROM ".$keyost_prefix."ticket_thread WHERE `ticket_id` = ".$ticketid." ORDER BY id ASC LIMIT 0 , 1");
	return $getOriginalMessage;
}
function getLastMessage($ticketid)
{
	$config = get_option('os_ticket_config');
	extract($config);
	$ost_wpdb = new wpdb($username, $password, $database, $host);	
	$getLastMessage=$ost_wpdb->get_var("SELECT body FROM ".$keyost_prefix."ticket_thread WHERE `ticket_id` = ".$ticketid." ORDER BY id DESC LIMIT 0 , 1");
	return $getLastMessage;
}
function getNumberToID($ticketnumber)
{
	$config = get_option('os_ticket_config');
	extract($config);
	$ost_wpdb = new wpdb($username, $password, $database, $host);	
	$getNumberToID=$ost_wpdb->get_var("SELECT ticket_id FROM ".$keyost_prefix."ticket WHERE `number` = ".$ticketnumber." LIMIT 0 , 1");
	return $getNumberToID;
}
function key4ce_ReplaceVar($str,$ticketid)
{
	$config = get_option('os_ticket_config');
	extract($config);
	$ost_wpdb = new wpdb($username, $password, $database, $host);	
	$sql="SELECT ost_ticket.user_id, ost_ticket.number,ost_ticket_thread.poster , ost_ticket.created,ost_team.name as teamname,ost_user.name,ost_department.dept_name,ost_help_topic.topic,ost_ticket.duedate,ost_ticket.closed,ost_ticket.ticket_id, ost_user_email.address AS email,ost_ticket_status.state as status,ost_ticket__cdata.subject,ost_ticket__cdata.priority,ost_ticket_priority.priority_desc
FROM ost_ticket 
LEFT JOIN ost_user_email ON ost_user_email.user_id = ost_ticket.user_id
LEFT JOIN ost_ticket_status ON ost_ticket_status.id = ost_ticket.status_id
LEFT JOIN ost_ticket__cdata on ost_ticket__cdata.ticket_id = ost_ticket.ticket_id
LEFT JOIN ost_ticket_priority ON ost_ticket_priority.priority_id = ost_ticket__cdata.priority
LEFT JOIN ost_help_topic ON ost_help_topic.topic_id = ost_ticket.topic_id
LEFT JOIN ost_department ON ost_department.dept_id = ost_ticket.dept_id
LEFT JOIN ost_user ON ost_user.id = ost_ticket.user_id
LEFT JOIN ost_team ON ost_team.team_id = ost_ticket.team_id
LEFT JOIN ost_ticket_thread ON ost_ticket_thread.ticket_id = ost_ticket.ticket_id
WHERE `number` = '".$ticketid."'";
	$getBasicVariables=$ost_wpdb->get_results($sql);
	$ticketid=$getBasicVariables[0]->ticket_id;
	$ticket_number=$getBasicVariables[0]->number;
	$email=$getBasicVariables[0]->email;
	$subject=$getBasicVariables[0]->subject;
	$status=$getBasicVariables[0]->status;
	$priority_desc=$getBasicVariables[0]->priority_desc;
	$created=date("d-m-Y h:i:s",strtotime($getBasicVariables[0]->created));
	$duedate=date("d-m-Y h:i:s",strtotime($getBasicVariables[0]->duedate));
	$closed=$getBasicVariables[0]->closed;
	$topic=$getBasicVariables[0]->topic;
	$dept_name=$getBasicVariables[0]->dept_name;
	$fullname=$getBasicVariables[0]->name;
	$fullnameary=explode(" ",$fullname);
	$first="";
	$last="";
	$first=@$fullnameary[0];
	$last=@$fullnameary[1];
	$short=$first." ".strtoupper($last[0]).".";
	$shortformal=strtoupper($first[0]).". ".$last;
	$lastfirst="";
		if($last!="")
			$lastfirst.=$last;
		if($first!="")
			$lastfirst.=", ".$last;
	$teamname=$getBasicVariables[0]->teamname;
	$poster=$getBasicVariables[0]->poster;
	$staffname="";
$vars = array(
  '%{ticket.id}'=>$ticketid,
  '%{ticket.number}'=>$ticket_number,
  '%{ticket.email}'=>$email,
  '%{ticket.name}' =>$fullname,
  '%{ticket.subject}'=>$subject,
  '%{ticket.phone}'=>'',
  '%{ticket.status}'=>$status,
  '%{ticket.priority}'=>$priority_desc,
  '%{ticket.assigned}'=>'',
  '%{ticket.create_date}' =>$created,
  '%{ticket.due_date}'=>$duedate,
  '%{ticket.close_date}'=>$closed,
  '%{ticket.recipients}'=>$fullname,
  '%{recipient.ticket_link}'=>'',
  '%{ticket.topic}'=>$topic,
  '%{ticket.dept}'=>$dept_name,
  '%{ticket.staff}'=>$staffname,
  '%{ticket.team}'=>$teamname,
  '%{ticket.thread}'=>'',
  '%{message}'=>getOriginalMessage($ticketid),
  '%{response}'=>'',
  '%{comments}'=>'',
  '%{note}'=>'',
  '%{assignee}'=>'',
  '%{assigner}'=>'',
  '%{url}'=>site_url(),
  '%{reset_link}'=>'',
  '%{ticket.name.first}'=>strtoupper($first),
  '%{ticket.name.last}'=>strtoupper($last),
  '%{ticket.name.full}'=>$fullname,
  '%{ticket.name.short}'=>$short,
  '%{ticket.name.shortformal}'=>$shortformal,
  '%{ticket.name.lastfirst}'=>$lastfirst,
  '%{ticket.dept.name}'=>$dept_name,
  '%{ticket.original}'=>getOriginalMessage($ticketid),
  '%{ticket.lastmessage}'=>getLastMessage($ticketid),
  '%{ticket.poster}'=>$poster,
);
return strtr($str, $vars);
}
function getFAQNumFromCat($categoryid)
{
	$config = get_option('os_ticket_config');
	extract($config);
	$ost_wpdb = new wpdb($username, $password, $database, $host);	
	$getFAQNumFromCat=$ost_wpdb->get_var("SELECT count(*) as num FROM ".$keyost_prefix."faq WHERE `category_id`=".$categoryid."");
	return $getFAQNumFromCat;
}
function checkLicense()
{
	$license_data=key4ce_getKeyValue('key4celicensekey');
	$l_ary=explode("|",$license_data);
	$license_activate=$l_ary[2];
	return $license_activate;
}
function getPoster($thread_id)
{
	$config = get_option('os_ticket_config');
	extract($config);
	$ost_wpdb = new wpdb($username, $password, $database, $host);	
	$getPoster=$ost_wpdb->get_var("SELECT poster FROM ".$keyost_prefix."thread_entry WHERE `thread_id`=".$thread_id."");
	return $getPoster;
}
function getStrftimeFormat($format) {
        static $codes, $ids;

        if (!isset($codes)) {
            // This array is flipped because of duplicated formats on the
            // intl side due to slight differences in the libraries
            $codes = array(
            '%d' => 'dd',
            '%a' => 'EEE',
            '%e' => 'd',
            '%A' => 'EEEE',
            '%w' => 'e',
            '%w' => 'c',
            '%z' => 'D',

            '%V' => 'w',

            '%B' => 'MMMM',
            '%m' => 'MM',
            '%b' => 'MMM',

            '%g' => 'Y',
            '%G' => 'Y',
            '%Y' => 'y',
            '%y' => 'yy',

            '%P' => 'a',
            '%l' => 'h',
            '%k' => 'H',
            '%I' => 'hh',
            '%H' => 'HH',
            '%M' => 'mm',
            '%S' => 'ss',

            '%z' => 'ZZZ',
            '%Z' => 'z',
            );

            $flipped = array_flip($codes);
            krsort($flipped);

            // Also establish a list of ids, so we can do a creative replacement
            // without clobbering the common letters in the formats
            $keys = array_keys($flipped);
            $ids = array_combine($keys, array_map('chr', array_flip($keys)));

            // Now create an array from the id codes back to strftime codes
            $codes = array_combine($ids, $flipped);
        }
        // $ids => array(intl => #id)
        // $codes => array(#id => strftime)
        $format = str_replace(array_keys($ids), $ids, $format);
        $format = str_replace($ids, $codes, $format);

        return preg_replace_callback('`[\x00-\x1f]`',
            function($m) use ($ids) {
                return $ids[ord($m[0])];
            },
            $format
        );
    }
function __formatDate($datetime_format,$datetime)
{
	$strftimeFallback="%x %X";
	return strftime($datetime_format ?: $strftimeFallback,strtotime($datetime));
}
function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 1) . ' gb';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 1) . ' mb';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 1) . ' kb';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }
        return $bytes;
}
function getIDTOEmail($id)
{
	$config = get_option('os_ticket_config');
	extract($config);
	$ost_wpdb = new wpdb($username, $password, $database, $host);	
	$getEmailAddress=$ost_wpdb->get_var("SELECT address FROM ".$keyost_prefix."user_email WHERE `user_id`=".$id."");
	return $getEmailAddress;
}
function getStaffIDTOEmail($id)
{
	$config = get_option('os_ticket_config');
	extract($config);
	$ost_wpdb = new wpdb($username, $password, $database, $host);	
	$getEmailAddress=$ost_wpdb->get_var("SELECT email FROM ".$keyost_prefix."staff WHERE `staff_id`=".$id."");
	return $getEmailAddress;
}
function getEmailToStaffID($staff_email)
{
	$config = get_option('os_ticket_config');
	extract($config);
	$ost_wpdb = new wpdb($username, $password, $database, $host);	
	$getStaffID=$ost_wpdb->get_var("SELECT staff_id FROM ".$keyost_prefix."staff WHERE `email`='".$staff_email."'");
	return $getStaffID;
}
?>
