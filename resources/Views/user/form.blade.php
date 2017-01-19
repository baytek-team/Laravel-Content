<div class="two fields">
    <div class="eight wide field{{ $errors->has('first_name') ? ' error' : '' }}">
        <label for="first-name">First Name</label>
        <input type="text" id="first-name" name="first_name" placeholder="First Name" value="{{ old('first_name', $user->first_name) }}">
    </div>
    <div class="eight wide field{{ $errors->has('last_name') ? ' error' : '' }}">
        <label for="last-name">Last Name</label>
        <input type="text" id="last-name" name="last_name" placeholder="Last Name" value="{{ old('last_name', $user->last_name) }}">
    </div>
</div>
<div class="two fields">
    <div class="field{{ $errors->has('email') ? ' error' : '' }}">
        <label for="email">Email Address</label>
        <input type="text" readonly="" id="email" name="email" placeholder="{{ old('email', $user->email) }}">
    </div>
    <div class="field">
        <label for="password">Password</label>
        {!! Menu::form(
            ['Send Password Reset Link' => [
                'action' => 'Admin\UserController@sendUserPasswordResetLink',
                'method' => 'POST',
                'class' => 'ui button',
                'prepend' => '<i class="mail icon"></i>',
                'confirm' => 'Are you sure you want to send a reset password email: '.$user->first_name.' '.$user->last_name.'?',
            ]],
            $user)
        !!}
    </div>
</div>