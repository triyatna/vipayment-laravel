<?php

namespace Triyatna\Vipayment\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallVipayment extends Command
{
    protected $signature = 'ty-vipayment:install';
    protected $description = 'Install Vipayment package and setup .env variables.';

    public function handle()
    {
        $this->info('Installing Vipayment Laravel Package...');

        $this->info('Publishing configuration...');
        $this->call('vendor:publish', [
            '--provider' => "Triyatna\Vipayment\VipaymentServiceProvider",
            '--tag' => "vipayment-config"
        ]);

        $this->addEnvVariables();

        $this->info('Vipayment Laravel Package installed successfully.');
        $this->comment('Please fill in your VIPAYMENT_API_ID and VIPAYMENT_API_KEY in your .env file.');
    }

    protected function addEnvVariables()
    {
        $envPath = base_path('.env');

        if (File::exists($envPath)) {
            $envContent = File::get($envPath);

            if (!str_contains($envContent, 'VIPAYMENT_API_ID')) {
                File::append($envPath, "\nVIPAYMENT_API_ID=\n");
            }
            if (!str_contains($envContent, 'VIPAYMENT_API_KEY')) {
                File::append($envPath, "VIPAYMENT_API_KEY=\n");
            }
            $this->info('.env variables added.');
        } else {
            $this->warn('.env file not found. Please create one and add the variables manually.');
        }
    }
}
