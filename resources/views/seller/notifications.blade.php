@extends('layouts.seller')

@section('title', 'Notifications')

@section('content')
<div class="container" style="max-width: 1200px; margin: 30px auto; padding: 0 20px;">
    <h1 style="margin-bottom: 30px; color: #1E1E1E;">Notifications</h1>

    @if(count($notifications) > 0)
        <div class="card" style="background: white; border-radius: 8px; padding: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <ul style="list-style: none; padding: 0; margin: 0;">
                @foreach($notifications as $notification)
                    <li style="margin-bottom: 20px; padding: 15px; background: #F9F9F9; border-radius: 6px; border-left: 4px solid 
                        @if($notification['type'] === 'success') #28a745
                        @elseif($notification['type'] === 'warning') #ffc107
                        @else #17a2b8
                        @endif;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div style="flex: 1;">
                                <div style="font-size: 13px; color: #666; margin-bottom: 8px;">
                                    {{ $notification['date']->format('M j, Y g:i A') }}
                                </div>
                                <div style="font-size: 15px; color: #1E1E1E; line-height: 1.5;">
                                    <span style="
                                        color: @if($notification['type'] === 'success') #28a745
                                        @elseif($notification['type'] === 'warning') #ffc107
                                        @else #17a2b8
                                        @endif;
                                        font-weight: 600;
                                        margin-right: 8px;
                                    ">{{ $notification['icon'] ?? '•' }}</span>
                                    {{ $notification['message'] }}
                                </div>
                            </div>
                            @if(isset($notification['link']))
                                <a href="{{ $notification['link'] }}" style="color: #2CB8B4; text-decoration: none; font-size: 14px; margin-left: 15px; white-space: nowrap;">
                                    View →
                                </a>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <div class="card" style="background: white; border-radius: 8px; padding: 40px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center;">
            <p style="color: #999; font-size: 16px; margin: 0;">No notifications at this time.</p>
        </div>
    @endif
</div>
@endsection

