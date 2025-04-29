<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\FlowNotifications\Notification;

use OCA\FlowNotifications\AppInfo\Application;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\Notification\INotification;
use OCP\Notification\INotifier;
use OCP\Notification\UnknownNotificationException;
use OCP\WorkflowEngine\EntityContext\IContextPortation;
use OCP\WorkflowEngine\EntityContext\IDisplayText;
use OCP\WorkflowEngine\EntityContext\IIcon;
use OCP\WorkflowEngine\IEntity;
use OCP\WorkflowEngine\IEntityEvent;
use Psr\Container\ContainerInterface;

class Notifier implements INotifier {

	public function __construct(
		private readonly IL10N $l,
		private readonly IURLGenerator $urlGenerator,
		private readonly ContainerInterface $container,
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function getID(): string {
		return Application::APP_ID;
	}

	/**
	 * @inheritDoc
	 */
	public function getName(): string {
		return $this->l->t('Flow Notifications');
	}

	/**
	 * @inheritDoc
	 */
	public function prepare(INotification $notification, string $languageCode): INotification {
		if ($notification->getApp() !== Application::APP_ID) {
			throw new UnknownNotificationException();
		}

		/** @var IEntity $entity */
		$p = $notification->getSubjectParameters();
		$entity = $this->container->get($p['entityClass']);
		if ($entity instanceof IContextPortation && isset($p['entityContext'])) {
			$entity->importContextIDs($p['entityContext']);
		}

		foreach ($entity->getEvents() as $availableEvent) {
			// this should be more comfortably provided by the Flow engine
			if (!$availableEvent instanceof IEntityEvent) {
				continue;
			}
			if ($availableEvent->getEventName() === $notification->getSubject()) {
				if (!empty($p['inscription'])) {
					$p['inscription'] .= ': ';
				}
				$notification->setParsedSubject($p['inscription'] . $availableEvent->getDisplayName());
				break;
			}
		}

		if ($entity instanceof IDisplayText) {
			$notification->setParsedMessage($entity->getDisplayText(2));
		}

		$iconUrl = $entity instanceof IIcon
			? $entity->getIconUrl()
			: $this->urlGenerator->imagePath('workflowengine', 'app-dark.svg');

		$baseUrl = $this->urlGenerator->getBaseUrl();
		$isForeignHost = filter_var($iconUrl, FILTER_VALIDATE_URL)
			&& !str_starts_with($iconUrl, $baseUrl);

		if (empty($iconUrl) || $isForeignHost) {
			// foreign sources would fail to display due to content security policy
			$iconUrl = $this->urlGenerator->imagePath('workflowengine', 'app-dark.svg');
		}

		// we need an absolute URL for support in clients
		if (!isset(parse_url($iconUrl)['scheme'])) {
			$iconUrl = $this->urlGenerator->getAbsoluteURL($iconUrl);
		}

		$notification->setIcon($iconUrl);

		return $notification;
	}
}
