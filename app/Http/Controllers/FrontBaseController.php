<?php

namespace App\Http\Controllers;


class FrontBaseController extends Controller
{
    public $user;
    public $pageTitle;
    public $settings;

    public function __construct()
    {
        parent::__construct();
    }
}
