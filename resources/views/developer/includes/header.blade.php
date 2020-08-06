<header class="header">
    <div class="ss-container-fluid">
        <div class="header__wrap clearfix">
            <div class="header__left">
                <button type="button" class="nav__trigger" data-toggle=".sidebar">
                    <i class="material-icons">menu</i>
                </button>
                <div class="header__logo">
                    <a href="{!! url('/') !!}">
                        <h1 class="header__name">Laravel Boilerplate</h1>
                    </a>
                </div>
            </div>
            <div class="header__right">
                <nav class="nav">
                    <div class="md--box">
                        <button type="button" class="nav__trigger" data-toggle=".nav__list" data-autoclose>
                            <i class="material-icons">more_vert</i>
                        </button>
                        <a class="nav__button button nav__button2  has-icon" href="{!! url('/logout') !!}">
                            <i class="material-icons">exit_to_app</i>
                        </a>
                    </div>
                    <ul class="nav__list nav__list--admin has-border clearfix" >
                        <li>
                            <a class="nav__button nav__user"  href="javascript:;">
                                <div class="nav__user-photo">
                                    <img src="{!! asset('img/admin.svg') !!}" alt="">
                                </div>
                                <?php $user = Auth::user(); ?>
                                <span class="nav__user-name">{!! $user->name !!}</span>
                            </a>
                        </li>
                        <li>
                            <a class="nav__button  nav__log has-icon" href="{!! url('/logout') !!}">
                                    <i class="material-icons">exit_to_app</i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</header>
