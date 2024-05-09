<?php

namespace App\Command;

use App\Service\SponteRequestDataService;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCommand extends Command
{
    protected static $defaultName        = 'import';
    protected static $defaultDescription = 'Add a short description for your command';
    private SponteRequestDataService $requestDataService;

    public function __construct(
        SponteRequestDataService $requestDataService
    ) {
        $this->requestDataService = $requestDataService;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('escola', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    /**
     * @throws GuzzleException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $escola = $input->getArgument('escola');
    

        // $escola = 7447;
        // $escola = 70092;
        // $escola = 69669;
        $this->requestDataService->import($escola);

        return 0;
    }


}
