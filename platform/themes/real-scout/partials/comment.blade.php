
<div class="comment-box">
<div class="card-body d-comment" >



    {!! Theme::partial('replys', ['comments' => $data->comments, 'property_id' => $data->id]) !!}

</div>
<div class="card-body">
    @if(auth('account')->user())
    <div class="comment-head" >Leave a comment</div>
    <form method="post" id="agent_comment"> <!-- action="{{ route('comment.add') }}"-->
        @csrf
        <div class="form-group">
            <input type="text" name="comment" id="comment" class="form-control comment" required/>
            <input type="hidden" name="property_id" value="{{ $data->id }}" />
        </div>

        <div class="form-group">
            <input type="button" class="btn btn-primary" id="add_comment" style="font-size: 0.8em;" value="Add Comment" />
        </div>
        @endif
    </form>
</div>
</div>

