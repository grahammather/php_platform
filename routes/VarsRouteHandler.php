<?php
class VarsRouteHandler extends PageRouteHandler {
    public function display() {
        echo F3::render('templates/vars.js.php');
    }
}
