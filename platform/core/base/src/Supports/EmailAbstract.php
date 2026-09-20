<?php

namespace Botble\Base\Supports;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class EmailAbstract extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var string
     */
    public $content;

    /**
     * @var string
     */
    public $subject;

    /**
     * @var array
     */
    public $data;

    /**
     * Create a new message instance.
     *
     * @param string $content
     * @param string $subject
     * @param array $data
     */
    public function __construct($content, $subject, $data = [])
    {
        $this->content = $content;
        $this->subject = $subject;
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return EmailAbstract
     */
    public function build()
    {
        $inlineCss = new CssToInlineStyles;
        $email = $this->from(setting('email_from_address', config('mail.from.address')),
            setting('email_from_name', config('mail.from.name')))
            ->subject($this->subject)
            ->html($inlineCss->convert($this->content));

        $attachments = Arr::get($this->data, 'attachments');
        if (!empty($attachments)) {
            if (!is_array($attachments)) {
                $attachments = [$attachments];
            }
            foreach ($attachments as $file) {
                $email->attach($file);
            }
        }

        // In-memory attachments: each item is ['data' => raw bytes, 'name' =>
        // filename, 'options' => optional Mailable::attachData() options].
        // For callers that have the bytes already in hand and no file on
        // disk to point ->attach() at (e.g. because the only stored copy is
        // encrypted, not a real file of that type).
        $attachData = Arr::get($this->data, 'attach_data');
        if (!empty($attachData)) {
            foreach ($attachData as $item) {
                $email->attachData(
                    Arr::get($item, 'data'),
                    Arr::get($item, 'name'),
                    Arr::get($item, 'options', ['mime' => 'application/pdf'])
                );
            }
        }

        return $email;
    }
}
