<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

/**
 * Sent once a booking is paid. For guests this email is the only copy of the e-ticket,
 * so it carries a signed link that opens the ticket without an account.
 */
class TicketIssuedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        $route = $this->booking->schedule?->route;

        return new Envelope(
            subject: 'E-ticket '.$this->booking->booking_code.': '
                .($route?->origin?->city ?: $route?->origin?->name).' ke '
                .($route?->destination?->city ?: $route?->destination?->name),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.ticket-issued',
            with: [
                'ticketUrl' => URL::signedRoute('booking.e-ticket', $this->booking),
                'registerUrl' => route('register'),
            ],
        );
    }
}
