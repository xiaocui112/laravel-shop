<nav class="navbar navbar-expand-lg navbar-light bg-light navbar-static-top">
    <div class="container">
        <a href="{{url('/')}}" class="navbar-brand">Laravel Shop</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <!-- 顶部类目菜单开始 -->
                <!-- 判断模板是否有 $categoryTree 变量 -->
                @if(isset($categoryTree))
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false" id="categoryTree">所有类目 <b class="caret"></b></a>
                    <ul class="dropdown-menu" aria-labelledby="categoryTree">
                        <!-- 遍历 $categoryTree 集合，将集合中的每一项以 $category 变量注入 layouts._category_item 模板中并渲染 -->
                        @each('layouts._category_item', $categoryTree, 'category')
                    </ul>
                </li>
                @endif
            </ul>
            <ul class="navbar-nav navbar-right">
                @guest
                <li class="nav-item"><a href="{{route('login')}}" class="nav-link">登录</a></li>
                <li class="nav-item"><a href="{{route('register')}}" class="nav-link">注册</a></li>
                @else
                <li class="nav-item">
                    <a href="{{route('cart.index')}}" class="nav-link mt-1">
                        <i class="fa fa-shopping-cart  mr-1"></i>购物车
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a href="" class="nav-link dropdown-toggle" id="navbarDropdown" role="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <img src="https://cdn.learnku.com/uploads/images/201709/20/1/PtDKbASVcz.png?imageView2/1/w/60/h/60"
                            alt="" class="img-responsivef img-circle" width="30px" height="30px">
                        {{Auth::user()->name}}
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a href="{{ route('orders.index') }}" class="dropdown-item">我的订单</a>
                        <a href="{{ route('products.favorites') }}" class="dropdown-item">我的收藏</a>
                        <a href="{{ route('user_addresses.index') }}" class="dropdown-item">收货地址</a>
                        <a class="dropdown-item" id="logout" href="#"
                            onclick="event.preventDefault();document.getElementById('logout-form').submit();">退出登录</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </div>
                </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>