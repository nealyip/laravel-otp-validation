<?php

namespace Nealyip\LaravelOTPValidation\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SimpleMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var string
     */
    protected $_from;

    /**
     * @var string
     */
    protected $_name;


    /**
     * SimpleMail constructor.
     *
     * @param string $from       email
     * @param string $name       name
     * @param array  $introLines Intro lines
     * @param array  $outroLines Outtro lines
     */
    public function __construct($from, $name, array $introLines, array $outroLines = [])
    {

        $this->_from = $from;
        $this->_name = $name;

        $this->markdown('notifications::email', ['level' => 'info', 'introLines' => $introLines, 'outroLines' => $outroLines]);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->_from, $this->_name);
    }

}
