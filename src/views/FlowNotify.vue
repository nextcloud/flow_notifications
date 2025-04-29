<!--
  - SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->
<template>
	<div>
		<input v-model="currentInscription"
			type="text"
			maxlength="80"
			:placeholder="placeholder"
			@input="emitInput">
	</div>
</template>

<script>

export default {
	name: 'FlowNotify',
	components: {},
	props: {
		modelValue: {
			default: '',
			type: String,
		},
	},
	emits: ['update:model-value'],
	data() {
		return {
			inscription: '',
			placeholder: t('flow_notifications', 'Choose a notification title (optional)'),
		}
	},
	computed: {
		currentInscription() {
			if (!this.modelValue) {
				return ''
			}
			return JSON.parse(this.modelValue).inscription
		},
	},
	methods: {
		emitInput(value) {
			if (value === null) {
				return
			}
			this.$emit('update:model-value', JSON.stringify({ inscription: value.target.value }))
		},
	},
}

</script>

<style scoped>
	input {
		width: 100% !important;
	}
</style>
