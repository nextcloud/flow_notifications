<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\FlowNotifications\Listener;

use OCA\FlowNotifications\AppInfo\Application;
use OCA\FlowNotifications\Flow\Operation;
use OCP\App\IAppManager;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Util;
use OCP\WorkflowEngine\Events\RegisterOperationsEvent;
use Psr\Log\LoggerInterface;

/**
 * @template-implements IEventListener<Event|RegisterOperationsEvent>
 */
class RegisterOperationsListener implements IEventListener {
	public function __construct(
		protected readonly Operation $operation,
		protected IAppManager $appManager,
		private readonly LoggerInterface $logger,
	) {
	}

	public function handle(Event $event): void {
		if (!$event instanceof RegisterOperationsEvent) {
			return;
		} elseif (!$this->appManager->isEnabledForUser('notifications')) {
			$this->logger->error('Failed to register `flow_notifications` app. This could happen due to the `notifications` app isn\'t installed or enabled.', ['app' => 'flow_notifications']);
			return;
		}

		$event->registerOperation($this->operation);
		Util::addScript(Application::APP_ID, 'flow_notifications-main');
	}
}
