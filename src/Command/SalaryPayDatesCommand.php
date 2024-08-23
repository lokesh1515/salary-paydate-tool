<?php

namespace App\Command;

use Carbon\Carbon;
use League\Csv\Writer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;


#[AsCommand(
    name: 'app:salary-pay-dates',
    description: 'Get the salary and bonus payment dates for the remainder of the year.',
    aliases: ['app:spd']
)]
class SalaryPayDatesCommand extends Command
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        parent::__construct();
        $this->logger = $logger;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('filename', InputArgument::REQUIRED, 'CSV File Name')
            ->setHelp('This command allows you to generate a CSV file containing 
            the payment dates for salaries and bonuses for the remainder of the year.

            <info>php %command.full_name%</info> <comment>filename (without extension)</comment>
            ');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filesystem = new Filesystem();

        try {
            $now = Carbon::now();
            $monthsRemaining = 12 - $now->month + 1;
            $rows = [];

            for ($i = 0; $i < $monthsRemaining; $i++) {
                $date = $now->copy()->addMonths($i);
                $monthName = $date->format('F');

                // Calculate Salary Payment Date as last day of
                $salaryDate = $date->copy()->endOfMonth();
                if ($salaryDate->isWeekend()) {
                    $salaryDate = $salaryDate->previous(Carbon::FRIDAY);
                }

                // Calculate Bonus Payment Date
                $bonusDate = $date->copy()->day(15);
                if ($bonusDate->isWeekend()) {
                    $bonusDate = $bonusDate->next(Carbon::WEDNESDAY);
                }

                $rows[] = [$monthName, $salaryDate->toDateString(), $bonusDate->toDateString()];

                // Log the generated dates
                $this->logger->info("Generated dates for {$monthName}: Salary on {$salaryDate->toDateString()}, Bonus on {$bonusDate->toDateString()}");
            }
            
            $filename = $input->getArgument('filename');
            $csvPath = $filename . '.csv';
            

            // Write CSV file
            $csv = Writer::createFromPath($csvPath, 'w+');
            $csv->insertOne(['Month', 'Salary Payment Date', 'Bonus Payment Date']);
            $csv->insertAll($rows);

            // Confirm success to user
            $io->success("CSV file generated successfully at {$csvPath}");
            $this->logger->info("CSV file successfully generated at {$csvPath}");

        } catch (\Exception $e) {
            $io->error("An error occurred: {$e->getMessage()}");
            $this->logger->error("An error occurred: {$e->getMessage()}", ['exception' => $e]);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
