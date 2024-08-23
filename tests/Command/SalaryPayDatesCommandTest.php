<?php 
// tests/Command/SalaryPayDatesCommandTest.php
namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use League\Csv\Reader;

class SalaryPayDatesCommandTest extends KernelTestCase
{
    private const TEST_CSV_FILE = 'salary2024.csv';

    public function testCommandGeneratesCsvFile(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $command = $application->find('app:salary-pay-dates');

       
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'filename' => self::TEST_CSV_FILE,
        ]);

        // Check for success output
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('CSV file generated successfully at', $output);
        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());

        // Verify the CSV file was created and contains the correct data
        $this->assertFileExists(self::TEST_CSV_FILE);

        // Read and verify CSV content
        $csv = Reader::createFromPath(self::TEST_CSV_FILE, 'r');
        $records = iterator_to_array($csv->getRecords());

        // Check the header row
        $this->assertEquals(['Month', 'Salary Payment Date', 'Bonus Payment Date'], $records[0]);

        // Check that there are rows after the header
        $this->assertGreaterThan(1, count($records));

        // Clean up the CSV file after test
        unlink(self::TEST_CSV_FILE);
    }
}
