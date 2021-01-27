<template>
	<div>
		<input type="text"
			maxlength="80"
			:placeholder="placeholder"
			:value="currentInscription"
			@input="emitInput">
	</div>
</template>

<script>

export default {
	name: 'FlowNotify',
	components: {},
	props: {
		value: {
			default: JSON.stringify({ inscription: '' }),
			type: String,
		},
	},
	data() {
		return {
			inscription: '',
			placeholder: t('flow_notifications', 'Choose a notification title (optional)'),
		}
	},
	computed: {
		currentInscription() {
			if (!this.value) {
				return ''
			}
			return JSON.parse(this.value).inscription
		},
	},
	methods: {
		emitInput(value) {
			if (value !== null) {
				this.$emit('input', JSON.stringify({ inscription: value.target.value }))
			}
		},
	},
}

</script>

<style scoped>
	input {
		width: 100% !important;
	}
</style>
