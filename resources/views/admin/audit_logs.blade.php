@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between px-2">
        <h2 class="text-2xl font-bold text-gray-800">Audit Logs</h2>
        <div class="bg-indigo-50 text-indigo-600 px-3 py-1 rounded-full text-xs font-bold uppercase">
            {{ $logs->total() }} Data
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Waktu</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">User</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Aksi</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Model</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($logs as $log)
                    <tr class="hover:bg-gray-50/30 transition-colors">
                        <td class="px-6 py-4 text-xs text-gray-600 whitespace-nowrap">
                            {{ $log->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($log->user->name ?? 'System') }}&background=random" class="w-6 h-6 rounded-full">
                                <span class="text-xs font-bold text-gray-700">{{ $log->user->name ?? 'System' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-lg text-[10px] font-bold uppercase 
                                @if($log->action == 'created') bg-emerald-50 text-emerald-600
                                @elseif($log->action == 'updated') bg-blue-50 text-blue-600
                                @else bg-red-50 text-red-600 @endif">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-medium text-gray-500">{{ class_basename($log->model_type) }} ({{ $log->model_id }})</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-[10px] text-gray-400 max-w-xs break-words">{{ $log->description }}</p>
                            @if($log->action == 'updated' && $log->new_values)
                                <div class="mt-2 space-y-1">
                                    @foreach($log->new_values as $key => $val)
                                    @if($key != 'updated_at')
                                    <div class="text-[9px] text-indigo-600">
                                        <span class="font-bold">{{ $key }}:</span> 
                                        <span class="text-gray-400 line-through mr-1">{{ $log->old_values[$key] ?? 'N/A' }}</span>
                                        <i class="fas fa-arrow-right mx-1 opacity-30"></i>
                                        <span>{{ $val }}</span>
                                    </div>
                                    @endif
                                    @endforeach
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-50">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
