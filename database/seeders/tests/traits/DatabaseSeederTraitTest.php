<?php

namespace Database\Seeders\tests\traits;

use Database\Seeders\tests\DatabaseSeederTest;

trait DatabaseSeederTraitTest
{

    public function setUpDatabaseSeederTraitTest()
    {
        $this->artisan('db:seed',['--class' => DatabaseSeederTest::class]);
    }

}
