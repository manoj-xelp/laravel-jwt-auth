<html>
<header>
<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='expires' content='0'>
<meta http-equiv='pragma' content='no-cache'>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
   <style>
        .login-form {
            font-style: normal;
            width: 380px;
            margin: 50px auto;
            padding: 15px;
            color:#3D3D3D;
        }
        .text-left {
            font-style: normal;
            font-weight: bold;
            font-size: 24px;
            line-height: 36px;
            display: flex;
            align-items: center;
            letter-spacing: 0.03em;
            color: #3D3D3D;
            margin-bottom: 0;
        }
        .line {
            font-size:13.5px;
            font-style: normal;
            font-weight: normal;
            line-height: 20px;
            display: flex;
            align-items: flex-end;
            letter-spacing: 0.03em;
            color: #3D3D3D;
        }
        .btn {
            border: 1px solid;
            font-weight: bold;
            font-family: 'Noto Sans';
            width:380px;
            height:50px;
            background: #3D3D3D;
            border-radius: 5px;

        }
        .form-control{
            width:380px;
            height:46px;
        }

        .messages{
            margin:2% auto;
            color: grey;
            padding-left:15px;
            font-size:14px;
            font-style: normal;
            font-weight: normal;
        }
        .alert-success{
            display: none;
            width:380px;
        }
        .alert-danger{
            display: none;
            margin-top:10px;
            width:380px;
        }
        ul {
           list-style: none;
        }

        li::before {
            content: "•";
            color: grey;
            display: inline-block;
            width: 1em;
            margin-left: -0.9em;
        }
        .bullet::before{
            content: "•";
            color: orange;
            display: inline-block;
            width: 1em;
            margin-left: -0.9em;
        }

        i{
            margin:auto -35px;
            cursor:pointer;
        }

        .input1{
            display:inline-flex;
        }

        .input-label {
            position: absolute;
            left: 35px;
            top: -18px;
            background: #fff;
            padding: 2px 10px;
            font-size: 12px;
        }

    </style>
</header>

<body>
    <div class="login-form">
        <form action={{env('SERVER_URL').'/api/v1/auth/reset_password/change?type=changepassword'}} method="post" enctype="multipart/form-data">
            <!--<img src={{url('logo.jpeg')}} class="rounded mx-auto d-block" alt="logo"> -->
            <h4 class="text-left mt-3">Reset your password</h4>
            <p class="line">Almost done. set your new password, and you're good to go.</p>
            <div class="form-group">
                <div class="row" style="margin-bottom:-10px; margin-top:12%;">
                <div class="col-sm"><label class="input-label">New password</label></div>
                </div>
                <div class="input1">
                <input type="password" minlength="8"  name="password" class="form-control pass" required="required" autocomplete='off' />
                <i class="fa fa-eye-slash" id="togglePassword"></i>
                </div>
                <div class="error" style="display:block">
                <ul class="messages">
                    <div class="row ">
                        <div class="col">
                            <li id="min">Minimum 8 characters </li>
                            <li id="uppercase">One uppercase</li>
                        </div>
                        <div class="col">
                            <li id="lowercase">One lowercase </li>
                            <li id="num" >One number</li>

                        </div>
                    </div>
                </ul>
                <div class="alert alert-success" role="alert">
                 <span style='font-size:1em;'>&#10004;</span>   Your password is secure and you're all set!
                </div>
            </div>
            </div>
            <div class="form-group">
                <div class="row" style="margin-bottom:-10px; margin-top:12%;">
                <div class="col-sm"><label class="input-label">Confirm new password</label></div>
                </div>
                <input id="conformPass" type="password" minlength="8"  name="password_confirmation" class="form-control rpass" disabled autocomplete='off'  required="required" />
                         <div class="alert alert-danger" role="alert">
                         <span style='font-size:1em;'>&#10006;</span> Password Mismatch</div>
            </div>

            <div class="form-group">
                <input type="hidden" name="token" class="form-control" placeholder="token" value="{{$token}}" />
            </div>
            <div class="form-group">
                <input type="hidden" name="source" class="form-control" placeholder="source" value={{$source}} /> 
            </div>
            <div class="form-group">
                <button disabled type="submit" class="btn btn-dark">
                    RESET PASSWORD
                </button>
            </div>
        </form>
    </div>

<script src="https://code.jquery.com/jquery-3.5.0.js" integrity="sha256-r/AaFHrszJtwpe+tHyNi/XCfMxYpbsRg2Uqn0x3s2zc=" crossorigin="anonymous"></script>
<script>

        var passw = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,20}$/;

        $(document).ready(function(){

            // console.log("init");
            $('#togglePassword').on('click', function(){
                const type = $('.pass').attr("type") === 'password' ? 'text' : 'password';
                    $('.pass').attr("type",type);
                    $('#togglePassword').toggleClass("fa fa-eye-slash fa fa-eye");
            });

            $('.pass').on('keyup', function(){
                if($('.pass').val().match(/[A-Z]/)){
                    $('#uppercase').css('color','black');
                    $('#uppercase').addClass("bullet");
                } else{
                    $('#uppercase').css('color','grey');
                    $('#uppercase').removeClass("bullet");
                }
                if($('.pass').val().match(/[a-z]/)){
                    $('#lowercase').css('color','black');
                    $('#lowercase').addClass("bullet");
                }
                else{
                    $('#lowercase').css('color','grey');
                    $('#lowercase').removeClass("bullet");
                }
                if($('.pass').val().match(/[0-9]/)){
                    $('#num').css('color','black');
                    $('#num').addClass("bullet");
                }
                else{
                    $('#num').css('color','grey');
                    $('#num').removeClass("bullet");
                }
                if($('.pass').val().match(/.{8,20}/)){
                    $('#min').css('color','black');
                    $('#min').addClass("bullet");
                }else{
                    $('#min').css('color','grey');
                    $('#min').removeClass("bullet");
                }
                if($('.pass').val().match(passw)){
                    $('.col').css('display','none');
                    $('.alert-success').css('display','block');
                }else{
                    $('.col').css('display','block');
                    $('.alert-success').css('display','none');
                }

                checkPasswordValidation();

            });

            $('.pass, .rpass').on('keyup', function(){
                    if($('.pass').val()!=$('.rpass').val() && $('.rpass').val()!=""){
                        $('button').prop('disabled', true);
                        $('.alert-danger').css('display','block');
                    }else if($('.pass').val()!="" && $('.rpass').val()!="") {
                        $('button').prop('disabled', false);
                        $('.alert-danger').css('display','none');
                        checkPasswordValidation();
                    }
            });
        });
        function checkPasswordValidation() {
            if($('#uppercase').hasClass("bullet")
                && $('#lowercase').hasClass("bullet")
                && $('#num').hasClass("bullet")
                && $('#min').hasClass("bullet") ) {
                    $('#conformPass').prop('disabled', false);
                    // $('button').prop('disabled', true);
                }else {
                    $('#conformPass').prop('disabled', true);
                    $('button').prop('disabled', true);
                }
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>
