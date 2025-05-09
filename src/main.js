/**
 * SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import wrap from '@vue/web-component-wrapper'
import Vue from 'vue'
import semver from 'semver'

import FlowNotify from './views/FlowNotify.vue'

let customElementId
// we may only compare first three numbers of the version
if (semver.gt(window.OC.config.version.split('.', 3).join('.'), '31.0.2')) {
	const FlowNotifyComponent = wrap(Vue, FlowNotify)
	customElementId = 'oca-flow_notifications-operation-flow_notify'
	window.customElements.define(customElementId, FlowNotifyComponent)

	// In Vue 2, wrap doesn't support disabling shadow :(
	// Disable with a hack
	Object.defineProperty(FlowNotifyComponent.prototype, 'attachShadow', { value() { return this } })
	Object.defineProperty(FlowNotifyComponent.prototype, 'shadowRoot', { get() { return this } })
}

window.OCA.WorkflowEngine.registerOperator({
	id: 'OCA\\FlowNotifications\\Flow\\Operation',
	color: '#f1d340',
	operation: '',
	element: customElementId,
	options: FlowNotify,
})
