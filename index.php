<?php
// Authors:Jonathan Davis <davis@snickers.org>
//          Jon Nistor <nistor@snickers.org>
// Purpose:	Generate snickers.org profile entries in iOS

$o_name = NULL;
$o_username = NULL;
$o_email = NULL;

$o_name_m = NULL;
$o_email_m = NULL;

$generate = TRUE;

function gen_uuid(){
	return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		// 32 bits for "time_low"
		mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

		// 16 bits for "time_mid"
		mt_rand( 0, 0xffff ),

		// 16 bits for "time_hi_and_version",
		// four most significant bits holds version number 4
		mt_rand( 0, 0x0fff ) | 0x4000,

		// 16 bits, 8 bits for "clk_seq_hi_res",
		// 8 bits for "clk_seq_low",
		// two most significant bits holds zero and one for variant DCE1.1
		mt_rand( 0, 0x3fff ) | 0x8000,

		// 48 bits for "node"
		mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
	);
}

if( isset($_POST) ){
	if(!empty($_POST['name'])){
		$o_name = trim(strip_tags ($_POST['name']));
	}
	else {
		$generate = FALSE;
		$o_name_m = 'class="missing" ';
	}

	if(!empty($_POST['email'])){
		$o_email = trim(strip_tags($_POST['email']));

		if( !filter_var($o_email,FILTER_VALIDATE_EMAIL) ){
			$generate = FALSE;
			$o_email_m = 'class="missing" ';
		}

		$o_username = explode("@", $o_email)[0];
	}
	else {
		$generate = FALSE;
		$o_email_m = 'class="missing" ';
	}

	if( $generate ){
		$o_uuid1	= gen_uuid();
		$o_uuid2	= gen_uuid();
	}
}
else {
	$generate = FALSE;
}

$html = <<< EOHTMLF
<!DOCTYPE html>
<html lang="ja-JP">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">
<meta name="author" content="jonathan j davis@snickers.org, guredora contact:guredora.com">
<title>iOS Email Profile Generator</title>
<link rel="stylesheet" href="default.css">
</head>
<body>
<h1>iOS Email Profile Generator v1.1</h1>
<p>このページでは主にSFC CNSメールアカウントをIOSデバイスに設定するための構成プロファイルを生成することができます。</p>
<p>このページにIOSデバイスでアクセスすることで、サーバー情報などの入力を省略して、デバイスにCNSメールを設定することができます。</p>
<p>利用には事前にCNSアカウントを取得し、メールを利用可能にしておく必要があります。また、生成したプロファイルで行われた設定は突然利用できなくなることがあることに注意してください。利用はあくまで自己責任でお願いします。</p>
<p>IOSデバイスでこのページにアクセスし、以下の情報を入力して「submit」を押してください。入力に問題が無ければ、構成プロファイルが生成され自動的にダウンロードされます。</p>
<form name="profile_info" action="{$_SERVER['PHP_SELF']}" method="post">
<ul>
	<li>
		<label for="name" {$o_name_m}>表示名（自分の名前など）:</label>
		<input type="text" name="name" value="{$o_name}">
	</li>
	<li>
		<label for="email" {$o_email_m}>メールアドレス:</label>
		<input type="email" name="email" size="32" value="{$o_email}">
		<p>※ドメインがsfc.keio.ac.jpである必要があります。</p>
	</li>
	<li class="submit">
		 <input type="submit" value="submit" >
	</li>
</ul>
</form>
<p>Note: 構成プロファイルのインストール後に、メールパスワードの入力を求められます。CNSメールのパスワードを入力してください。</p>
<p>IOS14.4で動作を確認しています。</p>
<p>このページでは名前とメールアドレスの情報のみを元にプロファイルを生成しています。</p>
<footer>
<p> Created by <a href="https://github.com/jonathanjdavis/iosmailprofile"> Jonathan Davis </a> and modified by <a href="https://github.com/guredora403/iosmailprofile">Guredora</a></p>
</footer>
</body>
</html>
EOHTMLF;

if($generate){
$xml = <<< EOXMLF
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
	<key>PayloadContent</key>
	<array>
		<dict>
			<key>EmailAccountDescription</key>
			<string>sfc.keio.ac.jp</string>
			<key>EmailAccountName</key>
			<string>{$o_name}</string>
			<key>EmailAccountType</key>
			<string>EmailTypeIMAP</string>
			<key>EmailAddress</key>
			<string>{$o_email}</string>
			<key>IncomingMailServerAuthentication</key>
			<string>EmailAuthPassword</string>
			<key>IncomingMailServerHostName</key>
			<string>imap.sfc.keio.ac.jp</string>
			<key>IncomingMailServerIMAPPathPrefix</key>
			<string>/</string>
			<key>IncomingMailServerPortNumber</key>
			<integer>993</integer>
			<key>IncomingMailServerUseSSL</key>
			<true/>
			<key>IncomingMailServerUsername</key>
			<string>{$o_username}</string>
			<key>IncomingPassword</key>
			<string></string>
			<key>OutgoingMailServerAuthentication</key>
			<string>EmailAuthPassword</string>
			<key>OutgoingMailServerHostName</key>
			<string>smtp.sfc.keio.ac.jp</string>
			<key>OutgoingMailServerPortNumber</key>
			<integer>465</integer>
			<key>OutgoingMailServerUseSSL</key>
			<true/>
			<key>OutgoingMailServerUsername</key>
			<string>{$o_username}</string>
			<key>OutgoingPasswordSameAsIncomingPassword</key>
			<true/>

			<key>PayloadDescription</key>
			<string>Configures email account.</string>
			<key>PayloadDisplayName</key>
			<string>IMAP Account (SFC Email Account)</string>
			<key>PayloadIdentifier</key>
			<string>org.sfc.cns.email.profile.</string>
			<key>PayloadOrganization</key>
			<string>anonymous</string>
			<key>PayloadType</key>
			<string>com.apple.mail.managed</string>
			<key>PayloadUUID</key>
			<string>{$o_uuid1}</string>
			<key>PayloadVersion</key>
			<integer>1</integer>

			<key>PreventAppSheet</key>
			<false/>
			<key>PreventMove</key>
			<false/>
			<key>SMIMEEnabled</key>
			<false/>
		</dict>
	</array>
	<key>PayloadDescription</key>
	<string>SFC cns mail Configuration Profile for iOS Devices</string>
	<key>PayloadDisplayName</key>
	<string>{$o_email} Profile</string>
	<key>PayloadIdentifier</key>
	<string>org.sfc.cns.email.profile</string>
	<key>PayloadOrganization</key>
	<string>anonymous</string>
	<key>PayloadRemovalDisallowed</key>
	<false/>
	<key>PayloadType</key>
	<string>Configuration</string>
	<key>PayloadUUID</key>
	<string>{$o_uuid2}</string>
	<key>PayloadVersion</key>
	<integer>1</integer>
</dict>
</plist>
EOXMLF;
}

if($generate) {
	// header("Content-type: text/plain");
	// Modified per: http://www.rootmanager.com/iphone-ota-configuration/iphone-ota-setup-with-signed-mobileconfig.html
	header('Content-type: application/x-apple-aspen-config; chatset=utf-8');
	header('Content-Disposition: attachment; filename="cns.mobileconfig"');
	echo $xml;

} 
else {
	echo $html;
}

?>
