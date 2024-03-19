<?php

/*
 * This file is part of the PHPCR Migrations package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\PhpcrMigrationsBundle\Command;

use PHPCR\Migrations\VersionFinder;
use PHPCR\Migrations\VersionStorage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatusCommand extends Command
{
    public function __construct(
        private VersionStorage $versionStorage,
        private VersionFinder $finder,
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->setName('phpcr:migrations:status');
        $this->setDescription('Show the current migration status');
        $this->setHelp(<<<EOT
Display a table which displays all available migrations and whether they have been migrated or not:

- <info>Version</info>: Name of the migration version
- <info>Date</info>: Date that the migration was <comment>created</comment>
- <info>Migrated</info>: Date that the migration was <comment>executed</comment> or <comment>n/a</comment>
- <info>Path</info>: Path to the migration file

EOT
        );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $versionCollection = $this->finder->getCollection();
        $executedVersions = $this->versionStorage->getPersistedVersions();
        $currentVersion = $this->versionStorage->getCurrentVersion();

        $table = new Table($output);
        $table->setHeaders([
            '', 'Version', 'Date', 'Migrated', 'Path',
        ]);

        foreach ($versionCollection->getAllVersions() as $versionName => $versionClass) {
            $reflection = new \ReflectionClass($versionClass);
            $table->addRow([
                $versionName === $currentVersion ? '*' : '',
                $versionName,
                $this->getDate($versionName),
                isset($executedVersions[$versionName]) ? '<info>'.$executedVersions[$versionName]['executed']->format('Y-m-d H:i:s').'</info>' : 'n/a',
                substr($reflection->getFileName(), strlen(getcwd()) + 1),
            ]);
        }

        $table->render();

        if ($currentVersion) {
            $output->writeln(sprintf('<info>Current:</info> %s (%s)', $currentVersion, $this->getDate($currentVersion)));
        } else {
            $output->writeln('<info>No migrations have been executed</info>');
        }

        return 0;
    }

    private function getDate($versionName): string
    {
        return date('Y-m-d H:i', strtotime($versionName));
    }
}
