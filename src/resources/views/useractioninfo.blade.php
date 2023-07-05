<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UserAction</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous" />

</head>
<body>
  <br><br>

  @if ($type == "VERIFY_SUCCESS")
    <h3 style="display:flex;justify-content:center;font-family: 'Poppins', sans-serif;font-weight:bold">Email Verification Successful!</h3><br>
    <div style="display:flex;justify-content:center;">
      <img src="{{env('APP_URL')}}/assets/VerifiedAcc.png" alt="" >
    </div>
    <div style="display:flex;justify-content:center;">
      <a href="{{env('APP_URL')}}"><button type="button">Go to login</button></a>
    </div>
  @endif

  @if ($type == "VALIDATION_ERROR")
    <h3 style="display:flex;justify-content:center;font-family: 'Poppins', sans-serif;font-weight:bold">Data broken!</h3><br>
    <div style="display:flex;justify-content:center;">
      <img src="{{env('APP_URL')}}/assets/validation.png" alt="" >
    </div>
    <p style="display:flex;justify-content:center;">Looks like the required data mismatch. Please try again later or contact our team for more support.</p>
  @endif

  @if ($type == "SERVER_ERROR")
    <h3 style="display:flex;justify-content:center;font-family: 'Poppins', sans-serif;font-weight:bold">Something went wrong! Try again later.</h3><br>
    <div style="display:flex;justify-content:center;">
      <img src="{{env('APP_URL')}}/assets/500ServerError.jpg" alt="" >
    </div>
    <p style="display:flex;justify-content:center;">Looks like there is an issue on our side, we are working on fixing it. Please try again later or contact our team for more support.</p>
  @endif

  @if ($type == "ALREADY_ACTIVATED")
    <h3 style="display:flex;justify-content:center;font-family: 'Poppins', sans-serif;font-weight:bold">Your account already been activated!</h3><br>
    <div style="display:flex;justify-content:center;">
      <img src="{{env('APP_URL')}}/assets/AlreadyActivated.jpg" alt="" width="400px" height="400px">
    </div>
    <div style="display:flex;justify-content:center;">
      <a href="{{env('APP_URL')}}"><button type="button">Go to login</button></a>
    </div>
  @endif

  @if ($type == "USER_NOT_FOUND")
      <h3 style="display:flex;justify-content:center;font-family: 'Poppins', sans-serif;font-weight:bold;">User not found...!</h3>
      <div style="display:flex;justify-content:center;">
        <img src="{{env('APP_URL')}}/assets/404Error1.png" alt="">
      </div>  
      <p style="display:flex;justify-content:center;">Looks like the user is not registered yet!.</p>
      <div style="display:flex;justify-content:center;">
        <a href="{{env('APP_URL')}}"><button type="button">Click here to sign up</button></a>
      </div>
  @endif
</body>
</html>