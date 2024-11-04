<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\FlowNotifications\Listener;

use OCA\FlowNotifications\AppInfo\Application;
use OCA\FlowNotifications\Flow\Operation;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Util;
use OCP\WorkflowEngine\Events\RegisterOperationsEvent;

/**
 * @template-implements IEventListener<Event|RegisterOperationsEvent>
 */
class RegisterOperationsListener implements IEventListener {
	public function __construct(
		protected readonly Operation $operation,
	) {
	}

	public function handle(Event $event): void {
		if (!$event instanceof RegisterOperationsEvent) {
			return;
		}

		$event->registerOperation($this->operation);
		Util::addScript(Application::APP_ID, 'flow_notifications-main');
	}
}
