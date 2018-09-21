<?php
$this->assign('title', 'ToDoList');

echo $this->Flash->render('auth');

echo $this->Form->create('',array(
                'url' => 'login',
                'inputDefaults' => array(
                        'div' => 'form-group',
                ),
                "class" => "col-lg-4"
        )
);

echo $this->Form->input("user",array(
        'class' => 'form-control',
        'placeholder' => 'Introduce your username',
));
echo $this->Form->input("password",array(
        
        'class' => 'form-control',
        'placeholder' => 'Introduce your password'
));

echo $this->Form->submit('Log in',array(
        'class' => 'btn btn-success'
));

echo $this->Html->link('Still not registered? Sign up', "#",
        array(
                'data-toggle' => 'modal',
                'data-target' => '#sign_up_modal'
        )
        );


echo $this->Form->end();

?>

    <div class="modal fade" tabindex="-1" role="dialog" id="sign_up_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Sign up</h4>
                </div>
                <div class="modal-body">

                    <?php

                        echo $this->Form->create('signUp',array(
                                        'url' => 'signUp',
                                        'inputDefaults' => array(
                                                'div' => 'form-group',
                                        ),
                                        'autocomplete' => 'off'
                                )
                        );

                        echo $this->Form->input("user",array(
                                'label' => 'Username',
                                'class' => 'form-control',
                                'placeholder' => 'Introduce your username',
                                'required' => 'required'
                        ));
                        echo $this->Form->input("password",array(
                                'label' => 'Password',
                                'class' => 'form-control',
                                'placeholder' => 'Introduce your password',
                                'required' => 'required'
                        ));
                        
                        
                        echo $this->Form->input("name",array(
                                'label' => 'Your name',
                                'class' => 'form-control',
                                'placeholder' => 'Introduce your name',
                                'required' => 'required'
                        ));
                        
                         echo $this->Form->submit('Sign up',array(
                                'class' => 'btn btn-primary'
                        ));
                        
                        echo $this->Form->end();
                        
                        ?>

                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function () {

            var validForm = true;

            $("#signUpUser").on("blur", function () {

                var $input = $(this);

                $.ajax({
                        'method': 'POST',
                        url: '/users/checkUser',
                        data: {
                            user: $input.val()
                        }
                    })
                    .done(function (data) {

                        if (data) {

                            $input.css("border", "2px red solid");
                            $input.prev().text("Username already exists").css("color", "red");
                            validForm = false;

                            $("#signUpIndexForm input:submit").prop("disabled", true);


                        } else {

                            $input.css("border", "1px solid #ccc");
                            $input.prev().text("Username").css("color", "inherit");
                            validForm = true;

                            $("#signUpIndexForm input:submit").removeAttr("disabled");

                        }

                    })
                    .fail(function () {

                    })

            });

            $("#signUpIndexForm").on("submit", function () {

                if (!validForm) {
                    return false;
                }
            })

        });
    </script>