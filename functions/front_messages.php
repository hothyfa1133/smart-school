<?php

function message ($message, $type = false) {

    if($type){
        $type = 'alert-success';
    }else{
        $type = 'alert-danger';
    }

    return '<div class="alert ' . $type . ' alert-dismissible fade show main-user-messages" role="alert">
    ' . $message . '
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}