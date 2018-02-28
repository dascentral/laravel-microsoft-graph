@extends('layout')

@section('content')
    <div id="inbox" class="panel panel-default">
        <div class="panel-heading">
            <h1 class="panel-title">Inbox</h1>
        </div>
        <div class="panel-body">
            Here are the 10 most recent messages in your inbox.
        </div>
        <div class="list-group">
            @if ($messages->count())
                @foreach ($messages as $message)
                    <div class="list-group-item">
                        <h3 class="list-group-item-heading">{{ $message->getSubject() }}</h3>
                        <h4 class="list-group-item-heading">{{ $message->getFrom()->getEmailAddress()->getName() }}</h4>
                        <p class="list-group-item-heading text-muted"><em>Received: {{ $message->getReceivedDateTime()->format(DATE_RFC2822) }}</em></p>
                    </div>
                @endforeach
            @endif
        </div>
        </div>
@endsection