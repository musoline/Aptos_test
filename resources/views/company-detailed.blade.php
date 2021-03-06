<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Aptos</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="content">

                <div>
                    <img src="{{ '/storage/' . $company['logo']}}" width=100 height=100 alt="">
                    <h1>{{$company["name"]}}</h1>
                    <p>კომპანიის ვებგვერდი: <a href="{{$company['website']}}">{{$company['name']}}</a></p>

                    <p style="text-align:left"> <a href="/">კომპანიები</a></p>
                    <h3>თანამშრომლები</h3>
                </div>
                <table border="1">
                    <tr>
                        <th>სახელი</th>
                        <th>გვარი</th>
                        <th>ფოსტა</th>
                        <th>ტელეფონი</th>
                        <th>Actions</th>
                    </tr>
                    @foreach($employees as $employee)
                    <tr>
                        <td>{{$employee["first_name"]}}</td>        
                        <td>{{$employee["last_name"]}}</td>        
                        <td>{{$employee["email"]}}</td>        
                        <td>{{$employee["phone"]}} </td>        
                        <td>
                        {{$employee['id']}}
                        <a href="/employee-detailed/{{$employee['id']}}">დეტალურად</a></td>        
                    </tr>
                    @endforeach

                </table>
                
                
                
            </div>
        </div>
    </body>
</html>
