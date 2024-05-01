<!--<div class="socials">
    <span>{{ $title }}:</span>
    <ul>
        <li>
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}&title={{ $description }}" target="_blank" title="{{ __('Share on Facebook') }}"><i class="fab fa-facebook-f"></i></a>
        </li>
        <li>
            <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&summary={{ rawurldecode($description) }}&source=Linkedin" title="{{ __('Share on Linkedin') }}" target="_blank"><i class="fab fa-linkedin-in"></i></a>
        </li>
        <li>
            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ $description }}" target="_blank" title="{{ __('Share on Twitter') }}"><i class="fab fa-twitter"></i></a>
        </li>
        <li>

            <a href="https://web.whatsapp.com/send?url={{ urlencode(url()->current().'/') }}&text={{ urlencode(url()->current().'/') }}" target="_blank" title="{{ __('Share on Whatsapp') }}"><i class="fab fa-whatsapp"></i></a>
        </li>
    </ul>
</div>-->
<div class="row">

<div id="socialHolder" class="col-md-6">
    <b class="pr-2">{{$title}}</b>
    <div id="socialShare" class="btn-group share-group">
        <a data-toggle="dropdown" class="btn btn-info">
            <i class="fa fa-share-alt fa-inverse"></i>
        </a>
        <button href="#" data-toggle="dropdown" class="btn btn-info dropdown-toggle share">
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu socials_links">
            <li>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}&title={{ $description }}" target="_blank" title="{{ __('Share on Facebook') }}"><img src="{{ Theme::asset()->url('images/fb.png')  }}" class="m-1 p-1 social_icons" alt="Image"></a>
            </li>
            <li>
                <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&summary={{ rawurldecode($description) }}&source=Linkedin" title="{{ __('Share on Linkedin') }}" target="_blank"><img class="m-1 p-1 social_icons" src="{{ Theme::asset()->url('images/in.png')  }}" alt="Image"></a>
            </li>
            <li>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ $description }}" target="_blank" title="{{ __('Share on Twitter') }}"><img class="m-1 p-1 social_icons" src="{{ Theme::asset()->url('images/twitter.png')  }}" alt="Image"></a>
            </li>
            <li>

                <a href="https://web.whatsapp.com/send?url={{ urlencode(url()->current().'/') }}&text={{ urlencode(url()->current().'/') }}" target="_blank" title="{{ __('Share on Whatsapp') }}"><img class="m-1 p-1 social_icons" src="{{ Theme::asset()->url('images/whatsapp.png')  }}" alt="Image"></a>
            </li>
        </ul>
    </div>
</div>
</div>
