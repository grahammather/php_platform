<?php
class TestRouteHandler extends RouteHandler {

    public function display() {
       echo F3::render('templates/test.php');
    }

}
