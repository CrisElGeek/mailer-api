<?php
namespace App\Helpers;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailerException;

abstract class Mailer {
	static public function Send($data) {
		$mail = new PHPMailer(true);
		try {
			//Server settings
			$mail->SMTPDebug = $GLOBALS['config']['mailer']['debug'];                      //Enable verbose debug output
			if($GLOBALS['config']['mailer']['smtp']) {
				$mail->isSMTP();                                            //Send using SMTP
			}
			$mail->Host       = $GLOBALS['config']['mailer']['host'];                     //Set the SMTP server to send through
			$mail->SMTPAuth   = $GLOBALS['config']['mailer']['smtp_auth'];                                   //Enable SMTP authentication
			$mail->Username   = $GLOBALS['config']['mailer']['user'];                     //SMTP username
			$mail->Password   = $GLOBALS['config']['mailer']['password'];                               //SMTP password
			$mail->SMTPSecure = $GLOBALS['config']['mailer']['smtp_secure'];         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
			$mail->Port       = $GLOBALS['config']['mailer']['port'];                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
			$mail->Timeout	=	15; // set the timeout (seconds)
			$mail->CharSet = 'UTF-8';

			//Recipients
			$mail->setFrom($data['from']['email'], $data['from']['name']);
			$mail->addReplyTo($data['from']['email'], $data['from']['name']);
			foreach($data['to'] as $to) {
				$mail->addAddress($to['email'], $to['name']);     //Add a recipient
			}
			if($data['cc'] && count($data['cc']) > 0) {
				foreach($data['cc'] as $cc) {
					$mail->addCC($cc['email'], $cc['name']);
				}
			}
			if($data['bcc'] && count($data['bcc']) > 0) {
				foreach($data['bcc'] as $bcc) {
					$mail->addBCC($bcc['email'], $bcc['name']);
				}
			}
			if($data['attachments'] && count($data['attachments']) > 0) {
				foreach($data['attachments'] as $attach) {
					$mail->addAttachment($attach['file'], $attach['name']);
				}
			}

			//Content
			$mail->isHTML(true);                                  //Set email format to HTML
			$mail->Subject = $data['subject'];
			$mail->Body    = $data['body'];
			$mail->AltBody = $data['alt_body'];

			$mail->send();
		} catch (Exception $e) {
				throw new \Exception('Message could not be sent. Mailer error ' .$mail->ErrorInfo, 1);
		}
	}
}
?>
