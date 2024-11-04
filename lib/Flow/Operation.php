<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\FlowNotifications\Flow;

use DateTime;
use OCA\FlowNotifications\AppInfo\Application;
use OCP\EventDispatcher\Event;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\IUser;
use OCP\IUserSession;
use OCP\Notification\IManager;
use OCP\WorkflowEngine\EntityContext\IContextPortation;
use OCP\WorkflowEngine\EntityContext\IUrl;
use OCP\WorkflowEngine\IManager as FlowManager;
use OCP\WorkflowEngine\IOperation;
use OCP\WorkflowEngine\IRuleMatcher;
use Psr\Log\LoggerInterface;
use UnexpectedValueException;
use function json_decode;
use function json_encode;

class Operation implements IOperation {
	public function __construct(
		private readonly IL10N $l,
		private readonly IURLGenerator $urlGenerator,
		private readonly IManager $notificationManager,
		private readonly IUserSession $userSession,
		private readonly LoggerInterface $logger,
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function getDisplayName(): string {
		return $this->l->t('Send a notification');
	}

	/**
	 * @inheritDoc
	 */
	public function getDescription(): string {
		return $this->l->t('Triggers a notification');
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon(): string {
		return $this->urlGenerator->imagePath('notifications', 'notifications.svg');
	}

	/**
	 * @inheritDoc
	 */
	public function isAvailableForScope(int $scope): bool {
		return $scope === FlowManager::SCOPE_USER;
	}

	/**
	 * @inheritDoc
	 */
	public function validateOperation(string $name, array $checks, string $operation): void {
		// pass
	}

	/**
	 * @inheritDoc
	 */
	public function onEvent(string $eventName, Event $event, IRuleMatcher $ruleMatcher): void {
		$flows = $ruleMatcher->getFlows(false);
		foreach ($flows as $flow) {
			try {
				$uid = $flow['scope_actor_id'];
				$sessionUser = $this->userSession->getUser();
				if ($sessionUser instanceof IUser && $uid === $sessionUser->getUID()) {
					continue;
				}

				$entity = $ruleMatcher->getEntity();
				$parameters = ['entityClass' => get_class($entity)];
				if ($entity instanceof IContextPortation) {
					if (json_encode($entity->exportContextIDs()) !== false) {
						$parameters['entityContext'] = $entity->exportContextIDs();
					} else {
						$this->logger->debug('Context of {entity} cannot be JSON-encoded',
							[
								'entity' => get_class($entity),
							]
						);
					}
				}

				$flowOptions = json_decode($flow['operation'], true);
				if (!is_array($flowOptions) || empty($flowOptions)) {
					throw new UnexpectedValueException('Cannot decode operation details');
				}
				$parameters['inscription'] = trim($flowOptions['inscription'] ?? '');

				$notification = $this->notificationManager->createNotification();
				$notification->setApp(Application::APP_ID)
					->setSubject($eventName, $parameters)
					->setUser($uid)
					->setObject($flow['entity'], '0')
					->setDateTime(new DateTime());

				if ($entity instanceof IUrl) {
					$url = $entity->getUrl();
					if ($url === '') {
						$notification->setLink($url);
					}
				}

				$this->notificationManager->notify($notification);
			} catch (UnexpectedValueException) {
				continue;
			}
		}
	}
}
