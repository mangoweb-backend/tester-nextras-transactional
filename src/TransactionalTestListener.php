<?php declare(strict_types = 1);

namespace Mangoweb\NextrasTransactional;

use Mangoweb\Tester\Infrastructure\ITestCaseListener;
use Mangoweb\Tester\Infrastructure\TestCase;
use Nextras\Dbal\IConnection;


class TransactionalTestListener implements ITestCaseListener
{
	/** @var null|IConnection */
	private $connection;


	public function setUp(TestCase $testCase): void
	{
	}


	public function startTransaction(IConnection $connection): void
	{
		$this->connection = $connection;
		$this->connection->beginTransaction();
		foreach ($connection->getPlatform()->getTables() as $table) {
			if ($table['name'] === 'migrations') {
				continue;
			}
			if ($table['is_view']) {
				continue;
			}
			$connection->query('ALTER TABLE %table AUTO_INCREMENT = %i', $table['name'], 1);
		}
		$this->connection->query('SET autocommit = 0');

		// this will be reverted. if not, migration continue will fail, database creator will recreate a database
		$this->connection->query('UPDATE migrations SET checksum = \'\'');
	}


	public function tearDown(TestCase $testCase): void
	{
		if (!$this->connection) {
			return;
		}
		$this->connection->rollbackTransaction();
	}
}
