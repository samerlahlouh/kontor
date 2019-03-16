@extends('layouts.app')

@section('content')
    <!-- Header -->
    <header class="masthead">
        <div class="container">
            <div class="intro-text">
                <div class="intro-lead-in">{{ __('home_lng.welcome') }}</div>
                <div class="intro-heading text-uppercase">{{ __('home_lng.intro') }}</div>
                <a class="btn btn-primary btn-xl text-uppercase js-scroll-trigger" href="#services">{{ __('home_lng.more') }}</a>
            </div>
        </div>
    </header>

    <!-- Services -->
    <section id="services">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2 class="section-heading text-uppercase">{{ __('home_lng.services') }}</h2>
                    <h3 class="section-subheading text-muted">{{ __('home_lng.services_more') }}</h3>
                </div>
            </div>
            <div class="row text-center">
                <div class="col-md-4">
                    <span class="fa-stack fa-4x">
                    <i class="fa fa-circle fa-stack-2x text-primary"></i>
                    <i class="fas fa-school fa-stack-1x fa-inverse"></i>
                    </span>
                    <h4 class="service-heading">{{ __('home_lng.school') }}</h4>
                    <p class="text-muted">{{ __('home_lng.school_txt') }}</p>
            </div>
            <div class="col-md-4">
                <span class="fa-stack fa-4x">
                <i class="fa fa-circle fa-stack-2x text-primary"></i>
                <i class="fas fa-university fa-stack-1x fa-inverse"></i>
                </span>
                <h4 class="service-heading">{{ __('home_lng.university') }}</h4>
                <p class="text-muted">{{ __('home_lng.university_txt') }}</p>
            </div>
            <div class="col-md-4">
                <span class="fa-stack fa-4x">
                <i class="fa fa-circle fa-stack-2x text-primary"></i>
                <i class="fas fa-briefcase fa-stack-1x fa-inverse"></i>
                </span>
                <h4 class="service-heading">{{ __('home_lng.job') }}</h4>
                <p class="text-muted">{{ __('home_lng.job_txt') }}</p>
            </div>
            <div class="col-md-4">
                <span class="fa-stack fa-4x">
                <i class="fa fa-circle fa-stack-2x text-primary"></i>
                <i class="fas fa-users fa-stack-1x fa-inverse"></i>
                </span>
                <h4 class="service-heading">{{ __('home_lng.employees') }}</h4>
                <p class="text-muted">{{ __('home_lng.job_txt') }}</p>
            </div>
            <div class="col-md-4">
                <span class="fa-stack fa-4x">
                <i class="fa fa-circle fa-stack-2x text-primary"></i>
                <i class="fas fa-book fa-stack-1x fa-inverse"></i>
                </span>
                <h4 class="service-heading">{{ __('home_lng.courses') }}</h4>
                <p class="text-muted">{{ __('home_lng.courses_txt') }}</p>
            </div>
            </div>
        </div>
    </section>

    <!-- About -->
    <section id="about">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2 class="section-heading text-uppercase">{{ __('home_lng.about') }}</h2>
                    <h3 class="section-subheading text-muted">{{ __('home_lng.about_owner') }}</h3>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <ul class="timeline">
                        <li class="timeline-inverted">
                            <div class="timeline-image">
                                <img class="rounded-circle img-fluid" src="img/about/1.jpg" alt="">
                            </div>
                            <div class="timeline-panel">
                                <div class="timeline-heading">
                                    <h4>{{ __('home_lng.my_name') }}</h4>
                                    <h3 class="section-subheading text-muted">{{ __('home_lng.about_me') }}</h3>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <br><br><br>

            <div class="row">
                <div class="col-lg-12 text-center">
                    <h3 class="section-subheading text-muted">{{ __('home_lng.about_title') }}</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <ul class="timeline">
                        <li>
                            <div class="timeline-image">
                                <img class="rounded-circle img-fluid" src="img/about/2.jpg" alt="">
                            </div>
                            <div class="timeline-panel">
                                <div class="timeline-heading">
                                    <h4 class="subheading">{{ __('home_lng.timeline1_title') }}</h4>
                                </div>
                                <div class="timeline-body">
                                    <p class="text-muted">{{ __('home_lng.timeline1_text') }}</p>
                                </div>
                            </div>
                        </li>
                        <li class="timeline-inverted">
                            <div class="timeline-image">
                                <img class="rounded-circle img-fluid" src="img/about/3.jpg" alt="">
                            </div>
                            <div class="timeline-panel">
                                <div class="timeline-heading">
                                    <h4>{{ __('home_lng.timeline21_title') }}</h4>
                                    <h4 class="subheading">{{ __('home_lng.timeline22_title') }}</h4>
                                </div>
                                <div class="timeline-body">
                                    <p class="text-muted">{{ __('home_lng.timeline2_text') }}</p>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="timeline-image">
                                <img class="rounded-circle img-fluid" src="img/about/4.jpg" alt="">
                            </div>
                            <div class="timeline-panel">
                                <div class="timeline-heading">
                                    <h4>{{ __('home_lng.timeline31_title') }}</h4>
                                    <h4 class="subheading">{{ __('home_lng.timeline32_title') }}</h4>
                                </div>
                                <div class="timeline-body">
                                    <p class="text-muted">{{ __('home_lng.timeline3_text') }}</p>
                                </div>
                            </div>
                        </li>
                        <li class="timeline-inverted">
                            <div class="timeline-image">
                                <h4>{{ __('home_lng.timeline_end_1') }}
                                    <br>{{ __('home_lng.timeline_end_2') }}
                                    <br>{{ __('home_lng.timeline_end_3') }}</h4>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact -->
    <section id="contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2 class="section-heading text-uppercase">Contact Us</h2>
                    <h3 class="section-subheading text-muted">Lorem ipsum dolor sit amet consectetur.</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <form id="contactForm" name="sentMessage" novalidate="novalidate">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input class="form-control" id="name" type="text" placeholder="Your Name *" required="required" data-validation-required-message="Please enter your name.">
                                    <p class="help-block text-danger"></p>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" id="email" type="email" placeholder="Your Email *" required="required" data-validation-required-message="Please enter your email address.">
                                    <p class="help-block text-danger"></p>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" id="phone" type="tel" placeholder="Your Phone *" required="required" data-validation-required-message="Please enter your phone number.">
                                    <p class="help-block text-danger"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <textarea class="form-control" id="message" placeholder="Your Message *" required="required" data-validation-required-message="Please enter a message."></textarea>
                                    <p class="help-block text-danger"></p>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-lg-12 text-center">
                                <div id="success"></div>
                                <button id="sendMessageButton" class="btn btn-primary btn-xl text-uppercase" type="submit">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

@endsection
