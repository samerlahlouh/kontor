<footer class="footer">
    <div class="container">
        <div class="row  big-footer">
            <div class="col">
                <span class="copyright">Copyright &copy; {{ __('layout_lng.edu_logo') }}</span>
            </div>
            <div id="social-media" class="col">
                <ul class="list-inline social-buttons">
                    <li class="list-inline-item">
                        <a href="#">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </li>
                    <li class="list-inline-item">
                        <a href="#">
                            <i class="fab fa-facebook"></i>
                        </a>
                    </li>
                    <li class="list-inline-item">
                        <a href="#">
                            <i class="fab fa-linkedin"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="col">
                <ul class="list-inline quicklinks">
                    <li class="list-inline-item">
                        <a id="lng_en" class="lang" val="en" href="">{{ __('layout_lng.en') }}</a>
                    </li>
                    <li  class="list-inline-item">
                        <a id="lng_ar" class="lang" val="ar" href="">{{ __('layout_lng.ar') }}</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="small-footer">
            <div class="row">
                <div class="col">
                    <span class="copyright">Copyright &copy; {{ __('layout_lng.edu_logo') }}</span>
                    <ul class="list-inline quicklinks">
                        <li class="list-inline-item">
                            <a id="lng_en" class="lang" val="en" href="">{{ __('layout_lng.en') }}</a>
                        </li>
                        <li  class="list-inline-item">
                            <a id="lng_ar" class="lang" val="ar" href="">{{ __('layout_lng.ar') }}</a>
                        </li>
                    </ul>
                </div>
                <div class="col">
                    <div class="row">
                        <div id="social-media" class="col">
                            <ul class="list-inline social-buttons">
                                <li class="list-inline-item">
                                    <a href="#">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="#">
                                        <i class="fab fa-facebook"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="#">
                                        <i class="fab fa-linkedin"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</footer>

<!--____________________________________Java Scripts___________________________________-->
    <!-- Get Lang -->
    <script>var LANGS = [];</script>
    <script src="{{ asset('js/langs/'.app()->getLocale().'/data_table_lng.js') }}" ></script>
    <script src="{{ asset('js/langs/'.app()->getLocale().'/home_lng.js') }}" ></script>
    <script src="{{ asset('js/langs/'.app()->getLocale().'/users_lng.js') }}" ></script>
    <script src="{{ asset('js/langs/'.app()->getLocale().'/packets.js') }}" ></script>

    <!-- Bootstrap core JavaScript -->
    <script src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.js') }}" ></script>

    <!-- Plugin JavaScript -->
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}" ></script>

    <!-- Contact form JavaScript -->
    <script src="{{ asset('js/jqBootstrapValidation.js') }}" ></script>
    <script src="{{ asset('js/contact_me.js') }}" ></script>

    <!-- Custom scripts for this template -->
    <script src="{{ asset('js/agency.min.js') }}" ></script>

    <!--________________________________DataTable Style__________________________________-->
    <script src="{{ asset('DataTable/datatables.js') }}" ></script>
    <script src = "https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('js/pages/data_table.js') }}" ></script>
    
    <!-- Sweet alert JavaScript-->
    <script src="{{ asset('my-tools/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('my-tools/sweetalert2/dist/promise-polyfill.js') }}"></script>

    <!-- Sweet modals JavaScript-->
    <script src="{{ asset('my-tools/sweet-modals/min/jquery.sweet-modal.min.js') }}"></script>

    <!-- Page script -->
    @if(isset($page_js))
        <script src="{{ asset('js/pages/'.$page_js.'.js') }}"></script>
    @endif

    <!--___________________________________Special java script____________________________________-->
    <script src="{{ asset('js/pages/layout.js') }}" ></script>
    
    <script>
        $(window).load(function () {
            $('.loading').hide();
        });
    </script>

</body>
</html>