<?php
require '../../imports/src/Exception.php';
require '../../imports/src/PHPMailer.php';
require '../../imports/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmail($to, $subject, $body, $username, &$error = null) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'labacelemschool@gmail.com';
        $mail->Password = 'hszkwyrssrcagdda';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('labacelemschool@gmail.com', 'Hire Path System');
        $mail->addAddress($to);
        $mail->Subject = $subject;

        
        if (is_array($body)) {
            if (!empty($body['rejected'])) {
                
                $mail->Body = "
                    <div style='font-family:Poppins,Arial,sans-serif;max-width:440px;margin:auto;background:#fff;border-radius:10px;box-shadow:0 2px 12px rgba(220,53,69,0.10);padding:32px 24px;'>
                        <h2 style='color:#dc3545;text-align:center;margin-bottom:18px;'>Application Update</h2>
                        <p style='font-size:16px;color:#222;text-align:center;margin-bottom:18px;'>
                            Dear <strong>" . htmlspecialchars($body['username']) . "</strong>,
                        </p>
                        <p style='font-size:15px;color:#333;text-align:center;margin-bottom:18px;'>
                            We appreciate your interest in <strong style='color:#144272;'>" . htmlspecialchars($body['company_name']) . "</strong> and your application for <strong style='color:#007bff;'>" . htmlspecialchars($body['job_title']) . "</strong>.
                        </p>
                        <p style='font-size:14px;color:#444;text-align:center;margin-bottom:22px;'>
                            After careful consideration, we regret to inform you that your application was not selected for this position.
                        </p>
                        <div style='margin-top:24px;text-align:center;'>
                            <span style='display:inline-block;background:#dc3545;color:#fff;padding:8px 24px;border-radius:6px;font-size:14px;'>Best wishes,<br><strong>" . htmlspecialchars($body['company_name']) . "</strong></span>
                        </div>
                        <hr style='border:none;border-top:1px solid #eee;margin:28px 0 14px 0;'>
                        <p style='font-size:12px;color:#aaa;text-align:center;'>This is an automated email from Hire Path. Please do not reply to this message.</p>
                    </div>
                ";
            } elseif (!empty($body['reviewed'])) {
                
                $mail->Body = "
                    <div style='font-family:Poppins,Arial,sans-serif;max-width:440px;margin:auto;background:#fff;border-radius:10px;box-shadow:0 2px 12px rgba(255,193,7,0.10);padding:32px 24px;'>
                        <h2 style='color:#ffc107;text-align:center;margin-bottom:18px;'>Application Update</h2>
                        <p style='font-size:16px;color:#222;text-align:center;margin-bottom:18px;'>
                            Dear <strong>" . htmlspecialchars($body['username']) . "</strong>,
                        </p>
                        <p style='font-size:15px;color:#333;text-align:center;margin-bottom:18px;'>
                            Your application for <strong style='color:#007bff;'>" . htmlspecialchars($body['job_title']) . "</strong> at <strong style='color:#144272;'>" . htmlspecialchars($body['company_name']) . "</strong> is currently <span style='color:#ffc107;font-weight:bold;'>under review</span>.
                        </p>
                        <p style='font-size:14px;color:#444;text-align:center;margin-bottom:22px;'>
                            We appreciate your patience. You will be notified once a decision has been made.
                        </p>
                        <div style='margin-top:24px;text-align:center;'>
                            <span style='display:inline-block;background:#ffc107;color:#fff;padding:8px 24px;border-radius:6px;font-size:14px;'>Best regards,<br><strong>" . htmlspecialchars($body['company_name']) . "</strong></span>
                        </div>
                        <hr style='border:none;border-top:1px solid #eee;margin:28px 0 14px 0;'>
                        <p style='font-size:12px;color:#aaa;text-align:center;'>This is an automated email from Hire Path. Please do not reply to this message.</p>
                    </div>
                ";
            } else {
                
                $mail->Body = "
                    <div style='font-family:Poppins,Arial,sans-serif;max-width:440px;margin:auto;background:#fff;border-radius:10px;box-shadow:0 2px 12px rgba(20,66,114,0.10);padding:32px 24px;'>
                        <h2 style='color:#144272;text-align:center;margin-bottom:18px;'>Congratulations!</h2>
                        <p style='font-size:16px;color:#222;text-align:center;margin-bottom:18px;'>
                            Dear <strong>" . htmlspecialchars($body['username']) . "</strong>,
                        </p>
                        <p style='font-size:15px;color:#333;text-align:center;margin-bottom:18px;'>
                            Your application for <strong style='color:#007bff;'>" . htmlspecialchars($body['job_title']) . "</strong> at <strong style='color:#144272;'>" . htmlspecialchars($body['company_name']) . "</strong> has been <span style='color:#28a745;font-weight:bold;'>approved</span>.
                        </p>
                        <p style='font-size:14px;color:#444;text-align:center;margin-bottom:22px;'>
                            We will contact you soon with the next steps.
                        </p>
                        <div style='margin-top:24px;text-align:center;'>
                            <span style='display:inline-block;background:#144272;color:#fff;padding:8px 24px;border-radius:6px;font-size:14px;'>Best regards,<br><strong>" . htmlspecialchars($body['company_name']) . "</strong></span>
                        </div>
                        <hr style='border:none;border-top:1px solid #eee;margin:28px 0 14px 0;'>
                        <p style='font-size:12px;color:#aaa;text-align:center;'>This is an automated email from Hire Path. Please do not reply to this message.</p>
                    </div>
                ";
            }
        } elseif (strip_tags($body) !== $body) {
            $mail->Body = $body;
        } else {
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                    <h2 style='color: #007bff;'>Dear $username,</h2>
                    <p>We have received a request to reset your password. To proceed, please use the following One-Time Password (OTP):</p>
                    <div style='text-align: center; margin: 20px 0;'>
                        <span style='display: inline-block; font-size: 24px; font-weight: bold; color: #d9534f; padding: 10px 20px; border: 2px dashed #d9534f; border-radius: 8px;'>
                            Your OTP code is: $body
                        </span>
                    </div>
                    <p style='font-size: 14px; color: #555;'>Please note that this OTP is valid for a limited time and should not be shared with anyone.</p>
                    <p>If you did not request this, please contact our support team immediately.</p>
                    <p style='margin-top: 20px;'>Best regards,<br><strong>Hire Path Team</strong></p>
                    <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
                    <p style='font-size: 12px; color: #999;'>This is an automated email. Please do not reply to this message.</p>
                </div>
            ";
        }
        $mail->isHTML(true);

        $mail->send();
        return true;
    } catch (Exception $e) {
        $error = "Mailer Error: " . $mail->ErrorInfo;
        return false;
    }
}