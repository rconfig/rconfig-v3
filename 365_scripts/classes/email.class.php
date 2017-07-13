<?php

/**
 * O365 XMl Email Notification Class
 * @version 0.1
 * @author Stephen Stack
 */

class email {
    
    public function __construct() {
        
    
    }
    
    
    /**
     * Transactions allow multiple changes to a database all in one batch.
     */
    public function adminNotification($message, $log) {
        require 'PHPMailer/PHPMailerAutoload.php';

        $mail = new PHPMailer;

        //$mail->SMTPDebug = 3;                               // Enable verbose debug output

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.office365.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'noreply@rconfig.com';                 // SMTP username
        $mail->Password = 'N0kia5110';                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to

        $mail->setFrom('noreply@rconfig.com', 'Mailer@rConfig');
        $mail->addAddress('noreply@rconfig.net', 'Stephen');     // Add a recipient
        $mail->addAddress('stephenstack@gmail.com');               // Name is optional
//        $mail->addReplyTo('info@example.com', 'Information');
//        $mail->addCC('cc@example.com');
//        $mail->addBCC('bcc@example.com');

//        $mail->addAttachment('/var/www/html/file.tar.gz');         // Add attachments
//        $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = 'Alert Notification from O365 XML Parser';
        $mail->Body    = $message;
//        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        if(!$mail->send()) {
            $message = 'Email Message could not be sentto administrator.';
            echo $message; echo br();
            echo 'Mailer Error: ' . $mail->ErrorInfo;
            $log->Fatal(basename(__FILE__)."(Line:".__LINE__."): ".$message);
        } else {
            $message =  'Message has been sent to administrator.';
            echo $message; echo br();
            $log->Fatal(basename(__FILE__)."(Line:".__LINE__."): ".$message);
        }


    }    

}