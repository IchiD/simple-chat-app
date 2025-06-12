// 新しいConversationベースのグループ型定義
export interface GroupConversation {
  id: number;
  type: "group";
  name: string;
  description?: string | null;
  max_members?: number;
  owner_user_id: number;
  qr_code_token?: string;
  room_token: string;
  chat_styles?: string[];
  created_at?: string;
  updated_at?: string;
  conversationParticipants?: GroupParticipant[];
  member_count?: number; // withCountで追加される場合
}

export interface GroupParticipant {
  id: number;
  conversation_id: number;
  user_id: number;
  joined_at?: string;
  user?: {
    id: number;
    name: string;
    friend_id: string;
    email?: string;
  };
}

export interface GroupMessage {
  id: number;
  conversation_id: number;
  sender_id: number;
  text_content: string;
  sent_at: string;
  sender?: {
    id: number;
    name: string;
  } | null;
  sender_has_left?: boolean;
  sender_left_at?: string | null;
}

export interface Paginated<T> {
  data: T[];
  links: {
    first?: string | null;
    last?: string | null;
    prev?: string | null;
    next?: string | null;
    [key: string]: unknown;
  };
  meta: Record<string, unknown>;
}

export type PaginatedGroupMessages = Paginated<GroupMessage>;
