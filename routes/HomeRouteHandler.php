<?php
class HomeRouteHandler extends PageRouteHandler {
    public function display() {
        echo F3::render('templates/home.php');
    }
}
