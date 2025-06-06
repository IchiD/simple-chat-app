export interface Group {
  id: number
  owner_user_id: number
  name: string
  description?: string | null
  max_members?: number
  qr_code_token?: string
  created_at?: string
  updated_at?: string
  members?: GroupMember[]
}

export interface GroupMember {
  id: number
  group_id: number
  user_id: number | null
  guest_identifier?: string | null
  nickname: string
  joined_at?: string
  is_active: boolean
}

export interface GroupMessage {
  id: number
  group_id: number
  sender_user_id: number | null
  message: string
  created_at: string
  sender?: { id: number; name: string } | null
}

export interface Paginated<T> {
  data: T[]
  links: {
    first?: string | null
    last?: string | null
    prev?: string | null
    next?: string | null
    [key: string]: unknown
  }
  meta: Record<string, unknown>
}

export type PaginatedGroupMessages = Paginated<GroupMessage>
