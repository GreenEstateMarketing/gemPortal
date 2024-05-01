<div class="comment-box">
    <div class="card-body d-comment">


        {!! Theme::partial('admin_replys', ['comments' => $properties->comments, 'property_id' => $properties->id]) !!}

    </div>
    <div class="card-body">
        <div class="comment-head" >Leave a comment</div>
        <form method="post" id="admin_form">
            @csrf
            <div class="form-group">
                <input type="text" name="comment" class="form-control comment" required/>
                <input type="hidden" name="property_id" value="{{ $properties->id }}" />
            </div>
            <div class="form-group">
                <input type="button" class="btn btn-primary" id="admin_comment" style="font-size: 0.8em;" value="Add Comment" />
            </div>
        </form>
    </div>
</div>
