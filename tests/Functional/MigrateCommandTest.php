<?php

/*
 * This file is part of the PHPCR Migrations package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCR\PhpcrMigrationsBundle\Tests\Functional;

class MigrateCommandTest extends BaseTestCase
{
    /**
     * It should migrate all the unexecuted migrators.
     */
    public function testMigrateToLatest(): void
    {
        $this->executeCommand('phpcr_migrations.command.migrate', []);

        $versionNodes = $this->session->getNode('/jcr:migrations')->getNodes();
        $this->assertCount(5, $versionNodes);
    }

    /**
     * It should upgrade to a given version.
     */
    public function testUpgradeTo(): void
    {
        $tester = $this->executeCommand('phpcr_migrations.command.migrate', ['to' => '201401011300']);
        $display = $tester->getDisplay();

        $this->assertStringContainsString('Upgrading 1 version', $display);

        $versionNodes = $this->session->getNode('/jcr:migrations')->getNodes();
        $this->assertCount(1, $versionNodes);
    }

    /**
     * It should downgrade to a given version.
     */
    public function testUpgradeRevertTo(): void
    {
        $this->executeCommand('phpcr_migrations.command.migrate', []);
        $tester = $this->executeCommand('phpcr_migrations.command.migrate', ['to' => '201501011200']);
        $display = $tester->getDisplay();

        $this->assertStringContainsString('Reverting 3 version', $display);

        $versionNodes = $this->session->getNode('/jcr:migrations')->getNodes();
        $this->assertCount(2, $versionNodes);
    }
}
