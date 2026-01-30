<!--begin::Header-->
<div id="kt_header" class="header header-bg">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Brand-->
        <div class="header-brand me-5">
            <!--begin::Aside toggle-->
            <div class="d-flex align-items-center d-lg-none ms-n2 me-2" title="Show aside menu">
                <div class="btn btn-icon btn-color-white btn-active-color-primary w-30px h-30px" id="kt_aside_toggle">
                    <i class="ki-duotone ki-abstract-14 fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <!--end::Aside toggle-->
            <!--begin::Logo-->
            <a href="index.html">
                <img alt="Logo" src="{{ asset('assets/media/logos/Logo_Bethsaida_Hospitals.png') }}" class="h-35px h-lg-45px d-none d-md-block bg-white rounded p-1" />
                <img alt="Logo" src="{{ asset('assets/media/logos/Logo_Bethsaida_Hospitals.png') }}" class="h-35px h-lg-45px d-block d-md-none bg-white rounded p-1" />
            </a>
            <!--end::Logo-->
        </div>
        <!--end::Brand-->
        <!--begin::Topbar-->
        <div class="topbar d-flex align-items-stretch">
            <!--begin::Item-->
            <div class="d-flex align-items-center me-2 me-lg-4">
                <a href="#" class="btn btn-icon btn-borderless btn-color-white btn-active-primary bg-white bg-opacity-10 position-relative" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                    <i class="ki-duotone ki-notification-bing fs-1 text-white">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <span class="bullet bullet-dot bg-success h-6px w-6px position-absolute translate-middle top-0 start-50 animation-blink"></span>
                </a>
                <!--begin::Menu-->
                <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px" data-kt-menu="true" id="kt_menu_notifications">
                    <!--begin::Heading-->
                    <div class="d-flex flex-column bgi-no-repeat rounded-top" style="background-image:url('{{ asset('assets/media/misc/header-bg.png') }}')">
                        <!--begin::Title-->
                        <h3 class="text-white fw-semibold px-9 mt-10 mb-6">Notifications 
                        <span class="fs-8 opacity-75 ps-3">24 reports</span></h3>
                        <!--end::Title-->
                        <!--begin::Tabs-->
                        <ul class="nav nav-line-tabs nav-line-tabs-2x nav-stretch fw-semibold px-9">
                            <li class="nav-item">
                                <a class="nav-link text-white opacity-75 opacity-state-100 pb-4 active" data-bs-toggle="tab" href="#kt_topbar_notifications_1">Alerts</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white opacity-75 opacity-state-100 pb-4" data-bs-toggle="tab" href="#kt_topbar_notifications_3">Logs</a>
                            </li>
                        </ul>
                        <!--end::Tabs-->
                    </div>
                    <!--end::Heading-->
                    <!--begin::Tab content-->
                    <div class="tab-content">
                        <!--begin::Tab panel-->
                        <div class="tab-pane fade show active" id="kt_topbar_notifications_1" role="tabpanel">
                            <!--begin::Items-->
                            <div class="scroll-y mh-325px my-5 px-8">
                                <!--begin::Item-->
                                <div class="d-flex flex-stack py-4">
                                    <!--begin::Section-->
                                    <div class="d-flex align-items-center">
                                        <!--begin::Symbol-->
                                        <div class="symbol symbol-35px me-4">
                                            <span class="symbol-label bg-light-primary">
                                                <i class="ki-duotone ki-abstract-28 fs-2 text-primary">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>
                                        </div>
                                        <!--end::Symbol-->
                                        <!--begin::Title-->
                                        <div class="mb-0 me-2">
                                            <a href="#" class="fs-6 text-gray-800 text-hover-primary fw-bold">Project Alice</a>
                                            <div class="text-gray-500 fs-7">Phase 1 development</div>
                                        </div>
                                        <!--end::Title-->
                                    </div>
                                    <!--end::Section-->
                                    <!--begin::Label-->
                                    <span class="badge badge-light fs-8">1 hr</span>
                                    <!--end::Label-->
                                </div>
                                <!--end::Item-->
                                <!--begin::Item-->
                                <div class="d-flex flex-stack py-4">
                                    <!--begin::Section-->
                                    <div class="d-flex align-items-center">
                                        <!--begin::Symbol-->
                                        <div class="symbol symbol-35px me-4">
                                            <span class="symbol-label bg-light-danger">
                                                <i class="ki-duotone ki-information fs-2 text-danger">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                            </span>
                                        </div>
                                        <!--end::Symbol-->
                                        <!--begin::Title-->
                                        <div class="mb-0 me-2">
                                            <a href="#" class="fs-6 text-gray-800 text-hover-primary fw-bold">HR Confidential</a>
                                            <div class="text-gray-500 fs-7">Confidential staff documents</div>
                                        </div>
                                        <!--end::Title-->
                                    </div>
                                    <!--end::Section-->
                                    <!--begin::Label-->
                                    <span class="badge badge-light fs-8">2 hrs</span>
                                    <!--end::Label-->
                                </div>
                                <!--end::Item-->
                            </div>
                            <!--end::Items-->
                            <!--begin::View more-->
                            <div class="py-3 text-center border-top">
                                <a href="pages/user-profile/activity.html" class="btn btn-color-gray-600 btn-active-color-primary">View All 
                                <i class="ki-duotone ki-arrow-right fs-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i></a>
                            </div>
                            <!--end::View more-->
                        </div>
                        <!--end::Tab panel-->
                        <!--begin::Tab panel-->
                        <div class="tab-pane fade" id="kt_topbar_notifications_3" role="tabpanel">
                            <!--begin::Items-->
                            <div class="scroll-y mh-325px my-5 px-8">
                                <!--begin::Item-->
                                <div class="d-flex flex-stack py-4">
                                    <!--begin::Section-->
                                    <div class="d-flex align-items-center me-2">
                                        <!--begin::Code-->
                                        <span class="w-70px badge badge-light-success me-4">200 OK</span>
                                        <!--end::Code-->
                                        <!--begin::Title-->
                                        <a href="#" class="text-gray-800 text-hover-primary fw-semibold">New order</a>
                                        <!--end::Title-->
                                    </div>
                                    <!--end::Section-->
                                    <!--begin::Label-->
                                    <span class="badge badge-light fs-8">Just now</span>
                                    <!--end::Label-->
                                </div>
                                <!--end::Item-->
                                <!--begin::Item-->
                                <div class="d-flex flex-stack py-4">
                                    <!--begin::Section-->
                                    <div class="d-flex align-items-center me-2">
                                        <!--begin::Code-->
                                        <span class="w-70px badge badge-light-danger me-4">500 ERR</span>
                                        <!--end::Code-->
                                        <!--begin::Title-->
                                        <a href="#" class="text-gray-800 text-hover-primary fw-semibold">New customer</a>
                                        <!--end::Title-->
                                    </div>
                                    <!--end::Section-->
                                    <!--begin::Label-->
                                    <span class="badge badge-light fs-8">2 hrs</span>
                                    <!--end::Label-->
                                </div>
                                <!--end::Item-->
                            </div>
                            <!--end::Items-->
                            <!--begin::View more-->
                            <div class="py-3 text-center border-top">
                                <a href="pages/user-profile/activity.html" class="btn btn-color-gray-600 btn-active-color-primary">View All 
                                <i class="ki-duotone ki-arrow-right fs-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i></a>
                            </div>
                            <!--end::View more-->
                        </div>
                        <!--end::Tab panel-->
                    </div>
                    <!--end::Tab content-->
                </div>
                <!--end::Menu-->
            </div>
            <!--end::Item-->
            <!--begin::Item-->
            <div class="d-flex align-items-center me-2 me-lg-4">
                <a href="#" class="btn btn-icon btn-borderless btn-color-white btn-active-primary bg-white bg-opacity-10" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                    <i class="ki-duotone ki-user fs-1 text-white">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </a>
                <!--begin::User account menu-->
                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px" data-kt-menu="true">
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        <div class="menu-content d-flex align-items-center px-3">
                            <!--begin::Avatar-->
                            <div class="symbol symbol-50px me-5">
                                <div class="symbol-label fs-2 fw-semibold bg-primary text-inverse-primary">{{ ucwords(substr(Auth::user()->name, 0, 1)) }}</div>
                            </div>
                            <!--end::Avatar-->
                            <!--begin::Username-->
                            <div class="d-flex flex-column">
                                <div class="fw-bold d-flex align-items-center fs-5">{{ Auth::user()->name }}</div>
                                <a href="#" class="fw-semibold text-muted text-hover-primary fs-7">{{ Auth::user()->email }}</a>
                            </div>
                            <!--end::Username-->
                        </div>
                    </div>
                    <!--end::Menu item-->
                    <!--begin::Menu separator-->
                    <div class="separator my-2"></div>
                    <!--end::Menu separator-->
                    <!--begin::Menu item-->
                    <div class="menu-item px-5">
                        <a href="account/overview.html" class="menu-link px-5">My Profile</a>
                    </div>
                    <!--end::Menu item-->
                    <!--begin::Menu item-->
                    <div class="menu-item px-5">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="#" class="menu-link px-5" onclick="event.preventDefault(); this.closest('form').submit();">Sign Out</a>
                        </form>
                    </div>
                    <!--end::Menu item-->
                </div>
                <!--end::User account menu-->
            </div>
            <!--end::Item-->
            <!--begin::Theme mode-->
            <div class="d-flex align-items-center me-2 me-lg-4">
                <!--begin::Menu toggle-->
                <a href="#" class="btn btn-icon btn-borderless btn-color-white btn-active-primary bg-white bg-opacity-10" data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                    <i class="ki-duotone ki-night-day theme-light-show fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                        <span class="path5"></span>
                        <span class="path6"></span>
                        <span class="path7"></span>
                        <span class="path8"></span>
                        <span class="path9"></span>
                        <span class="path10"></span>
                    </i>
                    <i class="ki-duotone ki-moon theme-dark-show fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </a>
                <!--begin::Menu toggle-->
                <!--begin::Menu-->
                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px" data-kt-menu="true" data-kt-element="theme-mode-menu">
                    <!--begin::Menu item-->
                    <div class="menu-item px-3 my-0">
                        <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
                            <span class="menu-icon" data-kt-element="icon">
                                <i class="ki-duotone ki-night-day fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                    <span class="path6"></span>
                                    <span class="path7"></span>
                                    <span class="path8"></span>
                                    <span class="path9"></span>
                                    <span class="path10"></span>
                                </i>
                            </span>
                            <span class="menu-title">Light</span>
                        </a>
                    </div>
                    <!--end::Menu item-->
                    <!--begin::Menu item-->
                    <div class="menu-item px-3 my-0">
                        <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark">
                            <span class="menu-icon" data-kt-element="icon">
                                <i class="ki-duotone ki-moon fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">Dark</span>
                        </a>
                    </div>
                    <!--end::Menu item-->
                    <!--begin::Menu item-->
                    <div class="menu-item px-3 my-0">
                        <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="system">
                            <span class="menu-icon" data-kt-element="icon">
                                <i class="ki-duotone ki-screen fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </span>
                            <span class="menu-title">System</span>
                        </a>
                    </div>
                    <!--end::Menu item-->
                </div>
                <!--end::Menu-->
            </div>
            <!--end::Theme mode-->
        </div>
        <!--end::Topbar-->
    </div>
    <!--end::Container-->
</div>
<!--end::Header-->