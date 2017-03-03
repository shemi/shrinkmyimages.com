<?php

namespace App\Http\Controllers;

use View;

class PageController extends Controller
{
    private $viewsPath = "pages";

    public function show($name)
    {
        $view = "{$this->viewsPath}.{$name}";

        if(! view()->exists($view)) {
            return $this->respondNotFound();
        }

        return $this->respond([
            'content' => view($view)->render()
        ]);
    }

}