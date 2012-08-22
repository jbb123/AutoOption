<?php
class EmailException extends Exception {};
class EmailAddressException extends Exception {};

class Email
{
	const AGENT = "MLG-Mail";
	const VERSION = "v1.01";

	const PRIORITY_HIGH = 1;
	const PRIORITY_NORMAL = 3;
	const PRIORITY_LOW = 5;
	
	const EOL = "\n"; //PHP_EOL

	private $sendmail_path = false;
	private $mime_boundary = false;
	private $return_path = false;
	private $from_email = false;
	private $from_name = false;
	private $reply_to_email = false;
	private $reply_to_name = false;
	private $to = array();
	private $cc = array();
	private $bcc = array();
	private $bademails = array();
	private $subject = false;
	private $body_plain = false;
	private $body_html = false;
	private $attachments = array();

	/**************************************************************************
	 * constructor
	 *************************************************************************/
	public function __construct()
	{
		$this->sendmail_path = get_cfg_var("sendmail_path");
	}

	/**************************************************************************
	 * addTo
	 * Adds an email & optional name to the internal To list
	 *************************************************************************/
	public function addTo($email, $name=false)
	{
		$validated = $this->validateEmail($email);

		if ($validated)
		{
			array_push($this->to, array('email'=>$email, 'name'=>$name));
		}
		else
		{
			array_push($this->bademails, array('email'=>$email, 'name'=>$name));
		}

		return $validated;
	}

	/**************************************************************************
	 * addCc
	 * Adds an email & optional name to the internal Cc list
	 *************************************************************************/
	public function addCc($email, $name=false)
	{
		$validated = $this->validateEmail($email);

		if ($validated)
		{
			array_push($this->cc, array('email'=>$email, 'name'=>$name));
		}
		else
		{
			array_push($this->bademails, array('email'=>$email, 'name'=>$name));
		}

		return $validated;
	}

	/**************************************************************************
	 * addBcc
	 * Adds an email & optional name to the internal Bcc list
	 *************************************************************************/
	public function addBcc($email, $name=false)
	{
		$validated = $this->validateEmail($email);

		if ($validated)
		{
			array_push($this->bcc, array('email'=>$email, 'name'=>$name));
			$retval = true;
		}
		else
		{
			array_push($this->bademails, array('email'=>$email, 'name'=>$name));
		}

		return $validated;
	}

	public function setFrom($email, $name=false) 	{ $this->from_email = $email; $this->from_name = $name; }
	public function setReplyTo($email, $name=false) { $this->reply_to_email = $email; $this->reply_to_name = $name; }
	public function setReturnPath($email) 			{ $this->return_path = $email; }
	public function setSubject($subject)		 	{ $this->subject = $subject; }
	public function setTextBody($body) 				{ $this->body_plain = $body; }
	public function setHtmlBody($body) 				{ $this->body_html = $body; }
	public function setPriority($priority) 			{ $this->priority = intval($priority); }

	/**************************************************************************
	 * addAttachment
	 *************************************************************************/
	public function addAttachment($attach_file) { array_push($this->attachments, $attach_file); }
	

	/**************************************************************************
	 * send
	 *************************************************************************/
	public function send($use_binary=false)
	{
		$to				= $this->constructEmailList($this->to);
		$header			= $this->constructHeader();
		$message		= $this->constructBody();
		$sendmail_args	= $this->return_path ? " -f" . $this->clean($this->return_path) : "";

		if ($header && $message)
		{
			if ($use_binary)
			{
				$sendmail = $this->sendmail_path . $sendmail_args;
				$fp = popen($sendmail, "w");
				if ($fp)
				{
					fputs($fp, "To: " . $to . self::EOL);
					fputs($fp, $header . self::EOL);
					fputs($fp, $message . self::EOL . self::EOL);
					pclose($fp);
				}
			}
			else
			{
				mail($to, $this->subject, $message, $header, ltrim($sendmail_args));
			}
			return true;
		}
		else
		{
			return false;
		}
	}

	/**************************************************************************
	 * clearRecipients
	 *************************************************************************/
	public function clearRecipients()
	{
		unset($this->to, $this->cc, $this->bcc, $this->bademails);

		$this->to = array();
		$this->cc = array();
		$this->bcc = array();
		$this->bademails = array();
	}

	/**************************************************************************
	 * clearAttachments
	 *************************************************************************/
	public function clearAttachments()
	{
		//placeholder
	}

	/**************************************************************************
	 * validateEmail
	 * Returns boolean if email address passes pattern test
	 *************************************************************************/
	public function validateEmail($email)
	{
		$retval = false;

		if (preg_match('/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/', $email) == 1)
		{
			$retval = true;
		}

		return $retval;
	}

	/**************************************************************************
	 * clean
	 * Strips EOL character to prevent mail injection (MTA spamming)
	 *************************************************************************/
	private function clean($s)
	{
		return str_replace(self::EOL, "", $s);
	}

	/**************************************************************************
	 * generateMimeBoundary
	 * Returns a random MIME boundary for MIME encoded emails
	 *************************************************************************/
	private function generateMimeBoundary()
	{
		$unique_hash = strtoupper(md5(uniqid(time())));
		$this->mime_boundary = "----=_NextPart_"
							 . substr("000".rand(0,999), -3) . "_"
							 . substr("0000".rand(0,9999), -4) . "_"
							 . substr($unique_hash, rand(0,15), 8) . "."
							 . substr($unique_hash, -8);
		
	}

	/**************************************************************************
	 * constructEmail
	 * Input a email & name; returns the RCPT-TO line (RFC compliant)
	 *************************************************************************/
	private function constructEmail($email, $name=false)
	{
		if ($name)
		{
			return $this->clean($name . " <" . $email . ">");
		}
		else
		{
			return $this->clean($email);
		}
	}

	/**************************************************************************
	 * constructEmailList
	 * Builds a comma seperated list of email addresses (RFC compliant)
	 *************************************************************************/
	private function constructEmailList($recipients)
	{
		$list = '';

		foreach ($recipients as $r)
		{
			$list .= $this->constructEmail($r["email"], $r["name"]) . ", ";
		}

		if (!empty($list))
		{
			return substr($list, 0, strlen($list) - 2);
		}
		else
		{
			return false;
		}
	}

	/**************************************************************************
	 * constructHeader
	 * Builds the header for the email
	 *************************************************************************/
	private function constructHeader()
	{
		$header = '';

		if (count($this->to) == 0)
		{
			throw new EmailAddressException("No email recipients specified.");
			return false;
		}
		else if (strlen($this->from_email) == 0)
		{
			throw new EmailAddressException("From email address not specified.");
			return false;
		}

		$header .= "From: " . $this->constructEmail($this->from_email, $this->from_name) . self::EOL;

		if ($this->reply_to_email)	{ $header .= "Reply-To: " . $this->constructEmail($this->reply_to_email, $this->reply_to_name) . self::EOL; }
		if ($this->return_path) 	{ $header .= "Return-Path: " . $this->constructEmail($this->return_path) . self::EOL; }
		if (count($this->cc) > 0)	{ $header .= "Cc: " . $this->constructEmailList($this->cc) . self::EOL; }
		if (count($this->bcc) > 0)	{ $header .= "Bcc: " . $this->constructEmailList($this->bcc) . self::EOL; }
		if ($this->priority)		{ $header .= "X-Priority: " . $this->priority . self::EOL; }
		
		$now = !empty($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time();
		$header .= "Message-ID: <" . date('YmdHis', $now) . "." . strtoupper(substr(md5(uniqid($now)),rand(0,24),8)) . "@" . $_SERVER['SERVER_NAME'] . ">" . self::EOL;
		
		$header .= "X-Mailer: " . self::AGENT . " " . self::VERSION . self::EOL;
		$header .= "MIME-Version: 1.0" . self::EOL;
		
		
		if ((count($this->attachments)))
			{
			$this->generateMimeBoundary();
			$header .= "Content-Type: multipart/mixed; boundary=\"{$this->mime_boundary}\"";

			}
		else
			{
			
				if ($this->body_plain && $this->body_html)
				{
					$this->generateMimeBoundary();
					$header .= "Content-Type: multipart/alternative; boundary=\"" . $this->mime_boundary . "\"";
				}
				elseif ($this->body_html)
				{
					$header .= "Content-Type: text/html; charset=UTF-8";
				}
				else
				{
					$header .= "Content-Type: text/plain; charset=UTF-8";
				}
			
			}
		

		return $header;
	}

	/**************************************************************************
	 * constructBody
	 * Builds the body parts of the email
	 *************************************************************************/
	private function constructBody()
	{
		
		
		
		if ((count($this->attachments)))
			{
				if ($this->body_plain && $this->body_html)
				{
					$message  = "This is a multi-part message in MIME format.  You should never see this part of" . self::EOL;
					$message .= "the message. If you do, please upgrade to a MIME-compatible email client." . self::EOL;
					$message .= "--{$this->mime_boundary}" . self::EOL;
					$message .= "Content-Type: text/plain; charset=UTF-8" . self::EOL;
					$message .= "Content-Transfer-Encoding: 8bit" . self::EOL . self::EOL;
					$message .= $this->body_plain . self::EOL;
					
					$message .= "--{$this->mime_boundary}" . self::EOL;
					$message .= "Content-Type:text/html; charset=\"iso-8859-1\"" . self::EOL;
					$message .= "Content-Transfer-Encoding: 7bit" . self::EOL . self::EOL;
					$message .= $this->body_plain . self::EOL;
					
					foreach ($this->attachments as $af)
					{
					$fileurl=array();
					$fileurl=explode('/', $af);
					$filename=$fileurl[count($fileurl)-1];
					
					$message .= "--{$this->mime_boundary}" . self::EOL;
					
					$message .= "Content-Type: application/octet-stream; name=\"" . $filename . "\"" . self::EOL;
					$message .= "Content-Transfer-Encoding: base64" . self::EOL . self::EOL;

					
					$file=fopen($af,'r');
					$data=fread($file,filesize($af));
					fclose($file);
					$data_chunk=chunk_split(base64_encode($data));
					$message .=$data_chunk. self::EOL;
					
					}
					$message .= "--{$this->mime_boundary}" . self::EOL;
				}
				elseif ($this->body_html)
				{
					$message  = "This is a multi-part message in MIME format.  You should never see this part of" . self::EOL;
					$message .= "the message. If you do, please upgrade to a MIME-compatible email client." . self::EOL;
					$message .= "--{$this->mime_boundary}" . self::EOL;
					$message .= "Content-Type:text/html; charset=\"iso-8859-1\"" . self::EOL;
					$message .= "Content-Transfer-Encoding: 7bit" . self::EOL . self::EOL;
					$message .= $this->body_html . self::EOL;
					
					foreach ($this->attachments as $af)
					{
					$fileurl=array();
					$fileurl=explode('/', $af);
					$filename=$fileurl[count($fileurl)-1];
					
					$message .= "--{$this->mime_boundary}" . self::EOL;
					
					$message .= "Content-Type: application/octet-stream; name=\"" . $filename . "\"" . self::EOL;
					$message .= "Content-Transfer-Encoding: base64" . self::EOL . self::EOL;

					
					$file=fopen($af,'r');
					$data=fread($file,filesize($af));
					fclose($file);
					$data_chunk=chunk_split(base64_encode($data));
					$message .=$data_chunk. self::EOL;
					
					}
					$message .= "--{$this->mime_boundary}" . self::EOL;
				}
				else if ($this->body_plain)
				{
					$message  = "This is a multi-part message in MIME format.  You should never see this part of" . self::EOL;
					$message .= "the message. If you do, please upgrade to a MIME-compatible email client." . self::EOL;
					$message .= "--{$this->mime_boundary}" . self::EOL;
					$message .= "Content-Type: text/plain; charset=UTF-8" . self::EOL;
					$message .= "Content-Transfer-Encoding: 8bit" . self::EOL . self::EOL;
					$message .= $this->body_plain . self::EOL;
					
					foreach ($this->attachments as $af)
					{
					$fileurl=array();
					$fileurl=explode('/', $af);
					$filename=$fileurl[count($fileurl)-1];
					
					$message .= "--{$this->mime_boundary}" . self::EOL;
					
					$message .= "Content-Type: application/octet-stream; name=\"" . $filename . "\"" . self::EOL;
					$message .= "Content-Transfer-Encoding: base64" . self::EOL . self::EOL;

					
					$file=fopen($af,'r');
					$data=fread($file,filesize($af));
					fclose($file);
					$data_chunk=chunk_split(base64_encode($data));
					$message .=$data_chunk. self::EOL;
					
					}
					$message .= "--{$this->mime_boundary}" . self::EOL;
					
				}
				else
				{
					$message = false;
					throw new EmailException("No email body specified.");
				}
			
			
			
			}
		else
			{
				if ($this->body_plain && $this->body_html)
				{
					$message  = "This is a multi-part message in MIME format.  You should never see this part of" . self::EOL;
					$message .= "the message. If you do, please upgrade to a MIME-compatible email client." . self::EOL;
					$message .= "--" . $this->mime_boundary . self::EOL;
					$message .= "Content-Type: text/plain; charset=UTF-8" . self::EOL;
					$message .= "Content-Transfer-Encoding: 8bit" . self::EOL . self::EOL;
					$message .= $this->body_plain . self::EOL;
					$message .= "--" . $this->mime_boundary . self::EOL;
					$message .= "Content-Type: text/html; charset=UTF-8" . self::EOL;
					$message .= "Content-Transfer-Encoding: 8bit" . self::EOL . self::EOL;
					$message .= $this->body_html . self::EOL;
					$message .= "--" . $this->mime_boundary . "--";
				}
				else if ($this->body_html)
				{
					$message = $this->body_html;
				}
				else if ($this->body_plain)
				{
					$message = $this->body_plain;
				}
				else
				{
					$message = false;
					throw new EmailException("No email body specified.");
				}
			}
		
		
		
		return $message;
	}
}
?>
