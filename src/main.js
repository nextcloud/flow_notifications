/**
 * @copyright Copyright (c) 2020 Arthur Schiwon <blizzz@arthur-schiwon.de>
 * @author Arthur Schiwon <blizzz@arthur-schiwon.de>
 * @license AGPL-3.0-or-later
 */

import FlowNotify from './views/FlowNotify'

window.OCA.WorkflowEngine.registerOperator({
	id: 'OCA\\FlowNotifications\\Flow\\Operation',
	color: '#f1d340',
	operation: '',
	options: FlowNotify,
})
