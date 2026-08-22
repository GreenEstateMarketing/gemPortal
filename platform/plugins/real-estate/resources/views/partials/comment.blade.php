<div class="comment-box">
<div class="card-body">
    <div class="comment-head" >Comments</div>

    {!! Theme::partial('admin_replys', ['comments' => $properties->comments, 'property_id' => $properties->id]) !!}

</div>
<div class="card-body">
    <div class="comment-head" >Leave a comment</div>
    <form method="post" action="{{ route('comment.add.admin') }}">
        @csrf
        <div class="form-group">
            <input type="text" name="comment" class="form-control" />
            <input type="hidden" name="property_id" value="{{ $properties->id }}" />
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" style="font-size: 0.8em;" value="Add Comment" />
        </div>
    </form>
</div>
</div>
