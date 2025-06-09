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
  user_id: number;
}

interface GroupMember extends Record<string, unknown> {
  id: number;
  name: string;
  friend_id: string;
  group_member_label: string;
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
    participantId: number
  ): Promise<void> => {
    await api(
      `/conversations/groups/${conversationId}/members/${participantId}`,
      {
        method: "DELETE",
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

  // グループメンバー一覧を取得
  const getGroupMembers = async (
    conversationId: number
  ): Promise<GroupMember[]> => {
    return await api<GroupMember[]>(
      `/conversations/groups/${conversationId}/members`
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
    getMessages,
    sendMessage,
    getGroupMembers,
    sendBulkMessage,
  };
};
