/**
 * SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import { defineCustomElement } from 'vue'
import FlowNotify from './views/FlowNotify.vue'

const FlowNotifyComponent = defineCustomElement(FlowNotify, { shadowRoot: false })
const customElementId = 'oca-flow_notifications-operation-flow_notify'
window.customElements.define(customElementId, FlowNotifyComponent)

window.OCA.WorkflowEngine.registerOperator({
	id: 'OCA\\FlowNotifications\\Flow\\Operation',
	color: '#f1d340',
	operation: '',
	element: customElementId,
})
