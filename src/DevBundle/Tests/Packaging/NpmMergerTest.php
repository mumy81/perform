<?php

namespace Perform\DevBundle\Tests\Packaging;

use Perform\DevBundle\Packaging\NpmMerger;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class NpmMergerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->merger = new NpmMerger();
    }

    public function testLoadRequirements()
    {
        $expected = [
            "vue" => "^2.5.3",
            "bootstrap" => "^4.0.0-beta.2",
            "bootstrap-vue" => "^1.0.2",
        ];
        $this->assertSame($expected, $this->merger->loadRequirements(__DIR__.'/sample_package.json'));
    }

    public function validProvider()
    {
        return [
            [
                //existing
                [
                    'some-dep' => '^3.6.0',
                ],
                //new
                [
                    'some-other-dep' => '^0.1',
                ],
                //expected
                [
                    'some-dep' => '^3.6.0',
                    'some-other-dep' => '^0.1',
                ]
            ],

            [
                [
                    'some-dep' => '^3.6.0',
                ],
                [
                    'some-dep' => '^3.6.0',
                ],
                [
                    'some-dep' => '^3.6.0',
                ]
            ],

            [
                [
                    'some-dep' => '^3.6.0',
                ],
                [
                    'some-dep' => '^3.7.0',
                ],
                [
                    'some-dep' => '^3.7.0',
                ],
            ],

            [
                [
                    'some-dep' => '^3.7.0',
                ],
                [
                    'some-dep' => '^3.6.0',
                ],
                [
                    'some-dep' => '^3.7.0',
                ],
            ],
        ];
    }

    /**
     * @dataProvider validProvider
     */
    public function testMergeWithValidConstraints($existing, $new, $expected)
    {
        $result = $this->merger->mergeRequirements($existing, $new);
        $this->assertTrue($result->isValid());
        $this->assertSame($expected, $result->getResolvedRequirements());
        // $this->assertSame($expectedNew, $result->getNewRequirements());
    }
}
