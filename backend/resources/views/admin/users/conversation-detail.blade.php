@extends('admin.layouts.app')

@section('title', '会話詳細')

@section('content')
<div class="row mb-4">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1 class="h3 mb-0">
          <i class="fas fa-comment-dots me-2"></i>会話詳細
        </h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">ダッシュボード</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users') }}">ユーザー管理</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.show', $user->id) }}">{{ $user->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.conversations', $user->id) }}">会話管理</a></li>
            <li class="breadcrumb-item active">会話 #{{ $conversation->id }}</li>
          </ol>
        </nav>
      </div>
      <div>
        <a href="{{ route('admin.users.conversations', $user->id) }}" class="btn btn-outline-secondary">
          <i class="fas fa-arrow-left me-1"></i>会話一覧に戻る
        </a>
      </div>
    </div>
  </div>
</div>

<!-- 会話情報カード -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card {{ $conversation->isDeleted() ? 'border-danger' : '' }}">
      <div class="card-header {{ $conversation->isDeleted() ? 'bg-danger text-white' : '' }}">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">
            <i class="fas fa-info-circle me-2"></i>会話情報
          </h5>
          @if($conversation->isDeleted())
          <form method="POST" action="{{ route('admin.users.conversations.restore', [$user->id, $conversation->id]) }}" class="d-inline">
            @csrf
            <button type="submit"
              class="btn btn-sm btn-outline-light"
              onclick="return confirm('この会話の削除を取り消しますか？')">
              <i class="fas fa-undo me-1"></i>削除を取り消し
            </button>
          </form>
          @else
          <button type="button"
            class="btn btn-sm btn-outline-danger"
            onclick="showDeleteConversationModal()">
            <i class="fas fa-trash me-1"></i>会話を削除
          </button>
          @endif
        </div>
      </div>
      <div class="card-body">
        @if($conversation->isDeleted())
        <div class="alert alert-danger mb-3">
          <i class="fas fa-exclamation-triangle me-2"></i>
          <strong>この会話は削除されています</strong>
          <div class="mt-2">
            <strong>削除日時:</strong> {{ $conversation->deleted_at->format('Y年m月d日 H:i') }}<br>
            @if($conversation->deletedByAdmin)
            <strong>削除者:</strong> {{ $conversation->deletedByAdmin->name }}<br>
            @endif
            @if($conversation->deleted_reason)
            <strong>削除理由:</strong> {{ $conversation->deleted_reason }}
            @endif
          </div>
        </div>
        @endif

        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label text-muted">会話ID</label>
              <div class="fw-bold">#{{ $conversation->id }}</div>
            </div>
            <div class="mb-3">
              <label class="form-label text-muted">ルームトークン</label>
              <div><code class="bg-light p-2 rounded">{{ $conversation->room_token }}</code></div>
            </div>
            <div class="mb-3">
              <label class="form-label text-muted">作成日時</label>
              <div>{{ $conversation->created_at->format('Y年m月d日 H:i') }}</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label text-muted">参加者 ({{ $conversation->participants->count() }}人)</label>
              <div class="d-flex flex-wrap gap-1">
                @foreach($conversation->participants as $participant)
                <span class="badge bg-light text-dark border {{ $participant->id == $user->id ? 'border-primary' : '' }}">
                  {{ $participant->name }}
                  @if($participant->id == $user->id)
                  <i class="fas fa-star text-warning ms-1" title="対象ユーザー"></i>
                  @endif
                </span>
                @endforeach
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label text-muted">メッセージ統計</label>
              <div>
                <span class="badge bg-primary">総数: {{ $messages->total() }}</span>
                <span class="badge bg-success">アクティブ: {{ $messages->whereNull('admin_deleted_at')->whereNull('deleted_at')->count() }}</span>
                <span class="badge bg-warning">ユーザー削除: {{ $messages->whereNotNull('deleted_at')->whereNull('admin_deleted_at')->count() }}</span>
                <span class="badge bg-danger">管理者削除: {{ $messages->whereNotNull('admin_deleted_at')->count() }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- メッセージ一覧 -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">
          <i class="fas fa-envelope me-2"></i>メッセージ一覧
        </h5>
      </div>
      <div class="card-body">
        @if($messages->count() > 0)
        <div class="messages-container">
          @foreach($messages as $message)
          <div class="message-item mb-3 p-3 border rounded {{ $message->isAdminDeleted() ? 'border-danger bg-light' : ($message->deleted_at ? 'border-warning bg-light' : '') }}">
            <div class="d-flex justify-content-between align-items-start">
              <div class="flex-grow-1">
                <div class="d-flex align-items-center mb-2">
                  <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                    style="width: 30px; height: 30px;">
                    <span class="text-white fw-bold small">
                      {{ strtoupper(substr($message->sender->name, 0, 1)) }}
                    </span>
                  </div>
                  <div>
                    <div class="fw-bold">
                      {{ $message->sender->name }}
                      @if($message->sender->id == $user->id)
                      <span class="badge bg-primary ms-1">対象ユーザー</span>
                      @endif
                    </div>
                    <small class="text-muted">
                      {{ $message->sent_at->format('Y/m/d H:i:s') }}
                      @if($message->edited_at)
                      <span class="badge bg-info ms-1">編集済み</span>
                      @endif
                    </small>
                  </div>
                </div>

                @if($message->isAdminDeleted())
                <div class="alert alert-danger mb-2">
                  <i class="fas fa-trash me-1"></i>
                  <strong>管理者により削除されました</strong>
                  <div class="mt-1">
                    <small>
                      削除日時: {{ $message->admin_deleted_at->format('Y/m/d H:i') }}
                      @if($message->adminDeletedBy)
                      | 削除者: {{ $message->adminDeletedBy->name }}
                      @endif
                      @if($message->admin_deleted_reason)
                      <br>理由: {{ $message->admin_deleted_reason }}
                      @endif
                    </small>
                  </div>
                </div>
                <div class="text-muted fst-italic">
                  元のメッセージ: "{{ $message->text_content }}"
                </div>
                @elseif($message->deleted_at)
                <div class="alert alert-warning mb-2">
                  <i class="fas fa-exclamation-triangle me-1"></i>
                  <strong>ユーザーにより削除されました</strong>
                  <small class="d-block">削除日時: {{ $message->deleted_at->format('Y/m/d H:i') }}</small>
                </div>
                <div class="text-muted fst-italic">
                  元のメッセージ: "{{ $message->text_content }}"
                </div>
                @else
                <div class="message-content" id="message-content-{{ $message->id }}">
                  {{ $message->text_content }}
                </div>
                <div class="message-edit-form d-none" id="message-edit-{{ $message->id }}">
                  <form method="POST" action="{{ route('admin.users.messages.update', [$user->id, $conversation->id, $message->id]) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-2">
                      <textarea class="form-control" name="text_content" rows="3" required>{{ $message->text_content }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                      <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-save me-1"></i>保存
                      </button>
                      <button type="button" class="btn btn-sm btn-secondary" onclick="cancelEdit({{ $message->id }})">
                        キャンセル
                      </button>
                    </div>
                  </form>
                </div>
                @endif
              </div>

              @if(!$message->isAdminDeleted() && !$conversation->isDeleted())
              <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                  type="button"
                  data-bs-toggle="dropdown">
                  <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu">
                  @if(!$message->deleted_at)
                  <li>
                    <button class="dropdown-item" onclick="startEdit({{ $message->id }})">
                      <i class="fas fa-edit me-2"></i>編集
                    </button>
                  </li>
                  @endif
                  <li>
                    <button class="dropdown-item text-danger"
                      onclick="showDeleteMessageModal({{ $message->id }})">
                      <i class="fas fa-trash me-2"></i>削除
                    </button>
                  </li>
                </ul>
              </div>
              @endif
            </div>
          </div>
          @endforeach
        </div>

        <!-- ページネーション -->
        <div class="d-flex justify-content-between align-items-center mt-3">
          <div class="text-muted">
            {{ $messages->firstItem() }}〜{{ $messages->lastItem() }}件目 / 全{{ $messages->total() }}件
          </div>
          <div>
            {{ $messages->links() }}
          </div>
        </div>
        @else
        <div class="text-center py-5">
          <i class="fas fa-envelope fa-3x text-muted mb-3"></i>
          <h5 class="text-muted">メッセージがありません</h5>
          <p class="text-muted">この会話にはまだメッセージが投稿されていません。</p>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- 会話削除確認モーダル -->
<div class="modal fade" id="deleteConversationModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">会話削除確認</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ route('admin.users.conversations.delete', [$user->id, $conversation->id]) }}">
        @csrf
        @method('DELETE')
        <div class="modal-body">
          <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>警告:</strong> この操作により、会話が論理削除され、
            参加者全員から見えなくなります。この操作は慎重に行ってください。
          </div>
          <div class="mb-3">
            <label for="deleteConversationReason" class="form-label">削除理由</label>
            <textarea class="form-control" id="deleteConversationReason" name="reason" rows="3"
              placeholder="削除理由を入力してください（任意）"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
          <button type="submit" class="btn btn-danger">削除する</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- メッセージ削除確認モーダル -->
<div class="modal fade" id="deleteMessageModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">メッセージ削除確認</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="deleteMessageForm" method="POST">
        @csrf
        @method('DELETE')
        <div class="modal-body">
          <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>警告:</strong> この操作により、メッセージが管理者により削除されます。
          </div>
          <div class="mb-3">
            <label for="deleteMessageReason" class="form-label">削除理由</label>
            <textarea class="form-control" id="deleteMessageReason" name="reason" rows="2"
              placeholder="削除理由を入力してください（任意）"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
          <button type="submit" class="btn btn-danger">削除する</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  function showDeleteConversationModal() {
    new bootstrap.Modal(document.getElementById('deleteConversationModal')).show();
  }

  function showDeleteMessageModal(messageId) {
    document.getElementById('deleteMessageForm').action = `/admin/users/{{ $user->id }}/conversations/{{ $conversation->id }}/messages/${messageId}`;
    document.getElementById('deleteMessageReason').value = '';
    new bootstrap.Modal(document.getElementById('deleteMessageModal')).show();
  }

  function startEdit(messageId) {
    document.getElementById('message-content-' + messageId).classList.add('d-none');
    document.getElementById('message-edit-' + messageId).classList.remove('d-none');
  }

  function cancelEdit(messageId) {
    document.getElementById('message-content-' + messageId).classList.remove('d-none');
    document.getElementById('message-edit-' + messageId).classList.add('d-none');
  }
</script>

<style>
  .message-item {
    transition: all 0.2s ease;
  }

  .message-item:hover {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .messages-container {
    max-height: 600px;
    overflow-y: auto;
  }
</style>
@endsection