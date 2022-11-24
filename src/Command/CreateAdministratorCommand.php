<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(
    name: 'app:create:administrator',
    description: 'Create an administrator for your app',
)]
class CreateAdministratorCommand extends Command
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, )
    {
        parent::__construct("app:create:administrator");

        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('fullName', InputArgument::OPTIONAL, 'Firstname Lastname')
            ->addArgument('email', InputArgument::OPTIONAL, 'Address email')
            ->addArgument('password', InputArgument::OPTIONAL, 'Password')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper("question");
        $io = new SymfonyStyle($input, $output);

        $fullName = $input->getArgument('fullName');

        if( !$fullName )
        {
            $question = new Question("Full Name of the administrator : ");
            $fullName = $helper->ask($input , $output, $question);
        }

        $email = $input->getArgument('email');

        if( !$email )
        {
            $question = new Question("Email of $fullName : ");
            $email = $helper->ask($input , $output, $question);
        }
        
        $plainPassword = $input->getArgument('password');

        if( !$plainPassword )
        {
            $question = new Question("Password of $fullName : ");
            $plainPassword = $helper->ask($input , $output, $question);
        }

        $user = (new User())
                    ->setFullName($fullName)
                    ->setEmail($email)
                    ->setPlainPassword($plainPassword)
                    ->setRoles(["ROLE_USER","ROLE_ADMIN"])
        ;

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // if ($arg1) {
        //     $io->note(sprintf('You passed an argument: %s', $arg1));
        // }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('Administrator created');

        return Command::SUCCESS;
    }
}
