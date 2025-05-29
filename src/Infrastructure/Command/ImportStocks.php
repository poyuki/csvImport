<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use App\Application\Contract\StockImporterInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'csv-import:stocks')]
final class ImportStocks extends Command
{
    private const INPUT_ARGUMENT_NAME = 'input';
    private const FORCE_OPTION_NAME = 'force';
    private const MODE_OPTION_NAME = 'mode';

    private SymfonyStyle $io;
    private bool $force = false;
    private string $input;
    private InputMode $mode;

    public function __construct(
        private readonly StockImporterInterface $stockImporter,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->addArgument(self::INPUT_ARGUMENT_NAME, InputArgument::REQUIRED);
        $this->addOption(self::FORCE_OPTION_NAME, null, InputOption::VALUE_NONE);
        $this->addOption(
            self::MODE_OPTION_NAME,
            'm',
            InputOption::VALUE_OPTIONAL,
            default: InputMode::FILE->value
        );
    }


    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        parent::initialize($input, $output);
        $this->io = new SymfonyStyle($input, $output);
        $this->input = $input->getArgument(self::INPUT_ARGUMENT_NAME);
        $this->force = (bool)$input->getOption(self::FORCE_OPTION_NAME);
        $this->mode = InputMode::from($input->getOption(self::MODE_OPTION_NAME));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $report = $this->stockImporter->import($this->getStream(), $this->force);
        $this->io->writeln($report->toString());

        return self::SUCCESS;
    }

    /**
     * @return false|resource
     */
    private function getStream()
    {
        switch ($this->mode) {
            case InputMode::FILE:
                $stream = fopen($this->input, 'r+');
                $this->validateResource($stream);
                return $stream;
            case InputMode::URL:
                $context = stream_context_create([
                    'http' => [
                        'method' => 'GET',
                        'timeout' => 5, // seconds
                        'header' => "User-Agent: PHP\r\n"
                    ]
                ]);
                $stream = fopen($this->input, 'r', false, $context);
                $this->validateResource($stream);

                return $stream;
            case InputMode::STRING:
                $stream = fopen('php://temp', 'r+');
                fwrite($stream, $this->input);
                rewind($stream);
                return $stream;
        }
    }

    private function validateResource(mixed $stream): void
    {
        if (!is_resource($stream)) {
            throw new \RuntimeException('Unable to open file.');
        }
    }
}
