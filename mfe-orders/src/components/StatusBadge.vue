<template>
  <span :class="badgeClass">
    {{ translatedStatus }}
  </span>
</template>

<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  status: { type: String, required: true },
})

const translatedStatus = computed(() => {
  const statusKey = props.status.toLowerCase()
  switch (statusKey) {
    case 'aprovado':
      return t('status.confirmed')
    case 'cancelado':
      return t('status.cancelled')
    case 'solicitado':
      return t('status.pending')
    default:
      return props.status
  }
})

const badgeClass = computed(() => {
  const baseClasses = 'badge'
  switch (props.status.toLowerCase()) {
    case 'aprovado':
      return `${baseClasses} badge-success`
    case 'cancelado':
      return `${baseClasses} badge-danger`
    case 'solicitado':
      return `${baseClasses} badge-warning`
    default:
      return baseClasses
  }
})
</script>