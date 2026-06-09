export interface AuthUser {
  id: number
  name: string
  email: string
  is_premium: boolean
}

export interface SharedProps {
  [key: string]: unknown
  auth: { user: AuthUser | null }
  flash: { success: string | null }
  errors: Record<string, string[]>
}
