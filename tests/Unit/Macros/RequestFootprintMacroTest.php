<?php

namespace Ermradulsharma\Footprints\Tests\Unit\Macros;

use Ermradulsharma\Footprints\Tests\TestCase;

class RequestFootprintMacroTest extends TestCase
{
    public function test_footprint_macro()
    {
        $request = \Illuminate\Http\Request::create('/test', 'GET');

        // The macro is registered in the ServiceProvider
        $this->assertTrue(\Illuminate\Http\Request::hasMacro('footprint'));

        $this->assertNotEmpty($request->footprint());
    }
}
