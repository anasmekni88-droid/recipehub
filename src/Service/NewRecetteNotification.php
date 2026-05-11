<?php

namespace App\Service;

use App\Entity\Recette;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

final class NewRecetteNotification
{
    public function __construct(
        private MailerInterface $mailer,
        private UserRepository $userRepository,
    ) {}

    public function notifyUsers(Recette $recette): void
    {
        $users = $this->userRepository->findAll();

        if (empty($users)) {
            return;
        }

        $email = (new TemplatedEmail())
            ->from('no-reply@recipehub.app')
            ->to($users[0]->getEmail())
            ->subject('Nouvelle recette : ' . $recette->getTitre())
            ->htmlTemplate('emails/new_recette.html.twig')
            ->context([
                'recette' => $recette,
            ]);

        for ($i = 1, $count = count($users); $i < $count; ++$i) {
            $email->addBcc($users[$i]->getEmail());
        }

        $this->mailer->send($email);
    }
}
