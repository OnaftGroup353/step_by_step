<?php
require_once "SmtpMail.php";
$mailSMTP = new SmtpMail('admin@m-creater.s-host.net', '123Qwe123', 'ssl://server10.shneider-host.ru', 465);
$headers = "Методички Online <admin@m-creater.s-host.net>"; // от кого письмо
//$mailTO = "by.wanderer.od@gmail.com"; //тут почта указанная при регистрации.
$subject = "Подтверждение регистрации";
$message = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</head>
<body style="padding:0px;margin:0px;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr><td align="center" bgcolor="#282f37"><div style="height: 30px; line-height:30px; font-size:28px;">&nbsp;</div>	
		<table width="100%" height="50px" border="0" cellspacing="0" cellpadding="0">
			<tr><td width="340" align="left" valign="top" style="line-height:15px;">
					
			</td></tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="0" width="600" style="min-width:600px;">
			<tr><td align="center" bgcolor="#ffffff" style="border-top-width:1px;border-top-style:solid;border-top-color:#ffffff;">
				<table width="600" border="0" cellspacing="0" cellpadding="0">
					<tr><td height="40" bgcolor="#E3F1FA" style="font-size:24px;font-family:cursive;font-style:italic;border-bottom:1px solid #d6b578;color:#ad3e23;text-align: center">Методички Online</td></tr>
				</table>
				<table width="490" border="0" cellspacing="0" cellpadding="0">
					<tr><td align="left">
						<div style="height: 45px; line-height:45px; font-size:40px;">&nbsp;</div>
						<div style="line-height:24px;">
							<font face="Tahoma, Arial, Helvetica, sans-serif" size="3" color="#282f37" style="font-size:16px;">
							<span style="font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 16px; color:#282f37;">
								<strong>Здравствуйте <Имя>!</strong><br /><br />
								Для создания или внесения изменений в методички Вам нужно подтвердить почту. 
							</span></font>
						</div>
						<div style="height: 25px; line-height:25px; font-size:23px;">&nbsp;</div>
						<table width="490" border="0" cellspacing="0" cellpadding="0">
							<tr><td width="210" align="left" valign="top">
							<img src="http://m-creater.s-host.net/assets/images/yellow_book.png" alt="45" border="0" width="150" height="150" style="display:block;"/></td>
							<td align="left" valign="middle" style="line-height:24px;">
								<font face="Tahoma, Arial, Helvetica, sans-serif" size="3" color="#282f37" style="font-size:16px;">
								<span style="font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 16px; color:#282f37;line-height:24px;">
									
								</span></font> 
								<div style="height: 26px; line-height:26px; font-size:23px;">&nbsp;</div>
								<table border="0" cellspacing="0" cellpadding="0">
									<tr><td align="center" width="217" height="39" >
										<a href="http://m-creater.s-host.net/confirmRegistration'.$confirmationCode.'"  target="_blank"><p style="color: #333333; font: 20px Arial, sans-serif; line-height: 30px; -webkit-text-size-adjust:none; display: block;" >Подтверждение</p></a>
									</td></tr>
								</table>
							</td></tr>
						</table>			
						<div style="height: 50px; line-height:50px; font-size:45px;">&nbsp;</div>	
					</td></tr>
				</table>
			</td></tr>
			<tr><td align="center" bgcolor="#282f37">
				<div style="height: 30px; line-height:30px; font-size:28px;">&nbsp;</div>	
				<table width="100%" height="40px" border="0" cellspacing="0" cellpadding="0">
					<tr><td width="340" align="left" valign="top" style="line-height:15px;">
						<font face="Tahoma, Arial, Helvetica, sans-serif" size="2" color="#929ca8" style="font-size:13px;">
						<span style="font-family: Tahoma, Arial, Helvetica, sans-serif; font-size: 13px; color:#929ca8;line-height:15px;">
							&copy; ООО &laquo;Онлайн методички &raquo; 2016<br> 
							<a href="http://m-creater.s-host.net" target="_blank" style="color:#929ca8;text-decoration:none;"><font color="#929ca8">m-creater.s-host.net</font></a><br><br>
						</span></font>
					</td></tr>
				</table>
				<div style="height: 30px; line-height:30px; font-size:28px;">&nbsp;</div>	
			</td></tr>
		</table>
	</td></tr>
</table>
			
</body>
</html>';
$result =  $mailSMTP->send($mailTO, $subject, $message, $headers);
/*
if($result === true)
    echo "Письмо успешно отправлено";
else
    echo "Письмо не отправлено. Ошибка: " . $result;
*/
?>