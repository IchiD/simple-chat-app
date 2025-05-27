@extends('admin.layouts.app')

@section('title', 'ダッシュボード')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 mb-0">
            <i class="fas fa-tachometer-alt me-2"></i>ダッシュボード
        </h1>
        <p class="text-muted">システムの概要と管理機能</p>
    </div>
</div>

<!-- 統計カード -->
<div class="row mb-4">
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-users fa-3x text-primary"></i>
                </div>
                <h5 class="card-title">総ユーザー数</h5>
                <h2 class="text-primary">{{ number_format($userCount) }}</h2>
                <p class="text-muted mb-0">登録済みユーザー</p>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-comments fa-3x text-success"></i>
                </div>
                <h5 class="card-title">チャットルーム数</h5>
                <h2 class="text-success">{{ number_format($chatRoomCount) }}</h2>
                <p class="text-muted mb-0">作成済みルーム</p>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-user-clock fa-3x text-warning"></i>
                </div>
                <h5 class="card-title">本日のアクティブユーザー数</h5>
                <h2 class="text-warning">{{ number_format($todayActiveUsersCount) }}</h2>
                <p class="text-muted mb-0">本日メッセージ送信</p>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-envelope fa-3x text-info"></i>
                </div>
                <h5 class="card-title">本日送信されたメッセージ数</h5>
                <h2 class="text-info">{{ number_format($todayMessagesCount) }}</h2>
                <p class="text-muted mb-0">本日の投稿</p>
            </div>
        </div>
    </div>
</div>

<!-- 管理者統計カード -->
<div class="row mb-4">
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-user-shield fa-3x text-danger"></i>
                </div>
                <h5 class="card-title">管理者数</h5>
                <h2 class="text-danger">{{ number_format($adminCount) }}</h2>
                <p class="text-muted mb-0">システム管理者</p>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-cog fa-3x text-secondary"></i>
                </div>
                <h5 class="card-title">システム状態</h5>
                <h2 class="text-secondary">正常</h2>
                <p class="text-muted mb-0">全システム稼働中</p>
            </div>
        </div>
    </div>
</div>

<!-- 管理機能 -->
<div class="row mb-4">
    <div class="col-12">
        <h3 class="h5 mb-3">
            <i class="fas fa-tools me-2"></i>管理機能
        </h3>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-lg-4 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users fa-2x text-primary"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="card-title mb-0">ユーザー管理</h5>
                        <p class="text-muted mb-0">ユーザーの管理・編集</p>
                    </div>
                </div>
                <a href="{{ route('admin.users') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-arrow-right me-1"></i>管理画面へ
                </a>
            </div>
        </div>
    </div>

    @if($admin->isSuperAdmin())
    <div class="col-md-6 col-lg-4 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-shield fa-2x text-danger"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="card-title mb-0">アドミン管理</h5>
                        <p class="text-muted mb-0">管理者の管理・作成</p>
                    </div>
                </div>
                <a href="{{ route('admin.admins') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-arrow-right me-1"></i>管理画面へ
                </a>
            </div>
        </div>
    </div>
    @endif

    <div class="col-md-6 col-lg-4 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <i class="fas fa-chart-bar fa-2x text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="card-title mb-0">レポート</h5>
                        <p class="text-muted mb-0">システム分析・統計</p>
                    </div>
                </div>
                <button class="btn btn-success btn-sm" disabled>
                    <i class="fas fa-clock me-1"></i>準備中
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 最近の活動 -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>最近の活動
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center py-2">
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-plus text-success me-3"></i>
                    </div>
                    <div class="flex-grow-1">
                        <strong>新規ユーザー登録</strong>
                        <p class="text-muted mb-0">3名の新規ユーザーが登録されました</p>
                    </div>
                    <div class="flex-shrink-0">
                        <small class="text-muted">2時間前</small>
                    </div>
                </div>
                <hr>
                <div class="d-flex align-items-center py-2">
                    <div class="flex-shrink-0">
                        <i class="fas fa-sign-in-alt text-primary me-3"></i>
                    </div>
                    <div class="flex-grow-1">
                        <strong>管理者ログイン</strong>
                        <p class="text-muted mb-0">{{ $admin->name }} がログインしました</p>
                    </div>
                    <div class="flex-shrink-0">
                        <small class="text-muted">現在</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection