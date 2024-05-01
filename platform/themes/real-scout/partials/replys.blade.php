
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
<!--                <a href="" id="reply"></a>
                    @if(auth('account')->user())
                            <form method="post" action="{{ route('reply.add') }}">
                                @csrf
                                <div class="form-group">
                                    <input type="text" name="comment" class="form-control comment" required/>
                                    <input type="hidden" name="property_id" value="{{ $property_id }}" />
                                    <input type="hidden" name="comment_id" value="{{ $comment->id }}" />
                                </div>

                                <div class="form-group">
                                    <input type="submit" class="btn btn-primary" style="font-size: 0.8em;" value="Reply" />
                                </div>
                            </form>
                    @endif-->
                {!! Theme::partial('replys', ['comments' => $comment->replies,'property_id' =>$property_id]) !!}

        </div>
        @endforeach

