<?php

declare(strict_types=1);

namespace ShipWell\CornerstoneSupport\Console\Commands;

use Illuminate\Support\Str;
use Livewire\Features\SupportConsoleCommands\Commands\MakeCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:livewire', aliases: ['livewire:make'])]
class LivewireMakeCommand extends MakeCommand
{
    protected $name = 'make:livewire';

    protected $aliases = ['livewire:make'];

    public function handle(): int
    {
        $type = $this->option('type');

        if ($this->option('sfc') || $this->option('mfc') || ($type !== null && $type !== 'class') || $this->option('js') || $this->option('css')) {
            $this->components->error('Cornerstone requires named class-based Livewire components with external views and direct Pest tests.');

            return self::INVALID;
        }

        $this->input->setOption('class', true);
        $this->input->setOption('test', true);

        $result = parent::handle();

        return is_int($result) ? $result : self::SUCCESS;
    }

    protected function buildClassBasedComponentTest(string $name): string
    {
        $segments = explode('.', $name);
        $class = collect($segments)->map(fn (string $segment): string => Str::studly($segment))->implode('\\');
        $stub = $this->files->get($this->getStubPath('livewire.pest.stub'));

        return str_replace('[component]', 'App\\Livewire\\' . $class, $stub);
    }
}
