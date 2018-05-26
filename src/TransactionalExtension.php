<?php declare(strict_types = 1);

namespace Mangoweb\NextrasTransactional;

use Mangoweb\Tester\Infrastructure\MangoTesterExtension;
use Nette\DI\CompilerExtension;


class TransactionalExtension extends CompilerExtension
{
	public function loadConfiguration()
	{
		parent::loadConfiguration();

		$builder = $this->getContainerBuilder();
		$builder->addDefinition($this->prefix('connectionTransactionalHook'))
			->setClass(ConnectionTransactionalHook::class)
			->addTag(MangoTesterExtension::TAG_HOOK);
		$builder->addDefinition($this->prefix('transactionTestListener'))
			->setClass(TransactionalTestListener::class);
	}
}
