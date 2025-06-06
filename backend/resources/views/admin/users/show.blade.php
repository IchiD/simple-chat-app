@extends('admin.layouts.app')

@section('title', 'ユーザー詳細')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-user me-2"></i>ユーザー詳細
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">ダッシュボード</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users') }}">ユーザー管理</a></li>
                        <li class="breadcrumb-item active">{{ $user->name }}</li>
                    </ol>
                </nav>
            </div>
            <div>
                @if(!$user->isDeleted())
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary me-2">
                        <i class="fas fa-edit me-1"></i>編集
                    </a>
                    <a href="{{ route('admin.users.conversations', $user->id) }}" class="btn btn-outline-primary">
                        <i class="fas fa-comments me-1"></i>会話管理
                    </a>
                @else
                    <form method="POST" action="{{ route('admin.users.restore', $user->id) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success" 
                                onclick="return confirm('ユーザーの削除を取り消しますか？')">
                            <i class="fas fa-undo me-1"></i>削除取り消し
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

@if($user->isDeleted())
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>このユーザーは削除されています</strong>
            <div class="mt-2">
                <strong>削除日時:</strong> {{ $user->deleted_at->format('Y年m月d日 H:i') }}<br>
                @if($user->deletedByAdmin)
                    <strong>削除者:</strong> {{ $user->deletedByAdmin->name }}<br>
                @endif
                @if($user->deleted_reason)
                    <strong>削除理由:</strong> {{ $user->deleted_reason }}
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- 基本情報 -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user me-2"></i>基本情報
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">ユーザーID</label>
                            <div class="fw-bold">#{{ $user->id }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">名前</label>
                            <div class="fw-bold">{{ $user->name }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">メールアドレス</label>
                            <div>{{ $user->email }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">フレンドID</label>
                            <div><code class="bg-light p-2 rounded">{{ $user->friend_id }}</code></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">登録日時</label>
                            <div>{{ $user->created_at->format('Y年m月d日 H:i') }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">認証状態</label>
                            <div>
                                @if($user->is_verified)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>認証済み
                                    </span>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="fas fa-exclamation-circle me-1"></i>未認証
                                    </span>
                                @endif
                            </div>
                        </div>
                        @if($user->email_verified_at)
                        <div class="mb-3">
                            <label class="form-label text-muted">認証日時</label>
                            <div>{{ $user->email_verified_at->format('Y年m月d日 H:i') }}</div>
                        </div>
                        @endif
                        <div class="mb-3">
                            <label class="form-label text-muted">アカウント状態</label>
                            <div>
                                @if($user->isBanned())
                                    <span class="badge bg-danger">
                                        <i class="fas fa-ban me-1"></i>バン済み
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>アクティブ
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>統計情報
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="fs-2 fw-bold text-primary">{{ $stats['total_conversations'] }}</div>
                        <div class="text-muted">参加会話数</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="fs-2 fw-bold text-success">{{ $stats['total_messages'] }}</div>
                        <div class="text-muted">送信メッセージ数</div>
                    </div>
                    <div class="col-6">
                        <div class="fs-2 fw-bold text-info">{{ $stats['friends_count'] }}</div>
                        <div class="text-muted">友達数</div>
                    </div>
                    <div class="col-6">
                        <div class="fs-6 fw-bold text-secondary">-</div>
                        <div class="text-muted">最終ログイン</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 最近の会話 -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-comments me-2"></i>最近の会話
                </h5>
                <a href="{{ route('admin.users.conversations', $user->id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-external-link-alt me-1"></i>全ての会話を見る
                </a>
            </div>
            <div class="card-body">
                @if($user->conversations->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>会話ID</th>
                                    <th>参加者</th>
                                    <th>最新メッセージ</th>
                                    <th>最終更新</th>
                                    <th>状態</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->conversations->take(5) as $conversation)
                                <tr>
                                    <td>
                                        <span class="text-primary">#{{ $conversation->id }}</span>
                                    </td>
                                    <td>
                                        <small>
                                            @foreach($conversation->participants->take(3) as $participant)
                                                {{ $participant->name }}@if(!$loop->last), @endif
                                            @endforeach
                                            @if($conversation->participants->count() > 3)
                                                <span class="text-muted">他{{ $conversation->participants->count() - 3 }}人</span>
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        @if($conversation->latestMessage)
                                            <small class="text-truncate d-inline-block" style="max-width: 200px;">
                                                {{ $conversation->latestMessage->text_content }}
                                            </small>
                                        @else
                                            <span class="text-muted">メッセージなし</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($conversation->latestMessage)
                                            <small class="text-muted">
                                                {{ $conversation->latestMessage->sent_at->format('m/d H:i') }}
                                            </small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($conversation->isDeleted())
                                            <span class="badge bg-danger">削除済み</span>
                                        @else
                                            <span class="badge bg-success">アクティブ</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.users.conversations.detail', [$user->id, $conversation->id]) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-comments fa-2x text-muted mb-2"></i>
                        <div class="text-muted">参加している会話がありません</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- 最近のメッセージ -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-envelope me-2"></i>最近のメッセージ
                </h5>
            </div>
            <div class="card-body">
                @if($user->messages->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>メッセージID</th>
                                    <th>内容</th>
                                    <th>会話</th>
                                    <th>送信日時</th>
                                    <th>状態</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->messages->take(10) as $message)
                                <tr>
                                    <td>
                                        <span class="text-primary">#{{ $message->id }}</span>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 300px;">
                                            {{ $message->text_content }}
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.users.conversations.detail', [$user->id, $message->conversation_id]) }}" 
                                           class="text-decoration-none">
                                            #{{ $message->conversation_id }}
                                        </a>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $message->sent_at->format('m/d H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($message->isAdminDeleted())
                                            <span class="badge bg-danger">管理者削除</span>
                                        @elseif($message->deleted_at)
                                            <span class="badge bg-warning">ユーザー削除</span>
                                        @elseif($message->edited_at)
                                            <span class="badge bg-info">編集済み</span>
                                        @else
                                            <span class="badge bg-success">正常</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-envelope fa-2x text-muted mb-2"></i>
                        <div class="text-muted">送信したメッセージがありません</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection