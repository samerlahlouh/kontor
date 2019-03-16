<section id="contact">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2 class="section-heading text-uppercase">{{ __('home_lng.contact_us') }}</h2>
                <h3 class="section-subheading text-muted">{{ __('home_lng.contact_us_text') }}</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <form id="contactForm" name="sentMessage" novalidate="novalidate" method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input class="form-control" id="name" name="name" type="text" placeholder="{{ __('home_lng.your_name') }}" required="required" data-validation-required-message="{{ __('home_lng.name_required') }}">
                                <p class="help-block text-danger"></p>
                            </div>
                            <div class="form-group">
                                <input class="form-control" id="email" name="email" type="email" placeholder="{{ __('home_lng.your_email') }}" required="required" data-validation-required-message="{{ __('home_lng.email_required') }}">
                                <p class="help-block text-danger"></p>
                            </div>
                            <div class="form-group">
                                <input class="form-control" id="phone" name="phone" type="tel" placeholder="{{ __('home_lng.youe_phone') }}" required="required" data-validation-required-message="{{ __('home_lng.phone_required') }}">
                                <p class="help-block text-danger"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <textarea class="form-control" id="message" name="message" placeholder="{{ __('home_lng.your_message') }}" required="required" data-validation-required-message="{{ __('home_lng.message_required') }}"></textarea>
                                <p class="help-block text-danger"></p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-lg-12 text-center">
                            <div id="success"></div>
                            <button id="sendMessageButton" class="btn btn-primary btn-xl text-uppercase" type="submit">
                                {{ __('home_lng.send') }}
                                <i class='fa fa-paper-plane' aria-hidden='true'></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>