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

		if (empty($iconUrl) || isset(parse_url($iconUrl)['scheme'])) {
			// foreign sources would fail to display due to content security policy
			$iconUrl = $this->urlGenerator->imagePath('workflowengine', 'app-dark.svg');
		}

		$notification->setIcon($iconUrl);

		return $notification;
	}
}
