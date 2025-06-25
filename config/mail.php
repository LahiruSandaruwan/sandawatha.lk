<?php
/**
 * Mail Configuration
 * Uses PHPMailer for sending emails
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Mail configuration
$mail = [
    'host' => env('MAIL_HOST', 'smtp.mailtrap.io'),
    'port' => env('MAIL_PORT', 2525),
    'user' => env('MAIL_USER', null),
    'pass' => env('MAIL_PASS', null),
    'from' => env('MAIL_FROM', 'noreply@sandawatha.lk'),
    'name' => env('MAIL_NAME', 'Sandawatha.lk'),
    'encryption' => env('MAIL_ENCRYPTION', 'tls')
];

/**
 * Send email
 * 
 * @param string|array $to Recipient email(s)
 * @param string $subject Email subject
 * @param string $body Email body (HTML)
 * @param array $options Additional options
 * @return bool Whether email was sent
 * @throws Exception
 */
function sendMail($to, $subject, $body, $options = []) {
    global $mail;
    
    try {
        // Create PHPMailer instance
        $mailer = new PHPMailer(true);
        
        // Server settings
        $mailer->isSMTP();
        $mailer->Host = $mail['host'];
        $mailer->SMTPAuth = true;
        $mailer->Username = $mail['user'];
        $mailer->Password = $mail['pass'];
        $mailer->SMTPSecure = $mail['encryption'];
        $mailer->Port = $mail['port'];
        
        // Set debug level in development
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            $mailer->SMTPDebug = SMTP::DEBUG_SERVER;
        }
        
        // Recipients
        $mailer->setFrom($mail['from'], $mail['name']);
        
        if (is_array($to)) {
            foreach ($to as $recipient) {
                $mailer->addAddress($recipient);
            }
        } else {
            $mailer->addAddress($to);
        }
        
        // Add CC recipients
        if (!empty($options['cc'])) {
            $cc = is_array($options['cc']) ? $options['cc'] : [$options['cc']];
            foreach ($cc as $recipient) {
                $mailer->addCC($recipient);
            }
        }
        
        // Add BCC recipients
        if (!empty($options['bcc'])) {
            $bcc = is_array($options['bcc']) ? $options['bcc'] : [$options['bcc']];
            foreach ($bcc as $recipient) {
                $mailer->addBCC($recipient);
            }
        }
        
        // Add reply-to
        if (!empty($options['replyTo'])) {
            $mailer->addReplyTo($options['replyTo']);
        }
        
        // Add attachments
        if (!empty($options['attachments'])) {
            $attachments = is_array($options['attachments']) ? $options['attachments'] : [$options['attachments']];
            foreach ($attachments as $attachment) {
                if (is_array($attachment)) {
                    $mailer->addAttachment($attachment['path'], $attachment['name']);
                } else {
                    $mailer->addAttachment($attachment);
                }
            }
        }
        
        // Content
        $mailer->isHTML(true);
        $mailer->Subject = $subject;
        $mailer->Body = $body;
        
        // Add plain text version
        if (!empty($options['text'])) {
            $mailer->AltBody = $options['text'];
        } else {
            $mailer->AltBody = strip_tags($body);
        }
        
        // Send email
        return $mailer->send();
    } catch (Exception $e) {
        // Log error
        error_log("Email sending failed: " . $e->getMessage());
        
        // Throw exception in debug mode
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            throw $e;
        }
        
        return false;
    }
}

/**
 * Send welcome email
 * 
 * @param string $to Recipient email
 * @param array $data Template data
 * @return bool Whether email was sent
 */
function sendWelcomeEmail($to, $data) {
    $subject = 'Welcome to ' . APP_NAME;
    $body = renderEmailTemplate('welcome', $data);
    return sendMail($to, $subject, $body);
}

/**
 * Send password reset email
 * 
 * @param string $to Recipient email
 * @param array $data Template data
 * @return bool Whether email was sent
 */
function sendPasswordResetEmail($to, $data) {
    $subject = 'Reset Your Password';
    $body = renderEmailTemplate('password-reset', $data);
    return sendMail($to, $subject, $body);
}

/**
 * Send email verification email
 * 
 * @param string $to Recipient email
 * @param array $data Template data
 * @return bool Whether email was sent
 */
function sendVerificationEmail($to, $data) {
    $subject = 'Verify Your Email Address';
    $body = renderEmailTemplate('verify-email', $data);
    return sendMail($to, $subject, $body);
}

/**
 * Send match notification email
 * 
 * @param string $to Recipient email
 * @param array $data Template data
 * @return bool Whether email was sent
 */
function sendMatchNotificationEmail($to, $data) {
    $subject = 'New Match Found!';
    $body = renderEmailTemplate('match-notification', $data);
    return sendMail($to, $subject, $body);
}

/**
 * Send message notification email
 * 
 * @param string $to Recipient email
 * @param array $data Template data
 * @return bool Whether email was sent
 */
function sendMessageNotificationEmail($to, $data) {
    $subject = 'New Message Received';
    $body = renderEmailTemplate('message-notification', $data);
    return sendMail($to, $subject, $body);
}

/**
 * Send subscription confirmation email
 * 
 * @param string $to Recipient email
 * @param array $data Template data
 * @return bool Whether email was sent
 */
function sendSubscriptionConfirmationEmail($to, $data) {
    $subject = 'Subscription Confirmed';
    $body = renderEmailTemplate('subscription-confirmation', $data);
    return sendMail($to, $subject, $body);
}

/**
 * Send subscription expiry reminder email
 * 
 * @param string $to Recipient email
 * @param array $data Template data
 * @return bool Whether email was sent
 */
function sendSubscriptionExpiryEmail($to, $data) {
    $subject = 'Subscription Expiring Soon';
    $body = renderEmailTemplate('subscription-expiry', $data);
    return sendMail($to, $subject, $body);
}

/**
 * Render email template
 * 
 * @param string $template Template name
 * @param array $data Template data
 * @return string Rendered template
 */
function renderEmailTemplate($template, $data = []) {
    // Extract data to variables
    extract($data);
    
    // Start output buffering
    ob_start();
    
    // Include template
    require_once APP_PATH . "/views/emails/{$template}.php";
    
    // Get and clean buffer
    return ob_get_clean();
} 