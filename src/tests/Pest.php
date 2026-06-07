<?php

declare(strict_types=1);

uses(Tests\TestCase::class)->in('Feature');
uses(Tests\TestCase::class)->in('../app/Packages/Tests');
uses(\PHPUnit\Framework\TestCase::class)->in('Unit');
uses(\PHPUnit\Framework\TestCase::class)->in('../app/Packages/Domain');
uses(\PHPUnit\Framework\TestCase::class)->in('../app/Packages/Application');
uses(\PHPUnit\Framework\TestCase::class)->in('../app/Packages/Infrastructure');
uses(\PHPUnit\Framework\TestCase::class)->in('../app/Packages/Controller');
