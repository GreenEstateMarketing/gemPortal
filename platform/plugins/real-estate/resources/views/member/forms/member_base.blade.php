@extends('plugins/real-estate::member.layouts.member_skeleton')
@section('content')
    <div class="container">
        <div class="row">

            <div class="col-md-12 col-xl-12 pt-5 pr-5">
               <!-- @if ($showStart)
                    {!! Form::open(Arr::except($formOptions, ['template'])) !!}
                @endif-->
<!--                        action="{{route('general-save-property')}}"-->
                        <form method="POST" action="" class="custom_form" accept-charset="UTF-8" id="form_member" enctype="multipart/form-data" novalidate="novalidate dafdasf">
                @csrf
                            {{-- Hiding Language Notification --}}
               {{-- @php do_action(BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION, request(), $form->getModel()) @endphp --}}
                <div class="row">
                    <div class="col-md-12">
                        @if ($showFields && $form->hasMainFields())
                            <div class="main-form p-4">
                                <div class="{{ $form->getWrapperClass() }}">
                                    @foreach ($fields as $key => $field)
                                        @if ($field->getName() == $form->getBreakFieldPoint())
                                            @break
                                        @else
                                            @unset($fields[$key])
                                        @endif
                                        @if (!in_array($field->getName(), $exclude))
                                            {!! $field->render() !!}
                                            @if ($field->getName() == 'name' && defined('BASE_FILTER_SLUG_AREA'))
                                                {!! apply_filters(BASE_FILTER_SLUG_AREA, $form->getModel()) !!}
                                            @endif
                                        @endif
                                    @endforeach
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        @endif
                            <input type="hidden" id="super_user" name="super_user" value="" />
                            <input type="hidden" id="json_documents" name="json_documents" value="{{$form->getModel()->documents?:""}}" />
                            <input type="hidden" id="category_id" name="category_id" value="{{$form->getModel()->category_id}}" />
                            <input type="hidden" id="category_name" name="category_name" value="{{$form->getModel()->category->name}}" />
                            <input type="hidden" id="type" name="type" value="{{$form->getModel()->type}}" />
                            <input type="hidden" id="template_desp" name="template_desp" value="" />
                        @foreach ($form->getMetaBoxes() as $key => $metaBox)
                            {!! $form->getMetaBox($key) !!}
                        @endforeach
                        {{-- Hiding SEO widget -- }}
                        {{--@php do_action(BASE_ACTION_META_BOXES, 'advanced', $form->getModel()) @endphp --}}
                    </div>

                @include('plugins/real-estate::partials.form-contact_form')

                <!--<div class="col-md-3 right-sidebar">
                        {!! $form->getActionButtons() !!}
                        {{-- hiding language on right sidebar --}}
                       {{-- @php do_action(BASE_ACTION_META_BOXES, 'top', $form->getModel()) @endphp --}}
                        @foreach ($fields as $field)
                            @if (!in_array($field->getName(), $exclude))
                                <div class="widget meta-boxes">
                                    <div class="widget-title">
                                        <h4>{!! Form::customLabel($field->getName(), $field->getOption('label'), $field->getOption('label_attr')) !!}</h4>
                                    </div>
                                    <div class="widget-body">
                                        {!! $field->render([], in_array($field->getType(), ['radio', 'checkbox'])) !!}
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        @php do_action(BASE_ACTION_META_BOXES, 'side', $form->getModel()) @endphp
                    </div>-->
                </div>

                @if ($showEnd)
                    {!! Form::close() !!}
                @endif

        </div>
    </div>
        <section class="footer-bar mt-5">
            <div class="container">
                <div class="inner wow fadeIn">
                    <div class="row">
                        <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.05s">
                            <figure><img src="{{ Theme::asset()->url('images/footer-icon01.png')  }}" alt="Image"></figure>
                            <h3>Address Infos</h3>
                            <p>{{ theme_option('address') }}</p>
                        </div>
                        <!-- end col-4 -->
                        <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.10s">
                            <figure><img src="{{ Theme::asset()->url('images/footer-icon02.png')  }}" alt="Image"></figure>
                            <h3>Working Hours</h3>
                            <p>Monday to Friday <strong>09:00</strong> to <strong>18:30</strong> <br>
                                Saturday we work until <strong>15:30</strong></p>
                        </div>
                        <!-- end col-4 -->
                        <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.15s">
                            <figure><img src="{{ Theme::asset()->url('images/footer-icon03.png')  }}" alt="Image"></figure>
                            <h3>Sales Office</h3>
                            <p># 23 Block - A North Avenue, Gulberg
                                Greens, Islamabad</p>
                        </div>
                        <!-- end col-4 -->
                    </div>
                    <!-- end row -->
                </div>
                <!-- end inner -->
            </div>
            <!-- end container -->
            <div class="modal fade terms-modal"  id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document" >
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Terms & Conditions</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" id="term_condition_body">

                        </div>
                        <div class="modal-footer">
                            <div class="form-group">


                                <input type="checkbox"
                                       name="modal_terms" id="modal_terms" value="1" required /><label>&nbsp; I accept</label>
                                <span  style="cursor: pointer" class="red" >
                    GEM Terms & Conditions
                </span>
                                {{--{{ trans('plugins/real-estate::dashboard.gem-terms') }}--}}


                            </div>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end footer-bar -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.05s">
                        @if (theme_option('logo'))
                            <a class="navbar-brand" href="{{ route('public.single') }}">
                                <img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}"
                                     class="logo" height="40" alt="{{ theme_option('site_name') }}">
                            </a>
                        @endif
                        <p>GEM has been established in 2020 to
                            introduce novelty and modernity to the real
                            estate sector of Pakistan.</p>

                        <!-- end select-box -->
                    </div>
                    <!-- end col-4 -->
                    <div class="col-lg-2 col-md-6 wow fadeInUp" data-wow-delay="0.10s">
                        <ul class="footer-menu">
                            <li><a href="#">Home</a></li>
                            <li><a href="#">Apartments</a></li>
                            <li><a href="#">Facilities</a></li>
                            <li><a href="#">News</a></li>
                            <li><a href="/contact">Contact</a></li>
                        </ul>
                    </div>
                    <!-- end col-2 -->
                    <div class="col-lg-2 col-md-6 wow fadeInUp" data-wow-delay="0.15s">
                        <ul class="footer-menu">
                            <li><a href="#">Suites</a></li>
                            <li><a href="#">Apartments</a></li>
                            <li><a href="#">Villas & Houses</a></li>
                            <li><a href="#">Butique Room</a></li>
                            <li><a href="#">Buildings</a></li>
                        </ul>
                    </div>
                    <!-- end col-2 -->
                    <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.20s">
                        <div class="contact-box">
                            <h5>CALL CENTER</h5>
                            <h3>{{ theme_option('hotline') }}</h3>
                            <p><a href="#">{{ theme_option('email') }}</a></p>
                            <ul>
                                <li><a href="{{ theme_option('facebook') }}"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="{{ theme_option('twitter') }}"><i class="fab fa-twitter"></i></a></li>
                                <li><a href="{{ theme_option('youtube') }}"><i class="fab fa-youtube"></i></a></li>
                            </ul>
                        </div>
                        <!-- end contact-box -->
                    </div>
                    <!-- end col-4 -->
                    <div class="col-12"> <span class="copyright">© {{ date('Y') }} {!! clean(theme_option('copyright')) !!}</span> <span class="creation">Site created by <a href="#">SwiftLogix</a></span> </div>
                    <!-- end col-12 -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->

        </footer>

<!--
        </body>
        </html>-->
@stop

@push('footer')
    {!! $form->renderValidatorJs() !!}
@endpush
