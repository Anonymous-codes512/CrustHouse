<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Crust House</title>
  <link rel="stylesheet" href="{{ asset('CSS/index.css') }}">
  <link rel="icon" href="{{ asset('Images/Web_Images/chlogo.png')}}" type="image/png" />
</head>

<body>
  <header class="header">
    <div class="logo">
      <img src="{{ asset('Images/Web_Images/chlogo.png')}}" alt="">
    </div>
    <h1 class="company-name">CRUST <span>HOUSE</span></h1>
    <div class="menu">
      <a href="{{route('viewLoginPage')}}">Login</a>
    </div>
  </header>
  <main class="main">
    <div class="content1">
      <div class="container1">
        <div class="left1">
          <h1>Meet, Eat & <br /><span>Enjoy The Taste</span></h1>
          <p> Food tastes better when you share it with your family and friends.</p>
        </div>
        <div class="right1">
          <img src="{{ asset('Images/Web_Images/c1.jpg')}}" alt="">
        </div>
      </div>
      <div class="menu-btn">
        <a href="{{route('onlineOrderPage')}}">Our Menu</a>
      </div>
    </div>

    <div class="content2">
      <h1>Our Special <span>Recipes</span></h1>
      <p>Those are our top recipes considering the taste and price our customers claimed to be perfect !</p>
    </div>

    <div class="content3">
      <div class="card">
        <div class="cardImg">
          <img src="{{ asset('Images/Web_Images/c1.jpg')}}" alt="">
        </div>
        <div class="card-detail">
          <h1>Pizza Peperoni</h1>
          <p><span>Toppings</span></p>
          <ul>
            <li>
              <h2>Fresh Basils</h2>
            </li>
            <li>
              <h2>Crushed Red Pepper Flakes</h2>
            </li>
            <li>
              <h2>Jalapeno Slices</h2>
            </li>
          </ul>
          <p><span>Price</span></p>
          <ul>
            <li>
              <h2>
                Starting From
                <h3>RS.650</h3>
              </h2>
            </li>
          </ul>
        </div>
      </div>

      <div class="card">
        <div class="cardImg">
          <img src="{{ asset('Images/Web_Images/p2.jpg')}}" alt="">
        </div>
        <div class="card-detail">
          <h1>Pizza Margarita</h1>
          <p><span>Toppings</span></p>
          <ul>
            <li>
              <h2>Fresh Basil</h2>
            </li>
            <li>
              <h2>Mozzarella Cheese</h2>
            </li>
            <li>
              <h2>Garlic</h2>
            </li>
          </ul>
          <p><span>Price</span></p>
          <ul>
            <li>
              <h2>
                Starting From
                <h3>RS.850</h3>
              </h2>
            </li>
          </ul>
        </div>
      </div>

      <div class="card">
        <div class="cardImg">
          <img src="{{ asset('Images/Web_Images/p3.jpg')}}" alt="">
        </div>
        <div class="card-detail">
          <h1>Hawaiian Pizza</h1>
          <p><span>Toppings</span></p>
          <ul>
            <li>
              <h2>Pineapple</h2>
            </li>
            <li>
              <h2>Tomato Sauce</h2>
            </li>
            <li>
              <h2>Cheese</h2>
            </li>
          </ul>
          <p><span>Price</span></p>
          <ul>
            <li>
              <h2>
                Starting From
                <h3>RS.950</h3>
              </h2>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="spacer"></div>
    <div class="content3">
        <div class="card">
          <div class="cardImg">
            <img src="{{ asset('Images/Web_Images/p4.jpg')}}" alt="">
          </div>
          <div class="card-detail">
          <h1>BBQ Pizza</h1>
          <p><span>Toppings</span></p>
          <ul>
            <li>
              <h2>Fresh Basils</h2>
            </li>
            <li>
              <h2>Crushed Red Pepper Flakes</h2>
            </li>
            <li>
              <h2>BBQ Chicken</h2>
            </li>
          </ul>
          <p><span>Price</span></p>
          <ul>
            <li>
              <h2>
                Starting From
                <h3>RS.750</h3>
              </h2>
            </li>
          </ul>
        </div>
        </div>

        <div class="card">
          <div class="cardImg">
            <img src="{{ asset('Images/Web_Images/p5.jpg')}}" alt="">
          </div>
          <div class="card-detail">
          <h1>Mughlai Pizza</h1>
          <p><span>Toppings</span></p>
          <ul>
            <li>
              <h2>Fresh Basils</h2>
            </li>
            <li>
              <h2>Crushed Red Pepper Flakes</h2>
            </li>
            <li>
              <h2>Mughlai Base</h2>
            </li>
          </ul>
          <p><span>Price</span></p>
          <ul>
            <li>
              <h2>
                Starting From
                <h3>RS.1000</h3>
              </h2>
            </li>
          </ul>
        </div>
        </div>
      </div>
      <div class="spacer"></div>
      <div class="content4">
        <div class="container4">
          <div class="left4">
            <img src="{{ asset('Images/Web_Images/menu1.png')}}" alt="">
          </div>
          <div class="right4">
            <h1>OUR <span>MENU:</span></h1>
            <p>
              Our menu features a wide variety of pizzas, from classic margarita
              to our signature BBQ chicken pizza. We also offer a selection of
              salads, appetizers, and desserts to complement your meal. All of our
              pizzas are available in small, medium, and large sizes, so you can
              order the perfect amount for your group
            </p>
          </div>
        </div>
        <div class="menu-btn4">
          <a href="{{route('onlineOrderPage')}}">Our Menu</a>
        </div>
      </div>
      
      <div class="spacer"></div>
      <div class="content5">
        <div class="card2">
          <img src="{{ asset('Images/Web_Images/icon1.png')}}" alt="">
          <h1>Order <span>Food !</span></h1>
          <p>
            Pick-Up your food if you are taking it away or just seat, relax and
            have it on your table when ready !
          </p>
        </div>
        <div class="card2">
          <img src="{{ asset('Images/Web_Images/icon2.png')}}" alt="">
          <h1>Pick-Up <span>Food !</span></h1>
          <p>
            As easy as never ever before ! Now with our advanced stuff, ordering
            food it’s a piece of cake ! Also you can order online, something
            even easier with our brand new website !
          </p>
        </div>

        <div class="card2">
          <img src="{{ asset('Images/Web_Images/icon3.png')}}" alt="">
          <h1>Enjoy <span>Food !</span></h1>
          <p>
            As soon as you get your food you can enjoy it till the last piece of
            it and come back soon for another one !
          </p>
        </div>
      </div>
      <div class="spacer"></div>
      <div class="content6">
        <div class="left6">
          <h1>Cooked by the <span>best Chefs in the world:</span></h1>
          <p>
            There are many benefits regarding to that but the main ones are:
          </p>
          <p><img src="{{ asset('Images/Web_Images/tick.png')}}" alt=""> Perfect Tase</p>
          <p><img src="{{ asset('Images/Web_Images/tick.png')}}" alt=""> Done Quickly</p>
          <p><img src="{{ asset('Images/Web_Images/tick.png')}}" alt=""> Food Guaranteed Hygenic</p>
        </div>
        <div class="right6">
          <img src="{{ asset('Images/Web_Images/pizza.jpg')}}" alt="">
        </div>
      </div>
      
  </main>

  <footer class="footer">
    <h1>
      Powered By:
      <a href="https://tachyontechs.com/"><img src="{{ asset('Images/Web_Images/logo.png')}}" alt=""></a>
    </h1>
    <h2>&copy;2023 Crust House. All rights reserved.</h2>
  </footer>
</body>

</html>