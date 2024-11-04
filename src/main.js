/**
 * SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import FlowNotify from './views/FlowNotify.vue'

window.OCA.WorkflowEngine.registerOperator({
	id: 'OCA\\FlowNotifications\\Flow\\Operation',
	color: '#f1d340',
	operation: '',
	options: FlowNotify,
})
