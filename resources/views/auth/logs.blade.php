@extends('components.main-layout')

@section('title', 'Activity Logs')

@section('content')

<div class="page-header">
    <div>
        <h4 class="page-title">Daily Activity Logs</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                <li class="breadcrumb-item active">Activity Logs</li>
            </ol>
        </nav>
    </div>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Event</th>
                    <th>Description</th>
                    <th>IP Address</th>
                    <th>Causer</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <span class="s-badge" style="background:#eff6ff;color:#3b82f6;">
                                {{ $log->event ?? '—' }}
                            </span>
                        </td>
                        <td>{{ $log->description }}</td>
                        <td><code>{{ $log->properties['ip_address'] ?? '—' }}</code></td>
                        <td>{{ optional($log->causer)->name ?? 'System' }}</td>
                        <td style="white-space:nowrap;">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="tbl-empty">
                                <i class="mdi mdi-clipboard-text-off-outline"></i>
                                <p>No activity logs found for today.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($logs->hasPages())
        <div style="padding: 12px 16px; border-top: 1px solid #f1f5f9;">
            {{ $logs->links('vendor.pagination.bootstrap-5') }}
        </div>
    @endif
</div>

@endsection
