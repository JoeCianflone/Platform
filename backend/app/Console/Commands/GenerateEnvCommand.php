<?php declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

final class GenerateEnvCommand extends Command
{
    protected $description = 'Generate .env from .env.example + .env.local overrides';
    protected $signature = 'env:generate';

    public function handle(): int
    {
        $envPath = $this->laravel->environmentPath();
        $exampleFile = $envPath.'/.env.example';
        $localExampleFile = $envPath.'/.env.local.example';
        $localFile = $envPath.'/.env.local';
        $outputFile = $envPath.'/.env';

        if (! file_exists($exampleFile)) {
            $this->error('.env.example not found at '.$exampleFile);

            return self::FAILURE;
        }

        $lines = file($exampleFile);

        if ($lines === false) {
            $this->error('Could not read .env.example');

            return self::FAILURE;
        }

        $this->generateLocalExample($lines, $localExampleFile);

        if (! file_exists($localFile)) {
            copy($localExampleFile, $localFile);
            $this->info('.env.local created from .env.local.example — fill in your local values');
        }

        $local = $this->parseEnvFile($localFile);
        $usedKeys = [];

        $output = ['# Generated from .env.example + .env.local — do not edit directly.'.PHP_EOL];

        foreach ($lines as $line) {
            if (str_starts_with($line, '# Generated from')) {
                continue;
            }

            if (str_starts_with(trim($line), '#') || trim($line) === '') {
                $output[] = $line;
                continue;
            }

            if (str_contains($line, '=')) {
                [$key] = explode('=', $line, 2);
                $key = trim($key);
                $usedKeys[] = $key;
                $output[] = array_key_exists($key, $local)
                    ? $key.'='.$local[$key].PHP_EOL
                    : $line;
                continue;
            }

            $output[] = $line;
        }

        $extra = array_diff_key($local, array_flip($usedKeys));

        if ($extra !== []) {
            $output[] = PHP_EOL;
            $output[] = '# Keys from .env.local not in .env.example'.PHP_EOL;
            foreach ($extra as $key => $value) {
                $output[] = $key.'='.$value.PHP_EOL;
            }
        }

        file_put_contents($outputFile, implode('', $output));
        $this->info('.env generated from .env.example + .env.local');

        return self::SUCCESS;
    }

    /** @param list<string> $exampleLines */
    private function generateLocalExample(array $exampleLines, string $path): void
    {
        $output = ['# Copy this file to .env.local and fill in your local values.'.PHP_EOL];

        foreach ($exampleLines as $line) {
            if (str_starts_with($line, '# Copy this file')) {
                continue;
            }

            if (str_starts_with(trim($line), '#') || trim($line) === '') {
                $output[] = $line;
                continue;
            }

            if (str_contains($line, '=')) {
                [$key] = explode('=', $line, 2);
                $output[] = trim($key).'='.PHP_EOL;
                continue;
            }

            $output[] = $line;
        }

        file_put_contents($path, implode('', $output));
    }

    /** @return array<string, string> */
    private function parseEnvFile(string $path): array
    {
        if (! file_exists($path)) {
            return [];
        }

        $result = [];
        $lines = file($path, \FILE_IGNORE_NEW_LINES | \FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            return [];
        }

        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#') || ! str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $result[trim($key)] = trim($value);
        }

        return $result;
    }
}
