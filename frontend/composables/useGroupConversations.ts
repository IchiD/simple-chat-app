import type {
  GroupConversation,
  GroupParticipant,
  GroupMessage,
  PaginatedGroupMessages,
} from "~/types/group";

interface CreateGroupRequest extends Record<string, unknown> {
  name: string;
  description?: string;
  max_members?: number;
  chatStyles?: string[];
}

interface AddMemberRequest extends Record<string, unknown> {
  friend_id: string;
}

interface GroupMember extends Record<string, unknown> {
  id: number;
  name: string;
  friend_id: string;
  group_member_label: string;
}

interface ExtendedGroupMember extends GroupMember {
  member_id: number; // GroupMemberのID
  role: string;
  owner_nickname: string | null; // オーナー専用ニックネーム
  joined_at: string;
  left_at: string | null;
  can_rejoin: boolean;
  removal_type: string | null;
  removed_by_user: {
    id: number;
    name: string;
  } | null;
  is_active: boolean;
}

interface BulkMessageRequest extends Record<string, unknown> {
  target_user_ids: number[];
  text_content: string;
}

interface BulkMessageResponse extends Record<string, unknown> {
  message: string;
  sent_count: number;
  sent_messages: Array<{
    conversation_id: number;
    target_user_id: number;
    message_id: number;
  }>;
}

export const useGroupConversations = () => {
  const { api } = useApi();

  // グループ一覧を取得
  const getGroups = async (): Promise<GroupConversation[]> => {
    return await api<GroupConversation[]>("/conversations/groups");
  };

  // グループを作成
  const createGroup = async (
    data: CreateGroupRequest
  ): Promise<GroupConversation> => {
    return await api<GroupConversation>("/conversations/groups", {
      method: "POST",
      body: data,
    });
  };

  // グループ詳細を取得
  const getGroup = async (
    conversationId: number
  ): Promise<GroupConversation> => {
    return await api<GroupConversation>(
      `/conversations/groups/${conversationId}`
    );
  };

  // グループを更新
  const updateGroup = async (
    conversationId: number,
    data: Partial<CreateGroupRequest>
  ): Promise<GroupConversation> => {
    return await api<GroupConversation>(
      `/conversations/groups/${conversationId}`,
      {
        method: "PUT",
        body: data,
      }
    );
  };

  // グループを削除
  const deleteGroup = async (conversationId: number): Promise<void> => {
    await api(`/conversations/groups/${conversationId}`, {
      method: "DELETE",
    });
  };

  // メンバーを追加
  const addMember = async (
    conversationId: number,
    data: AddMemberRequest
  ): Promise<GroupParticipant> => {
    return await api<GroupParticipant>(
      `/conversations/groups/${conversationId}/members`,
      {
        method: "POST",
        body: data,
      }
    );
  };

  // メンバーを削除
  const removeMember = async (
    conversationId: number,
    participantId: number,
    canRejoin: boolean = true
  ): Promise<{ message: string; can_rejoin: boolean }> => {
    return await api<{ message: string; can_rejoin: boolean }>(
      `/conversations/groups/${conversationId}/members/${participantId}`,
      {
        method: "DELETE",
        body: { can_rejoin: canRejoin },
      }
    );
  };

  // QRコードトークンを取得
  const getQrCode = async (
    conversationId: number
  ): Promise<{ qr_code_token: string }> => {
    return await api<{ qr_code_token: string }>(
      `/conversations/groups/${conversationId}/qr-code`
    );
  };

  // QRコードトークンを再生成
  const regenerateQrCode = async (
    conversationId: number
  ): Promise<{ qr_code_token: string }> => {
    return await api<{ qr_code_token: string }>(
      `/conversations/groups/${conversationId}/qr-code/regenerate`,
      {
        method: "POST",
      }
    );
  };

  // QRコードトークンでグループに参加
  const joinByToken = async (token: string): Promise<GroupParticipant> => {
    return await api<GroupParticipant>(`/conversations/groups/join/${token}`, {
      method: "POST",
    });
  };

  // QRコードトークンでグループ情報を取得（認証不要）
  const getGroupInfoByToken = async (
    token: string
  ): Promise<{
    id: number;
    name: string;
    description?: string;
    member_count: number;
    max_members: number;
    owner_name: string;
    can_join: boolean;
  }> => {
    return await api<{
      id: number;
      name: string;
      description?: string;
      member_count: number;
      max_members: number;
      owner_name: string;
      can_join: boolean;
    }>(`/conversations/groups/info/${token}`, {
      auth: false, // 認証不要
    });
  };

  // グループメッセージを取得
  const getMessages = async (
    roomToken: string
  ): Promise<PaginatedGroupMessages> => {
    return await api<PaginatedGroupMessages>(
      `/conversations/room/${roomToken}/messages`
    );
  };

  // グループメッセージを送信
  const sendMessage = async (
    roomToken: string,
    message: string
  ): Promise<GroupMessage> => {
    return await api(`/conversations/room/${roomToken}/messages`, {
      method: "POST",
      body: { text_content: message },
    });
  };

  // グループメンバー一覧を取得（アクティブメンバーのみ）
  const getGroupMembers = async (
    conversationId: number
  ): Promise<GroupMember[]> => {
    return await api<GroupMember[]>(
      `/conversations/groups/${conversationId}/members`
    );
  };

  // 全メンバー一覧を取得（削除済み含む）- オーナー専用
  const getAllGroupMembers = async (
    conversationId: number
  ): Promise<ExtendedGroupMember[]> => {
    return await api<ExtendedGroupMember[]>(
      `/conversations/groups/${conversationId}/members/all`
    );
  };

  // グループメンバーに一斉メッセージ送信
  const sendBulkMessage = async (
    conversationId: number,
    data: BulkMessageRequest
  ): Promise<BulkMessageResponse> => {
    return await api<BulkMessageResponse>(
      `/conversations/groups/${conversationId}/messages/bulk`,
      {
        method: "POST",
        body: data,
      }
    );
  };

  // 再参加可否を切り替え
  const toggleMemberRejoin = async (
    conversationId: number,
    memberId: number,
    canRejoin: boolean
  ): Promise<{ message: string; can_rejoin: boolean }> => {
    return await api<{ message: string; can_rejoin: boolean }>(
      `/conversations/groups/${conversationId}/members/${memberId}/rejoin`,
      {
        method: "PATCH",
        body: { can_rejoin: canRejoin },
      }
    );
  };

  // メンバーを復活
  const restoreMember = async (
    conversationId: number,
    memberId: number
  ): Promise<{ message: string }> => {
    return await api<{ message: string }>(
      `/conversations/groups/${conversationId}/members/${memberId}/restore`,
      {
        method: "POST",
      }
    );
  };

  // メンバーのニックネームを更新
  const updateMemberNickname = async (
    conversationId: number,
    memberId: number,
    nickname: string | null
  ): Promise<{ message: string; owner_nickname: string | null }> => {
    return await api<{ message: string; owner_nickname: string | null }>(
      `/conversations/groups/${conversationId}/members/${memberId}/nickname`,
      {
        method: "PATCH",
        body: { owner_nickname: nickname },
      }
    );
  };

  return {
    getGroups,
    createGroup,
    getGroup,
    updateGroup,
    deleteGroup,
    addMember,
    removeMember,
    getQrCode,
    regenerateQrCode,
    joinByToken,
    getGroupInfoByToken,
    getMessages,
    sendMessage,
    getGroupMembers,
    getAllGroupMembers,
    sendBulkMessage,
    toggleMemberRejoin,
    restoreMember,
    updateMemberNickname,
  };
};
