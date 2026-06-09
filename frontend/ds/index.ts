// Layout primitives
export { default as PlatStack } from '@ds/components/layouts/PlatStack.vue'
export { default as PlatCluster } from '@ds/components/layouts/PlatCluster.vue'
export { default as PlatGrid } from '@ds/components/layouts/PlatGrid.vue'
export { default as PlatShell } from '@ds/components/layouts/PlatShell.vue'

// UI primitives
export { default as PlatButton } from '@ds/components/primitives/PlatButton.vue'
export { default as PlatText } from '@ds/components/primitives/PlatText.vue'
export { default as PlatIcon } from '@ds/components/primitives/PlatIcon.vue'
export { default as PlatBadge } from '@ds/components/primitives/PlatBadge.vue'
export { default as PlatCard } from '@ds/components/primitives/PlatCard.vue'
export { default as PlatCardHeader } from '@ds/components/primitives/PlatCardHeader.vue'
export { default as PlatCardBody } from '@ds/components/primitives/PlatCardBody.vue'
export { default as PlatCardFooter } from '@ds/components/primitives/PlatCardFooter.vue'
export { default as PlatDialog } from '@ds/components/primitives/PlatDialog.vue'

// Forms
export { default as PlatForm } from '@ds/components/forms/PlatForm.vue'
export { default as PlatFormSection } from '@ds/components/forms/PlatFormSection.vue'
export { default as PlatTextField } from '@ds/components/forms/PlatTextField.vue'
export { default as PlatTextarea } from '@ds/components/forms/PlatTextarea.vue'
export { default as PlatSelect } from '@ds/components/forms/PlatSelect.vue'
export { default as PlatCheckbox } from '@ds/components/forms/PlatCheckbox.vue'
export { default as PlatSwitch } from '@ds/components/forms/PlatSwitch.vue'
export { default as PlatRadioGroup } from '@ds/components/forms/PlatRadioGroup.vue'

// Tables
export { default as PlatDataTable } from '@ds/components/tables/PlatDataTable.vue'

// Patterns
export { default as PlatPage } from '@ds/components/patterns/PlatPage.vue'
export { default as PlatPageHeader } from '@ds/components/patterns/PlatPageHeader.vue'
export { default as PlatEmptyState } from '@ds/components/patterns/PlatEmptyState.vue'
export { default as PlatConfirmDialog } from '@ds/components/patterns/PlatConfirmDialog.vue'
export { default as PlatStatCard } from '@ds/components/patterns/PlatStatCard.vue'
export { default as PlatFilterBar } from '@ds/components/patterns/PlatFilterBar.vue'

// Composables
export { usePlatConfirm } from '@ds/composables/usePlatConfirm'
export { usePlatTheme } from '@ds/composables/usePlatTheme'
export { usePlatToast } from '@ds/composables/usePlatToast'

// Types
export type {
  AppVariant,
  AppSize,
  AppPlacement,
  AppTheme,
  InertiaForm,
  TableColumn,
  PaginationMeta,
} from '@ds/types/ui'
