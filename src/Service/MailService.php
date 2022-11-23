<?php

nameSpace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MailService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendMail(
        string $from,
        string $subject,
        string $htmlTemplate,
        array $context,
        string $to = "admin@symrecipe.com"
        ): void
    {
        // $email = (new Email())
        $email = (new TemplatedEmail())
        ->from($from)
        ->to($to)
        ->subject($subject)
        ->htmlTemplate($htmlTemplate)   // path of the Twig template to render
        ->context($context)
        ;

        $this->mailer->send($email);
    }
}

