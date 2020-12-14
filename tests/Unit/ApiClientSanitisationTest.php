<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tests\CreatesApiClient;

class ApiClientSanitisationTest extends TestCase
{
    use CreatesApiClient;


    /**
     * Test stripping out HTML
     *
     * @return void
     */
    public function testStripHtml() {   
        $this->assertEquals(
            $this->createApiClient()->sanitiseText('Before <span>Inbetween</span>After'),
            'Before After'
        );
    }

    /**
     * Test stripping characters outside of printable ASCII range
     *
     * @return void
     */
    public function testStripUnprintableChars() {
        $this->assertEquals(
            $this->createApiClient()->sanitiseText("Test with unprintable chars\x00\x1f"),
            'Test with unprintable chars'
        );
    }

    /**
     * Test stripping out surrounding quotes
     *
     * @return void
     */
    public function testStripSurroundingQuotes() {
        $this->assertEquals(
            $this->createApiClient()->sanitiseText('"string in quotes"'),
            'string in quotes'
        );
    }
}
