<!--
  - SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->
<template>
	<div>
		<input v-model="inscription"
			type="text"
			maxlength="80"
			:placeholder="placeholder">
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
		value: {
			default: '',
			type: String,
		},
	},
	emits: ['input', 'update:model-value'],
	data() {
		const inscription = (this.modelValue || this.value)
			? JSON.parse(this.modelValue || this.value).inscription
			: ''
		return {
			inscription,
			placeholder: t('flow_notifications', 'Choose a notification title (optional)'),
		}
	},
	watch: {
		modelValue() {
			this.inscription = JSON.parse(this.modelValue || this.value).inscription
		},
		value() {
			this.inscription = JSON.parse(this.modelValue || this.value).inscription
		},
		inscription() {
			this.$emit('input', JSON.stringify({ inscription: this.inscription }))
			this.$emit('update:model-value', JSON.stringify({ inscription: this.inscription }))
		},
	},
}

</script>

<style scoped>
	input {
		width: 100% !important;
	}
</style>
