<?php


namespace common\components;


use yii\mail\BaseMessage;
use yii\mail\MessageInterface;

class Message extends BaseMessage
{
    /** @var string */
    private $_charset;

    /** @var string */
    private $_from;

    /** @var string[] */
    private $_to;

    /** @var string[] */
    private $_replyTo;

    /** @var string[] */
    private $_cc;

    /** @var string[] */
    private $_bcc;

    /** @var string */
    private $_subject;

    /** @var string */
    private $_textBody;

    /** @var string */
    private $_htmlBody;

    public function getCharset()
    {
        return $this->_charset ?? 'UTF-8';
    }

    public function setCharset($charset)
    {
        $this->_charset = $charset;
    }

    public function getFrom()
    {
        return $this->_from;
    }

    public function setFrom($from)
    {
        if (is_array($from) && count($from) > 0) {
            $this->_from = $from[0];
        } else {
            $this->_from = $from;
        }
    }

    public function getTo()
    {
        return $this->_to;
    }

    public function setTo($to)
    {
        if (is_array($to)) {
            $this->_to = $to;
        } else {
            $this->_to = explode(",", $to);
        }
    }

    public function getReplyTo()
    {
        return $this->_replyTo ?? [$this->getFrom()];
    }

    public function setReplyTo($replyTo)
    {
        if (is_array($replyTo)) {
            $this->_replyTo = $replyTo;
        } else {
            $this->_replyTo = explode(",", $replyTo);
        }
    }

    public function getCc()
    {
        return $this->_cc ?? [];
    }

    public function setCc($cc)
    {
        if (is_array($cc)) {
            $this->_cc = $cc;
        } else {
            $this->_cc = explode(",", $cc);
        }
    }

    public function getBcc()
    {
        return $this->_bcc ?? [];
    }

    public function setBcc($bcc)
    {
        if (is_array($bcc)) {
            $this->_bcc = $bcc;
        } else {
            $this->_bcc = explode(",", $bcc);
        }
    }

    public function getSubject()
    {
        return $this->_subject;
    }

    public function setSubject($subject)
    {
        $this->_subject = $subject;
    }

    /**
     * @return string
     */
    public function getTextBody()
    {
        if (empty($this->_textBody)) {
            return strip_tags($this->_htmlBody);
        }
        return $this->_textBody;
    }

    public function setTextBody($text)
    {
        $this->_textBody = $text;
    }

    /**
     * @return string
     */
    public function getHtmlBody()
    {
        if (empty($this->_htmlBody)) {
            return htmlspecialchars($this->_textBody);
        }
        return $this->_htmlBody;
    }

    public function setHtmlBody($html)
    {
        $this->_htmlBody = $html;
    }

    public function attach($fileName, array $options = [])
    {
        // TODO: Implement attach() method.
    }

    public function attachContent($content, array $options = [])
    {
        // TODO: Implement attachContent() method.
    }

    public function embed($fileName, array $options = [])
    {
        // TODO: Implement embed() method.
    }

    public function embedContent($content, array $options = [])
    {
        // TODO: Implement embedContent() method.
    }

    public function toString()
    {
        return $this->_textBody;
    }
}
