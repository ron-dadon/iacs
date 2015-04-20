<?php

class Main_Forgot_Password_View extends IACS_View
{

    public function render()
    {
        $this->include_shared_view('header');
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-lg-4 col-lg-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>איפוס סיסמא</strong>
                </div>
                <div class="panel-body">
                    <p>לצורך איפוס הססיסמא אנא הכנס את האימייל המשוייך לחשבונך בשדה מטה. הודעת דואר אלקטרוני המכילה קישור לאיפוס הסיסמא תישלח אלייך בדקות הקרובות.</p>
                    <form method="post" id="forgot-password-form" data-toggle="validator">
                        <div class="form-group">
                            <label for="user-email">דואר אלקטרוני:</label>
                            <input class="form-control" type="email" id="user-email" name="user_email" required>
                            <div class="help-block with-errors"></div>
                        </div>
                    </form>
                </div>
                <div class="panel-footer text-left">
                    <a class="btn btn-link" href="<?php $this->public_path() ?>">חזור למסך התחברות</a>
                    <button class="btn btn-primary" onclick="$('#forgot-password-form').submit()"><i class="fa fa-check"></i> שלח</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
        $this->include_shared_view('footer');
    }

} 