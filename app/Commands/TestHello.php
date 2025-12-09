<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestHello extends BaseCommand
{
    protected $group = 'test';
    protected $name = 'test:hello';
    protected $description = 'Test command';

    public function run(array $params)
    {
        CLI::write('Hello from Spark!');
    }
}
