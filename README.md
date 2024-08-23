# Salary Payment Date tool
A command-line utility to generate a CSV file with the dates for salary payment based on below conditions.
## Requirements Breakdown:

•	**Base Salary Payment Date:** The last day of the month unless it's a weekend, then it’s the preceding Friday. 

•	**Bonus Payment Date:** The 15th of the month unless it's a weekend, then it’s the first Wednesday after the 15th. 

•	**Output:** A CSV file with 3 columns as "Month", "Salary Payment Date", "Bonus Payment Date" 

## Requirements
•	PHP 8.2+ 

•	Composer 

•	Symfony 6.4+ 

## Packages Used
•	Carbon

•	logger

•	filesystem

•	league csv

•	phpunit

## Installation Process

### Clone the Git repository:
```
git clone https://github.com/lokesh1515/salary-paydate-tool.git
```
```
cd salary-paydate-tool
```

### Install dependencies:
```
composer install
```

### Run the command :

```
php bin/console app:salary-pay-dates <filename>
```

Command Alias : php bin/console app:spd <filename>


**Note:** Filename is Mendatory [Without .CSV Extension]

### Command Help
To see the available options and arguments for the command, you can use:
```
php bin/console app:salary-pay-dates --help
```

### Run the PHPUnit Test
```
vendor/bin/phpunit tests/Command/SalaryPayDatesCommandTest.php
```
