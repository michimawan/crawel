<?php

class BaseRouteTest extends TestCase
{
    public function assertRoutesTo($response, $controllerAndAction, $expectedRouteInputs = []) {
        foreach ($expectedRouteInputs as $inputKey => $inputValue) {
            $this->assertEquals($inputValue, Route::input($inputKey));
        }
        $this->assertEquals('App\Http\Controllers\\' . $controllerAndAction, $this->app['router']->currentRouteAction());
    }

    public function assertCurrentRouteName($name)
    {
        $this->assertEquals($name, Route::getCurrentRoute()->getName(), "Current route name mismatch");
    }

    public function assertCurrentRouteAction($action)
    {
        $namespace = 'App\Http\Controllers';
        if (!(strpos($action, '\\') === 0)) $action = $namespace.'\\'.$action;
        $this->assertEquals($action, Route::getCurrentRoute()->getActionName(), "Current route action mismatch");
    }

    public function assertNotRedirected()
    {
        $this->assertNotEquals(\Illuminate\Http\RedirectResponse::class, get_class($this->response), "Current response is redirected");
    }
}
