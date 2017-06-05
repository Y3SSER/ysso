<?php
define('API_KEY','توکن شما');
//----######------
function makereq($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($datas));
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}
//##############=--API_REQ
function apiRequest($method, $parameters) {
  if (!is_string($method)) {
    error_log("Method name must be a string\n");
    return false;
  }
  if (!$parameters) {
    $parameters = array();
  } else if (!is_array($parameters)) {
    error_log("Parameters must be an array\n");
    return false;
  }
  foreach ($parameters as $key => &$val) {
    // encoding to JSON array parameters, for example reply_markup
    if (!is_numeric($val) && !is_string($val)) {
      $val = json_encode($val);
    }
  }
  $url = "https://api.telegram.org/bot".API_KEY."/".$method.'?'.http_build_query($parameters);
  $handle = curl_init($url);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($handle, CURLOPT_TIMEOUT, 60);
  return exec_curl_request($handle);
}
//----######------
//---------
$update = json_decode(file_get_contents('php://input'));
var_dump($update);
//=========
$chat_id = $update->message->chat->id;
$message_id = $update->message->message_id;
$from_id = $update->message->from->id;
$name = $update->message->from->first_name;
$username = $update->message->from->username;
$textmessage = isset($update->message->text)?$update->message->text:'';
$txtmsg = $update->message->text;
$reply = $update->message->reply_to_message->forward_from->id;
$stickerid = $update->message->reply_to_message->sticker->file_id;
$admin =  ;
$step = file_get_contents("data/".$from_id."/step.txt");
$ban = file_get_contents('data/banlist.txt');
//-------
function SendMessage($ChatId, $TextMsg)
{
 makereq('sendMessage',[
'chat_id'=>$ChatId,
'text'=>$TextMsg,
'parse_mode'=>"MarkDown"
]);
}
function SendSticker($ChatId, $sticker_ID)
{
 makereq('sendSticker',[
'chat_id'=>$ChatId,
'sticker'=>$sticker_ID
]);
}
function Forward($KojaShe,$AzKoja,$KodomMSG)
{
makereq('ForwardMessage',[
'chat_id'=>$KojaShe,
'from_chat_id'=>$AzKoja,
'message_id'=>$KodomMSG
]);
}
function save($filename,$TXTdata)
	{
	$myfile = fopen($filename, "w") or die("Unable to open file!");
	fwrite($myfile, "$TXTdata");
	fclose($myfile);
	}
//===========
$inch = file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=@Channel&user_id=".$from_id);
	
	if (strpos($inch , '"status":"left"') !== false ) {
SendMessage($chat_id," لاستخدام اول بوت يجب الاشتراك في القناه لدينا
@ll011ll");
}
if (strpos($ban , "$from_id") !== false  ) {
SendMessage($chat_id,"You Are Banned From Server.🤓\nDon't Message Again...😎\n➖➖➖➖➖➖➖➖➖➖\nدسترسی شما به این سرور مسدود شده است.🤓\nلطفا پیام ندهید...😎");
	}

elseif(isset($update->callback_query)){
    $callbackMessage = '';
    var_dump(makereq('answerCallbackQuery',[
        'callback_query_id'=>$update->callback_query->id,
        'text'=>$callbackMessage
    ]));
    $chat_id = $update->callback_query->message->chat->id;
    
    $message_id = $update->callback_query->message->message_id;
    $data = $update->callback_query->data;
    if (strpos($data, "del") !== false ) {
    $botun = str_replace("del ","",$data);
    unlink("bots/".$botun."/index.php");
    save("data/$chat_id/bots.txt","");
    save("data/$chat_id/tedad.txt","0");
    var_dump(
        makereq('editMessageText',[
            'chat_id'=>$chat_id,
            'message_id'=>$message_id,
            'text'=>"تم حذف البوت بنجاح ✔️ !\n Robot has ben deleted!",
            'reply_markup'=>json_encode([
                'inline_keyboard'=>[
                    [
                        ['text'=>" تابع جديدنا 📢 ",'url'=>"https://telegram.me/ll011ll"]
                    ]
                ]
            ])
        ])
    );
 }
 else {
    var_dump(
        makereq('editMessageText',[
            'chat_id'=>$chat_id,
            'message_id'=>$message_id,
            'text'=>"خطا",
            'reply_markup'=>json_encode([
                'inline_keyboard'=>[
                    [
                        ['text'=>" تابع جديدنا 📢 ",'url'=>"https://telegram.me/ll011ll"]
                    ]
                ]
            ])
        ])
    );
 }
}

elseif ($textmessage == '🔙 رجوع ') {
save("data/$from_id/step.txt","none");
var_dump(makereq('sendMessage',[
        	'chat_id'=>$update->message->chat->id,
        	'text'=>"🔃 اهلا وسهلا بك في القائمه الرئيسيه

🔃 Welcome To Main Menu",
		'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
            	'keyboard'=>[
  [
                   ['text'=>"فارسی 🇮🇷"],['text'=>"English 🇦🇺"]          
]
                
            	],
            	'resize_keyboard'=>true
       		])
    		]));
}
elseif ($step == 'delete') {
$botun = $txtmsg ;
if (file_exists("bots/".$botun."/index.php")) {

$src = file_get_contents("bots/".$botun."/index.php");

if (strpos($src , $from_id) !== false ) {
save("data/$from_id/step.txt","none");
unlink("bots/".$botun."/index.php");
var_dump(makereq('sendMessage',[
        	'chat_id'=>$update->message->chat->id,
        	'text'=>"🚀 حذفت البوت بنجاح 
بوت جديد😄
Yur Robot has ben deleted 
Please create new bot",
		'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
            	'keyboard'=>[
                [
                   ['text'=>"/start"]
                ]
                
            	],
            	'resize_keyboard'=>true
       		])
    		]));
}
else {
SendMessage($chat_id,"خطا!
لايمكنك حذف البوت الخاص بك !
Error 
You cant delete this bot");
}
}
else {
SendMessage($chat_id,"لم يتم العثور على");
}
}
elseif ($step == 'create bot') {
$token = $textmessage ;

			$userbot = json_decode(file_get_contents('https://api.telegram.org/bot'.$token .'/getme'));
			//==================
			function objectToArrays( $object ) {
				if( !is_object( $object ) && !is_array( $object ) )
				{
				return $object;
				}
				if( is_object( $object ) )
				{
				$object = get_object_vars( $object );
				}
			return array_map( "objectToArrays", $object );
			}

	$resultb = objectToArrays($userbot);
	$un = $resultb["result"]["username"];
	$ok = $resultb["ok"];
		if($ok != 1) {
			//Token Not True
			SendMessage($chat_id,"توکن نا معتبر!\nYour token is invalid");
		}
		else
		{
		SendMessage($chat_id,"در حال ساخت ربات ...\n Are making robots...");
		if (file_exists("bots/$un/index.php")) {
		$source = file_get_contents("bot/index.php");
		$source = str_replace("**TOKEN**",$token,$source);
		$source = str_replace("**ADMIN**",$from_id,$source);
		save("bots/$un/index.php",$source);	
		file_get_contents("http://api.telegram.org/bot".$token."/setwebhook?url=https://webhook/bots/$un/index.php");

var_dump(makereq('sendMessage',[
        	'chat_id'=>$update->message->chat->id,
        	'text'=>"🚀 تم صنع البوت الخاص بك بنجاح
[اضغط للدخول الى البوت 😃 - start Bot](https://telegram.me/$un)",
		'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
            	'keyboard'=>[
                [
                   ['text'=>"🔙 برگشت - Back"]
                ]
                
            	],
            	'resize_keyboard'=>true
       		])
    		]));
		}
		else {
		save("data/$from_id/tedad.txt","1");
		save("data/$from_id/step.txt","none");
		save("data/$from_id/bots.txt","$un");
		
		mkdir("bots/$un");
		mkdir("bots/$un/data");
		mkdir("bots/$un/data/btn");
		mkdir("bots/$un/data/words");
		mkdir("bots/$un/data/profile");
		mkdir("bots/$un/data/setting");
		
		save("bots/$un/data/blocklist.txt","");
		save("bots/$un/data/last_word.txt","");
		save("bots/$un/data/pmsend_txt.txt","Message Sent!");
		save("bots/$un/data/start_txt.txt","Hello World!");
		save("bots/$un/data/forward_id.txt","");
		save("bots/$un/data/users.txt","$from_id\n");
		mkdir("bots/$un/data/$from_id");
		save("bots/$un/data/$from_id/type.txt","admin");
		save("bots/$un/data/$from_id/step.txt","none");
		
		save("bots/$un/data/btn/btn1_name","");
		save("bots/$un/data/btn/btn2_name","");
		save("bots/$un/data/btn/btn3_name","");
		save("bots/$un/data/btn/btn4_name","");
		
		save("bots/$un/data/btn/btn1_post","");
		save("bots/$un/data/btn/btn2_post","");
		save("bots/$un/data/btn/btn3_post","");
		save("bots/$un/data/btn/btn4_post","");
	
		save("bots/$un/data/setting/sticker.txt","✅");
		save("bots/$un/data/setting/video.txt","✅");
		save("bots/$un/data/setting/voice.txt","✅");
		save("bots/$un/data/setting/file.txt","✅");
		save("bots/$un/data/setting/photo.txt","✅");
		save("bots/$un/data/setting/music.txt","✅");
		save("bots/$un/data/setting/forward.txt","✅");
		save("bots/$un/data/setting/joingp.txt","✅");
		
		$source = file_get_contents("bot/index.php");
		$source = str_replace("**TOKEN**",$token,$source);
		$source = str_replace("**ADMIN**",$from_id,$source);
		save("bots/$un/index.php",$source);	
		file_get_contents("http://api.telegram.org/bot".$token."/setwebhook?url=https://webhook/bots/$un/index.php");

var_dump(makereq('sendMessage',[
        	'chat_id'=>$update->message->chat->id,
        	'text'=>"🚀 تم صنع البوت الخاص بك بنجاح
[اضغط للدخول الى البوت الخاص بك 😃 - start Bot](https://telegram.me/$un)",		
                'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
            	'keyboard'=>[
                [
                   ['text'=>"🔙 برگشت - Back"]
                ]
                
            	],
            	'resize_keyboard'=>true
       		])
    		]));
		}
}
}
elseif (strpos($textmessage, "/setvip") !== false) {
$botun = str_replace("/setvip ","",$textmessage);
SendMessage($chat_id,"$textmessage");
/*$src = file_get_contents("bots/$botun/index.php");
$nsrc = str_replace("**free**","gold",$src);
save("data/$botun/index.php",$nsrc);
SendMessage($chat_id,"Updated!");*/
}
elseif (strpos($textmessage , "/toall") !== false ) {
if ($from_id == $admin) {
$text = str_replace("/toall","",$textmessage);
$fp = fopen( "data/users.txt", 'r');
while( !feof( $fp)) {
 $users = fgets( $fp);
SendMessage($users,"$text");
}
}
else {
SendMessage($chat_id,"You Are Not Admin");
}
}
elseif (strpos($textmessage , "/feedback") !== false ) {
if ($from_id == $ban) {
$text = str_replace("/feedback","",$textmessage);
SendMessage($chat_id,"تم ارسال ملاحضاتك");
SendMessage($admin,"FeedBack : \n name: $name \n username: $username \n id: $from_id\n Text: $text");
}
else {
SendMessage($chat_id,"You Are banned");
}
}
elseif (strpos($textmessage , "/report") !== false ) {
if ($from_id == $ban) {
$text = str_replace("/report","",$textmessage);
SendMessage($chat_id,"اكد البوت في وقت لاحق");
SendMessage($admin,"Report : \n name: $name \n username: $username \n id: $from_id\n Bot: $text");
}
else {
SendMessage($chat_id,"You Are banned");
}
}
elseif($textmessage == '🚀 البوتات')
{
$botname = file_get_contents("data/$from_id/bots.txt");
if ($botname == "") {
SendMessage($chat_id," لم يتم تفعيل بوتات!");
return;
}
 	var_dump(makereq('sendMessage',[
	'chat_id'=>$update->message->chat->id,
	'text'=>"قائمه البوتات : ",
	'parse_mode'=>'MarkDown',
	'reply_markup'=>json_encode([
	'inline_keyboard'=>[
	[
	['text'=>"👉 @".$botname,'url'=>"https://telegram.me/".$botname]
	]
	]
	])
	]));
}
elseif($textmessage == '🚀 my robots')
{
$botname = file_get_contents("data/$from_id/bots.txt");
if ($botname == "") {
SendMessage($chat_id,"You still have not robots!");
return;
}
 	var_dump(makereq('sendMessage',[
	'chat_id'=>$update->message->chat->id,
	'text'=>"Your Bot Lists : ",
	'parse_mode'=>'MarkDown',
	'reply_markup'=>json_encode([
	'inline_keyboard'=>[
	[
	['text'=>"👉 @".$botname,'url'=>"https://telegram.me/".$botname]
	]
	]
	])
	]));
}
elseif($textmessage == '/start' )
{
if (!file_exists("data/$from_id/step.txt")) {
mkdir("data/$from_id");
save("data/$from_id/step.txt","none");
save("data/$from_id/tedad.txt","0");
save("data/$from_id/bots.txt","");
$myfile2 = fopen("data/users.txt", "a") or die("Unable to open file!"); 
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
}

var_dump(makereq('sendMessage',[
         'chat_id'=>$update->message->chat->id,
         'text'=>"
➖➖➖➖➖
اختر لغتك .",
  'parse_mode'=>'MarkDown',
         'reply_markup'=>json_encode([
             'keyboard'=>[
[
                   ['text'=>"عربي 🇮🇶"],['text'=>"English 🇦🇺"]          
]
             ],
             'resize_keyboard'=>true
         ])
      ]));
}
elseif($textmessage == '🇦🇺 تغير لغه 🇮🇶' )
{
if (!file_exists("data/$from_id/step.txt")) {
mkdir("data/$from_id");
save("data/$from_id/step.txt","none");
save("data/$from_id/tedad.txt","0");
save("data/$from_id/bots.txt","");
$myfile2 = fopen("data/users.txt", "a") or die("Unable to open file!"); 
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
}

var_dump(makereq('sendMessage',[
         'chat_id'=>$update->message->chat->id,
         'text'=>"
➖➖➖➖➖
اختر لغتك ",
  'parse_mode'=>'MarkDown',
         'reply_markup'=>json_encode([
             'keyboard'=>[
[
['text'=>"عربي 🇮🇶"],['text'=>"English 🇦🇺"]]
             ],
             'resize_keyboard'=>true
         ])
      ]));
}
elseif($textmessage == '🇦🇺 Language 🇮🇷' )
{
if (!file_exists("data/$from_id/step.txt")) {
mkdir("data/$from_id");
save("data/$from_id/step.txt","none");
save("data/$from_id/tedad.txt","0");
save("data/$from_id/bots.txt","");
$myfile2 = fopen("data/users.txt", "a") or die("Unable to open file!"); 
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
}

var_dump(makereq('sendMessage',[
         'chat_id'=>$update->message->chat->id,
         'text'=>"
➖➖➖➖➖
اختر لغتك ",
  'parse_mode'=>'MarkDown',
         'reply_markup'=>json_encode([
             'keyboard'=>[
[
['text'=>"عربي 🇮🇶"],['text'=>"English 🇦🇺"]]
             ],
             'resize_keyboard'=>true
         ])
      ]));
}
elseif($textmessage == 'English 🇦🇺')
{

if (!file_exists("data/$from_id/step.txt")) {
mkdir("data/$from_id");
save("data/$from_id/step.txt","none");
save("data/$from_id/tedad.txt","0");
save("data/$from_id/bots.txt","");
$myfile2 = fopen("data/users.txt", "a") or die("Unable to open file!"); 
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
}

var_dump(makereq('sendMessage',[
         'chat_id'=>$update->message->chat->id,
         'text'=>"Hello👋😉

🔹 WellCome To PvResan Robot 🌹

🔸 with this Service you can Provide Support  your Robot Mmbers , Channel , Groups and  Websites 

🔹 To Create Robot Select '🔄 Create a Robot' Button!",
  'parse_mode'=>'MarkDown',
         'reply_markup'=>json_encode([
             'keyboard'=>[
                [
                   ['text'=>"🔄 Create a Robot"],['text'=>"🚀 my robots"],['text'=>"☢ Delete a Robot"]
                ],
                [
                   ['text'=>"ℹ️ help"],['text'=>"🔰 rules"]
                ],
                     [
                   ['text'=>"✅ Send Comment"],['text'=>"⚠️ Robot Report"]
                ],
[
                   ['text'=>"🇦🇺 Language 🇮🇷"]          
]
             ],
             'resize_keyboard'=>true
         ])
      ]));
}
elseif($textmessage == 'عربي 🇮🇶' )
{

if (!file_exists("data/$from_id/step.txt")) {
mkdir("data/$from_id");
save("data/$from_id/step.txt","none");
save("data/$from_id/tedad.txt","0");
save("data/$from_id/bots.txt","");
$myfile2 = fopen("data/users.txt", "a") or die("Unable to open file!"); 
fwrite($myfile2, "$from_id\n");
fclose($myfile2);
}

var_dump(makereq('sendMessage',[
         'chat_id'=>$update->message->chat->id,
         'text'=>"مرحبا👋😉

🔹 نرحب بك في بوت التواصل 🌹.

🔸🔸 مع هذه الخدمة يمكنك تقديم دعم البوت الخاص بك، قناة والجماعات والمواقع الإلكترونية.

🔹انشاء بوت اختار 🔄  .",
  'parse_mode'=>'MarkDown',
         'reply_markup'=>json_encode([
             'keyboard'=>[
                [
                   ['text'=>"انشا بوت 🔄"],['text'=>"البوتات 🚀"],['text'=>"☢️ حذف بوت"]
                ],
                [
                   ['text'=>"ℹ️ مساعده"],['text'=>"🔰 قوانین"]
                ],
                [
                   ['text'=>"✅تعليق "],['text'=>"⚠️ ابلاغ عن اساءة استخدام "]
                ],
           [
                ['text'=>"🇦🇺 تغییر لغه 🇮🇶"]
            ]
                
             ],
             'resize_keyboard'=>true
         ])
      ]));
}
elseif($textmessage == '🔰 قوانین') {
SendMessage($chat_id, "1⃣ اطلاعات ثبت شده در ربات های ساخته شده توسط پیوی رسان از قبیل اطلاعات پروفایل نزد مدیران پیوی رسان محفوظ است و در اختیار اشخاص حقیقی یا حقوقی قرار نخواهد گرفت.\n\n2⃣ ربات هایی که اقدام به انشار تصاویر یا مطالب مستهجن کنند و یا به مقامات ایران ، ادیان و اقوام و نژادها توهین کنند مسدود خواهند شد.\n\n3⃣ ایجاد ربات با عنوان های مبتذل و خارج از عرف جامعه که برای جذب آمار و فروش محصولات غیر متعارف باشند ممنوع می باشد و در صورت مشاهده ربات مورد نظر حذف و مسدود میشود.\n\n4⃣ مسئولیت پیام های رد و بدل شده در هر ربات با مدیر آن ربات میباشد و پیوی رسان هیچ گونه مسئولیتی قبول نمیکند.\n\n5⃣ رعایت حریم خصوصی و حقوقی افراد از جمله، عدم اهانت به شخصیت های مذهبی، سیاسی، حقیقی و حقوقی کشور و به ویژه کاربران ربات ضروری می باشد.\n\n6⃣ ساخت هرگونه ربات و فعالیت در ضمینه های Hacking و Sexology خلاف قوانین است در صورت برخورد دستری مدیر ربات و ربات ساخته شده به سرور ما مسدود خواهد شد.\n\n7⃣ در صورت مشاهده استفاده از قابلیت های ربات در جهات منفی مانند Spam یا Hack کاربران تلگرام شدیدا بخورد میشود و تمامی اطلاعات شخص مورد نظر گزارش میشود.\n\n8⃣ اگر تعداد درخواست های ربات شما به سرور ما بیش از حد معمول باید و همچنین ربات شما VIP نباشد چند بار به شما اخطاری جهت VIP کردن ربات نمایش داده میشود اگر این اخطار نادیده گرفته شود، ربات شما به صورت اتوماتیک به حالت تعلیغ در.");
}
elseif($textmessage == '🔰 rules') {
SendMessage($chat_id, "1⃣ Recorded data in robots made by PvResan such as profile data , are preserved to PvResan's managers and will not be available for real or juridical people.\n\n 2⃣ Robots that publish obscene pictures or subjects and insult the officials , religions and nations and races , will be blocked.\n\n3⃣ Creating a robot with vulgar titles and out of norm of society which absorbs the statistics and selling offbeat products are prohibited and in case of witnessing  intended robot will be deleted and blocked.\n\n4⃣ The responsibility of exchanged messages in each robot is with the manager of that robot and PvResan does not accept any responsibilities.\n\n5⃣ Respecting the privacy and rights of individuals is necessary. Including no offense to religious , political and juridical figures of the country specially robot users.");
}
elseif($textmessage == 'ℹ️ راهنما') {
SendMessage($chat_id, "ربات پیام رسان چیست؟ 🤔\n\n🔶 ربات پیام رسان این امکان را به شما میدهد تا با مخاطب هایتان که ریپورت شده ایند یا به هر دلایلی نمیتواننید به شما به صورت خصوصی پیام دهند صحبت کنید.\n\nبرخی از ویژگی های ربات پیام رسان :\n\n🚀سرعت بـــــالــا\n1⃣ ارسال مطلب به تمام کاربران یا تمام گروه ها\n2⃣ قفل کردن ارسال عکس ، فیلم ، استیکر ، فایل ، وویس ، آهنگ به صورت مجزا\n3⃣ قفل کردن فروارد در ربات\n4⃣ قفل کردن عضویت ربات در گروه ها\n5⃣مشاهده تعداد اعضا و گروه ها و یوزرنیم 10 کاربر تازه عضو شده\n6⃣مشاهده تعداد اعضا در لیست سیاه و یوزرنیم 10 کاربر جدید در لیست سیاه\n7⃣ قرار دادن افراد در لیست سیاه و بن کردن آنها\n8⃣ قابلیت شیر کردن شماره ی شما به صورت سریع\n9⃣تغییر متن استارت و پیام پیشفرض\nو چندین ویژگی دیگر...");
}
elseif($textmessage == 'ℹ️ help') {
SendMessage($chat_id, "What PvResan do?🤔\n\n🔶 With this Service you can Provide Support  your Robot Mmbers , Channel , Groups and  Websites\n\nSome of this Service Features :\n\n🚀 Fast Server\n\n1⃣ Send Mesage To Members Or Groups Or Both\n2⃣ Lock Sending Photos , Videos , Stickers , Documents , Voices and Audios Separately\n3⃣ Lock Forward To your Robot\n4⃣ Lock Adding Your Robots To Groups\n5⃣ Check Robot Membes And Groups\n6⃣ Check Your Black List\n7⃣ Put Members To Black List\n8⃣ Fast Share Your Contact\n9⃣ Change Your Start Text\nAnd several other features ...\n\n🔴 For information about how to get a token from @botFather use");
}
elseif($textmessage == '✅ ارسال نظر' ) {
SendMessage($chat_id, "🖊 نظرات شما به پیشرفت و ارائه خدمات بهتر کمک میکند.\n\n📝 برای ارسال نظر خوت از دستور زیر استفاده نمایید.\n\n/feedback (پیام خود را بنویسید)");
}
	elseif($textmessage == '⚠️ گزارش تخلف' ) {
SendMessage($chat_id, "اگر رباتی بر خلاف قوانین ما عمل میکند با دستور زیر به ما خبر دهید\n/report (usernamebot)");
}
elseif($textmessage == '✅ Send Comment') {
SendMessage($chat_id, "🖊 Your Comments Help Us To Be Better.\n\n📝 To Send Comment Use this Command Below.\n\n/feedback (Your Message)");
}
	elseif($textmessage ==  '⚠️ Robot Report') {
SendMessage($chat_id, "If the robot to act contrary to our laws let us know using the following command\n/report (usernamebot)");
}
elseif ($textmessage == '☢ حذف ربات' ) {
if (file_exists("data/$from_id/step.txt")) {

}
$botname = file_get_contents("data/$from_id/bots.txt");
if ($botname == "") {
SendMessage($chat_id,"لم يتم تفعيل البوتات !");

}
else {
//save("data/$from_id/step.txt","delete");


 	var_dump(makereq('sendMessage',[
	'chat_id'=>$update->message->chat->id,
	'text'=>"اختر بوت من البوتات الخاصه بك :",
	'parse_mode'=>'MarkDown',
	'reply_markup'=>json_encode([
	'inline_keyboard'=>[
	[
	['text'=>"👉 @".$botname,'callback_data'=>"del ".$botname]
	]
	]
	])
	]));

/*
var_dump(makereq('sendMessage',[
        	'chat_id'=>$update->message->chat->id,
        	'text'=>"اختر واحد من البوتات لمسح الاختيار",
		'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
            	'keyboard'=>[
            	[
            	['text'=>$botname]
            	],
                [
                   ['text'=>"🔙 رجوع"]
                ]
                
            	],
            	'resize_keyboard'=>true
       		])
    		])); */
}
}
elseif ($textmessage == '☢ Delete a Robot' ) {
if (file_exists("data/$from_id/step.txt")) {

}
$botname = file_get_contents("data/$from_id/bots.txt");
if ($botname == "") {
SendMessage($chat_id,"Do robots have not");

}
else {
//save("data/$from_id/step.txt","delete");


 	var_dump(makereq('sendMessage',[
	'chat_id'=>$update->message->chat->id,
	'text'=>"Select one of your robots:",
	'parse_mode'=>'MarkDown',
	'reply_markup'=>json_encode([
	'inline_keyboard'=>[
	[
	['text'=>"👉 @".$botname,'callback_data'=>"del ".$botname]
	]
	]
	])
	]));

/*
var_dump(makereq('sendMessage',[
        	'chat_id'=>$update->message->chat->id,
        	'text'=>"اختر واحد من البوتات لمسح ",
		'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
            	'keyboard'=>[
            	[
            	['text'=>$botname]
            	],
                [
                   ['text'=>"🔙 رجوع"]
                ]
                
            	],
            	'resize_keyboard'=>true
       		])
    		])); */
}
}
elseif ($textmessage == '☢ Delete a Robot' ) {
if (file_exists("data/$from_id/step.txt")) {

}
$botname = file_get_contents("data/$from_id/bots.txt");
if ($botname == "") {
SendMessage($chat_id,"Do robots have not");

}
else {
//save("data/$from_id/step.txt","delete");


 	var_dump(makereq('sendMessage',[
	'chat_id'=>$update->message->chat->id,
	'text'=>"Select one of your robots:",
	'parse_mode'=>'MarkDown',
	'reply_markup'=>json_encode([
	'inline_keyboard'=>[
	[
	['text'=>"👉 @".$botname,'callback_data'=>"del ".$botname]
	]
	]
	])
	]));

/*
var_dump(makereq('sendMessage',[
        	'chat_id'=>$update->message->chat->id,
        	'text'=>"اختر واحد من البوتات لمسح ",
		'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
            	'keyboard'=>[
            	[
            	['text'=>$botname]
            	],
                [
                   ['text'=>"🔙 رجوع"]
                ]
                
            	],
            	'resize_keyboard'=>true
       		])
    		])); */
}
}
elseif ($textmessage == '🔄 انشاء بوت' ) {

$tedad = file_get_contents("data/$from_id/tedad.txt");
if ($tedad >= 1) {
SendMessage($chat_id,"لديك بوتات كثيره !
يجب حذف البوت اولا ! $tedad");
return;
}
save("data/$from_id/step.txt","create bot");
var_dump(makereq('sendMessage',[
        	'chat_id'=>$update->message->chat->id,
        	'text'=>"ارسل توكن :",
		'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
            	'keyboard'=>[
                [
                   ['text'=>"🔙 رجوع"]
                ]
                
            	],
            	'resize_keyboard'=>true
       		])
    		]));
}
elseif ($textmessage == '🔄 Create a Robot') {

$tedad = file_get_contents("data/$from_id/tedad.txt");
if ($tedad >= 1) {
SendMessage($chat_id," The number of robots you $ tedad
Each person can only build other robots you can build a robot");
return;
}
save("data/$from_id/step.txt","create bot");
var_dump(makereq('sendMessage',[
        	'chat_id'=>$update->message->chat->id,
        	'text'=>"Please send your token ",
		'parse_mode'=>'MarkDown',
        	'reply_markup'=>json_encode([
            	'keyboard'=>[
                [
                   ['text'=>"🔙 برگشت - Back"]
                ]
                
            	],
            	'resize_keyboard'=>true
       		])
    		]));
}
	elseif (strpos($textmessage , "/ban" ) !== false ) {
if ($from_id == $admin) {
$text = str_replace("/ban","",$textmessage);
$myfile2 = fopen("data/banlist.txt", 'a') or die("Unable to open file!");	
fwrite($myfile2, "$text\n");
fclose($myfile2);
SendMessage($admin,"المستخدم $text لديك في قائمه الحظر!\n لازاله المستخدم من قائمه الحظر\n/unban $text");
}
else {
SendMessage($chat_id,"⛔️ انت ليس ادمن.");
}
}

elseif (strpos($textmessage , "/unban" ) !== false ) {
if ($from_id == $admin) {
$text = str_replace("/unban","",$textmessage);
			$newlist = str_replace($text,"",$ban);
			save("data/banlist.txt",$newlist);
SendMessage($admin,"المستخدم $text تم ازالته من قائمه الحظر");
}
else {
SendMessage($chat_id,"⛔️ انت ليس ادمن.");
}
}
	
else
{
SendMessage($chat_id,"تعذر العثور ع رسالتك❌");
}
?>
