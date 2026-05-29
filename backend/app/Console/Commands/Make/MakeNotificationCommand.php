<?php declare(strict_types=1);

namespace App\Console\Commands\Make;

use Illuminate\Foundation\Console\NotificationMakeCommand;

final class MakeNotificationCommand extends NotificationMakeCommand
{
    use ModuleAwareGenerator;

    protected string $structureKey = 'src.Notifications';
}
