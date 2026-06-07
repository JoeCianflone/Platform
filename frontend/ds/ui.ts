export type AppVariant = 'primary' | 'secondary' | 'success' | 'warning' | 'danger' | 'neutral'

export type AppSize = 'sm' | 'md' | 'lg'

export type AppPlacement =
    | 'top'
    | 'top-start'
    | 'top-end'
    | 'bottom'
    | 'bottom-start'
    | 'bottom-end'
    | 'left'
    | 'left-start'
    | 'left-end'
    | 'right'
    | 'right-start'
    | 'right-end'

export type AppTheme = 'light' | 'dark'

// Loose type for Inertia useForm() instances passed to form field components.
// eslint-disable-next-line @typescript-eslint/no-explicit-any
export interface InertiaForm extends Record<string, any> {
    errors: Record<string, string | string[]>
    clearErrors: (...fields: string[]) => void
    processing: boolean
}

export interface TableColumn {
    key: string
    label: string
    sortable?: boolean
}

export interface PaginationMeta {
    current_page: number
    last_page: number
    per_page: number
    total: number
    from: number
    to: number
}
