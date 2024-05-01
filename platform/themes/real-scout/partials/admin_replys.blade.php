
@foreach($comments as $comment)
    <div class="display-comment">
        @if($comment->user_id)
        <img src="{{ $comment->user->avatar_url }}" class="br-100 v-mid mr-1" style="width: 30px;">
        <strong>{{ $comment->user->first_name }}</strong>
        @else
            <img src="{{ $comment->admin->avatar_url }}" class="br-100 v-mid mr-1" style="width: 30px;">
            <strong>{{ $comment->admin->first_name }}</strong>
        @endif
            <p class="comment-desp">{{ $comment->comment }}</p>

        {!! Theme::partial('admin_replys', ['comments' => $comment->replies,'property_id' =>$property_id]) !!}

    </div>
@endforeach
