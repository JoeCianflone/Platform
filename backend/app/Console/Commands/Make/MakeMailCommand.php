<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Foundation\Console\MailMakeCommand;

final class MakeMailCommand extends MailMakeCommand
{
    use ModuleAwareGenerator;

    protected string $structureKey = 'src.Mail';
}
