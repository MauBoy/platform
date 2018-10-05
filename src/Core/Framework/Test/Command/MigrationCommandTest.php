<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Test\Command;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Command\MigrationCommand;
use Shopware\Core\Framework\Command\MigrationDestructiveCommand;
use Shopware\Core\Framework\Migration\Exception\MigrateException;
use Shopware\Core\Framework\Migration\MigrationCollectionLoader;
use Shopware\Core\Framework\Migration\MigrationRuntime;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

class MigrationCommandTest extends TestCase
{
    use IntegrationTestBehaviour;

    protected function tearDown()
    {
        $connection = $this->getConnection();

        $connection->createQueryBuilder()
            ->delete('migration')
            ->where('`class` LIKE "%_test_migrations_valid%"')
            ->execute();
    }

    public function getCommand(bool $exceptions = false): MigrationCommand
    {
        $container = $this->getKernel()->getContainer();

        $directories = $container->getParameter('migration.directories');

        $directories['Shopware\Core\Framework\Test\Migration\_test_migrations_valid'] =
            __DIR__ . '/../Migration/_test_migrations_valid';

        if ($exceptions) {
            $directories['Shopware\Core\Framework\Test\Migration\_test_migrations_valid_run_time_exceptions'] =
            __DIR__ . '/../Migration/_test_migrations_valid_run_time_exceptions';
        }

        return new MigrationCommand(
            new MigrationCollectionLoader($this->getConnection()),
            $container->get(MigrationRuntime::class),
            $directories
        );
    }

    public function getDestructiveCommand(bool $exceptions = false): MigrationDestructiveCommand
    {
        $container = $this->getKernel()->getContainer();

        $directories = $container->getParameter('migration.directories');

        $directories['Shopware\Core\Framework\Test\Migration\_test_migrations_valid'] =
            __DIR__ . '/../Migration/_test_migrations_valid';

        if ($exceptions) {
            $directories['Shopware\Core\Framework\Test\Migration\_test_migrations_valid_run_time_exceptions'] =
                __DIR__ . '/../Migration/_test_migrations_valid_run_time_exceptions';
        }

        return new MigrationDestructiveCommand(
            new MigrationCollectionLoader($this->getConnection()),
            $container->get(MigrationRuntime::class),
            $directories
        );
    }

    public function test_command_migrate_no_until_no_all_option(): void
    {
        self::assertSame(0, $this->getMigrationCount(true));

        $command = $this->getCommand();

        $this->expectException(\InvalidArgumentException::class);
        $command->run(new ArrayInput([]), new DummyOutput());
    }

    public function test_command_migrate_all_option(): void
    {
        self::assertSame(0, $this->getMigrationCount());

        $command = $this->getCommand();

        $command->run(new ArrayInput(['-all' => true]), new DummyOutput());

        self::assertSame(2, $this->getMigrationCount());
    }

    public function test_command_add_Migrations(): void
    {
        self::assertSame(0, $this->getMigrationCount());

        $command = $this->getCommand();

        $command->run(new ArrayInput(['until' => PHP_INT_MAX]), new DummyOutput());

        self::assertSame(2, $this->getMigrationCount());
    }

    public function test_command_migrate_migration_exception(): void
    {
        self::assertSame(0, $this->getMigrationCount(true));

        $command = $this->getCommand(true);

        try {
            $command->run(new ArrayInput(['-all' => true]), new DummyOutput());
        } catch (MigrateException $e) {
            //nth
        }

        self::assertSame(3, $this->getMigrationCount(true));
    }

    public function test_destructive_command_migrate_no_until_no_all_option(): void
    {
        self::assertSame(0, $this->getMigrationCount(true));

        $command = $this->getDestructiveCommand();

        $this->expectException(\InvalidArgumentException::class);
        $command->run(new ArrayInput([]), new DummyOutput());
    }

    public function test_destructive_command_migrate_all_option(): void
    {
        self::assertSame(0, $this->getMigrationCount());

        $command = $this->getDestructiveCommand();

        $command->run(new ArrayInput(['-all' => true]), new DummyOutput());

        self::assertSame(2, $this->getMigrationCount());
    }

    public function test_destructive_command_add_Migrations(): void
    {
        self::assertSame(0, $this->getMigrationCount());

        $command = $this->getDestructiveCommand();

        $command->run(new ArrayInput(['until' => PHP_INT_MAX]), new DummyOutput());

        self::assertSame(2, $this->getMigrationCount());
    }

    public function test_command_migrate_migration_destructive(): void
    {
        self::assertSame(0, $this->getMigrationCount(true, true));

        $command = $this->getCommand(true);

        try {
            $command->run(new ArrayInput(['-all' => true]), new DummyOutput());
        } catch (MigrateException $e) {
            //nth
        }

        $command = $this->getDestructiveCommand(true);

        try {
            $command->run(new ArrayInput(['-all' => true]), new DummyOutput());
        } catch (MigrateException $e) {
            //nth
        }

        self::assertSame(2, $this->getMigrationCount(true, true));
    }

    public function test_command_migrate(): void
    {
        self::assertSame(0, $this->getMigrationCount(true));

        $command = $this->getCommand();

        $command->run(new ArrayInput(['-all' => true]), new DummyOutput());

        self::assertSame(2, $this->getMigrationCount(true));
    }

    private function getConnection(): Connection
    {
        return $this->getKernel()->getContainer()->get(Connection::class);
    }

    private function getMigrationCount(bool $executed = false, bool $destructive = false): int
    {
        $connection = $this->getConnection();

        $query = $connection->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('migration')
            ->where('`class` LIKE "%_test_migrations_valid%"');

        if ($executed && $destructive) {
            $query->andWhere('`update_destructive` IS NOT NULL');
        } elseif ($executed && !$destructive) {
            $query->andWhere('`update` IS NOT NULL');
        }

        return (int) $query->execute()->fetchColumn();
    }
}