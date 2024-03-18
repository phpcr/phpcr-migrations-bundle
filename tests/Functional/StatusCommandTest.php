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

class StatusCommandTest extends BaseTestCase
{
    /**
     * It should list all of the migrations.
     */
    public function testShowAll(): void
    {
        $tester = $this->executeCommand('phpcr_migrations.command.status', []);
        $display = $tester->getDisplay();

        $this->assertStringContainsString('No migrations have been executed', $display);
    }

    /**
     * It should show the current version.
     */
    public function testShowCurrentVersion(): void
    {
        $this->executeCommand('phpcr_migrations.command.migrate', ['to' => '201501011500']);
        $tester = $this->executeCommand('phpcr_migrations.command.status', []);
        $display = $tester->getDisplay();

        $this->assertStringContainsString('201501011500', $display);
    }
}
