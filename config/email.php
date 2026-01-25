<?php
/**
 * Email Configuration & Mailer Class
 * Uses SMTP for reliable email delivery
 * 
 * HOSTINGER SMTP SETTINGS:
 * - Server: mail.liwondesunhotel.com (or your domain SMTP)
 * - Port: 587 (TLS) or 465 (SSL)
 * - Username: your-email@liwondesunhotel.com
 * - Password: Check Hostinger control panel under Email Accounts
 * 
 * To find your SMTP settings in Hostinger:
 * 1. Log in to hPanel
 * 2. Go to Email > Email Accounts
 * 3. Click on your email account
 * 4. Look for "SMTP Server" and "SMTP Port"
 */

class EmailSender {
    private $host;
    private $port;
    private $username;
    private $password;
    private $from_email;
    private $from_name;
    private $use_smtp;
    private $last_error;

    public function __construct() {
        // HOSTINGER SMTP CREDENTIALS (Production Only)
        // For LOCAL: Leave these empty to use PHP mail() fallback
        // For PRODUCTION: Set via environment variables or .htaccess
        
        $this->host = getenv('SMTP_HOST') ?: null;
        $this->port = getenv('SMTP_PORT') ?: 465;
        $this->username = getenv('SMTP_USER') ?: null;
        $this->password = getenv('SMTP_PASS') ?: null;
        
        $this->from_email = getSetting('email_main');
        $this->from_name = getSetting('site_name');
        
        // Determine if we should use SMTP
        $this->use_smtp = !empty($this->host) && !empty($this->username) && !empty($this->password);
    }

    /**
     * Send email via SMTP or mail()
     * 
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $body Email body (plain text)
     * @param array $headers Optional additional headers
     * @return bool True if sent successfully
     */
    public function send($to, $subject, $body, $headers = []) {
        if ($this->use_smtp) {
            return $this->send_via_smtp($to, $subject, $body, $headers);
        } else {
            return $this->send_via_mail($to, $subject, $body, $headers);
        }
    }

    /**
     * Send email via SMTP (more reliable)
     */
    private function send_via_smtp($to, $subject, $body, $headers = []) {
        try {
            // For port 465: SSL from start (implicit SSL)
            // For port 587: Plain connection then STARTTLS
            $protocol = ($this->port === 465) ? 'ssl://' : '';
            
            $socket = @fsockopen(
                $protocol . $this->host,
                $this->port,
                $errno,
                $errstr,
                30  // Increased timeout
            );

            if (!$socket) {
                $this->last_error = "SMTP Connection Failed: $errstr ($errno)";
                error_log("Email SMTP Error: " . $this->last_error);
                return false;
            }

            // Set non-blocking reads
            stream_set_blocking($socket, true);

            // Read server response
            $response = fgets($socket, 1024);
            error_log("SMTP Initial Response: " . trim($response));
            
            if (strpos($response, '220') === false) {
                fclose($socket);
                $this->last_error = "SMTP Server did not respond correctly: " . trim($response);
                error_log("Email SMTP Error: " . $this->last_error);
                return false;
            }

            // Send EHLO command
            fputs($socket, "EHLO " . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\r\n");
            $response = fgets($socket, 1024);
            error_log("SMTP EHLO Response: " . trim($response));

            // Handle STARTTLS only if port 587
            if ($this->port === 587) {
                fputs($socket, "STARTTLS\r\n");
                $response = fgets($socket, 1024);
                error_log("SMTP STARTTLS Response: " . trim($response));
                
                if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    fclose($socket);
                    $this->last_error = "STARTTLS failed";
                    error_log("Email SMTP Error: " . $this->last_error);
                    return false;
                }

                // Send EHLO again after TLS
                fputs($socket, "EHLO " . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\r\n");
                $response = fgets($socket, 1024);
                error_log("SMTP EHLO (post-TLS) Response: " . trim($response));
            }

            // Authenticate with LOGIN method
            fputs($socket, "AUTH LOGIN\r\n");
            $response = fgets($socket, 1024);
            error_log("SMTP AUTH LOGIN Response: " . trim($response));

            // Send username (base64 encoded)
            fputs($socket, base64_encode($this->username) . "\r\n");
            $response = fgets($socket, 1024);
            error_log("SMTP Username Response: " . trim($response));

            // Send password (base64 encoded)
            fputs($socket, base64_encode($this->password) . "\r\n");
            $auth_response = fgets($socket, 1024);
            error_log("SMTP Authentication Response: " . trim($auth_response));

            // Check for successful authentication (235 or 2.7.0)
            if (strpos($auth_response, '235') === false && strpos($auth_response, '2.7.0') === false) {
                fclose($socket);
                $this->last_error = "SMTP Authentication failed: " . trim($auth_response) . 
                                   "\nUsername: " . $this->username . 
                                   "\nHost: " . $this->host . ":" . $this->port;
                error_log("Email SMTP Error: " . $this->last_error);
                return false;
            }

            error_log("SMTP Authentication successful");

            // Send email
            fputs($socket, "MAIL FROM: <" . $this->from_email . ">\r\n");
            $response = fgets($socket, 1024);
            error_log("SMTP MAIL FROM Response: " . trim($response));

            fputs($socket, "RCPT TO: <$to>\r\n");
            $response = fgets($socket, 1024);
            error_log("SMTP RCPT TO Response: " . trim($response));

            fputs($socket, "DATA\r\n");
            $response = fgets($socket, 1024);
            error_log("SMTP DATA Response: " . trim($response));

            // Build email message
            $message = "From: " . $this->from_name . " <" . $this->from_email . ">\r\n";
            $message .= "To: $to\r\n";
            $message .= "Subject: $subject\r\n";
            $message .= "MIME-Version: 1.0\r\n";
            $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $message .= "Content-Transfer-Encoding: 8bit\r\n";
            
            // Add additional headers
            foreach ($headers as $header => $value) {
                if ($header !== 'From' && $header !== 'Subject') {
                    $message .= "$header: $value\r\n";
                }
            }

            $message .= "\r\n" . $body . "\r\n";

            fputs($socket, $message . "\r\n.\r\n");
            $response = fgets($socket, 1024);
            error_log("SMTP Message Response: " . trim($response));

            // Quit
            fputs($socket, "QUIT\r\n");
            fclose($socket);

            error_log("Email sent via SMTP to: $to | Subject: $subject | Host: " . $this->host . ":" . $this->port);
            return true;

        } catch (Exception $e) {
            $this->last_error = $e->getMessage();
            error_log("Email SMTP Exception: " . $this->last_error);
            return false;
        }
    }

    /**
     * Send email via PHP mail() (fallback)
     */
    private function send_via_mail($to, $subject, $body, $headers = []) {
        $header_string = "MIME-Version: 1.0\r\n";
        $header_string .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $header_string .= "From: " . $this->from_name . " <" . $this->from_email . ">\r\n";

        foreach ($headers as $key => $value) {
            if ($key !== 'From' && $key !== 'Subject') {
                $header_string .= "$key: $value\r\n";
            }
        }

        $result = @mail($to, $subject, $body, $header_string);
        
        if (!$result) {
            $this->last_error = "PHP mail() function failed or is disabled";
            error_log("Email via mail() failed to: $to | Subject: $subject");
        } else {
            error_log("Email sent via mail() to: $to | Subject: $subject");
        }

        return $result;
    }

    /**
     * Get the last error message
     */
    public function get_last_error() {
        return $this->last_error;
    }

    /**
     * Check if SMTP is configured
     */
    public function is_smtp_configured() {
        return $this->use_smtp;
    }

    /**
     * Get current email method (for debugging)
     */
    public function get_email_method() {
        return $this->use_smtp ? 'SMTP' : 'PHP mail()';
    }
}

/**
 * Global email sender instance
 */
$emailer = new EmailSender();
