<?php

class Mailer {

    protected $config;

    protected $from = [];

    protected $to = [];

    protected $cc = [];

    protected $bcc = [];

    protected $replyTo = [];

    protected $subject;

    protected $view;

    protected $textView;

    protected $viewData = [];

    protected $raw;

    /**
     * 选择配置
     *
     * @param $name
     *
     * @return static
     *
     * @author  liuchao
     */
    public static function use($name) {
        $config = config('mail.' . $name);
        if ( !$config) {
            throw new \InvalidArgumentException("$name: mail config don't exists");
        }

        $mailer = new static();
        $mailer->config = $config;
        $mailer->subject($config['subject']);
        $mailer->from($config['from']);

        return $mailer;
    }

    public function send() {
        $transport = new \Swift_SmtpTransport($this->config['host'], $this->config['port'], $this->config['encryption']);
        $transport->setUsername($this->config['username']);
        $transport->setPassword($this->config['password']);

        $message = new \Swift_Message();

        if ($this->subject) {
            $message->setSubject($this->subject);
        }

        if ( !empty($this->from)) {
            $message->setFrom($this->from['address'], $this->from['name']);
        }

        foreach (['to', 'cc', 'bcc', 'replyTo'] as $type) {
            foreach ($this->{$type} as $recipient) {
                $method = 'add' . ucfirst($type);
                $message->{$method}($recipient['address'], $recipient['name']);
            }
        }

        if ($this->view) {
            $message->setBody(view($this->view, $this->viewData), 'text/html', 'utf-8');
        } elseif ($this->textView) {
            $message->setBody(view($this->textView, $this->viewData), 'text/plain');
        } elseif ($this->raw) {
            $message->setBody($this->raw, 'text/plain');
        }

        return (new \Swift_Mailer($transport))->send($message);
    }

    protected function setAddress($address, $name = null, $property = 'to') {
        if ( !is_array($address)) {
            $address = is_string($name) ? [['name' => $name, 'address' => $address]] : [$address];
        }

        foreach ($address as $recipient) {
            if (is_string($recipient)) {
                $recipient = ['address' => $recipient];
            }

            $this->{$property}[] = [
                'name'    => isset($recipient['name']) ? $recipient['name'] : null,
                'address' => $recipient['address'],
            ];
        }

        return $this;
    }

    public function from($address, $name = null) {
        if ( !is_array($address)) {
            $address = [
                'address' => $address,
                'name'    => $name,
            ];
        }
        $this->from = $address;

        return $this;
    }

    public function to($address, $name = null) {
        return $this->setAddress($address, $name, 'to');
    }

    public function cc($address, $name = null) {
        return $this->setAddress($address, $name, 'cc');
    }

    public function bcc($address, $name = null) {
        return $this->setAddress($address, $name, 'bcc');
    }

    public function replyTo($address, $name = null) {
        return $this->setAddress($address, $name, 'replyTo');
    }

    public function subject($subject) {
        $this->subject = $subject;

        return $this;
    }

    public function view($view, array $data = []) {
        $this->view = $view;
        $this->viewData = array_merge($this->viewData, $data);

        return $this;
    }

    public function text($textView, array $data = []) {
        $this->textView = $textView;
        $this->viewData = array_merge($this->viewData, $data);

        return $this;
    }

    public function raw($raw) {
        $this->raw = $raw;

        return $this;
    }

    public function with($key, $value = null) {
        if (is_array($key)) {
            $this->viewData = array_merge($this->viewData, $key);
        } else {
            $this->viewData[$key] = $value;
        }

        return $this;
    }

}