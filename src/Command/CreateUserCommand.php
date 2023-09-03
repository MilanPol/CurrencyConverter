<?php

namespace App\Command;

use App\DataObject\LogContextDataObject;
use App\Service\LogService;
use App\Service\UserService;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'create:user',
    description: 'Import current currency exchange rates',
)]
class CreateUserCommand extends Command
{
    private UserService $userService;
    private LogService $logService;

    public function __construct(
        UserService $userService,
        LogService $logService
    ) {
        parent::__construct();
        $this->userService = $userService;
        $this->logService = $logService;
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
            $logMessage = sprintf(
                'User creation has failed because of the following error %s',
                $exception->getMessage()
            );

            $this->logService->addException(
                get_class($this),
                __FUNCTION__,
                LogContextDataObject::ERROR,
                $logMessage,
                $exception
            );
            $this->logService->logContext();
            return Command::FAILURE;
        }
        $logMessage = 'User has been successfully created';
        $this->logService->addInfoLog(
            get_class($this),
            __FUNCTION__,
            $logMessage
        );

        $inputOutput->success($logMessage);

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
