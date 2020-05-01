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

    /**
     * @inheritdoc
     */
    public function getCharset()
    {
        return $this->_charset ?? 'UTF-8';
    }

    /**
     * @inheritdoc
     */
    public function setCharset($charset)
    {
        $this->_charset = $charset;
    }

    /**
     * @inheritdoc
     */
    public function getFrom()
    {
        return $this->_from;
    }

    /**
     * @inheritdoc
     */
    public function setFrom($from)
    {
        if (is_array($from) && count($from) > 0) {
            $this->_from = $from[0];
        } else {
            $this->_from = $from;
        }
    }

    /**
     * @inheritdoc
     */
    public function getTo()
    {
        return $this->_to;
    }

    /**
     * @inheritdoc
     */
    public function setTo($to)
    {
        if (is_array($to)) {
            $this->_to = $to;
        } else {
            $this->_to = explode(",", $to);
        }
    }

    /**
     * @inheritdoc
     */
    public function getReplyTo()
    {
        return $this->_replyTo ?? [$this->_from];
    }

    /**
     * @inheritdoc
     */
    public function setReplyTo($replyTo)
    {
        if (is_array($replyTo)) {
            $this->_replyTo = $replyTo;
        } else {
            $this->_replyTo = explode(",", $replyTo);
        }
    }

    /**
     * @inheritdoc
     */
    public function getCc()
    {
        return $this->_cc ?? [];
    }

    /**
     * @inheritdoc
     */
    public function setCc($cc)
    {
        if (is_array($cc)) {
            $this->_cc = $cc;
        } else {
            $this->_cc = explode(",", $cc);
        }
    }

    /**
     * @inheritdoc
     */
    public function getBcc()
    {
        return $this->_bcc ?? [];
    }

    /**
     * @inheritdoc
     */
    public function setBcc($bcc)
    {
        if (is_array($bcc)) {
            $this->_bcc = $bcc;
        } else {
            $this->_bcc = explode(",", $bcc);
        }
    }

    /**
     * @inheritdoc
     */
    public function getSubject()
    {
        return $this->_subject;
    }

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function setTextBody($text)
    {
        $this->_textBody = $text;
    }

    /**
     * @return string
     */
    public function get_HtmlBody()
    {
        if (empty($this->_htmlBody)) {
            return $this->_textBody;
        }
        return $this->_htmlBody;
    }

    /**
     * @inheritdoc
     */
    public function setHtmlBody($html)
    {
        $this->_htmlBody = $html;
    }

    /**
     * @inheritdoc
     */
    public function attach($fileName, array $options = [])
    {
        // TODO: Implement attach() method.
    }

    /**
     * @inheritdoc
     */
    public function attachContent($content, array $options = [])
    {
        // TODO: Implement attachContent() method.
    }

    /**
     * @inheritdoc
     */
    public function embed($fileName, array $options = [])
    {
        // TODO: Implement embed() method.
    }

    /**
     * @inheritdoc
     */
    public function embedContent($content, array $options = [])
    {
        // TODO: Implement embedContent() method.
    }

    /**
     * @inheritdoc
     */
    public function toString()
    {
        return $this->_textBody;
    }
}
