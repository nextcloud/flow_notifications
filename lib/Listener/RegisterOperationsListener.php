<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2024 Arthur Schiwon <blizzz@arthur-schiwon.de>
 *
 * @author Arthur Schiwon <blizzz@arthur-schiwon.de>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
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
