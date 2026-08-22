<!-- modal -->
<div class="rating-section">
    <div class="modal fade" id="ratingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <!--                <button type="button" class="close" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>-->
                <div class="card-body text-center">
                    <div class="comment-box text-center">
                        <form id="rateform" method="post">
                            <div class="form-group">
                                <img src="" id="agent_img" class="br-100 v-mid mr-1" style="width: 30px;">


                            </div>
                            <h4>Add a comment for <strong id="agent_name"></strong></h4>
                            <input type="hidden" id="property_id" name="property_id" />
                            <input type="hidden" id="agent_id" name="agent_id" />
                            <div class="rating"> <input type="radio" name="rating" value="5"
                                    id="5"><label for="5">☆</label> <input type="radio" name="rating"
                                    value="4" id="4"><label for="4">☆</label> <input type="radio"
                                    name="rating" value="3" id="3"><label for="3">☆</label> <input
                                    type="radio" name="rating" value="2" id="2"><label
                                    for="2">☆</label> <input type="radio" name="rating" value="1"
                                    id="1"><label class="one" for="1">☆</label> </div>
                            <div class="comment-area">
                                <textarea class="form-control" id="comment" name="comment" placeholder="what is your view?" rows="4"></textarea>
                            </div>
                            <div class="text-center mt-4"> <button id="rate_send" class="btn btn-success   send px-5"
                                    type="button">Rate Now</button> </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--<div class="modal" id="ratingModal">
    <div class="modal-dialog">
        <div class="modal-content">

            &lt;!&ndash; Modal Header &ndash;&gt;
            <div class="modal-header">
                <h4 class="modal-title">Ratings</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            &lt;!&ndash; Modal body &ndash;&gt;
            <div class="modal-body">
                <form method="POST">
                    <div class="block max-w-3xl px-1 py-2 mx-auto">
                        <div class="flex space-x-1 rating">
                            <label for="star1">
                                <input hidden  type="radio" id="star1" name="rating" value="1" />
                                <svg class="cursor-pointer block w-8 h-8 @if ($rating >= 1) text-indigo-500 @else text-grey @endif " fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                            </label>
                            <label for="star2">
                                <input hidden  type="radio" id="star2" name="rating" value="2" />
                                <svg class="cursor-pointer block w-8 h-8 @if ($rating >= 2) text-indigo-500 @else text-grey @endif " fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                            </label>
                            <label for="star3">
                                <input hidden  type="radio" id="star3" name="rating" value="3" />
                                <svg class="cursor-pointer block w-8 h-8 @if ($rating >= 3) text-indigo-500 @else text-grey @endif " fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                            </label>
                            <label for="star4">
                                <input hidden  type="radio" id="star4" name="rating" value="4" />
                                <svg class="cursor-pointer block w-8 h-8 @if ($rating >= 4) text-indigo-500 @else text-grey @endif " fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                            </label>
                            <label for="star5">
                                <input hidden  type="radio" id="star5" name="rating" value="5" />
                                <svg class="cursor-pointer block w-8 h-8 @if ($rating >= 5) text-indigo-500 @else text-grey @endif " fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                            </label>
                        </div>
                        <div class="my-5">
                            @error('comment')
    <p class="mt-1 text-red-500">{{ $message }}</p>
@enderror
                            <textarea name="description"
                                class="block w-full px-4 py-3 border border-2 rounded-lg focus:border-blue-500 focus:outline-none"
                                placeholder="Comment.."></textarea>
                        </div>
                    </div>
                    <div class="block">
                        <button type="submit" class="w-full px-3 py-4 font-medium text-white bg-blue-600 rounded-lg">Rate</button>

                    </div>
                </form>
            </div>

            &lt;!&ndash; Modal footer &ndash;&gt;
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>-->
