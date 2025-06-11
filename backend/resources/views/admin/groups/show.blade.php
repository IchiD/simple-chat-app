@extends('admin.layouts.app')

@section('title', 'グループ詳細')

@section('content')
<div class="row mb-4">
  <div class="col-12">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.groups') }}">グループ</a></li>
        <li class="breadcrumb-item active">#{{ $group->id }}</li>
      </ol>
    </nav>
    <h1 class="h3 mb-0">
      <i class="fas fa-users-gear me-2"></i>{{ $group->name }}
    </h1>
  </div>
</div>

<div class="row mb-4">
  <div class="col-md-6">
    <div class="card mb-3">
      <div class="card-header">基本情報</div>
      <div class="card-body">
        <dl class="row mb-0">
          <dt class="col-sm-4">ID</dt>
          <dd class="col-sm-8">#{{ $group->id }}</dd>

          <dt class="col-sm-4">オーナー</dt>
          <dd class="col-sm-8">{{ $group->owner->name ?? '-' }}</dd>

          <dt class="col-sm-4">メンバー上限</dt>
          <dd class="col-sm-8">{{ $group->max_members }}</dd>

          <dt class="col-sm-4">スタイル</dt>
          <dd class="col-sm-8">
            {{ is_array($group->chat_styles) ? implode(', ', $group->chat_styles) : ($group->chat_styles ?? '-') }}
          </dd>

          <dt class="col-sm-4">説明</dt>
          <dd class="col-sm-8">{!! nl2br(e($group->description)) !!}</dd>
        </dl>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card mb-3">
      <div class="card-header">メンバー ({{ $group->groupMembers->count() }}名)</div>
      <div class="card-body p-0">
        <table class="table mb-0">
          <thead>
            <tr>
              <th style="width: 60px;">ID</th>
              <th>名前</th>
              <th>役割</th>
              <th>参加日</th>
            </tr>
          </thead>
          <tbody>
            @foreach($group->groupMembers as $member)
            <tr>
              <td>#{{ $member->user_id }}</td>
              <td>{{ $member->user->name ?? '-' }}</td>
              <td>{{ $member->role }}</td>
              <td>{{ optional($member->joined_at)->format('Y/m/d') }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
