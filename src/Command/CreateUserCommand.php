<?php

namespace App\Command;

use App\Constants\DefaultCurrencyConstants;
use App\Service\ExchangeRateCurrencyService;
use App\Service\UserService;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

#[AsCommand(
    name: 'create:user',
    description: 'Import current currency exchange rates',
)]
class CreateUserCommand extends Command
{
    private UserService $userService;

    public function __construct(
        UserService $userService
    ) {
        parent::__construct();
        $this->userService = $userService;
    }

    protected function configure()
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('password', InputArgument::REQUIRED, 'User password')
            ->addArgument(
                'role',
                InputArgument::REQUIRED,
                'User role, options are ROLE_ADMIN, ROLE_USER, ROLE_MODERATOR'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputOutput = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $role = $input->getArgument('role');

        try {
            if ($email and !$this->isEmail($email)) {
                throw new Exception(
                    sprintf(
                        "string %s is not a valid email address",
                        $email
                    )
                );
            }

            $this->userService->createUser($email, $password, $role);
        } catch (Exception $exception) {
            $inputOutput->error($exception->getMessage());

            return Command::FAILURE;
        }
        $inputOutput->success('User has been successfully created');

        return Command::SUCCESS;
    }

    private function isEmail(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }
}
