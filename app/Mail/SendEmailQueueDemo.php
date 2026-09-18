<?php
  
namespace App\Mail;
  
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
  
class SendEmailQueueDemo extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * Create a new message instance.
     */
    public function __construct()
    {
          
    }
  
    /**
     * Get the message envelope.
     */
    public function envelope()
    {
      return new Envelope('How To Send E-mail Using Queue In laravel 10');
    }
  
    /**
     * Get the message content definition.
     */
    public function content()
    {
         $content = new Content();
        return view('email.demo', compact('content'));
    }
  
    /**
     * Get the attachments for the message.
     *
     * @return array

     */
    public function attachments()
    {
        return [];
    }
}

