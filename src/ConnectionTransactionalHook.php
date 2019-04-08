<?php declare(strict_types = 1);

namespace Mangoweb\NextrasTransactional;

use Mangoweb\Tester\Infrastructure\Container\AppContainerHook;
use Nette\DI\Container;
use Nette\DI\ContainerBuilder;
use Nette\DI\Definitions\ServiceDefinition;
use Nextras\Dbal\IConnection;

class ConnectionTransactionalHook extends AppContainerHook
{
	/** @var TransactionalTestListener */
	private $transactionalTestListener;


	public function __construct(TransactionalTestListener $transactionalTestListener)
	{
		$this->transactionalTestListener = $transactionalTestListener;
	}


	public function onCompile(ContainerBuilder $builder): void
	{
		$builder->addImportedDefinition('transactionalTestListener')
			->setType(TransactionalTestListener::class);

		$definition = $builder->getDefinitionByType(IConnection::class);
		assert($definition instanceof ServiceDefinition);

		$definition->addSetup('$onConnect', [[['@transactionalTestListener', 'startTransaction']]]);
	}


	public function onCreate(Container $applicationContainer): void
	{
		$applicationContainer->addService('transactionalTestListener', $this->transactionalTestListener);
	}
}
