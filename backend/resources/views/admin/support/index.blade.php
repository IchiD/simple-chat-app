@extends('admin.layouts.app')

@section('title', 'お問い合わせ管理')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">
          <i class="fas fa-comments me-2"></i>お問い合わせ管理
        </h1>
      </div>

      <!-- 検索フォーム -->
      <div class="card mb-4">
        <div class="card-body">
          <form method="GET" action="{{ route('admin.support') }}" class="row g-3">
            <div class="col-md-6">
              <label for="search" class="form-label">ユーザー検索</label>
              <input type="text" class="form-control" id="search" name="search" 
                     value="{{ request('search') }}" placeholder="ユーザー名またはメールアドレス">
            </div>
            <div class="col-md-6 d-flex align-items-end">
              <button type="submit" class="btn btn-primary me-2">
                <i class="fas fa-search me-1"></i>検索
              </button>
              <a href="{{ route('admin.support') }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i>クリア
              </a>
            </div>
          </form>
        </div>
      </div>

      <!-- サポート会話リスト -->
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">サポート会話一覧 ({{ $conversations->total() }}件)</h5>
        </div>
        <div class="card-body">
          @if($conversations->count() > 0)
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>ユーザー</th>
                    <th>最新メッセージ</th>
                    <th>作成日時</th>
                    <th>更新日時</th>
                    <th>操作</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($conversations as $conversation)
                    <tr>
                      <td>
                        @if($conversation->participants->first())
                          <div>
                            <strong>{{ $conversation->participants->first()->name }}</strong>
                            <br>
                            <small class="text-muted">{{ $conversation->participants->first()->email }}</small>
                          </div>
                        @else
                          <span class="text-muted">ユーザー不明</span>
                        @endif
                      </td>
                      <td>
                        @if($conversation->latestMessage)
                          <div>
                            @if($conversation->latestMessage->sender)
                              <strong>{{ $conversation->latestMessage->sender->name }}:</strong>
                            @else
                              <strong class="text-primary">管理者:</strong>
                            @endif
                            <br>
                            {{ Str::limit($conversation->latestMessage->text_content, 50) }}
                            <br>
                            <small class="text-muted">{{ $conversation->latestMessage->sent_at->format('Y/m/d H:i') }}</small>
                          </div>
                        @else
                          <span class="text-muted">メッセージなし</span>
                        @endif
                      </td>
                      <td>{{ $conversation->created_at->format('Y/m/d H:i') }}</td>
                      <td>{{ $conversation->updated_at->format('Y/m/d H:i') }}</td>
                      <td>
                        <a href="{{ route('admin.support.detail', $conversation->id) }}" 
                           class="btn btn-sm btn-primary">
                          <i class="fas fa-eye me-1"></i>詳細
                        </a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <!-- ページネーション -->
            <div class="d-flex justify-content-center">
              {{ $conversations->links() }}
            </div>
          @else
            <div class="text-center py-4">
              <i class="fas fa-comments fa-3x text-muted mb-3"></i>
              <h5 class="text-muted">お問い合わせはありません</h5>
              <p class="text-muted">ユーザーからのお問い合わせがある場合、ここに表示されます。</p>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection